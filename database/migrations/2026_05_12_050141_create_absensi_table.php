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
    Schema::create('absensi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
        $table->date('tanggal');
        $table->time('waktu_masuk')->nullable();
        $table->time('waktu_keluar')->nullable();
        // status: masuk | terlambat | lengkap | terlambat_lengkap
        $table->string('status', 20)->default('masuk');
        $table->timestamps();
        $table->unique(['siswa_id', 'tanggal']); // 1 siswa 1 record per hari
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
