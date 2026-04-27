<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\DamageReport;
use App\Models\AppSetting;
use App\Models\PenaltyLog;
use App\Models\Thing;
use App\Models\Room;
use App\Models\RoomContent;
use App\Models\User;
use App\Services\BorrowLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function __construct(private readonly BorrowLifecycleService $borrowLifecycleService)
    {
    }

    // Dashboard Admin
    public function index()
    {
        $this->borrowLifecycleService->enforceRules();
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        $totalRuangan = Room::count();
        $totalBarang = Thing::count();
        $totalPeminjaman = Borrow::whereDate('waktu_mulai_booking', today())->count();
        $totalPelanggaran = Borrow::where('status', 'Pelanggaran')->count();
        $totalBookingHariIni = Borrow::whereDate('waktu_mulai_booking', today())->count();
        $bookingTanpaCheckin = Borrow::where('status', 'Booking')
            ->whereNull('waktu_checkin')
            ->where('waktu_mulai_booking', '<=', now()->subMinutes(15))
            ->distinct('kode_booking')
            ->count('kode_booking');
        
        // Booking yang sedang berlangsung & perlu diverifikasi
        $bookingSedangBerlangsung = Borrow::where('status', 'Berlangsung')
            ->count();
        $bookingPerluVerifikasi = Borrow::where('status', 'Berlangsung')
            ->where('diverifikasi_admin', false)
            ->count();
        
        $pelanggaran = Borrow::with(['user', 'room', 'thing'])
            ->where('waktu_selesai_booking', '<=', now())
            ->whereNull('waktu_checkin')
            ->whereNull('waktu_checkout')
            ->orderByDesc('waktu_selesai_booking')
            ->limit(10)
            ->get();
        
        // Booking yang perlu diverifikasi untuk ditampilkan di dashboard
        $bookingMenungguVerifikasi = Borrow::where('status', 'Berlangsung')
            ->with('user', 'room', 'thing')
            ->where('diverifikasi_admin', false)
            ->orderBy('waktu_selesai_booking', 'asc')
            ->limit(5)
            ->get();

        $tiketHariIni = Borrow::with(['user', 'thing', 'room'])
            ->whereDate('waktu_mulai_booking', today())
            ->orderBy('waktu_mulai_booking')
            ->get()
            ->unique('kode_booking')
            ->values()
            ->take(8);

        $tiketBerlangsung = Borrow::with(['user', 'thing', 'room'])
            ->where('status', 'Berlangsung')
            ->orderBy('waktu_selesai_booking')
            ->get()
            ->unique('kode_booking')
            ->values()
            ->take(8);

        $laporanRusakTerbaru = DamageReport::with(['user', 'thing'])
            ->latest()
            ->limit(8)
            ->get();

        $totalLaporanRusak = DamageReport::where(function ($query) {
            $query->whereIn('status', ['Sedang Ditinjau', 'Menunggu Verifikasi'])
                ->orWhereNull('status');
        })->count();
        $laporanRusakMenunggu = $totalLaporanRusak;
        $totalPeminjamDibatasi = User::where('role', 'peminjam')
            ->where(function ($query) use ($threshold) {
                $query->where('penalty_points', '>', $threshold)
                    ->orWhereNotNull('blocked_at');
            })
            ->count();
        
        return view('admin.dashboard', [
            'totalRuangan' => $totalRuangan,
            'totalBarang' => $totalBarang,
            'totalPeminjaman' => $totalPeminjaman,
            'totalPelanggaran' => $totalPelanggaran,
            'totalBookingHariIni' => $totalBookingHariIni,
            'bookingTanpaCheckin' => $bookingTanpaCheckin,
            'bookingSedangBerlangsung' => $bookingSedangBerlangsung,
            'bookingPerluVerifikasi' => $bookingPerluVerifikasi,
            'pelanggaran' => $pelanggaran,
            'bookingMenungguVerifikasi' => $bookingMenungguVerifikasi,
            'totalLaporanRusak' => $totalLaporanRusak,
            'laporanRusakMenunggu' => $laporanRusakMenunggu,
            'totalPeminjamDibatasi' => $totalPeminjamDibatasi,
            'tiketHariIni' => $tiketHariIni,
            'tiketBerlangsung' => $tiketBerlangsung,
            'laporanRusakTerbaru' => $laporanRusakTerbaru,
        ]);
    }

    // ===== MANAJEMEN RUANGAN =====
    
    public function daftarRuangan()
    {
        $search = trim((string) request('q', ''));
        $status = trim((string) request('status', ''));
        $lantai = trim((string) request('lantai', ''));
        $sortBy = trim((string) request('sort_by', 'kode_room'));
        $sortOrder = trim((string) request('sort_order', 'asc'));

        // Validasi sort parameters
        $validSortColumns = ['kode_room', 'nama', 'lantai', 'status'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'kode_room';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $ruangan = Room::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_room', 'like', "%{$search}%")
                        ->orWhere('lantai', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($lantai !== '', function ($query) use ($lantai) {
                $query->where('lantai', 'like', "%{$lantai}%");
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->withQueryString();

        return view('admin.sarana.ruangan.daftar', [
            'ruangan' => $ruangan,
            'search' => $search,
            'statusFilter' => $status,
            'lantaiFilter' => $lantai,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function tambahRuangan(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validasi
            $validated = $request->validate([
                'kode_room' => 'required|unique:rooms|max:20',
                'nama' => 'required|max:255',
                'lantai' => 'required|string|max:30',
                'status' => 'required|in:Tersedia,Dipakai,Maintenance,Tidak Tersedia',
            ]);

            Room::create($validated);
            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil ditambahkan');
        }

        return view('admin.sarana.ruangan.form', ['ruangan' => null]);
    }

    public function editRuangan($id, Request $request)
    {
        $ruangan = Room::findOrFail($id);

        if ($request->isMethod('post')) {
            // Validasi
            $validated = $request->validate([
                'kode_room' => 'required|unique:rooms,kode_room,' . $ruangan->id . '|max:20',
                'nama' => 'required|max:255',
                'lantai' => 'required|string|max:30',
                'status' => 'required|in:Tersedia,Dipakai,Maintenance,Tidak Tersedia',
            ]);

            $ruangan->update($validated);
            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil diperbarui');
        }

        return view('admin.sarana.ruangan.form', ['ruangan' => $ruangan]);
    }

    public function hapusRuangan($id)
    {
        $ruangan = Room::findOrFail($id);
        $ruangan->delete();
        return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil dihapus');
    }

    // ===== MANAJEMEN BARANG =====
    
    public function daftarBarang()
    {
        $search = trim((string) request('q', ''));
        $status = trim((string) request('status', ''));
        $roomId = trim((string) request('room_id', ''));
        $sortBy = trim((string) request('sort_by', 'kode_thing'));
        $sortOrder = trim((string) request('sort_order', 'asc'));

        // Validasi sort parameters
        $validSortColumns = ['kode_thing', 'nama', 'status'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'kode_thing';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $barang = Thing::with('room')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_thing', 'like', "%{$search}%")
                        ->orWhereHas('room', function ($roomQuery) use ($search) {
                            $roomQuery->where('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($roomId !== '', function ($query) use ($roomId) {
                $query->where('room_id', $roomId);
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->withQueryString();

        $ruanganFilter = Room::orderBy('nama', 'asc')->get(['id', 'nama']);

        return view('admin.sarana.barang.daftar', [
            'barang' => $barang,
            'ruanganFilter' => $ruanganFilter,
            'search' => $search,
            'statusFilter' => $status,
            'roomFilter' => $roomId,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function tambahBarang(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validasi
            $validated = $request->validate([
                'kode_thing' => 'required|unique:things|max:20',
                'nama' => 'required|max:255',
                'room_id' => 'required|exists:rooms,id',
                'status' => 'required|in:Tersedia,Dipinjam,Tidak Tersedia',
            ]);

            Thing::create($validated);
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil ditambahkan');
        }

        $ruangan = Room::orderBy('nama', 'asc')->get();
        return view('admin.sarana.barang.form', ['barang' => null, 'ruangan' => $ruangan]);
    }

    public function editBarang($id, Request $request)
    {
        $barang = Thing::findOrFail($id);

        if ($request->isMethod('post')) {
            // Validasi
            $validated = $request->validate([
                'kode_thing' => 'required|unique:things,kode_thing,' . $barang->id . '|max:20',
                'nama' => 'required|max:255',
                'room_id' => 'required|exists:rooms,id',
                'status' => 'required|in:Tersedia,Dipinjam,Tidak Tersedia',
            ]);

            $barang->update($validated);
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil diperbarui');
        }

        $ruangan = Room::orderBy('nama', 'asc')->get();
        return view('admin.sarana.barang.form', ['barang' => $barang, 'ruangan' => $ruangan]);
    }

    public function hapusBarang($id)
    {
        $barang = Thing::findOrFail($id);
        $barang->delete();
        return redirect()->route('admin.barang')->with('success', 'Barang berhasil dihapus');
    }

    // ===== MANAJEMEN PENALTI =====

    public function tambahPenaltiPeminjam(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1|max:10',
            'borrow_id' => 'nullable|exists:borrows,id',
            'reason' => 'required|string|min:5|max:255',
        ]);

        $user = User::where('id', $validated['user_id'])
            ->where('role', 'peminjam')
            ->firstOrFail();

        $user->increment('penalty_points', $validated['points']);

        PenaltyLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'borrow_id' => $validated['borrow_id'] ?? null,
            'action' => 'add',
            'points' => $validated['points'],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Penalti berhasil ditambahkan.');
    }

    public function setPenaltiPeminjam(Request $request, int $userId)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:0|max:100',
            'borrow_id' => 'nullable|exists:borrows,id',
            'reason' => 'required|string|min:5|max:255',
        ]);

        $user = User::where('id', $userId)
            ->where('role', 'peminjam')
            ->firstOrFail();

        $user->update(['penalty_points' => $validated['points']]);

        PenaltyLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'borrow_id' => $validated['borrow_id'] ?? null,
            'action' => 'set',
            'points' => $validated['points'],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Poin penalti berhasil diperbarui.');
    }

    public function kurangiPenaltiPeminjam(Request $request, int $userId)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:100',
            'borrow_id' => 'nullable|exists:borrows,id',
            'reason' => 'required|string|min:5|max:255',
        ]);

        $user = User::where('id', $userId)
            ->where('role', 'peminjam')
            ->firstOrFail();

        $currentPoints = (int) $user->penalty_points;
        $newPoints = max(0, $currentPoints - (int) $validated['points']);
        $reducedPoints = $currentPoints - $newPoints;

        $user->update(['penalty_points' => $newPoints]);

        PenaltyLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'borrow_id' => $validated['borrow_id'] ?? null,
            'action' => 'subtract',
            'points' => $reducedPoints,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Poin penalti berhasil dikurangi.');
    }

    public function resetPenaltiPeminjam(Request $request, int $userId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:5|max:255',
        ]);

        $user = User::where('id', $userId)
            ->where('role', 'peminjam')
            ->firstOrFail();

        $previousPoints = (int) $user->penalty_points;
        $user->update(['penalty_points' => 0]);

        PenaltyLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'action' => 'reset',
            'points' => $previousPoints,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Poin penalti berhasil direset ke 0.');
    }

    public function tindakLanjutPelanggaran($id, Request $request)
    {
        $borrow = Borrow::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Selesai,Berlangsung',
            'catatan_pelanggaran' => 'nullable|string',
        ]);

        $borrow->update([
            'status' => $validated['status'],
            'catatan_pelanggaran' => $validated['catatan_pelanggaran'] ?? $borrow->catatan_pelanggaran,
        ]);

        return redirect()->route('admin.laporan')->with('success', 'Penalti diperbarui');
    }

    // ===== LAPORAN =====
    
    public function laporan()
    {
        $bulan = request('bulan');
        $tahun = request('tahun');
        $search = trim((string) request('q', ''));

        $laporanUsers = User::where('role', 'peminjam')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->whereHas('borrows', function ($query) use ($bulan, $tahun) {
                $query->when($bulan, function ($borrowQuery) use ($bulan) {
                    $borrowQuery->whereMonth('created_at', $bulan);
                })->when($tahun, function ($borrowQuery) use ($tahun) {
                    $borrowQuery->whereYear('created_at', $tahun);
                });
            })
            ->withCount([
                'borrows as total_tiket' => function ($query) use ($bulan, $tahun) {
                    $query->when($bulan, function ($borrowQuery) use ($bulan) {
                        $borrowQuery->whereMonth('created_at', $bulan);
                    })->when($tahun, function ($borrowQuery) use ($tahun) {
                        $borrowQuery->whereYear('created_at', $tahun);
                    });
                }
            ])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $pelanggaran = Borrow::with(['user', 'room', 'thing'])
            ->where(function ($query) {
                $query->where('status', 'Pelanggaran')
                    ->orWhere(function ($lateQuery) {
                        $lateQuery->where('waktu_selesai_booking', '<=', now())
                            ->whereNull('waktu_checkin')
                            ->whereNull('waktu_checkout');
                    });
            })
            ->when($bulan, function ($query) use ($bulan) {
                $query->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('created_at', $tahun);
            })
            ->orderByDesc('waktu_selesai_booking')
            ->limit(20)
            ->get();

        return view('admin.laporan.index', [
            'laporanUsers' => $laporanUsers,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'search' => $search,
            'pelanggaran' => $pelanggaran,
        ]);
    }

    public function laporanTiketUser(int $userId, Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $user = User::where('role', 'peminjam')->findOrFail($userId);

        $borrows = Borrow::with(['room', 'thing'])
            ->where('user_id', $user->id)
            ->when($bulan, function ($query) use ($bulan) {
                $query->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('created_at', $tahun);
            })
            ->orderByDesc('created_at')
            ->get();

        $groupedTickets = $borrows->groupBy('kode_booking');

        return view('admin.laporan.tiket-user', [
            'user' => $user,
            'groupedTickets' => $groupedTickets,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    public function laporanRusak()
    {
        $this->borrowLifecycleService->enforceRules();

        $reports = DamageReport::with(['user', 'thing', 'borrow'])
            ->latest()
            ->paginate(20);

        return view('admin.laporan.rusak', [
            'reports' => $reports,
        ]);
    }

    public function laporanRusakDetail(int $id)
    {
        $this->borrowLifecycleService->enforceRules();

        $report = DamageReport::with(['user', 'thing.room', 'borrow'])
            ->findOrFail($id);

        return view('admin.laporan.rusak-detail', [
            'report' => $report,
        ]);
    }

    public function updateLaporanRusakStatus(int $id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:Sedang Ditinjau,Ditolak,Selesai Ditangani',
        ]);

        $report = DamageReport::findOrFail($id);
        $report->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.laporan.rusak.detail', $report->id)
            ->with('success', 'Status laporan barang rusak berhasil diperbarui.');
    }

    public function laporanRusakFoto(int $id)
    {
        $report = DamageReport::findOrFail($id);
        $path = trim((string) $report->foto_bukti);

        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    public function laporanTiketFoto(int $id, string $jenis)
    {
        $borrow = Borrow::findOrFail($id);

        $path = match ($jenis) {
            'awal' => trim((string) $borrow->foto_awal),
            'akhir' => trim((string) $borrow->foto_akhir),
            default => '',
        };

        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
    public function exportLaporan(Request $request)
    {
        $bulan = request('bulan');
        $tahun = request('tahun');

        $laporan = Borrow::query()
            ->when($bulan, function ($query) use ($bulan) {
                $query->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('created_at', $tahun);
            })
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // CSV export
        $headers = [
            "Content-type" => "text/csv;charset=UTF-8",
            "Content-Disposition" => "attachment; filename=laporan-peminjaman-" . date('Y-m-d-His') . ".csv"
        ];

        $callback = function() use ($laporan) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Kode Booking', 'Peminjam', 'Tipe', 'Waktu Mulai', 'Waktu Selesai', 'Status', 'Penalti']);
            
            // Data
            foreach ($laporan as $item) {
                fputcsv($file, [
                    $item->kode_booking,
                    $item->user->name,
                    $item->tipe,
                    $item->waktu_mulai_booking,
                    $item->waktu_selesai_booking,
                    $item->status,
                    $item->status === 'Pelanggaran' ? 'Ya' : 'Tidak',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ===== MANAJEMEN USER =====
    
    public function daftarUser()
    {
        return redirect()->route('admin.user.peminjam');
    }

    public function daftarUserPeminjam()
    {
        $threshold = AppSetting::integer('penalty_block_threshold', 20);
        $sortBy = trim((string) request('sort_by', 'name'));
        $sortOrder = trim((string) request('sort_order', 'asc'));

        // Validasi sort parameters
        $validSortColumns = ['name', 'email', 'created_at', 'penalty_points'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'name';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $users = User::where('role', 'peminjam')
            ->with(['penaltyLogs' => function ($query) {
                $query->with(['admin', 'borrow'])->latest()->limit(10);
            }])
            ->orderBy($sortBy, $sortOrder)
            ->paginate(15)
            ->withQueryString();

        return view('admin.user.daftar', [
            'users' => $users,
            'pageType' => 'peminjam',
            'penaltyBlockThreshold' => $threshold,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function daftarUserStaff()
    {
        $sortBy = trim((string) request('sort_by', 'name'));
        $sortOrder = trim((string) request('sort_order', 'asc'));

        // Validasi sort parameters
        $validSortColumns = ['name', 'email', 'role', 'created_at'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'name';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $users = User::where('role', 'admin')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(15)
            ->withQueryString();
        
        return view('admin.user.daftar', [
            'users' => $users,
            'pageType' => 'staff',
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function tambahUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:admin,peminjam',
            ]);

            $this->validatePeminjamEmailDomain($validated['email'], $validated['role']);

            $validated['password'] = bcrypt($validated['password']);
            User::create($validated);
            return redirect()->route($validated['role'] === 'peminjam' ? 'admin.user.peminjam' : 'admin.user.staff')->with('success', 'User berhasil ditambahkan');
        }

        return view('admin.user.form', ['user' => null]);
    }

    public function editUser($id, Request $request)
    {
        $user = User::findOrFail($id);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'role' => 'required|in:admin,peminjam',
            ]);

            $this->validatePeminjamEmailDomain($validated['email'], $validated['role']);

            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);
            return redirect()->route($user->role === 'peminjam' ? 'admin.user.peminjam' : 'admin.user.staff')->with('success', 'User berhasil diperbarui');
        }

        return view('admin.user.form', ['user' => $user]);
    }

    public function hapusUser($id)
    {
        $user = User::findOrFail($id);
        $redirectRoute = $user->role === 'peminjam' ? 'admin.user.peminjam' : 'admin.user.staff';
        $user->delete();
        return redirect()->route($redirectRoute)->with('success', 'User berhasil dihapus');
    }

    public function blokirUserPeminjam(int $id)
    {
        $user = User::where('role', 'peminjam')->findOrFail($id);
        $user->update(['blocked_at' => now()]);

        return back()->with('success', 'Peminjam berhasil diblokir.');
    }

    public function bukaBlokirUserPeminjam(int $id)
    {
        $user = User::where('role', 'peminjam')->findOrFail($id);
        $user->update(['blocked_at' => null]);

        return back()->with('success', 'Blokir peminjam berhasil dibuka.');
    }

    public function importUserCSV(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt',
            ]);

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            // Skip header
            foreach (array_slice($data, 1) as $row) {
                if (count($row) >= 4) {
                    $role = trim($row[3]);
                    $email = trim($row[1]);

                    $this->validatePeminjamEmailDomain($email, $role);

                    User::updateOrCreate(
                        ['email' => $email],
                        [
                            'name' => trim($row[0]),
                            'password' => bcrypt(trim($row[2])),
                            'role' => $role,
                        ]
                    );
                }
            }

            return redirect()->route('admin.user.peminjam')->with('success', 'User berhasil diimport dari CSV');
        }

        return view('admin.user.import');
    }

    // ===== MANAJEMEN RUANGAN DENGAN BARANG =====
    
    public function editRuanganWithContents($id, Request $request)
    {
        $ruangan = Room::with(['contents.thing', 'things'])->findOrFail($id);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'kode_room' => 'required|unique:rooms,kode_room,' . $ruangan->id . '|max:20',
                'nama' => 'required|max:255',
                'lantai' => 'required|string|max:30',
                'status' => 'required|in:Tersedia,Dipakai,Maintenance,Tidak Tersedia',
                'barang' => 'nullable|array',
                'barang.*' => 'nullable|exists:things,id',
            ]);

            $rawSelectedThingIds = collect($validated['barang'] ?? [])
                ->filter(fn ($id) => !empty($id))
                ->map(fn ($id) => (int) $id)
                ->values();

            $selectedThingIds = $rawSelectedThingIds
                ->unique()
                ->values();

            if ($rawSelectedThingIds->count() !== $selectedThingIds->count()) {
                return back()->withErrors([
                    'barang' => 'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
                ])->withInput();
            }

            $currentRoomThingIds = $ruangan->contents->pluck('thing_id')
                ->merge($ruangan->things->pluck('id'))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $newlyAddedThingIds = $selectedThingIds
                ->diff($currentRoomThingIds)
                ->values();

            $isAssignedToOtherRoom = $newlyAddedThingIds->isNotEmpty() && (
                Thing::whereIn('id', $newlyAddedThingIds->all())
                    ->whereNotNull('room_id')
                    ->where('room_id', '!=', $ruangan->id)
                    ->exists()
                || RoomContent::whereIn('thing_id', $newlyAddedThingIds->all())
                    ->where('room_id', '!=', $ruangan->id)
                    ->exists()
            );

            if ($isAssignedToOtherRoom) {
                return back()->withErrors([
                    'barang' => 'Ada barang yang sudah dipakai di ruangan lain. Pilih barang yang belum dipakai.',
                ])->withInput();
            }

            DB::transaction(function () use ($ruangan, $validated, $selectedThingIds) {
                $ruangan->update([
                    'kode_room' => $validated['kode_room'],
                    'nama' => $validated['nama'],
                    'lantai' => $validated['lantai'],
                    'status' => $validated['status'],
                ]);

                // Sync mapping table room_contents.
                $ruangan->contents()->delete();
                if ($selectedThingIds->isNotEmpty()) {
                    RoomContent::whereIn('thing_id', $selectedThingIds->all())
                        ->where('room_id', '!=', $ruangan->id)
                        ->delete();

                    foreach ($selectedThingIds as $thingId) {
                        $ruangan->contents()->create([
                            'thing_id' => $thingId,
                        ]);
                    }
                }

                // Sync source-of-truth field used by booking flow.
                $query = Thing::where('room_id', $ruangan->id);
                if ($selectedThingIds->isNotEmpty()) {
                    $query->whereNotIn('id', $selectedThingIds->all());
                }
                $query->update(['room_id' => null]);

                if ($selectedThingIds->isNotEmpty()) {
                    Thing::whereIn('id', $selectedThingIds->all())
                        ->update(['room_id' => $ruangan->id]);
                }
            });

            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil diperbarui');
        }

        $selectedThingIds = $ruangan->contents->pluck('thing_id')
            ->merge($ruangan->things->pluck('id'))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $usedByOtherRoomsIds = RoomContent::where('room_id', '!=', $ruangan->id)
            ->pluck('thing_id')
            ->map(fn ($id) => (int) $id)
            ->unique();

        $semua_barang = Thing::query()
            ->where(function ($query) use ($ruangan, $selectedThingIds) {
                $query->whereNull('room_id')
                    ->orWhere('room_id', $ruangan->id);

                if ($selectedThingIds->isNotEmpty()) {
                    $query->orWhereIn('id', $selectedThingIds->all());
                }
            })
            ->when($usedByOtherRoomsIds->isNotEmpty(), function ($query) use ($usedByOtherRoomsIds, $selectedThingIds) {
                $query->where(function ($inner) use ($usedByOtherRoomsIds, $selectedThingIds) {
                    $inner->whereNotIn('id', $usedByOtherRoomsIds->all());

                    if ($selectedThingIds->isNotEmpty()) {
                        $inner->orWhereIn('id', $selectedThingIds->all());
                    }
                });
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('admin.sarana.ruangan.form-with-contents', [
            'ruangan' => $ruangan,
            'semua_barang' => $semua_barang,
            'selectedThingIds' => $selectedThingIds,
        ]);
    }

    public function tambahRuanganWithContents(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'kode_room' => 'required|unique:rooms|max:20',
                'nama' => 'required|max:255',
                'lantai' => 'required|string|max:30',
                'status' => 'required|in:Tersedia,Dipakai,Maintenance,Tidak Tersedia',
                'barang' => 'nullable|array',
                'barang.*' => 'nullable|exists:things,id',
            ]);

            $rawSelectedThingIds = collect($validated['barang'] ?? [])
                ->filter(fn ($id) => !empty($id))
                ->map(fn ($id) => (int) $id)
                ->values();

            $selectedThingIds = $rawSelectedThingIds
                ->unique()
                ->values();

            if ($rawSelectedThingIds->count() !== $selectedThingIds->count()) {
                return back()->withErrors([
                    'barang' => 'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
                ])->withInput();
            }

            $isAlreadyAssigned = $selectedThingIds->isNotEmpty() && (
                Thing::whereIn('id', $selectedThingIds->all())
                    ->whereNotNull('room_id')
                    ->exists()
                || RoomContent::whereIn('thing_id', $selectedThingIds->all())
                    ->exists()
            );

            if ($isAlreadyAssigned) {
                return back()->withErrors([
                    'barang' => 'Ada barang yang sudah dipakai di ruangan lain. Pilih barang yang belum dipakai.',
                ])->withInput();
            }

            DB::transaction(function () use ($validated, $selectedThingIds) {
                $ruangan = Room::create([
                    'kode_room' => $validated['kode_room'],
                    'nama' => $validated['nama'],
                    'lantai' => $validated['lantai'],
                    'status' => $validated['status'],
                ]);

                if ($selectedThingIds->isNotEmpty()) {
                    RoomContent::whereIn('thing_id', $selectedThingIds->all())
                        ->delete();

                    foreach ($selectedThingIds as $thingId) {
                        $ruangan->contents()->create([
                            'thing_id' => $thingId,
                        ]);
                    }

                    Thing::whereIn('id', $selectedThingIds->all())
                        ->update(['room_id' => $ruangan->id]);
                }
            });

            return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil ditambahkan');
        }

        $usedThingIds = RoomContent::pluck('thing_id')
            ->map(fn ($id) => (int) $id)
            ->unique();

        $semua_barang = Thing::query()
            ->whereNull('room_id')
            ->when($usedThingIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $usedThingIds->all()))
            ->orderBy('nama', 'asc')
            ->get();

        return view('admin.sarana.ruangan.form-with-contents', [
            'ruangan' => null,
            'semua_barang' => $semua_barang,
            'selectedThingIds' => collect(),
        ]);
    }

    public function faq()
    {
        return view('admin.faq');
    }

    private function validatePeminjamEmailDomain(string $email, string $role): void
    {
        if ($role !== 'peminjam') {
            return;
        }

        if (! str_ends_with(strtolower($email), '@itbss.ac.id')) {
            throw ValidationException::withMessages([
                'email' => 'Email peminjam wajib menggunakan domain @itbss.ac.id.',
            ]);
        }
    }
}
