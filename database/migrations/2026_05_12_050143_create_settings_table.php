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
    Schema::create('settings', function (Blueprint $table) {
        $table->string('kunci', 50)->primary();
        $table->string('nilai', 100);
        $table->timestamps();
    });

    // Default jam batas masuk
    DB::table('settings')->insert([
        'kunci'      => 'jam_batas_masuk',
        'nilai'      => '07:15:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
