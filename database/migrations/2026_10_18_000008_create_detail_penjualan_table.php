<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_transaksi');
            $table->unsignedInteger('id_barang')->nullable();
            $table->unsignedInteger('id_jasa')->nullable();
            $table->enum('jenis_item', ['barang', 'jasa']);
            $table->string('nama_item', 100);
            $table->integer('harga_satuan');
            $table->integer('qty')->default(1);
            $table->integer('subtotal')->nullable();

            $table->index('id_transaksi', 'idx_dtrx_transaksi');
            $table->index('id_barang', 'idx_dtrx_barang');
            $table->index('id_jasa', 'idx_dtrx_jasa');

            $table->foreign('id_transaksi', 'fk_dtrx_transaksi')
                ->references('id_transaksi')->on('tr_penjualan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_barang', 'fk_dtrx_barang')
                ->references('id_barang')->on('ms_barang')
                ->restrictOnDelete();

            $table->foreign('id_jasa', 'fk_dtrx_jasa')
                ->references('id_jasa')->on('ms_jasa')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
