<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Relación entre personal y cargos: permite que una persona tenga uno o varios cargos sin duplicarla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles_documento');
            $table->boolean('activo')->default(true);
            $table->unique(['personal_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_rol');
    }
};
