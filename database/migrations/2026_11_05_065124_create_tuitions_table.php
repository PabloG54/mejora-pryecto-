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
        Schema::create('tuitions', function (Blueprint $table) {
            $table->id('id_matricula');
            $table->foreignId('id_estudiante')->constrained('students', 'id_estudiante')->restrictOnDelete();
            $table->foreignId('id_asignatura')->constrained('courses', 'id_asignatura')->restrictOnDelete();
            $table->foreignId('id_periodo')->constrained('academic_periods', 'id_periodo')->restrictOnDelete();
            $table->decimal('nota_final', 3, 1)->nullable();
            $table->enum('estado_materia', ['inscrita', 'aprobada', 'reprobada', 'cancelada'])->default('inscrita');
            $table->unique(['id_estudiante', 'id_asignatura', 'id_periodo']); // evita duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuitions');
    }
};
