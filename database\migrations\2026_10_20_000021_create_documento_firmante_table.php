<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quién ocupó cada bloque de firma en un documento concreto. Guarda copia del nombre y puesto para que un cambio de personal no altere documentos ya emitidos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_firmante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_generado_id')->constrained('documentos_generados')->cascadeOnDelete();
            $table->foreignId('bloque_firma_id')->constrained('bloques_firma');
            $table->foreignId('personal_id')->constrained('personal');
            $table->string('nombre_snapshot', 200);
            $table->string('puesto_snapshot', 200);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_firmante');
    }
};
