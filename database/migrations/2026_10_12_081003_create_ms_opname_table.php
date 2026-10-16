<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_opname', function (Blueprint $table) {
            $table->id();
            $table->string('kode_opname', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('ms_supplier')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kategori', 100)->nullable()->default('Semua');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_opname');
    }
};
