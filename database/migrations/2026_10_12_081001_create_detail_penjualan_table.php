<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('tr_penjualan')->cascadeOnDelete();
            $table->string('tipe', 20)->default('barang'); // 'barang' atau 'jasa'
            $table->foreignId('barang_id')->nullable()->constrained('ms_barang')->nullOnDelete();
            $table->foreignId('jasa_id')->nullable()->constrained('ms_jasa')->nullOnDelete();
            $table->string('nama_item', 150);
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
