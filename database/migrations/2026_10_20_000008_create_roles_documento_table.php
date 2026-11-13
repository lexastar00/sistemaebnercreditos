<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de cargos que una persona puede ocupar al firmar o recibir un documento (Jefe de División, Docente, Destinatario…). No son los roles de acceso al sistema.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_documento', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50);
            $table->string('nombre_visible', 150);
            $table->unique(['clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_documento');
    }
};
