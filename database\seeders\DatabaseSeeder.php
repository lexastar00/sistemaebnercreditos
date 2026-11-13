<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CarreraSeeder::class,
            EstadoAtencionSeeder::class,
            RolDocumentoSeeder::class,
            TipoDocumentoSeeder::class,
            RolesPermisosSeeder::class,
            CatalogosEjemploSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
