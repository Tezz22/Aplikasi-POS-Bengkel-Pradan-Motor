<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_penjualan', function (Blueprint $table) {
            $table->increments('id_transaksi');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_pelanggan')->nullable();
            // Di model belum ada tabel/FK untuk kendaraan
            $table->unsignedInteger('id_kendaraan')->nullable();
            $table->string('no_transaksi', 25);
            $table->dateTime('tanggal')->useCurrent();
            $table->integer('total')->default(0);
            $table->integer('nominal_bayar')->default(0);
            $table->integer('kembalian')->nullable();

            $table->unique('no_transaksi', 'uq_transaksi_no');
            $table->index('tanggal', 'idx_transaksi_tanggal');
            $table->index('id_user', 'idx_transaksi_user');
            $table->index('id_pelanggan', 'idx_transaksi_pelanggan');

            $table->foreign('id_user', 'fk_transaksi_user')
                ->references('id_user')->on('ms_users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_pelanggan', 'fk_transaksi_pelanggan')
                ->references('id_pelanggan')->on('ms_pelanggan')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_penjualan');
    }
};
