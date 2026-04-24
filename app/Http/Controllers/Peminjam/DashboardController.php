<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\AppSetting;
use App\Models\Thing;
use App\Models\Room;
use App\Services\BorrowLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __construct(private readonly BorrowLifecycleService $borrowLifecycleService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $this->borrowLifecycleService->enforceRules($user->id);
        $nowServer = Carbon::now();
        $threshold = AppSetting::integer('penalty_block_threshold', 20);
        
        // Statistik peminjaman
        $bookingHariIni = Borrow::where('user_id', $user->id)
            ->whereDate('waktu_mulai_booking', $nowServer->toDateString())
            ->distinct('kode_booking')
            ->count('kode_booking');

        $ticketHariIni = Borrow::where('user_id', $user->id)
            ->whereDate('waktu_mulai_booking', $nowServer->toDateString())
            ->orderByDesc('created_at')
            ->first();
            
        $riwayatPeminjaman = Borrow::where('user_id', $user->id)
            ->with(['thing', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $bookingAktif = Borrow::where('user_id', $user->id)
            ->with(['thing', 'room'])
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->orderBy('waktu_mulai_booking', 'asc')
            ->get()
            ->unique('kode_booking')
            ->values();

        return view('peminjam.dashboard', [
            'bookingHariIni' => $bookingHariIni,
            'ticketHariIni' => $ticketHariIni,
            'riwayatPeminjaman' => $riwayatPeminjaman,
            'bookingAktif' => $bookingAktif,
            'todayLabel' => $nowServer->translatedFormat('l, d F Y H:i'),
            'penaltyPoints' => (int) $user->penalty_points,
            'bookingBlocked' => (int) $user->penalty_points > $threshold || ! empty($user->blocked_at),
            'showBlockedModal' => session('show_blocked_modal', false),
            'penaltyBlockThreshold' => $threshold,
        ]);
    }

    // Menampilkan daftar barang dari sarpras
    public function daftarBarang(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $cartIds = array_values(array_unique(array_map('intval', (array) session('peminjam_thing_cart', []))));
        $cartWindow = session('peminjam_thing_cart_window');
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        $search = trim((string) $request->query('q', ''));
        $filterStatus = trim((string) $request->query('status', ''));
        $filterLokasi = trim((string) $request->query('lokasi', ''));

        $barang = Thing::with('room')
            ->whereNull('room_id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('kode_thing', 'like', '%' . $search . '%');
                });
            })
            ->when($filterStatus !== '', fn ($query) => $query->where('status', $filterStatus))
            ->when($filterLokasi !== '', function ($query) use ($filterLokasi) {
                if ($filterLokasi === 'Sarpras') {
                    $query->whereNull('room_id');
                }
            })
            ->when(count($cartIds) > 0, fn ($query) => $query->whereNotIn('id', $cartIds))
            ->orderBy('nama')
            ->get();

        $cartBarang = count($cartIds) > 0
            ? Thing::whereIn('id', $cartIds)->orderBy('nama')->get()
            : collect();

        $allTimeOptions = $this->buildAvailableTimeOptions();
        $cartTimeOptions = count($cartIds) > 0
            ? $this->buildAvailableTimeOptionsForThings($cartIds)
            : $allTimeOptions;

        $itemTimeOptions = [];
        foreach ($barang as $item) {
            $itemTimeOptions[$item->id] = $this->buildAvailableTimeOptionsForThings([(int) $item->id])->values()->all();
        }

        return view('peminjam.barang.daftar', [
            'barang' => $barang,
            'cartBarang' => $cartBarang,
            'timeOptions' => $cartTimeOptions,
            'allTimeOptions' => $allTimeOptions,
            'itemTimeOptions' => $itemTimeOptions,
            'cartWindow' => is_array($cartWindow) ? $cartWindow : null,
            'lokasiOptions' => ['Laboratorium Komputer', 'Ruang Kelas', 'Ruang Meeting', 'Aula', 'Lainnya'],
            'search' => $search,
            'filterStatus' => $filterStatus,
            'filterLokasi' => $filterLokasi,
            'todayLabel' => Carbon::now()->translatedFormat('l, d F Y H:i'),
            'bookingBlocked' => (int) Auth::user()->penalty_points > $threshold || ! empty(Auth::user()->blocked_at),
        ]);
    }

    private function buildAvailableTimeOptions(): Collection
    {
        $now = Carbon::now()->addMinutes(30);
        $options = collect();

        for ($hour = 8; $hour <= 21; $hour++) {
            $slot = Carbon::now()->setTime($hour, 0, 0);

            if ($slot->greaterThanOrEqualTo($now)) {
                $options->push($slot->format('H:i'));
            }
        }

        return $options->values();
    }

    private function buildAvailableTimeOptionsForThings(array $thingIds): Collection
    {
        $baseOptions = $this->buildAvailableTimeOptions();

        return $baseOptions->filter(function (string $time) use ($thingIds) {
            $slotStart = Carbon::today()->setTimeFromTimeString($time . ':00');
            $slotEnd = $slotStart->copy()->addHour();

            foreach ($thingIds as $thingId) {
                $isBusy = Borrow::where('thing_id', $thingId)
                    ->whereIn('status', ['Booking', 'Berlangsung'])
                    ->where('waktu_mulai_booking', '<', $slotEnd)
                    ->where('waktu_selesai_booking', '>', $slotStart)
                    ->exists();

                if ($isBusy) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function buildAvailableTimeOptionsForRoom(int $roomId): Collection
    {
        $baseOptions = $this->buildAvailableTimeOptions();

        return $baseOptions->filter(function (string $time) use ($roomId) {
            $slotStart = Carbon::today()->setTimeFromTimeString($time . ':00');
            $slotEnd = $slotStart->copy()->addHour();

            return ! Borrow::where('room_id', $roomId)
                ->where('tipe', 'Ruangan')
                ->whereIn('status', ['Booking', 'Berlangsung'])
                ->where('waktu_mulai_booking', '<', $slotEnd)
                ->where('waktu_selesai_booking', '>', $slotStart)
                ->exists();
        })->values();
    }

    // Menampilkan daftar ruangan
    public function daftarRuangan(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $search = trim((string) $request->query('q', ''));
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        $ruangan = Room::with('things')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('kode_room', 'like', '%' . $search . '%')
                        ->orWhere('lantai', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama')
            ->get();

        return view('peminjam.ruangan.daftar', [
            'ruangan' => $ruangan,
            'timeOptions' => $this->buildAvailableTimeOptions(),
            'search' => $search,
            'todayLabel' => Carbon::now()->translatedFormat('l, d F Y H:i'),
            'bookingBlocked' => (int) Auth::user()->penalty_points > $threshold || ! empty(Auth::user()->blocked_at),
        ]);
    }

    public function formBookingRuangan($roomId)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $ruangan = Room::with(['things' => fn ($query) => $query->orderBy('nama')])->findOrFail($roomId);
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        return view('peminjam.ruangan.booking', [
            'ruangan' => $ruangan,
            'timeOptions' => $this->buildAvailableTimeOptionsForRoom((int) $roomId),
            'todayLabel' => Carbon::now()->translatedFormat('l, d F Y H:i'),
            'bookingBlocked' => (int) Auth::user()->penalty_points > $threshold || ! empty(Auth::user()->blocked_at),
        ]);
    }

    // Menampilkan barang dalam ruangan
    public function barangDalamRuangan($roomId)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $ruangan = Room::findOrFail($roomId);
        $barang = Thing::where('room_id', $roomId)->orderBy('nama')->get();
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        $itemTimeOptions = [];
        foreach ($barang as $item) {
            $itemTimeOptions[$item->id] = $this->buildAvailableTimeOptionsForThings([(int) $item->id])->values()->all();
        }

        return view('peminjam.ruangan.barang', [
            'ruangan' => $ruangan,
            'barang' => $barang,
            'timeOptions' => $this->buildAvailableTimeOptions(),
            'itemTimeOptions' => $itemTimeOptions,
            'todayLabel' => Carbon::now()->translatedFormat('l, d F Y H:i'),
            'bookingBlocked' => (int) Auth::user()->penalty_points > $threshold || ! empty(Auth::user()->blocked_at),
        ]);
    }

    // Menampilkan riwayat peminjaman
    public function riwayat()
    {
        $user = Auth::user();
        $this->borrowLifecycleService->enforceRules($user->id);
        $riwayat = Borrow::where('user_id', $user->id)
            ->with(['thing', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('peminjam.riwayat', ['riwayat' => $riwayat, 'penaltyPoints' => (int) $user->penalty_points]);
    }

    // Menampilkan riwayat penalti
    public function penalti()
    {
        $user = Auth::user();
        $this->borrowLifecycleService->enforceRules($user->id);
        $threshold = AppSetting::integer('penalty_block_threshold', 20);
        $penalti = Borrow::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNotNull('status_checkin')
                    ->orWhereNotNull('status_checkout')
                    ->orWhere('penalty_points_applied', '>', 0);
            })
            ->with(['thing', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('peminjam.penalti', [
            'penalti' => $penalti,
            'penaltyPoints' => (int) $user->penalty_points,
            'bookingBlocked' => (int) $user->penalty_points > $threshold || ! empty($user->blocked_at),
        ]);
    }

    // FAQ
    public function faq()
    {
        return view('peminjam.faq');
    }
}
