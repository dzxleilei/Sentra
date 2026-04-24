<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE things SET status = 'Tidak Tersedia' WHERE status IN ('Terpinjam Otomatis', 'Rusak')");
        DB::statement("UPDATE borrows SET status_checkin = NULL WHERE status_checkin IS NOT NULL AND status_checkin <> 'Tidak Check-in'");
        DB::statement("UPDATE borrows SET status_checkout = NULL WHERE status_checkout IS NOT NULL AND status_checkout <> 'Tidak Check-out'");

        DB::table('statuses')
            ->where('domain', 'borrow_checkin')
            ->whereIn('code', ['Tepat Waktu', 'Telat Check-in'])
            ->delete();

        DB::table('statuses')
            ->where('domain', 'borrow_checkout')
            ->whereIn('code', ['Tepat Waktu', 'Telat Check-out'])
            ->delete();

        DB::table('statuses')
            ->where('domain', 'thing')
            ->whereIn('code', ['Terpinjam Otomatis', 'Rusak'])
            ->delete();

        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia'");
        DB::statement("ALTER TABLE borrows MODIFY status_checkin ENUM('Tidak Check-in') NULL");
        DB::statement("ALTER TABLE borrows MODIFY status_checkout ENUM('Tidak Check-out') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam','Terpinjam Otomatis','Rusak') NOT NULL DEFAULT 'Tersedia'");
        DB::statement("ALTER TABLE borrows MODIFY status_checkin ENUM('Tepat Waktu','Telat Check-in','Tidak Check-in') NULL");
        DB::statement("ALTER TABLE borrows MODIFY status_checkout ENUM('Tepat Waktu','Telat Check-out','Tidak Check-out') NULL");
    }
};
