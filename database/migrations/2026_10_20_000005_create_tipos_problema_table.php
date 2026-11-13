<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de tipos de problema (ej. hardware, software, red).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_problema', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_problema');
    }
};
