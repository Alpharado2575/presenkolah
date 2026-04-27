<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
           $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['siswa', 'guru', 'admin'])->default('siswa');
            $table->string('kelas', 20)->nullable();   // misal: "XI-SIJA-1"
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
