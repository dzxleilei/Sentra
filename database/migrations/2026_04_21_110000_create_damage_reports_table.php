<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('borrow_id')->nullable()->constrained('borrows')->nullOnDelete();
            $table->foreignId('thing_id')->constrained('things');
            $table->string('lokasi_barang', 120);
            $table->text('keterangan')->nullable();
            $table->string('foto_bukti');
            $table->string('status', 30)->default('Menunggu Verifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
