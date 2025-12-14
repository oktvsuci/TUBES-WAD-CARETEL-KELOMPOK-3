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
        Schema::create('dokumentasi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('laporan_id')->constrained('laporan')->cascadeOnDelete();
        $table->foreignId('teknisi_id')->constrained('users');
        $table->string('foto_before')->nullable();
        $table->string('foto_after')->nullable();
        $table->text('catatan')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasi');
    }
};
