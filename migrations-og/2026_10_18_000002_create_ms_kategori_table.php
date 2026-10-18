<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_kategori', function (Blueprint $table) {
            $table->increments('id_kategori');
            $table->string('nama_kategori', 25);

            $table->unique('nama_kategori', 'uq_kategori_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_kategori');
    }
};
