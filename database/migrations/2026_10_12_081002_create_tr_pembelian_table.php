<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_masuk', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('ms_supplier')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_faktur', 100)->nullable();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('tr_pembelian')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('ms_barang')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
        Schema::dropIfExists('tr_pembelian');
    }
};
