<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Peminjam\DashboardController as PeminjamDashboard;

Route::get("/", function () {
    return redirect("/login");
});

Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"]);

Route::middleware(["auth"])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    Route::get("/setting", [AuthController::class, "showChangePasswordForm"])->name("setting");
    Route::post("/setting/password", [AuthController::class, "updatePassword"])->name("update-password");
    Route::post("/setting/penalty-threshold", [AuthController::class, "updatePenaltyThreshold"])->name("setting.penalty-threshold");
    Route::get("/change-password", [AuthController::class, "showChangePasswordForm"])->name("change-password");
    Route::post("/change-password", [AuthController::class, "updatePassword"])->name("update-password.legacy");

    // ===== PEMINJAM ROUTES =====
    Route::middleware(['role:peminjam', 'mobile.peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::get('/dashboard', [PeminjamDashboard::class, 'index'])->name('dashboard');
        Route::get('/pengaturan', [AuthController::class, 'showChangePasswordForm'])->name('pengaturan');
        Route::get('/barang', [PeminjamDashboard::class, 'daftarBarang'])->name('barang');
        Route::get('/ruangan', [PeminjamDashboard::class, 'daftarRuangan'])->name('ruangan');
        Route::get('/ruangan/{id}/booking', [PeminjamDashboard::class, 'formBookingRuangan'])->name('ruangan.booking.form');
        Route::get('/ruangan/{id}/barang', [PeminjamDashboard::class, 'barangDalamRuangan'])->name('ruangan.barang');
        Route::get('/tiket/{id}', [BorrowController::class, 'showTicket'])->name('tiket');
        Route::get('/riwayat', [PeminjamDashboard::class, 'riwayat'])->name('riwayat');
        Route::get('/penalti', [PeminjamDashboard::class, 'penalti'])->name('penalti');
        Route::get('/faq', [PeminjamDashboard::class, 'faq'])->name('faq');
    });

    // Proses peminjaman umum (untuk semua role)
    Route::middleware(['role:peminjam', 'mobile.peminjam'])->group(function () {
        Route::get("/borrow-thing", [BorrowController::class, "listThings"])->name("thing.list");
        Route::post('/thing-cart/add', [BorrowController::class, 'addThingToCart'])->name('thing.cart.add');
        Route::post('/thing-cart/{thingId}/remove', [BorrowController::class, 'removeThingFromCart'])->name('thing.cart.remove');
        Route::post('/thing-cart/clear', [BorrowController::class, 'clearThingCart'])->name('thing.cart.clear');
        Route::post('/thing-cart/checkout', [BorrowController::class, 'checkoutThingCart'])->name('thing.cart.checkout');
        Route::post('/quick-borrow-qr', [BorrowController::class, 'quickBorrowByQr'])->name('quick-borrow-qr');
        Route::post("/booking-thing", [BorrowController::class, "bookingThing"])->name("thing.booking");
        Route::post('/booking/cancel', [BorrowController::class, 'cancelBooking'])->name('booking.cancel');
        Route::post('/report-damage', [BorrowController::class, 'reportDamage'])->name('borrow.report-damage');
        
        Route::get("/borrow-room", [BorrowController::class, "listRooms"])->name("room.list");
        Route::post("/booking-room", [BorrowController::class, "bookingRoom"])->name("room.booking");
        Route::post('/quick-borrow-qr/preview', [BorrowController::class, 'previewQuickBorrowQr'])->name('quick-borrow-qr.preview');
        Route::post('/quick-borrow-qr/process', [BorrowController::class, 'processQuickBorrowQr'])->name('quick-borrow-qr.process');
        
        Route::get("/room/{id}/things", [BorrowController::class, "listThingsRoom"])->name("room.things.list");
        Route::post("/booking-things-room", [BorrowController::class, "bookingThingsRoom"])->name("room.things.booking");

        Route::post("/checkin", [BorrowController::class, "checkIn"])->name("borrow.checkin");
        Route::post("/checkout", [BorrowController::class, "checkOut"])->name("borrow.checkout");
    });

    // ===== ADMIN ROUTES =====
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // Manajemen Ruangan (dengan barang)
        Route::get('/ruangan', [AdminDashboard::class, 'daftarRuangan'])->name('ruangan');
        Route::get('/ruangan/tambah', [AdminDashboard::class, 'tambahRuanganWithContents'])->name('ruangan.tambah');
        Route::post('/ruangan/tambah', [AdminDashboard::class, 'tambahRuanganWithContents']);
        Route::get('/ruangan/{id}/edit', [AdminDashboard::class, 'editRuanganWithContents'])->name('ruangan.edit');
        Route::post('/ruangan/{id}/edit', [AdminDashboard::class, 'editRuanganWithContents']);
        Route::post('/ruangan/{id}/hapus', [AdminDashboard::class, 'hapusRuangan'])->name('ruangan.hapus');
        
        // Manajemen Barang
        Route::get('/barang', [AdminDashboard::class, 'daftarBarang'])->name('barang');
        Route::get('/barang/tambah', [AdminDashboard::class, 'tambahBarang'])->name('barang.tambah');
        Route::post('/barang/tambah', [AdminDashboard::class, 'tambahBarang']);
        Route::get('/barang/{id}/edit', [AdminDashboard::class, 'editBarang'])->name('barang.edit');
        Route::post('/barang/{id}/edit', [AdminDashboard::class, 'editBarang']);
        Route::post('/barang/{id}/hapus', [AdminDashboard::class, 'hapusBarang'])->name('barang.hapus');
        
        // Manajemen User
        Route::get('/user', [AdminDashboard::class, 'daftarUser'])->name('user');
        Route::get('/user/peminjam', [AdminDashboard::class, 'daftarUserPeminjam'])->name('user.peminjam');
        Route::get('/user/staff', [AdminDashboard::class, 'daftarUserStaff'])->name('user.staff');
        Route::get('/user/tambah', [AdminDashboard::class, 'tambahUser'])->name('user.tambah');
        Route::post('/user/tambah', [AdminDashboard::class, 'tambahUser']);
        Route::get('/user/{id}/edit', [AdminDashboard::class, 'editUser'])->name('user.edit');
        Route::post('/user/{id}/edit', [AdminDashboard::class, 'editUser']);
        Route::post('/user/{id}/hapus', [AdminDashboard::class, 'hapusUser'])->name('user.hapus');
        Route::post('/user/{id}/blokir', [AdminDashboard::class, 'blokirUserPeminjam'])->name('user.blokir');
        Route::post('/user/{id}/buka-blokir', [AdminDashboard::class, 'bukaBlokirUserPeminjam'])->name('user.buka-blokir');
        Route::post('/user/{id}/penalty/add', [AdminDashboard::class, 'tambahPenaltiPeminjam'])->name('user.penalty.add');
        Route::post('/user/{id}/penalty/set', [AdminDashboard::class, 'setPenaltiPeminjam'])->name('user.penalty.set');
        Route::post('/user/{id}/penalty/subtract', [AdminDashboard::class, 'kurangiPenaltiPeminjam'])->name('user.penalty.subtract');
        Route::post('/user/{id}/penalty/reset', [AdminDashboard::class, 'resetPenaltiPeminjam'])->name('user.penalty.reset');
        Route::get('/user/import', [AdminDashboard::class, 'importUserCSV'])->name('user.import');
        Route::post('/user/import', [AdminDashboard::class, 'importUserCSV']);
        
        // Manajemen Penalti (dipusatkan lewat halaman laporan)
        Route::post('/laporan/pelanggaran/{id}/tindak-lanjut', [AdminDashboard::class, 'tindakLanjutPelanggaran'])->name('pelanggaran.tindak');
        Route::post('/laporan/pelanggaran/tambah', [AdminDashboard::class, 'tambahPenaltiPeminjam'])->name('pelanggaran.tambah');
        Route::post('/laporan/pelanggaran/{userId}/set', [AdminDashboard::class, 'setPenaltiPeminjam'])->name('pelanggaran.set');
        Route::post('/laporan/pelanggaran/{userId}/reset', [AdminDashboard::class, 'resetPenaltiPeminjam'])->name('pelanggaran.reset');
        
        // Laporan
        Route::get('/laporan', [AdminDashboard::class, 'laporan'])->name('laporan');
        Route::get('/laporan/{userId}/tiket', [AdminDashboard::class, 'laporanTiketUser'])->name('laporan.tiket');
        Route::get('/laporan/tiket/{id}/foto/{jenis}', [AdminDashboard::class, 'laporanTiketFoto'])
            ->whereIn('jenis', ['awal', 'akhir'])
            ->name('laporan.tiket.foto');
        Route::get('/laporan-rusak', [AdminDashboard::class, 'laporanRusak'])->name('laporan.rusak');
        Route::get('/laporan-rusak/{id}', [AdminDashboard::class, 'laporanRusakDetail'])->name('laporan.rusak.detail');
        Route::get('/laporan-rusak/{id}/foto', [AdminDashboard::class, 'laporanRusakFoto'])->name('laporan.rusak.foto');
        Route::post('/laporan-rusak/{id}/status', [AdminDashboard::class, 'updateLaporanRusakStatus'])->name('laporan.rusak.status');
        Route::post('/laporan/export', [AdminDashboard::class, 'exportLaporan'])->name('laporan.export');
        
        // FAQ
        Route::get('/faq', [AdminDashboard::class, 'faq'])->name('faq');
    });
});

