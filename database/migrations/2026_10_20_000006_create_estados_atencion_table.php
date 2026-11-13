<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de estados del ciclo de vida de una atención (Registrada, En proceso, Pendiente, Resuelta, Cancelada, Reabierta). Es tabla para poder ajustarlos sin cambiar código.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados_atencion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->smallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->unique(['nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_atencion');
    }
};
