<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_opname_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('ms_opname')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('ms_barang')->cascadeOnDelete();
            $table->integer('stok_sistem')->default(0);
            $table->integer('stok_fisik')->default(0);
            $table->integer('selisih')->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_opname_detail');
    }
};
