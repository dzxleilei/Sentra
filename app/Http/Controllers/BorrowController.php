<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thing;
use App\Models\Room;
use App\Models\Borrow;
use App\Models\DamageReport;
use App\Models\Status;
use App\Services\BorrowLifecycleService;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BorrowController extends Controller
{
    private const THING_CART_KEY = 'peminjam_thing_cart';
    private const THING_CART_WINDOW_KEY = 'peminjam_thing_cart_window';

    private const ALASAN_OPTIONS = [
        'Praktikum',
        'Perkuliahan',
        'Kegiatan Organisasi',
        'Riset',
        'Lainnya',
    ];

    private const LOKASI_OPTIONS = [
        'Laboratorium Komputer',
        'Ruang Kelas',
        'Ruang Meeting',
        'Aula',
        'Lainnya',
    ];

    private const OPEN_HOUR = 8;
    private const OPEN_MINUTE = 0;
    private const CLOSE_HOUR = 21;
    private const CLOSE_MINUTE = 0;

    public function __construct(private readonly BorrowLifecycleService $borrowLifecycleService)
    {
    }

    public function listThings()
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        return redirect()->route('peminjam.barang');
    }

    public function listRooms()
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        return redirect()->route('peminjam.ruangan');
    }

    public function bookingThing(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'thing_id' => 'required|exists:things,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'alasan_peminjaman' => 'required|in:' . implode(',', self::ALASAN_OPTIONS),
            'alasan_lainnya' => 'nullable|string|min:5|max:255',
            'lokasi_penggunaan' => 'required|string',
            'lokasi_lainnya' => 'nullable|string|min:3|max:255',
        ]);

        $this->validateReason($request);
        $location = $this->resolveBorrowLocation($request);

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($request->jam_mulai, $request->jam_selesai);
        $thing = Thing::findOrFail($request->thing_id);

        if (! $this->isThingBookable($thing, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['thing_id' => 'Barang tidak tersedia pada slot waktu tersebut.'])->withInput();
        }

        $borrow = $this->createBorrowWithUniqueCode([
            'user_id' => Auth::id(),
            'tipe' => 'Barang',
            'thing_id' => $thing->id,
            'room_id' => $thing->room_id,
            'waktu_mulai_booking' => $waktuMulai,
            'waktu_selesai_booking' => $waktuSelesai,
            'alasan_peminjaman' => $request->alasan_peminjaman,
            'alasan_lainnya' => $request->alasan_peminjaman === 'Lainnya' ? $request->alasan_lainnya : null,
            'lokasi_penggunaan' => $location['lokasi_penggunaan'],
            'lokasi_lainnya' => $location['lokasi_lainnya'],
            'status' => 'Booking',
        ], 'BK-BRG-');

        return redirect()->route('peminjam.tiket', $borrow->id)->with('success', 'Booking barang berhasil. Tiket sudah dibuat.');
    }

    public function quickBorrowByQr(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'qr_code' => 'required|string|max:120',
        ]);

        $qrCode = strtoupper(trim((string) $request->input('qr_code')));

        $thing = Thing::whereRaw('UPPER(kode_thing) = ?', [$qrCode])->first();
        if (! $thing) {
            return back()->withErrors(['qr_code' => 'QR barang tidak dikenali. Pastikan kode QR sesuai ID barang.']);
        }

        $now = Carbon::now()->addMinutes(30);
        $slotStart = $now->copy()->minute > 0
            ? $now->copy()->addHour()->startOfHour()
            : $now->copy()->startOfHour();
        $slotEnd = $slotStart->copy()->addHour();

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($slotStart->format('H:i'), $slotEnd->format('H:i'));

        if (! $this->isThingBookable($thing, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['qr_code' => 'Barang tidak tersedia untuk slot 1 jam terdekat.']);
        }

        $borrow = $this->createBorrowWithUniqueCode([
            'user_id' => Auth::id(),
            'tipe' => 'Barang',
            'thing_id' => $thing->id,
            'room_id' => $thing->room_id,
            'waktu_mulai_booking' => $waktuMulai,
            'waktu_selesai_booking' => $waktuSelesai,
            'alasan_peminjaman' => 'Praktikum',
            'lokasi_penggunaan' => 'Laboratorium Komputer',
            'status' => 'Booking',
        ], 'BK-BRG-');

        return redirect()->route('peminjam.tiket', $borrow->id)
            ->with('success', 'Booking cepat via QR berhasil dibuat untuk slot ' . $waktuMulai->format('H:i') . ' - ' . $waktuSelesai->format('H:i') . '.');
    }

    public function previewQuickBorrowQr(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());

        $request->validate([
            'qr_code' => 'required|string|max:120',
        ]);

        $thing = Thing::with('room')
            ->whereRaw('UPPER(kode_thing) = ?', [strtoupper(trim((string) $request->input('qr_code')))])
            ->first();

        if (! $thing) {
            return response()->json([
                'message' => 'QR barang tidak dikenali. Pastikan kode QR sesuai ID barang.',
            ], 422);
        }

        $activeBorrow = Borrow::with(['thing', 'room'])
            ->where('user_id', Auth::id())
            ->where('thing_id', $thing->id)
            ->where('status', 'Berlangsung')
            ->latest('id')
            ->first();

        $directStart = Carbon::now();
        $directEnd = $directStart->copy()->addHour();

        if (! $activeBorrow && (! $this->isThingBookable($thing, $directStart, $directEnd))) {
            return response()->json([
                'message' => 'Barang sedang tidak tersedia untuk dipinjam saat ini.',
            ], 409);
        }

        return response()->json([
            'mode' => $activeBorrow ? 'checkout' : 'borrow',
            'thing' => [
                'id' => $thing->id,
                'kode_thing' => $thing->kode_thing,
                'nama' => $thing->nama,
                'status' => $thing->status,
                'room_name' => $thing->room->nama ?? null,
                'room_code' => $thing->room->kode_room ?? null,
            ],
            'borrow' => $activeBorrow ? [
                'id' => $activeBorrow->id,
                'kode_booking' => $activeBorrow->kode_booking,
                'status' => $activeBorrow->status,
            ] : null,
            'user' => [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ],
        ]);
    }

    public function processQuickBorrowQr(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'qr_code' => 'required|string|max:120',
            'action' => 'required|in:borrow,checkout',
        ]);

        $qrCode = strtoupper(trim((string) $request->input('qr_code')));
        $thing = Thing::with('room')
            ->whereRaw('UPPER(kode_thing) = ?', [$qrCode])
            ->first();

        if (! $thing) {
            return back()->withErrors(['qr_code' => 'QR barang tidak dikenali. Pastikan kode QR sesuai ID barang.']);
        }

        $activeBorrow = Borrow::with(['thing', 'room'])
            ->where('user_id', Auth::id())
            ->where('thing_id', $thing->id)
            ->where('status', 'Berlangsung')
            ->latest('id')
            ->first();

        if ($request->action === 'checkout') {
            if (! $activeBorrow) {
                return back()->withErrors(['qr_code' => 'Tidak ada peminjaman berlangsung untuk barang ini.']);
            }

            $request->validate([
                'foto_akhir' => 'required|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:10240',
            ]);

            $path = $this->storePeminjamanPhoto(
                $request->file('foto_akhir'),
                (string) $activeBorrow->kode_booking,
                'akhir'
            );
            $now = Carbon::now();

            $activeBorrow->update([
                'waktu_checkout' => $now,
                'foto_akhir' => $path,
                'status' => 'Selesai',
            ]);

            $this->refreshThingAvailability($thing);

            return redirect()->route('peminjam.riwayat')->with('success', 'Barang berhasil dikembalikan.');
        }

        $directStart = Carbon::now();
        $directEnd = $directStart->copy()->addHour();

        if (! $this->isThingBookable($thing, $directStart, $directEnd)) {
            return back()->withErrors(['qr_code' => 'Barang sedang tidak tersedia untuk dipinjam saat ini.']);
        }

        $request->validate([
            'alasan_peminjaman' => 'required|in:' . implode(',', self::ALASAN_OPTIONS),
            'alasan_lainnya' => 'nullable|string|min:5|max:255',
            'foto_awal' => 'required|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:10240',
        ]);

        $this->validateReason($request);

        $borrow = $this->createBorrowWithUniqueCode([
            'user_id' => Auth::id(),
            'tipe' => 'Barang',
            'thing_id' => $thing->id,
            'room_id' => $thing->room_id,
            'waktu_mulai_booking' => Carbon::now(),
            'waktu_selesai_booking' => Carbon::now()->addHour(),
            'waktu_checkin' => Carbon::now(),
            'foto_awal' => null,
            'alasan_peminjaman' => $request->alasan_peminjaman,
            'alasan_lainnya' => $request->alasan_peminjaman === 'Lainnya' ? $request->alasan_lainnya : null,
            'lokasi_penggunaan' => $thing->room ? 'Lainnya' : null,
            'lokasi_lainnya' => $thing->room?->nama,
            'status' => 'Berlangsung',
        ], 'BK-QR-');

        $path = $this->storePeminjamanPhoto(
            $request->file('foto_awal'),
            (string) $borrow->kode_booking,
            'awal'
        );

        $borrow->update([
            'foto_awal' => $path,
        ]);

        $thing->update(['status' => 'Dipinjam']);

        return redirect()->route('peminjam.tiket', $borrow->id)->with('success', 'Barang berhasil dipinjam dan sudah check-in.');
    }

    public function addThingToCart(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'thing_id' => 'required|exists:things,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
        ]);

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($request->jam_mulai, $request->jam_selesai);
        $thing = Thing::findOrFail($request->thing_id);

        if ($thing->status !== 'Tersedia') {
            return back()->withErrors(['thing_id' => 'Barang tidak tersedia untuk dimasukkan ke keranjang.']);
        }

        $cartWindow = session(self::THING_CART_WINDOW_KEY);
        if (is_array($cartWindow) && (($cartWindow['jam_mulai'] ?? null) !== $request->jam_mulai || ($cartWindow['jam_selesai'] ?? null) !== $request->jam_selesai)) {
            return back()->withErrors(['thing_id' => 'Keranjang ini sudah memakai sesi waktu yang berbeda. Kosongkan keranjang untuk mengganti sesi.']);
        }

        if (! $this->isThingBookable($thing, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['thing_id' => 'Barang tidak tersedia pada sesi waktu yang dipilih.']);
        }

        $cart = $this->getThingCart();

        if (! in_array($thing->id, $cart, true)) {
            $cart[] = $thing->id;
            session([self::THING_CART_KEY => $cart]);
        }

        session([
            self::THING_CART_WINDOW_KEY => [
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ],
        ]);

        return back()->with('success', 'Barang ditambahkan ke keranjang booking.');
    }

    public function removeThingFromCart(int $thingId)
    {
        $cart = array_values(array_filter($this->getThingCart(), fn (int $id) => $id !== $thingId));
        session([self::THING_CART_KEY => $cart]);

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }

    public function clearThingCart(Request $request)
    {
        session()->forget(self::THING_CART_KEY);
        session()->forget(self::THING_CART_WINDOW_KEY);

        $redirectTo = $request->input('redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) {
            return redirect($redirectTo);
        }

        return back()->with('success', 'Keranjang booking dikosongkan.');
    }

    public function checkoutThingCart(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'alasan_peminjaman' => 'required|in:' . implode(',', self::ALASAN_OPTIONS),
            'alasan_lainnya' => 'nullable|string|min:5|max:255',
            'lokasi_penggunaan' => 'required|string',
            'lokasi_lainnya' => 'nullable|string|min:3|max:255',
        ]);

        $this->validateReason($request);
        $location = $this->resolveBorrowLocation($request);

        $cartWindow = session(self::THING_CART_WINDOW_KEY);
        if (is_array($cartWindow) && (($cartWindow['jam_mulai'] ?? null) !== $request->jam_mulai || ($cartWindow['jam_selesai'] ?? null) !== $request->jam_selesai)) {
            return back()->withErrors(['thing_id' => 'Sesi waktu checkout harus sama dengan sesi yang dipilih saat menambahkan barang ke keranjang.']);
        }

        $cartIds = $this->getThingCart();
        if (count($cartIds) === 0) {
            return back()->withErrors(['thing_id' => 'Keranjang kosong. Tambahkan barang terlebih dahulu.']);
        }

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($request->jam_mulai, $request->jam_selesai);

        $things = Thing::whereIn('id', $cartIds)->get();

        if ($things->count() !== count($cartIds)) {
            return back()->withErrors(['thing_id' => 'Sebagian barang di keranjang tidak valid. Silakan muat ulang halaman.']);
        }

        $unavailableThingIds = [];
        $unavailableThingCodes = [];

        foreach ($things as $thing) {
            if (! $this->isThingBookable($thing, $waktuMulai, $waktuSelesai)) {
                $unavailableThingIds[] = (int) $thing->id;
                $unavailableThingCodes[] = $thing->kode_thing;
            }
        }

        if (! empty($unavailableThingIds)) {
            $remaining = array_values(array_filter($cartIds, fn (int $id) => ! in_array($id, $unavailableThingIds, true)));
            session([self::THING_CART_KEY => $remaining]);

            if (count($remaining) === 0) {
                session()->forget(self::THING_CART_WINDOW_KEY);
            }

            return back()->withErrors([
                'lokasi_penggunaan' => 'required|string',
            ]);
        }

        $kodeBooking = $this->generateBookingCode('BK-BRG-');
        $firstBorrowId = null;

        foreach ($things as $thing) {
            $borrow = Borrow::create([
                'kode_booking' => $kodeBooking,
                'user_id' => Auth::id(),
                'tipe' => 'Barang',
                'thing_id' => $thing->id,
                'room_id' => $thing->room_id,
                'waktu_mulai_booking' => $waktuMulai,
                'waktu_selesai_booking' => $waktuSelesai,
                'alasan_peminjaman' => $request->alasan_peminjaman,
                'alasan_lainnya' => $request->alasan_peminjaman === 'Lainnya' ? $request->alasan_lainnya : null,
                'lokasi_penggunaan' => $location['lokasi_penggunaan'],
                'lokasi_lainnya' => $location['lokasi_lainnya'],
                'status' => 'Booking',
            ]);

            if ($firstBorrowId === null) {
                $firstBorrowId = $borrow->id;
            }
        }

        session()->forget(self::THING_CART_KEY);
        session()->forget(self::THING_CART_WINDOW_KEY);

        return redirect()->route('peminjam.tiket', $firstBorrowId)->with('success', 'Checkout keranjang berhasil. Semua barang masuk ke 1 tiket booking.');
    }
    
    public function bookingRoom(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'alasan_peminjaman' => 'required|in:' . implode(',', self::ALASAN_OPTIONS),
            'alasan_lainnya' => 'nullable|string|min:5|max:255',
        ]);

        $this->validateReason($request);

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($request->jam_mulai, $request->jam_selesai);
        $room = Room::findOrFail($request->room_id);

        if (! $this->isRoomBookable($room, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['room_id' => 'Ruangan tidak tersedia pada slot waktu tersebut.'])->withInput();
        }

        if ($this->isRoomBlockedByItemThreshold($room, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['room_id' => 'Ruangan tidak bisa dibooking karena lebih dari 40% item sejenis di ruangan sedang dipinjam.'])->withInput();
        }

        $borrow = $this->createBorrowWithUniqueCode([
            'user_id' => Auth::id(),
            'tipe' => 'Ruangan',
            'room_id' => $room->id,
            'waktu_mulai_booking' => $waktuMulai,
            'waktu_selesai_booking' => $waktuSelesai,
            'alasan_peminjaman' => $request->alasan_peminjaman,
            'alasan_lainnya' => $request->alasan_peminjaman === 'Lainnya' ? $request->alasan_lainnya : null,
            'status' => 'Booking',
        ], 'BK-RGN-');

        return redirect()->route('peminjam.tiket', $borrow->id)->with('success', 'Booking ruangan berhasil. Tiket sudah dibuat.');
    }

    public function listThingsRoom($id)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        return redirect()->route('peminjam.ruangan.barang', $id);
    }

    public function bookingThingsRoom(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());
        $this->assertPenaltyNotBlocked();

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'thing_id' => 'required|exists:things,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'alasan_peminjaman' => 'required|in:' . implode(',', self::ALASAN_OPTIONS),
            'alasan_lainnya' => 'nullable|string|min:5|max:255',
        ]);

        $this->validateReason($request);

        [$waktuMulai, $waktuSelesai] = $this->buildBookingWindow($request->jam_mulai, $request->jam_selesai);
        $room = Room::findOrFail($request->room_id);
        $thing = Thing::findOrFail($request->thing_id);

        if ((int) $thing->room_id !== (int) $room->id) {
            return back()->withErrors(['thing_id' => 'Barang tidak terdaftar pada ruangan yang dipilih.'])->withInput();
        }

        if (! $this->isThingBookable($thing, $waktuMulai, $waktuSelesai)) {
            return back()->withErrors(['thing_id' => 'Barang tidak tersedia pada slot waktu tersebut.'])->withInput();
        }

        $roomBorrowConflict = Borrow::where('room_id', $room->id)
            ->where('tipe', 'Ruangan')
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                $query->where('waktu_mulai_booking', '<', $waktuSelesai)
                    ->where('waktu_selesai_booking', '>', $waktuMulai);
            })
            ->exists();

        if ($roomBorrowConflict) {
            return back()->withErrors(['thing_id' => 'Ruangan sedang dibooking sebagai ruangan utuh pada slot waktu tersebut.'])->withInput();
        }

        $isThingAlreadyBorrowed = Borrow::where('thing_id', $thing->id)
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                $query->where('waktu_mulai_booking', '<', $waktuSelesai)
                    ->where('waktu_selesai_booking', '>', $waktuMulai);
            })
            ->exists();

        if ($isThingAlreadyBorrowed) {
            return back()->withErrors(['thing_id' => 'Unit barang dengan ID tersebut sudah dibooking pada slot waktu yang sama.'])->withInput();
        }

        $borrow = $this->createBorrowWithUniqueCode([
            'user_id' => Auth::id(),
            'tipe' => 'Barang_Dari_Ruangan',
            'room_id' => $room->id,
            'thing_id' => $thing->id,
            'waktu_mulai_booking' => $waktuMulai,
            'waktu_selesai_booking' => $waktuSelesai,
            'alasan_peminjaman' => $request->alasan_peminjaman,
            'alasan_lainnya' => $request->alasan_peminjaman === 'Lainnya' ? $request->alasan_lainnya : null,
            'status' => 'Booking',
        ], 'BK-RBM-');

        return redirect()->route('peminjam.tiket', $borrow->id)->with('success', 'Booking barang dari ruangan berhasil. Tiket sudah dibuat.');
    }

    public function showTicket($id)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());

        $borrow = Borrow::with(['thing', 'room'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $ticketItems = Borrow::with(['thing', 'room'])
            ->where('user_id', Auth::id())
            ->where('kode_booking', $borrow->kode_booking)
            ->orderBy('id')
            ->get();

        return view('peminjam.tiket', [
            'borrow' => $borrow,
            'ticketItems' => $ticketItems,
        ]);
    }

    public function cancelBooking(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());

        $request->validate([
            'kode_booking' => 'required|string',
        ]);

        $borrows = Borrow::where('user_id', Auth::id())
            ->where('kode_booking', $request->kode_booking)
            ->get();

        if ($borrows->isEmpty()) {
            return back()->withErrors(['kode_booking' => 'Tiket tidak ditemukan.']);
        }

        if ($borrows->contains(fn (Borrow $borrow) => $borrow->status !== 'Booking')) {
            return back()->withErrors(['kode_booking' => 'Tiket tidak bisa dibatalkan karena sudah berjalan atau selesai.']);
        }

        $start = $borrows->min('waktu_mulai_booking');
        $now = Carbon::now();

        if (! $start || $now->greaterThanOrEqualTo($start->copy()->subMinutes(15))) {
            return back()->withErrors(['kode_booking' => 'Batas pembatalan sudah lewat (maksimal 15 menit sebelum jam mulai).']);
        }

        Borrow::where('user_id', Auth::id())
            ->where('kode_booking', $request->kode_booking)
            ->update([
                'status' => 'Dibatalkan',
                'status_id' => Status::idFor(Status::DOMAIN_BORROW_MAIN, 'Dibatalkan'),
            ]);

        $this->borrowLifecycleService->syncAssetStatuses();

        return redirect()->route('peminjam.riwayat')->with('success', 'Tiket berhasil dibatalkan.');
    }
    
    public function checkIn(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());

        $request->validate([
            'kode_booking' => 'required|string',
            'scanned_value' => 'nullable|string',
            'scanned_values' => 'nullable|string',
            'foto_awal' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:10240',
        ]);

        $group = Borrow::with(['thing', 'room'])
            ->where('kode_booking', $request->kode_booking)
            ->where('user_id', Auth::id())
            ->get();

        if ($group->isEmpty()) {
            return back()->withErrors(['kode_booking' => 'Tiket tidak ditemukan.']);
        }

        $sample = $group->first();
        $borrowsToCheckin = collect();

        if ($sample && $sample->tipe === 'Ruangan') {
            $scannedValue = trim((string) $request->input('scanned_value'));
            if ($scannedValue === '') {
                return back()->withErrors(['scanned_value' => 'Scan QR ruangan terlebih dahulu.']);
            }

            $borrow = $group->first(function (Borrow $item) use ($scannedValue) {
                return $item->status === 'Booking'
                    && $item->room
                    && strtoupper($item->room->kode_room) === strtoupper($scannedValue);
            });

            if (! $borrow) {
                return back()->withErrors(['scanned_value' => 'Objek scan tidak ada pada tiket booking atau sudah check-in.']);
            }

            $borrowsToCheckin = collect([$borrow]);
        } else {
            $bookingItems = $group->where('status', 'Booking')->values();
            if ($bookingItems->isEmpty()) {
                return back()->withErrors(['kode_booking' => 'Semua barang pada tiket ini sudah check-in atau tidak tersedia untuk check-in.']);
            }

            $rawScannedValues = trim((string) $request->input('scanned_values', ''));
            if ($rawScannedValues === '') {
                $rawScannedValues = trim((string) $request->input('scanned_value', ''));
            }

            $scannedTokens = collect(explode(',', $rawScannedValues))
                ->map(fn (string $token) => trim($token))
                ->filter(fn (string $token) => $token !== '')
                ->map(fn (string $token) => strtoupper($token))
                ->values();

            if ($scannedTokens->isEmpty()) {
                return back()->withErrors(['scanned_values' => 'Scan semua QR barang pada tiket sebelum check-in.']);
            }

            $expectedCodes = $bookingItems
                ->map(fn (Borrow $item) => strtoupper((string) optional($item->thing)->kode_thing))
                ->filter(fn (string $code) => $code !== '')
                ->unique()
                ->values();

            if ($expectedCodes->count() !== $bookingItems->count()) {
                return back()->withErrors(['scanned_values' => 'Data kode barang pada tiket tidak lengkap. Hubungi admin.']);
            }

            $scannedCodes = $scannedTokens->unique()->values();

            $missingCodes = $expectedCodes->diff($scannedCodes)->values();
            if ($missingCodes->isNotEmpty()) {
                return back()->withErrors([
                    'scanned_values' => 'Masih ada barang yang belum discan: Kode ' . $missingCodes->implode(', '),
                ]);
            }

            $invalidCodes = $scannedCodes->diff($expectedCodes)->values();
            if ($invalidCodes->isNotEmpty()) {
                return back()->withErrors([
                    'scanned_values' => 'QR tidak sesuai tiket: Kode ' . $invalidCodes->implode(', '),
                ]);
            }

            $borrowsToCheckin = $bookingItems->filter(function (Borrow $item) use ($scannedCodes) {
                $code = strtoupper((string) optional($item->thing)->kode_thing);
                return $code !== '' && $scannedCodes->contains($code);
            })->values();
        }

        $referenceBorrow = $borrowsToCheckin->first();
        $now = Carbon::now();
        $earliestCheckin = $referenceBorrow->waktu_mulai_booking->copy()->subMinutes(5);
        $latestCheckin = $referenceBorrow->waktu_mulai_booking->copy()->addMinutes(15);

        if ($now->lt($earliestCheckin)) {
            return back()->withErrors([
                'kode_booking' => 'Check-in paling cepat 5 menit sebelum jam mulai booking.',
            ]);
        }

        if ($now->gt($latestCheckin)) {
            return back()->withErrors([
                'kode_booking' => 'Melewati batas check-in. Booking otomatis dianggap tidak check-in.',
            ]);
        }

        $existingPhoto = $group->first(fn (Borrow $item) => ! empty($item->foto_awal))?->foto_awal;
        if (! $existingPhoto && ! $request->hasFile('foto_awal')) {
            return back()->withErrors(['foto_awal' => 'Selfie + kondisi awal wajib diupload saat scan pertama pada tiket ini.']);
        }

        $path = $existingPhoto;
        if ($request->hasFile('foto_awal')) {
            $path = $this->storePeminjamanPhoto(
                $request->file('foto_awal'),
                (string) $referenceBorrow->kode_booking,
                'awal'
            );
        }

        foreach ($borrowsToCheckin as $borrow) {
            if ($borrow->tipe === 'Ruangan' && $borrow->room) {
                $borrow->room->update(['status' => 'Dipakai']);
                $borrow->room->things()->update(['status' => 'Dipinjam']);
            } elseif ($borrow->thing) {
                $borrow->thing->update(['status' => 'Dipinjam']);
            }

            $borrow->update([
                'waktu_checkin' => $now,
                'foto_awal' => $path,
                'status' => 'Berlangsung',
            ]);
        }

        return redirect()->route('peminjam.tiket', $referenceBorrow->id)->with('success', 'Check-in berhasil. Peminjaman sedang berlangsung.');
    }
    
    public function checkOut(Request $request)
    {
        $this->borrowLifecycleService->enforceRules(Auth::id());

        $request->validate([
            'kode_booking' => 'required|string',
            'scanned_value' => 'nullable|string',
            'scanned_values' => 'nullable|string',
            'foto_akhir' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:10240',
        ]);

        $group = Borrow::with(['thing', 'room'])
            ->where('kode_booking', $request->kode_booking)
            ->where('user_id', Auth::id())
            ->get();

        if ($group->isEmpty()) {
            return back()->withErrors(['kode_booking' => 'Tiket tidak ditemukan.']);
        }

        $sample = $group->first();
        $borrowsToCheckout = collect();

        if ($sample && $sample->tipe === 'Ruangan') {
            $scannedValue = trim((string) $request->input('scanned_value'));
            if ($scannedValue === '') {
                return back()->withErrors(['scanned_value' => 'Scan QR ruangan terlebih dahulu.']);
            }

            $borrow = $group->first(function (Borrow $item) use ($scannedValue) {
                return $item->status === 'Berlangsung'
                    && $item->room
                    && strtoupper($item->room->kode_room) === strtoupper($scannedValue);
            });

            if (! $borrow) {
                return back()->withErrors(['scanned_value' => 'Objek scan belum check-in atau sudah check-out pada tiket ini.']);
            }

            $borrowsToCheckout = collect([$borrow]);
        } else {
            $runningItems = $group->where('status', 'Berlangsung')->values();
            if ($runningItems->isEmpty()) {
                return back()->withErrors(['kode_booking' => 'Tidak ada barang berstatus berlangsung untuk check-out pada tiket ini.']);
            }

            $rawScannedValues = trim((string) $request->input('scanned_values', ''));
            if ($rawScannedValues === '') {
                $rawScannedValues = trim((string) $request->input('scanned_value', ''));
            }

            $scannedTokens = collect(explode(',', $rawScannedValues))
                ->map(fn (string $token) => trim($token))
                ->filter(fn (string $token) => $token !== '')
                ->map(fn (string $token) => strtoupper($token))
                ->values();

            if ($scannedTokens->isEmpty()) {
                return back()->withErrors(['scanned_values' => 'Scan semua QR barang pada tiket sebelum check-out.']);
            }

            $expectedCodes = $runningItems
                ->map(fn (Borrow $item) => strtoupper((string) optional($item->thing)->kode_thing))
                ->filter(fn (string $code) => $code !== '')
                ->unique()
                ->values();

            if ($expectedCodes->count() !== $runningItems->count()) {
                return back()->withErrors(['scanned_values' => 'Data kode barang pada tiket tidak lengkap. Hubungi admin.']);
            }

            $scannedCodes = $scannedTokens->unique()->values();

            $missingCodes = $expectedCodes->diff($scannedCodes)->values();
            if ($missingCodes->isNotEmpty()) {
                return back()->withErrors([
                    'scanned_values' => 'Masih ada barang yang belum discan untuk check-out: Kode ' . $missingCodes->implode(', '),
                ]);
            }

            $invalidCodes = $scannedCodes->diff($expectedCodes)->values();
            if ($invalidCodes->isNotEmpty()) {
                return back()->withErrors([
                    'scanned_values' => 'QR tidak sesuai tiket: Kode ' . $invalidCodes->implode(', '),
                ]);
            }

            $borrowsToCheckout = $runningItems->filter(function (Borrow $item) use ($scannedCodes) {
                $code = strtoupper((string) optional($item->thing)->kode_thing);
                return $code !== '' && $scannedCodes->contains($code);
            })->values();
        }

        $referenceBorrow = $borrowsToCheckout->first();
        $now = Carbon::now();
        $latestCheckout = $referenceBorrow->waktu_selesai_booking->copy()->addMinutes(15);

        if ($now->gt($latestCheckout)) {
            return back()->withErrors([
                'kode_booking' => 'Melewati batas check-out. Booking otomatis dianggap tidak check-out.',
            ]);
        }

        $existingPhoto = $group->first(fn (Borrow $item) => ! empty($item->foto_akhir))?->foto_akhir;
        if (! $existingPhoto && ! $request->hasFile('foto_akhir')) {
            return back()->withErrors(['foto_akhir' => 'Selfie + kondisi akhir wajib diupload saat scan check-out pertama pada tiket ini.']);
        }

        $path = $existingPhoto;
        if ($request->hasFile('foto_akhir')) {
            $path = $this->storePeminjamanPhoto(
                $request->file('foto_akhir'),
                (string) $referenceBorrow->kode_booking,
                'akhir'
            );
        }

        foreach ($borrowsToCheckout as $borrow) {
            if ($borrow->tipe === 'Ruangan' && $borrow->room) {
                $borrow->room->update(['status' => 'Tersedia']);
                foreach ($borrow->room->things as $roomThing) {
                    $this->refreshThingAvailability($roomThing);
                }
            } elseif ($borrow->thing) {
                $this->refreshThingAvailability($borrow->thing);
            }

            $borrow->update([
                'waktu_checkout' => $now,
                'foto_akhir' => $path,
                'status' => 'Selesai',
            ]);
        }

        return redirect()->route('peminjam.tiket', $referenceBorrow->id)->with('success', 'Check-out barang berhasil diproses.');
    }

    public function reportDamage(Request $request)
    {
        $request->validate([
            'kode_booking' => 'nullable|string|max:40',
            'borrow_id' => 'nullable|exists:borrows,id',
            'thing_input' => 'required_without:borrow_id|string|min:2|max:120',
            'lokasi_barang' => 'required|string|min:3|max:120',
            'keterangan' => 'nullable|string|max:500',
            'foto_bukti' => 'required|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:10240',
        ]);

        $kodeBooking = trim((string) $request->kode_booking);
        $ticketBorrows = collect();
        if ($kodeBooking !== '') {
            $ticketBorrows = Borrow::with('thing')
                ->where('kode_booking', $kodeBooking)
                ->where('user_id', Auth::id())
                ->get();

            if ($ticketBorrows->isEmpty()) {
                return back()->withErrors(['kode_booking' => 'Kode tiket tidak ditemukan pada akun Anda.'])->withInput();
            }
        }

        $thing = null;
        $borrowId = null;
        $selectedBorrow = null;

        if ($request->filled('borrow_id')) {
            $selectedBorrow = Borrow::with('thing')
                ->where('id', (int) $request->borrow_id)
                ->where('user_id', Auth::id())
                ->first();

            if (! $selectedBorrow) {
                return back()->withErrors(['borrow_id' => 'ID tiket item tidak ditemukan pada akun Anda.'])->withInput();
            }

            if ($kodeBooking !== '' && strtoupper((string) $selectedBorrow->kode_booking) !== strtoupper($kodeBooking)) {
                return back()->withErrors(['kode_booking' => 'ID tiket item tidak sesuai dengan kode tiket.'])->withInput();
            }

            if (! $selectedBorrow->waktu_checkin) {
                return back()->withErrors(['borrow_id' => 'Laporan dari tiket hanya bisa dibuat setelah check-in.']);
            }

            if (! $selectedBorrow->thing_id) {
                return back()->withErrors(['borrow_id' => 'Tiket ini tidak terkait barang.']);
            }

            $thing = Thing::findOrFail($selectedBorrow->thing_id);
            $borrowId = $selectedBorrow->id;
        } else {
            $manualInput = trim((string) $request->thing_input);

            if ($kodeBooking !== '') {
                $selectedBorrow = $ticketBorrows->first(function (Borrow $borrow) use ($manualInput) {
                    if (! $borrow->thing) {
                        return false;
                    }

                    $isIdMatch = ctype_digit($manualInput) && (int) $borrow->thing->id === (int) $manualInput;
                    $isKodeMatch = strtoupper((string) $borrow->thing->kode_thing) === strtoupper($manualInput);
                    $isNamaMatch = strtoupper((string) $borrow->thing->nama) === strtoupper($manualInput);

                    return $isIdMatch || $isKodeMatch || $isNamaMatch;
                });

                if (! $selectedBorrow) {
                    return back()->withErrors(['thing_input' => 'Barang tidak ditemukan pada kode tiket tersebut.'])->withInput();
                }
            } else {
                $thing = Thing::query()
                    ->where('kode_thing', $manualInput)
                    ->orWhere('nama', $manualInput)
                    ->when(ctype_digit($manualInput), fn ($query) => $query->orWhere('id', (int) $manualInput))
                    ->first();

                if (! $thing) {
                    return back()->withErrors(['thing_input' => 'Barang tidak ditemukan. Isi dengan ID, kode, atau nama barang yang valid.'])->withInput();
                }
            }

            if ($selectedBorrow) {
                if (! $selectedBorrow->thing_id) {
                    return back()->withErrors(['kode_booking' => 'Kode tiket tidak terkait dengan barang yang bisa dilaporkan.'])->withInput();
                }

                if (! $selectedBorrow->waktu_checkin) {
                    return back()->withErrors(['kode_booking' => 'Laporan hanya bisa dibuat setelah tiket berstatus check-in.'])->withInput();
                }

                $thing = Thing::findOrFail($selectedBorrow->thing_id);
                $borrowId = $selectedBorrow->id;
            }
        }

        $path = $this->storeLaporanRusakPhoto(
            $request->file('foto_bukti'),
            (string) ($selectedBorrow?->kode_booking ?? ($kodeBooking !== '' ? $kodeBooking : ('MANUAL-' . ($thing?->kode_thing ?? 'BARANG'))))
        );

        DamageReport::create([
            'user_id' => Auth::id(),
            'borrow_id' => $borrowId,
            'thing_id' => $thing->id,
            'lokasi_barang' => $request->lokasi_barang,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $path,
            'status' => 'Sedang Ditinjau',
        ]);

        return back()->with('success', 'Laporan barang rusak berhasil dikirim.');
    }

    private function storePeminjamanPhoto(UploadedFile $file, string $kodeBooking, string $jenis): string
    {
        $safeCode = strtoupper(trim($kodeBooking));
        $safeCode = preg_replace('/[^A-Z0-9\-]/', '-', $safeCode) ?: 'TIKET';
        $safeType = $jenis === 'akhir' ? 'akhir' : 'awal';
        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension === '') {
            $extension = 'jpg';
        }

        $timestamp = now()->format('YmdHis');
        $name = $safeCode . '-' . $safeType . '-' . $timestamp . '-' . Str::lower(Str::random(6)) . '.' . $extension;

        return $file->storeAs('dokumentasi/peminjaman', $name, 'public');
    }

    private function storeLaporanRusakPhoto(UploadedFile $file, string $kodeBooking): string
    {
        $safeCode = strtoupper(trim($kodeBooking));
        $safeCode = preg_replace('/[^A-Z0-9\-]/', '-', $safeCode) ?: 'TIKET';
        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension === '') {
            $extension = 'jpg';
        }

        $timestamp = now()->format('YmdHis');
        $name = $safeCode . '-laporan-rusak-' . $timestamp . '-' . Str::lower(Str::random(6)) . '.' . $extension;

        return $file->storeAs('dokumentasi/laporan-rusak', $name, 'public');
    }

    private function generateBookingCode(string $prefix): string
    {
        do {
            $kode = $prefix . strtoupper(Str::random(8));
        } while (Borrow::where('kode_booking', $kode)->exists());

        return $kode;
    }

    private function createBorrowWithUniqueCode(array $attributes, string $prefix): Borrow
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $attributes['kode_booking'] = $this->generateBookingCode($prefix);

            try {
                return Borrow::create($attributes);
            } catch (QueryException $exception) {
                if (! $this->isDuplicateBookingCodeException($exception)) {
                    throw $exception;
                }
            }
        }

        throw ValidationException::withMessages([
            'kode_booking' => 'Gagal membuat kode booking unik. Silakan coba lagi.',
        ]);
    }

    private function isDuplicateBookingCodeException(QueryException $exception): bool
    {
        return (int) ($exception->errorInfo[1] ?? 0) === 1062
            || str_contains($exception->getMessage(), 'borrows_kode_booking_unique');
    }

    private function buildBookingWindow(string $jamMulai, string $jamSelesai): array
    {
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $mulai = Carbon::parse($today->format('Y-m-d') . ' ' . $jamMulai)->second(0);
        $selesai = Carbon::parse($today->format('Y-m-d') . ' ' . $jamSelesai)->second(0);
        $openingTime = $today->copy()->setTime(self::OPEN_HOUR, self::OPEN_MINUTE, 0);
        $closingTime = $today->copy()->setTime(self::CLOSE_HOUR, self::CLOSE_MINUTE, 0);

        if ($selesai->lessThanOrEqualTo($mulai)) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ]);
        }

        if (! $this->isAllowedMinute($mulai) || ! $this->isAllowedMinute($selesai)) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Menit yang diperbolehkan hanya 00 (interval 1 jam).',
            ]);
        }

        if ($mulai->lt($now->copy()->addMinutes(30))) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jam mulai harus minimal 30 menit dari waktu sekarang (GMT+7).',
            ]);
        }

        if ($mulai->lt($openingTime) || $mulai->gt($closingTime)) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jam mulai hanya boleh antara 08:00 sampai 21:00.',
            ]);
        }

        if ($selesai->lt($openingTime) || $selesai->gt($closingTime)) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jam selesai hanya boleh antara 08:00 sampai 21:00.',
            ]);
        }

        return [$mulai, $selesai];
    }

    private function validateReason(Request $request): void
    {
        if ($request->alasan_peminjaman === 'Lainnya' && empty(trim((string) $request->alasan_lainnya))) {
            throw ValidationException::withMessages([
                'alasan_lainnya' => 'Alasan lainnya wajib diisi jika memilih opsi Lainnya.',
            ]);
        }
    }

    private function resolveBorrowLocation(Request $request): array
    {
        $selection = trim((string) $request->input('lokasi_penggunaan'));

        if ($selection === '') {
            throw ValidationException::withMessages([
                'lokasi_penggunaan' => 'Lokasi penggunaan wajib dipilih.',
            ]);
        }

        if ($selection === 'Lainnya') {
            if (empty(trim((string) $request->lokasi_lainnya))) {
                throw ValidationException::withMessages([
                    'lokasi_lainnya' => 'Lokasi lainnya wajib diisi jika memilih opsi Lainnya.',
                ]);
            }

            return [
                'lokasi_penggunaan' => 'Lainnya',
                'lokasi_lainnya' => trim((string) $request->lokasi_lainnya),
            ];
        }

        $room = Room::find((int) $selection);
        if (! $room) {
            throw ValidationException::withMessages([
                'lokasi_penggunaan' => 'Lokasi penggunaan tidak valid.',
            ]);
        }

        return [
            'lokasi_penggunaan' => $room->nama,
            'lokasi_lainnya' => null,
        ];
    }

    private function assertPenaltyNotBlocked(): void
    {
        $user = Auth::user();

        if ($user && (int) $user->penalty_points >= 20) {
            throw ValidationException::withMessages([
                'alasan_peminjaman' => 'Akun Anda mencapai 20 poin penalti. Pengajuan peminjaman dinonaktifkan sampai admin membuka akun.',
            ]);
        }
    }

    private function isAllowedMinute(Carbon $time): bool
    {
        return (int) $time->format('i') === 0;
    }

    private function getThingCart(): array
    {
        $raw = session(self::THING_CART_KEY, []);

        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $raw)));
    }

    private function isThingBookable(Thing $thing, Carbon $mulai, Carbon $selesai): bool
    {
        if (! in_array($thing->status, ['Tersedia'], true)) {
            return false;
        }

        return ! Borrow::where('thing_id', $thing->id)
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->where(function ($query) use ($mulai, $selesai) {
                $query->where('waktu_mulai_booking', '<', $selesai)
                    ->where('waktu_selesai_booking', '>', $mulai);
            })
            ->exists();
    }

    private function isRoomBookable(Room $room, Carbon $mulai, Carbon $selesai): bool
    {
        if (! in_array($room->status, ['Tersedia'], true)) {
            return false;
        }

        return ! Borrow::where('room_id', $room->id)
            ->where('tipe', 'Ruangan')
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->where(function ($query) use ($mulai, $selesai) {
                $query->where('waktu_mulai_booking', '<', $selesai)
                    ->where('waktu_selesai_booking', '>', $mulai);
            })
            ->exists();
    }

    private function isRoomBlockedByItemThreshold(Room $room, Carbon $mulai, Carbon $selesai): bool
    {
        $thingsByName = Thing::where('room_id', $room->id)
            ->selectRaw('nama, COUNT(*) as total_unit')
            ->groupBy('nama')
            ->get();

        foreach ($thingsByName as $group) {
            $totalUnit = max(1, (int) $group->total_unit);

            $active = Borrow::where('room_id', $room->id)
                ->whereIn('tipe', ['Barang_Dari_Ruangan', 'Barang'])
                ->whereIn('status', ['Booking', 'Berlangsung'])
                ->where(function ($query) use ($mulai, $selesai) {
                    $query->where('waktu_mulai_booking', '<', $selesai)
                        ->where('waktu_selesai_booking', '>', $mulai);
                })
                ->whereHas('thing', function ($query) use ($group) {
                    $query->where('nama', $group->nama);
                })
                ->count();

            if (($active / $totalUnit) > 0.40) {
                return true;
            }
        }

        return false;
    }

    private function isQrMatch(Borrow $borrow, string $qrCode): bool
    {
        $normalized = strtoupper(trim($qrCode));

        if ($borrow->tipe === 'Ruangan' && $borrow->room) {
            return strtoupper($borrow->room->kode_room) === $normalized;
        }

        if ($borrow->thing) {
            return strtoupper($borrow->thing->kode_thing) === $normalized;
        }

        return false;
    }

    private function refreshThingAvailability(Thing $thing): void
    {
        if ($thing->status === 'Tidak Tersedia') {
            return;
        }

        $isStillBorrowed = Borrow::where('thing_id', $thing->id)
            ->whereIn('status', ['Booking', 'Berlangsung'])
            ->exists();

        $thing->update(['status' => $isStillBorrowed ? 'Dipinjam' : 'Tersedia']);
    }
}
