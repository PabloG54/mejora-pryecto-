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
        Schema::create('followup_interventions', function (Blueprint $table) {
            $table->id('id_intervencion');
            $table->foreignId('id_alerta')->constrained('early_warnings', 'id_alerta')->restrictOnDelete();
            $table->foreignId('id_profesor')->constrained('teachers', 'id_profesor')->restrictOnDelete();
            $table->date('fecha_sesion');
            $table->enum('tipo_intervencion', ['tutoria', 'llamada', 'citacion', 'remision', 'otra']);
            $table->text('observaciones')->nullable();
            $table->text('compromisos')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_interventions');
    }
};
