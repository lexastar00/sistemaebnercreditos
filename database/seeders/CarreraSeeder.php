<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carreras')->updateOrInsert(['nombre' => config('sistema.carrera')], ['activo' => true]);
    }
}
