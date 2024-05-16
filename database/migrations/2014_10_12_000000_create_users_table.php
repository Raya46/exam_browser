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
            $table->foreignId('kelas_jurusan_id')->constrained('kelas_jurusans')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->string('serial_number')->nullable()->unique();
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
