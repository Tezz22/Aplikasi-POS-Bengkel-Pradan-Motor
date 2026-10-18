<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_penyesuaian', function (Blueprint $table) {
            $table->increments('id_opname');
            $table->unsignedInteger('id_user');
            $table->dateTime('tanggal')->useCurrent();
            $table->string('keterangan', 255)->nullable();

            $table->index('id_user', 'idx_opname_user');

            $table->foreign('id_user', 'fk_opname_user')
                ->references('id_user')->on('ms_users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_penyesuaian');
    }
};
