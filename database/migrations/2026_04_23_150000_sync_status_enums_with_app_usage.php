<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY status ENUM('Tersedia','Dipakai','Maintenance','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia'");

        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia'");

        DB::statement("ALTER TABLE borrows MODIFY status_checkin ENUM('Tidak Check-in') NULL");

        DB::statement("ALTER TABLE borrows MODIFY status_checkout ENUM('Tidak Check-out') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY status ENUM('Tersedia','Dipakai','Maintenance') NOT NULL DEFAULT 'Tersedia'");

        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam') NOT NULL DEFAULT 'Tersedia'");

        DB::statement("ALTER TABLE borrows MODIFY status_checkin VARCHAR(30) NULL");

        DB::statement("ALTER TABLE borrows MODIFY status_checkout VARCHAR(30) NULL");
    }
};
