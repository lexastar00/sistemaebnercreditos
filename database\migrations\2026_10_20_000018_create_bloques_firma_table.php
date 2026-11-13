<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Define qué bloques de firma o destinatario lleva cada tipo de documento y en qué orden.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloques_firma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->foreignId('rol_id')->constrained('roles_documento');
            $table->string('etiqueta', 100);
            $table->smallInteger('orden');
            $table->boolean('obligatorio')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_firma');
    }
};
