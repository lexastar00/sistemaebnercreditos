<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Evidencias (imágenes o PDF) adjuntas a una atención.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atencion_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')->constrained('atenciones')->cascadeOnDelete();
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 255);
            $table->string('tipo_mime', 100);
            $table->unsignedInteger('tamano_kb');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamp('fecha_subida')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atencion_adjuntos');
    }
};
