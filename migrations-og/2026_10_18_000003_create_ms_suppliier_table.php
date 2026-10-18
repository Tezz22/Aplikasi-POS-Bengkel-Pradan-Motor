<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nama tabel mengikuti model Workbench ("ms_suppliier")
        Schema::create('ms_suppliier', function (Blueprint $table) {
            $table->increments('id_supplier');
            $table->string('nama_supplier', 50);
            $table->string('no_telepon', 15)->nullable();
            $table->string('alamat', 255)->nullable();

            $table->index('nama_supplier', 'idx_supplier_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_suppliier');
    }
};
