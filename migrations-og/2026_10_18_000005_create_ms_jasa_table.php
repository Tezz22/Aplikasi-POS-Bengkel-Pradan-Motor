<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_jasa', function (Blueprint $table) {
            $table->increments('id_jasa');
            $table->string('nama_jasa', 120);
            $table->integer('tarif')->default(0);

            $table->unique('nama_jasa', 'uq_jasa_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_jasa');
    }
};
