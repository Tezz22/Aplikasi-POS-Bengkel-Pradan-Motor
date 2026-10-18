<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama', 50);
            $table->string('plat_nomor', 12)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('nama', 'idx_pelanggan_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_pelanggan');
    }
};
