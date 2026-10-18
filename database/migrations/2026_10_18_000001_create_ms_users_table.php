<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_users', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('nama', 50);
            $table->string('email', 50);
            $table->string('password', 60);
            $table->enum('role', ['admin', 'kasir'])->default('kasir');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('email', 'uq_users_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_users');
    }
};
