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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('link_name');
            $table->string('link_title');
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('kelas_jurusan_id')->constrained('kelas_jurusans')->onDelete('cascade');
            $table->enum('link_status', ['active', 'inactive'])->default('active');
            $table->integer('waktu_pengerjaan');
            $table->timestamp('waktu_pengerjaan_mulai')->nullable();
            $table->timestamp('waktu_pengerjaan_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
