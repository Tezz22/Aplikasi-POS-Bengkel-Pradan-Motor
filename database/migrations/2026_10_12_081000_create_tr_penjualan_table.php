<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota', 50)->unique();
            $table->dateTime('tanggal');
            $table->foreignId('pelanggan_id')->nullable()->constrained('ms_pelanggan')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('metode_pembayaran', 50)->default('Tunai');
            $table->string('status', 50)->default('Lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_penjualan');
    }
};
