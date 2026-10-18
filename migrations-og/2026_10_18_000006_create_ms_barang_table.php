<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_barang', function (Blueprint $table) {
            $table->increments('id_barang');
            $table->unsignedInteger('id_kategori');
            $table->string('nama_barang', 30);
            $table->string('satuan', 20)->default('pcs');
            $table->integer('harga_jual')->default(0);
            $table->integer('stok')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('nama_barang', 'idx_barang_nama');
            $table->index('id_kategori', 'idx_barang_kategori');

            $table->foreign('id_kategori', 'fk_barang_kategori')
                ->references('id_kategori')->on('ms_kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_barang');
    }
};
