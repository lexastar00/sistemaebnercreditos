<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo único de personas que firman, reciben documentos o son responsables en la bitácora. Una persona aparece una sola vez aunque tenga varios cargos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 200);
            $table->string('puesto_actual', 200)->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
