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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'mahasiswa', 'teknisi'])->default('mahasiswa');
                $table->string('phone')->nullable();
                $table->string('photo')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'phone', 'photo', 'is_active']);
            });
        }
    }
};