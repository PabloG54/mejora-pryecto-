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
        Schema::create('student_activities', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->foreignId('id_estudiante')->constrained('students', 'id_estudiante')->restrictOnDelete();
            $table->foreignId('id_periodo')->constrained('academic_periods', 'id_periodo')->restrictOnDelete();
            $table->enum('tipo_actividad', ['semillero', 'investigacion', 'monitoria', 'beca', 'publicacion', 'evento', 'cultural', 'deportiva', 'practica', 'otra']);
            $table->string('nombre_actividad', 150);
            $table->string('organizacion_empresa', 150)->nullable();
            $table->string('rol_o_actividades', 255)->nullable();
            $table->string('evaluacion', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
};
