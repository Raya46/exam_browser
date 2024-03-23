<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('token')->nullable();
            $table->enum('role', ['super admin', 'admin sekolah', 'siswa']);
            $table->string('kelas_jurusan')->nullable();
            $table->string('sekolah')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('status')->nullable();
            $table->double('nilai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
