<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Models\Room;
use App\Models\Thing;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik umum
        $totalBookingHariIni = Borrow::whereDate('created_at', today())->count();
        $totalPelanggaran = Borrow::where('status', 'Pelanggaran')
            ->where('diverifikasi_admin', false)
            ->count();
        $totalRuangan = Room::count();
        $totalBarang = Thing::count();

        // Booking yang perlu perhatian
        $bookingTanpaCheckin = Borrow::where('status', 'Booking')
            ->where('waktu_mulai_booking', '<=', now())
            ->where('waktu_checkin', null)
            ->count();

        // Pelanggaran yang perlu ditindak lanjuti
        $pelanggaram = Borrow::where('status', 'Pelanggaran')
            ->where('diverifikasi_admin', false)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'totalBookingHariIni' => $totalBookingHariIni,
            'totalPelanggaran' => $totalPelanggaran,
            'totalRuangan' => $totalRuangan,
            'totalBarang' => $totalBarang,
            'bookingTanpaCheckin' => $bookingTanpaCheckin,
            'pelanggaram' => $pelanggaram,
        ]);
    }

    // ===== MANAJEMEN BARANG & RUANGAN =====

    public function daftarRuangan()
    {
        $ruangan = Room::paginate(15);
        return view('admin.sarana.ruangan.daftar', ['ruangan' => $ruangan]);
    }

    public function tambahRuangan(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'kode_room' => 'required|unique:rooms',
                'nama' => 'required|string',
            ]);

            Room::create([
                'kode_room' => $request->kode_room,
                'nama' => $request->nama,
                'status' => 'Tersedia'
            ]);

            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil ditambahkan');
        }

        return view('admin.sarana.ruangan.form');
    }

    public function editRuangan($id, Request $request)
    {
        $ruangan = Room::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'nama' => 'required|string',
                'status' => 'required|in:Tersedia,Dipakai,Maintenance',
            ]);

            $ruangan->update($request->only('nama', 'status'));
            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil diubah');
        }

        return view('admin.sarana.ruangan.form', ['ruangan' => $ruangan]);
    }

    public function hapusRuangan($id)
    {
        $ruangan = Room::findOrFail($id);
        $ruangan->delete();
        return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil dihapus');
    }

    // Daftar barang
    public function daftarBarang()
    {
        $barang = Thing::with('room')->paginate(15);
        return view('admin.sarana.barang.daftar', ['barang' => $barang]);
    }

    public function tambahBarang(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'kode_thing' => 'required|unique:things',
                'nama' => 'required|string',
                'room_id' => 'nullable|exists:rooms,id',
            ]);

            Thing::create([
                'kode_thing' => $request->kode_thing,
                'nama' => $request->nama,
                'room_id' => $request->room_id,
                'status' => 'Tersedia'
            ]);

            return redirect()->route('admin.barang')->with('success', 'Barang berhasil ditambahkan');
        }

        $ruangan = Room::all();
        return view('admin.sarana.barang.form', ['ruangan' => $ruangan]);
    }

    public function editBarang($id, Request $request)
    {
        $barang = Thing::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'nama' => 'required|string',
                'status' => 'required|in:Tersedia,Dipinjam,Tidak Tersedia',
                'room_id' => 'nullable|exists:rooms,id',
            ]);

            $barang->update($request->only('nama', 'status', 'room_id'));
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil diubah');
        }

        $ruangan = Room::all();
        return view('admin.sarana.barang.form', ['barang' => $barang, 'ruangan' => $ruangan]);
    }

    public function hapusBarang($id)
    {
        $barang = Thing::findOrFail($id);
        $barang->delete();
        return redirect()->route('admin.barang')->with('success', 'Barang berhasil dihapus');
    }

    // ===== MANAJEMEN PENALTI =====

    public function daftarPelanggaran()
    {
        $pelanggaran = Borrow::where('status', 'Pelanggaran')
            ->with(['user', 'room', 'thing'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.penalti.daftar', ['pelanggaran' => $pelanggaran]);
    }

    public function tindakLanjutPelanggaran($id, Request $request)
    {
        $request->validate([
            'keputusan' => 'required|string',
            'tipe_penalti' => 'nullable|string',
        ]);

        $borrow = Borrow::findOrFail($id);
        $borrow->update([
            'diverifikasi_admin' => true,
            'catatan_pelanggaran' => $borrow->catatan_pelanggaran . ' | Admin: ' . $request->keputusan
        ]);

        return redirect()->back()->with('success', 'Tindak lanjut penalti berhasil disimpan');
    }

    // ===== LAPORAN =====

    public function laporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();

        $laporan = Borrow::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
            ->with(['user', 'room', 'thing'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistik = [
            'total' => $laporan->total(),
            'selesai' => Borrow::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('status', 'Selesai')->count(),
            'pelanggaran' => Borrow::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('status', 'Pelanggaran')->count(),
        ];

        return view('admin.laporan.peminjaman', [
            'laporan' => $laporan,
            'statistik' => $statistik,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ]);
    }

    // Download laporan PDF
    public function exportLaporan(Request $request)
    {
        // Implementasi export ke PDF nantinya
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();

        $laporan = Borrow::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
            ->with(['user', 'room', 'thing'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Export PDF akan diimplementasikan',
            'total' => $laporan->count()
        ]);
    }

    // Notifikasi
    public function notifikasi()
    {
        return view('admin.notifikasi');
    }
}

