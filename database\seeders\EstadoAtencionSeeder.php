<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoAtencionSeeder extends Seeder
{
    public function run(): void
    {
        $estados = ['Registrada', 'En proceso', 'Pendiente', 'Resuelta', 'Cancelada', 'Reabierta'];

        foreach ($estados as $orden => $nombre) {
            DB::table('estados_atencion')->updateOrInsert(['nombre' => $nombre], ['orden' => $orden + 1, 'activo' => true]);
        }
    }
}
