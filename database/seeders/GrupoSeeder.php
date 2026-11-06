<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        // id 1
        Grupo::create([
            'nombre' => 'TDSV221',
            'descripcion' => 'Desarrollo de Aplicaciones Moviles',
        ]);

        // id 2
        Grupo::create([
            'nombre' => 'TDSV222',
            'descripcion' => 'Programacion Web',
        ]);
    }
}