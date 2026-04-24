<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->string('lokasi_penggunaan', 100)->nullable()->after('alasan_lainnya');
            $table->text('lokasi_lainnya')->nullable()->after('lokasi_penggunaan');
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['lokasi_penggunaan', 'lokasi_lainnya']);
        });
    }
};
