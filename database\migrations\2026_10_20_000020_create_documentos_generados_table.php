<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de cada documento Word generado: folio, estudiante, trámite, plantilla usada, archivo y quién lo generó.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_generados', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 30);
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->string('tramite_type', 100);
            $table->unsignedBigInteger('tramite_id');
            $table->unsignedInteger('version_plantilla_usada');
            $table->dateTime('fecha_generacion');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 255);
            $table->char('hash_archivo', 64)->nullable();
            $table->timestamps();
            $table->unique(['folio']);
            $table->index(['tramite_type', 'tramite_id']);
            $table->index(['fecha_generacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_generados');
    }
};
