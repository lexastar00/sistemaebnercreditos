<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trámite de Servicio Social de un estudiante. Alimenta sus cartas y el seguimiento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites_servicio_social', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->string('institucion_receptora', 255);
            $table->string('programa_actividad', 255)->nullable();
            $table->foreignId('periodo_id')->nullable()->constrained('periodos_escolares');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->unsignedSmallInteger('horas');
            $table->enum('estado', ['en_curso', 'terminado', 'cancelado'])->default('en_curso');
            $table->timestamps();
            $table->index(['estudiante_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites_servicio_social');
    }
};
