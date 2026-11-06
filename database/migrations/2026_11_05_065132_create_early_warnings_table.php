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
        Schema::create('early_warnings', function (Blueprint $table) {
            $table->id('id_alerta');
            $table->foreignId('id_estudiante')->constrained('students', 'id_estudiante')->restrictOnDelete();
            $table->foreignId('id_periodo')->constrained('academic_periods', 'id_periodo')->restrictOnDelete();
            $table->enum('tipo_riesgo', ['academico', 'inasistencia', 'socioeconomico', 'bienestar']);
            $table->enum('nivel_riesgo', ['bajo', 'medio', 'alto']);
            $table->date('fecha_reporte');
            $table->enum('estado', ['abierta', 'en_seguimiento', 'cerrada'])->default('abierta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('early_warnings');
    }
};
