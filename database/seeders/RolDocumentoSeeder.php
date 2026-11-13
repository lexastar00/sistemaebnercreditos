<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cargos que una persona puede ocupar al firmar o recibir un documento y responsables de la bitácora.
 * No son los roles de acceso al sistema.
 */
class RolDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'JEFE_DIVISION' => 'Jefe de División',
            'DOCENTE' => 'Docente',
            'SUBDIRECCION_ACADEMICA' => 'Subdirección académica',
            'DESTINATARIO_SS_RESIDENCIAS' => 'Destinatario de cartas de Servicio Social y Residencias',
            'DESTINATARIO_CONSTANCIA' => 'Destinatario de constancias',
            'SECRETARIO' => 'Secretario',
            'PERSONAL_SISTEMAS' => 'Personal de Sistemas',
            'JEFE_AREA' => 'Jefe de Área',
        ];

        foreach ($roles as $clave => $nombre) {
            DB::table('roles_documento')->updateOrInsert(['clave' => $clave], ['nombre_visible' => $nombre]);
        }
    }
}
