<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de periodos escolares con sus fechas. Solo uno debería estar activo a la vez.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_escolares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(false);
            $table->unique(['nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_escolares');
    }
};
