<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trámite de Residencia Profesional de un estudiante. Alimenta la Carta de Aceptación, la de Término y el seguimiento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites_residencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->string('proyecto', 255);
            $table->string('empresa_institucion', 255);
            $table->foreignId('asesor_interno_id')->nullable()->constrained('personal');
            $table->string('asesor_externo', 200)->nullable();
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
        Schema::dropIfExists('tramites_residencia');
    }
};
