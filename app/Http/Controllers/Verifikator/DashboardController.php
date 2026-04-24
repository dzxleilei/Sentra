<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Thing;
use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard Verifikator
    public function index()
    {
        // Daftar booking hari ini
        $bookingHariIni = Borrow::whereDate('waktu_mulai_booking', today())
            ->with('user', 'thing', 'room')
            ->orderBy('waktu_mulai_booking', 'desc')
            ->get();

        // Booking yang sedang berlangsung
        $sedangBerlangsung = Borrow::whereDate('waktu_mulai_booking', today())
            ->where('status', 'Berlangsung')
            ->get();

        // Booking yang perlu diverifikasi
        $perluDiverifikasi = Borrow::whereDate('waktu_mulai_booking', today())
            ->where('diverifikasi_admin', false)
            ->count();

        return view('verifikator.dashboard', [
            'bookingHariIni' => $bookingHariIni,
            'sedangBerlangsung' => $sedangBerlangsung,
            'perluDiverifikasi' => $perluDiverifikasi,
        ]);
    }

    // Detail booking untuk validasi
    public function detailBooking($id)
    {
        $booking = Borrow::with('user', 'thing', 'room')->findOrFail($id);
        return view('verifikator.booking.detail', ['booking' => $booking]);
    }

    // Proses validasi scan QR
    public function validasiScan($id, Request $request)
    {
        $borrow = Borrow::findOrFail($id);

        if ($borrow->status === 'Dibatalkan') {
            return redirect()->route('verifikator.booking.detail', $borrow->id)
                ->withErrors(['status' => 'Booking yang sudah dibatalkan tidak bisa divalidasi.']);
        }

        $validated = $request->validate([
            'waktu_checkin' => 'nullable|date_format:Y-m-d H:i:s',
            'waktu_checkout' => 'nullable|date_format:Y-m-d H:i:s',
            'catatan_pelanggaran' => 'nullable|string|max:500',
            'ada_pelanggaran' => 'boolean',
        ]);

        if ($validated['ada_pelanggaran'] ?? false) {
            $borrow->status = 'Pelanggaran';
            $borrow->catatan_pelanggaran = $validated['catatan_pelanggaran'];
        } else {
            $borrow->status = 'Selesai';
        }

        if ($validated['waktu_checkin'] ?? null) {
            $borrow->waktu_checkin = $validated['waktu_checkin'];
        }
        if ($validated['waktu_checkout'] ?? null) {
            $borrow->waktu_checkout = $validated['waktu_checkout'];
        }

        $borrow->diverifikasi_admin = true;
        $borrow->save();

        return redirect()->route('verifikator.dashboard')->with('success', 'Validasi berhasil disimpan');
    }

    // Laporan pelanggaran
    public function laporanPelanggaran()
    {
        $pelanggaran = Borrow::where('status', 'Pelanggaran')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('verifikator.laporan.pelanggaran', ['pelanggaran' => $pelanggaran]);
    }

    // Notifikasi booking
    public function notifikasi()
    {
        // Notifikasi untuk booking yang perlu divalidasi
        $notifikasi = Borrow::whereIn('status', ['Menunggu Verifikasi', 'Berlangsung'])
            ->where('diverifikasi_admin', false)
            ->with('user', 'thing', 'room')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('verifikator.notifikasi', ['notifikasi' => $notifikasi]);
    }

    // FAQ
    public function faq()
    {
        return view('verifikator.faq');
    }
}
