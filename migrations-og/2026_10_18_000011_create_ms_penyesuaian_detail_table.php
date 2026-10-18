<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_penyesuaian_detail', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_opname');
            $table->unsignedInteger('id_barang');
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->text('alasan');

            $table->index('id_opname', 'idx_dso_opname');
            $table->index('id_barang', 'idx_dso_barang');

            $table->foreign('id_opname', 'fk_dso_opname')
                ->references('id_opname')->on('ms_penyesuaian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_barang', 'fk_dso_barang')
                ->references('id_barang')->on('ms_barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_penyesuaian_detail');
    }
};
