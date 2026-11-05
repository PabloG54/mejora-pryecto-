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
            $table->id('id_usuario');
            $table->string('correo_inst', 120)->unique();
            $table->string('contrasena');
            $table->enum('rol',['estudiante', 'profesor', 'coordinador']);
            $table->enum('estado',['activo','inactivo'])->default('activo');
            
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
