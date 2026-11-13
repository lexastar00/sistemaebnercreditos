<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro principal de la bitácora: toda entrada de un alumno, docente u otra persona al Área de Sistemas es una atención, con su hora de entrada y salida, problema, solución, responsable y estado actual.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20);
            $table->dateTime('fecha_hora');
            $table->enum('tipo_solicitante', ['alumno', 'docente', 'administrativo', 'otro'])->default('alumno');
            $table->dateTime('hora_salida')->nullable();
            $table->string('solicitante_nombre', 200);
            $table->string('matricula', 20)->nullable();
            $table->foreignId('estudiante_id')->nullable()->constrained('estudiantes');
            $table->string('asunto', 255);
            $table->foreignId('jefe_id')->constrained('personal');
            $table->foreignId('secretario_id')->constrained('personal');
            $table->foreignId('atendido_por_id')->nullable()->constrained('personal');
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->foreignId('tipo_atencion_id')->constrained('tipos_atencion');
            $table->foreignId('tipo_problema_id')->nullable()->constrained('tipos_problema');
            $table->text('descripcion_problema');
            $table->text('solucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('estado_id')->constrained('estados_atencion');
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
            $table->unique(['folio']);
            $table->index(['fecha_hora']);
            $table->index(['matricula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones');
    }
};
