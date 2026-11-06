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
        Schema::create('academic_requests', function (Blueprint $table) {
            $table->id('id_solicitud');
            $table->foreignId('id_estudiante')->constrained('students', 'id_estudiante')->restrictOnDelete();
            $table->foreignId('id_periodo')->constrained('academic_periods', 'id_periodo')->restrictOnDelete();
            $table->enum('tipo_solicitud', ['cancelacion', 'reintegro', 'homologacion', 'excusa', 'otra']);
            $table->date('fecha_radicacion');
            $table->enum('estado', ['radicada', 'en_tramite', 'aprobada', 'rechazada'])->default('radicada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_requests');
    }
};
