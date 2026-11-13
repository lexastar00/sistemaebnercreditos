<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea la cuenta inicial del Administrador con los datos del archivo .env.
 * Si la cuenta ya existe no cambia su contraseña.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $correo = config('sistema.admin_email');
        $contrasena = config('sistema.admin_password');

        if (! $correo || ! $contrasena || strlen($contrasena) < 12) {
            $this->command?->warn('No se creó el Administrador: define ADMIN_EMAIL y ADMIN_PASSWORD (mínimo 12 caracteres) en .env y ejecuta «php artisan db:seed --class=AdminSeeder».');

            return;
        }

        $usuario = User::firstOrNew(['email' => $correo]);

        if (! $usuario->exists) {
            $usuario->password = $contrasena;
        }

        $usuario->name = 'Administrador';
        $usuario->activo = true;
        $usuario->save();

        $usuario->syncRoles('Administrador');
    }
}
