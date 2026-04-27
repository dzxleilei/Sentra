<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['domain' => 'damage_report', 'code' => 'Sedang Ditinjau', 'label' => 'Sedang Ditinjau', 'description' => 'Laporan sedang ditinjau admin', 'is_active' => true],
            ['domain' => 'damage_report', 'code' => 'Ditolak', 'label' => 'Ditolak', 'description' => 'Laporan ditolak admin', 'is_active' => true],
            ['domain' => 'damage_report', 'code' => 'Selesai Ditangani', 'label' => 'Selesai Ditangani', 'description' => 'Laporan selesai ditangani', 'is_active' => true],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('statuses')
                ->where('domain', $row['domain'])
                ->where('code', $row['code'])
                ->exists();

            if (! $exists) {
                DB::table('statuses')->insert($row);
            }
        }

        DB::table('damage_reports')
            ->where('status', 'Menunggu Verifikasi')
            ->update(['status' => 'Sedang Ditinjau']);

        DB::table('damage_reports')
            ->where('status', 'Selesai')
            ->update(['status' => 'Selesai Ditangani']);

        DB::statement("UPDATE damage_reports d JOIN statuses s ON s.domain = 'damage_report' AND s.code = d.status SET d.status_id = s.id");
    }

    public function down(): void
    {
        DB::table('damage_reports')
            ->where('status', 'Sedang Ditinjau')
            ->update(['status' => 'Menunggu Verifikasi']);

        DB::table('damage_reports')
            ->where('status', 'Selesai Ditangani')
            ->update(['status' => 'Selesai']);

        DB::statement("UPDATE damage_reports d JOIN statuses s ON s.domain = 'damage_report' AND s.code = d.status SET d.status_id = s.id");

        DB::table('statuses')
            ->where('domain', 'damage_report')
            ->whereIn('code', ['Sedang Ditinjau', 'Ditolak', 'Selesai Ditangani'])
            ->delete();
    }
};
