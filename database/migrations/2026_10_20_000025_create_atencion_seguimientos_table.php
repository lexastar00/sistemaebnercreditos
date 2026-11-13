<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historial de una atención: cada cambio de estado, de responsable o comentario queda como una fila.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atencion_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')->constrained('atenciones')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('estado_anterior_id')->nullable()->constrained('estados_atencion');
            $table->foreignId('estado_nuevo_id')->constrained('estados_atencion');
            $table->foreignId('responsable_anterior_id')->nullable()->constrained('personal');
            $table->foreignId('responsable_nuevo_id')->nullable()->constrained('personal');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->index(['atencion_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atencion_seguimientos');
    }
};
