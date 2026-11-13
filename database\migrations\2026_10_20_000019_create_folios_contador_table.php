<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contador de folios por tipo de documento y año. Se incrementa con bloqueo para que nunca se repita un folio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folios_contador', function (Blueprint $table) {
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->primary(['tipo_documento_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folios_contador');
    }
};
