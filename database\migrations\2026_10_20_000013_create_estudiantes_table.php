<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro central de estudiantes. Guarda solo datos permanentes; fechas, horas y proyectos viven en los trámites.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 200);
            $table->string('matricula', 20);
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->unsignedTinyInteger('semestre')->nullable();
            $table->string('correo_institucional', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->foreignId('periodo_id')->nullable()->constrained('periodos_escolares');
            $table->enum('estatus', ['activo', 'egresado', 'baja'])->default('activo');
            $table->timestamps();
            $table->unique(['matricula']);
            $table->index(['nombre_completo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
