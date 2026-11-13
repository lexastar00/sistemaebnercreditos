<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de los 5 documentos oficiales: su prefijo de folio, su plantilla de Word y la versión de esa plantilla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 20);
            $table->string('nombre', 150);
            $table->string('prefijo_folio', 20);
            $table->string('ruta_plantilla', 255);
            $table->unsignedInteger('version_plantilla')->default(1);
            $table->boolean('activo')->default(true);
            $table->unique(['clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento');
    }
};
