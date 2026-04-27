<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropUnique('borrows_kode_booking_unique');
            $table->index('kode_booking', 'borrows_kode_booking_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropIndex('borrows_kode_booking_index');
            $table->unique('kode_booking', 'borrows_kode_booking_unique');
        });
    }
};
