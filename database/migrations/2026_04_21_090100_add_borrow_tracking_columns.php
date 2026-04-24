<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->string('alasan_peminjaman', 100)->nullable()->after('waktu_checkout');
            $table->text('alasan_lainnya')->nullable()->after('alasan_peminjaman');
            $table->string('status_checkin', 30)->nullable()->after('status');
            $table->string('status_checkout', 30)->nullable()->after('status_checkin');
            $table->unsignedTinyInteger('penalty_points_applied')->default(0)->after('status_checkout');
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn([
                'alasan_peminjaman',
                'alasan_lainnya',
                'status_checkin',
                'status_checkout',
                'penalty_points_applied',
            ]);
        });
    }
};
