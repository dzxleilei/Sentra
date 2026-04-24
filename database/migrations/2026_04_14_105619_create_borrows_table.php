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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipe', ['Barang', 'Ruangan', 'Barang_Dari_Ruangan']);
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->foreignId('thing_id')->nullable()->constrained('things');
            $table->dateTime('waktu_mulai_booking');
            $table->dateTime('waktu_selesai_booking');
            $table->dateTime('waktu_checkin')->nullable();
            $table->dateTime('waktu_checkout')->nullable();
            $table->string('foto_awal')->nullable();
            $table->string('foto_akhir')->nullable();
            $table->enum('status', ['Booking', 'Berlangsung', 'Selesai', 'Dibatalkan', 'Pelanggaran'])->default('Booking');
            $table->text('catatan_pelanggaran')->nullable();
            $table->boolean('diverifikasi_admin')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
