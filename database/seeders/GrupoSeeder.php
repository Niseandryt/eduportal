<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        // id 1 - impartido por el maestro 1
        Grupo::create([
            'nombre' => 'TDSV221',
            'descripcion' => 'Desarrollo de Aplicaciones Moviles',
            'maestro_id' => 1,
        ]);

        // id 2 - impartido por el maestro 2
        Grupo::create([
            'nombre' => 'TDSV222',
            'descripcion' => 'Programacion Web',
            'maestro_id' => 2,
        ]);
    }
}