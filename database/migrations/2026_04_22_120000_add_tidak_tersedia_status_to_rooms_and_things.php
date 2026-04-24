<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY status ENUM('Tersedia','Dipakai','Maintenance','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia'");
        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY status ENUM('Tersedia','Dipakai','Maintenance') NOT NULL DEFAULT 'Tersedia'");
        DB::statement("ALTER TABLE things MODIFY status ENUM('Tersedia','Dipinjam') NOT NULL DEFAULT 'Tersedia'");
    }
};
