<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contador de folios de la bitácora (SIS-AAAA-000001) por año.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folios_atencion', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->primary();
            $table->unsignedInteger('ultimo_numero')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folios_atencion');
    }
};
