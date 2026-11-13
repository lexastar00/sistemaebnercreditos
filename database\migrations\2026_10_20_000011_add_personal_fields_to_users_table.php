<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Amplía la tabla users de Laravel: vincula al usuario con su registro de personal
 * (para no duplicar personas) y permite desactivar cuentas sin borrarlas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('personal_id')->nullable()->unique()->after('password')->constrained('personal');
            $table->boolean('activo')->default(true)->after('personal_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('personal_id');
            $table->dropColumn('activo');
        });
    }
};
