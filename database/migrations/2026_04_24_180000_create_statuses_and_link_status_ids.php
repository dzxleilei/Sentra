<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 40);
            $table->string('code', 50);
            $table->string('label', 80);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);

            $table->unique(['domain', 'code']);
        });

        DB::table('statuses')->insert([
            ['domain' => 'borrow_main', 'code' => 'Booking', 'label' => 'Booking', 'description' => 'Tiket dibuat dan menunggu check-in', 'is_active' => true],
            ['domain' => 'borrow_main', 'code' => 'Berlangsung', 'label' => 'Berlangsung', 'description' => 'Sedang dipinjam', 'is_active' => true],
            ['domain' => 'borrow_main', 'code' => 'Selesai', 'label' => 'Selesai', 'description' => 'Peminjaman selesai', 'is_active' => true],
            ['domain' => 'borrow_main', 'code' => 'Dibatalkan', 'label' => 'Dibatalkan', 'description' => 'Tiket dibatalkan peminjam', 'is_active' => true],
            ['domain' => 'borrow_main', 'code' => 'Pelanggaran', 'label' => 'Pelanggaran', 'description' => 'Ada pelanggaran pada tiket', 'is_active' => true],
            ['domain' => 'borrow_main', 'code' => 'Menunggu Verifikasi', 'label' => 'Menunggu Verifikasi', 'description' => 'Menunggu validasi verifikator/admin', 'is_active' => true],

            ['domain' => 'borrow_checkin', 'code' => 'Tidak Check-in', 'label' => 'Tidak Check-in', 'description' => 'Tidak melakukan check-in', 'is_active' => true],

            ['domain' => 'borrow_checkout', 'code' => 'Tidak Check-out', 'label' => 'Tidak Check-out', 'description' => 'Tidak melakukan check-out', 'is_active' => true],

            ['domain' => 'room', 'code' => 'Tersedia', 'label' => 'Tersedia', 'description' => null, 'is_active' => true],
            ['domain' => 'room', 'code' => 'Dipakai', 'label' => 'Dipakai', 'description' => null, 'is_active' => true],
            ['domain' => 'room', 'code' => 'Maintenance', 'label' => 'Maintenance', 'description' => null, 'is_active' => true],
            ['domain' => 'room', 'code' => 'Tidak Tersedia', 'label' => 'Tidak Tersedia', 'description' => null, 'is_active' => true],

            ['domain' => 'thing', 'code' => 'Tersedia', 'label' => 'Tersedia', 'description' => null, 'is_active' => true],
            ['domain' => 'thing', 'code' => 'Dipinjam', 'label' => 'Dipinjam', 'description' => null, 'is_active' => true],
            ['domain' => 'thing', 'code' => 'Tidak Tersedia', 'label' => 'Tidak Tersedia', 'description' => null, 'is_active' => true],

            ['domain' => 'damage_report', 'code' => 'Menunggu Verifikasi', 'label' => 'Menunggu Verifikasi', 'description' => null, 'is_active' => true],
            ['domain' => 'damage_report', 'code' => 'Selesai', 'label' => 'Selesai', 'description' => null, 'is_active' => true],
        ]);

        Schema::table('borrows', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('statuses');
            $table->foreignId('status_checkin_id')->nullable()->after('status_checkin')->constrained('statuses');
            $table->foreignId('status_checkout_id')->nullable()->after('status_checkout')->constrained('statuses');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('statuses');
        });

        Schema::table('things', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('statuses');
        });

        Schema::table('damage_reports', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('statuses');
        });

        DB::statement("UPDATE borrows b JOIN statuses s ON s.domain = 'borrow_main' AND s.code = b.status SET b.status_id = s.id");
        DB::statement("UPDATE borrows b JOIN statuses s ON s.domain = 'borrow_checkin' AND s.code = b.status_checkin SET b.status_checkin_id = s.id WHERE b.status_checkin IS NOT NULL");
        DB::statement("UPDATE borrows b JOIN statuses s ON s.domain = 'borrow_checkout' AND s.code = b.status_checkout SET b.status_checkout_id = s.id WHERE b.status_checkout IS NOT NULL");
        DB::statement("UPDATE rooms r JOIN statuses s ON s.domain = 'room' AND s.code = r.status SET r.status_id = s.id");
        DB::statement("UPDATE things t JOIN statuses s ON s.domain = 'thing' AND s.code = t.status SET t.status_id = s.id");
        DB::statement("UPDATE damage_reports d JOIN statuses s ON s.domain = 'damage_report' AND s.code = d.status SET d.status_id = s.id");
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::table('things', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::table('borrows', function (Blueprint $table) {
            $table->dropForeign(['status_checkout_id']);
            $table->dropForeign(['status_checkin_id']);
            $table->dropForeign(['status_id']);
            $table->dropColumn(['status_checkout_id', 'status_checkin_id', 'status_id']);
        });

        Schema::dropIfExists('statuses');
    }
};
