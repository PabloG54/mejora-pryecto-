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
        Schema::create('students', function (Blueprint $table) {
            $table->id('id_estudiante');
            $table->foreignId('id_usuario')->unique()->constrained('users', 'id_usuario')->restrictOnDelete();
            $table->string('codigo_estud', 20)->unique();
            $table->string('doc_identidad', 20)->unique();
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->string('colegio_origen', 120)->nullable();
            $table->unsignedSmallInteger('puntaje_saber11')->nullable();
            $table->unsignedSmallInteger('prueba_admision')->nullable();
            $table->text('salud_fisica_mental')->nullable(); // cifrado en el modelo (RNF02)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
