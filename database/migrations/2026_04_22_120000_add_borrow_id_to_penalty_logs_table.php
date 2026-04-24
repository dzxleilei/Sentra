<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penalty_logs', function (Blueprint $table) {
            $table->foreignId('borrow_id')->nullable()->after('admin_id')->constrained('borrows')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penalty_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('borrow_id');
        });
    }
};
