<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_jasa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_layanan', 50)->unique();
            $table->string('nama_layanan', 150);
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('durasi', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 50)->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_jasa');
    }
};
