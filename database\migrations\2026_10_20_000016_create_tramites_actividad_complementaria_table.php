<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Actividad complementaria acreditada a un estudiante. Alimenta la Constancia de Acreditación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites_actividad_complementaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->string('tipo_actividad', 100);
            $table->string('nombre_actividad', 255);
            $table->string('periodo_escolar', 20);
            $table->decimal('creditos', 3, 1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites_actividad_complementaria');
    }
};
