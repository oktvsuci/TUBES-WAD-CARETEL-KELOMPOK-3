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
        Schema::create('rating', function (Blueprint $table) {
        $table->id();
        $table->foreignId('laporan_id')->constrained('laporan')->cascadeOnDelete();
        $table->foreignId('mahasiswa_id')->constrained('users');
        $table->foreignId('teknisi_id')->constrained('users');
        $table->tinyInteger('nilai');
        $table->text('komentar')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating');
    }
};
