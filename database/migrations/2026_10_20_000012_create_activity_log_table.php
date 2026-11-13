<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de auditoría: quién hizo qué, sobre qué registro y cuándo. Su estructura es compatible con Spatie Activitylog; en la Fase 13 se decide si se usa el paquete o un registro propio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name', 255)->nullable();
            $table->text('description');
            $table->string('subject_type', 255)->nullable();
            $table->string('event', 255)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('causer_type', 255)->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->json('properties')->nullable();
            $table->char('batch_uuid', 36)->nullable();
            $table->timestamps();
            $table->index(['log_name']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['causer_type', 'causer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
