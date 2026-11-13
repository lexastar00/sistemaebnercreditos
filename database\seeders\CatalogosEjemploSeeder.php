<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * VALORES DE EJEMPLO. Reemplázalos por la lista real del Área de Sistemas.
 */
class CatalogosEjemploSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Área de Sistemas', 'División de Ingeniería en Sistemas Computacionales'] as $nombre) {
            DB::table('departamentos')->updateOrInsert(['nombre' => $nombre], ['activo' => true]);
        }

        $departamentoId = DB::table('departamentos')->where('nombre', 'Área de Sistemas')->value('id');

        foreach (['Soporte técnico', 'Cuentas y accesos'] as $nombre) {
            DB::table('servicios')->updateOrInsert(['nombre' => $nombre, 'departamento_id' => $departamentoId], ['activo' => true]);
        }

        foreach (['Soporte', 'Instalación', 'Asesoría', 'Trámite'] as $nombre) {
            DB::table('tipos_atencion')->updateOrInsert(['nombre' => $nombre], ['activo' => true]);
        }

        foreach (['Hardware', 'Software', 'Red', 'Cuenta de usuario'] as $nombre) {
            DB::table('tipos_problema')->updateOrInsert(['nombre' => $nombre], ['activo' => true]);
        }
    }
}
