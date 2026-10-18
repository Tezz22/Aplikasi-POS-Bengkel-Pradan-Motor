<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_pembelian', function (Blueprint $table) {
            $table->increments('id_tr_pembelian');
            $table->unsignedInteger('id_barang')->nullable();
            $table->unsignedInteger('id_supplier');
            $table->unsignedInteger('id_user');
            $table->string('no_referensi', 25);
            $table->date('tanggal');
            $table->string('keterangan', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('jumlah')->nullable();
            $table->integer('harga_beli')->nullable();
            $table->integer('subtotal')->nullable();

            $table->unique('no_referensi', 'uq_bm_ref');
            $table->index('tanggal', 'idx_bm_tanggal');
            $table->index('id_supplier', 'idx_bm_supplier');
            $table->index('id_user', 'idx_bm_user');
            $table->index('id_barang', 'fk_tr_pembelian_ms_barang1_idx');

            $table->foreign('id_supplier', 'fk_bm_supplier')
                ->references('id_supplier')->on('ms_suppliier')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_user', 'fk_bm_user')
                ->references('id_user')->on('ms_users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_barang', 'fk_tr_pembelian_ms_barang1')
                ->references('id_barang')->on('ms_barang')
                ->noActionOnUpdate()
                ->noActionOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_pembelian');
    }
};
