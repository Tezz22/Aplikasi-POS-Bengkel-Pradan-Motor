<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan', 50)->unique();
            $table->string('nama', 150);
            $table->string('no_hp', 30);
            $table->string('plat_nomor', 30)->index();
            $table->string('tipe_motor', 100)->nullable();
            $table->string('tahun', 10)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_pelanggan');
    }
};
