<?php

namespace Database\Seeders;

use App\Models\Inscripcion;
use Illuminate\Database\Seeder;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        // Grupo 1: los 3 alumnos (el maestro 1 se asigna en grupos.maestro_id)
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 3]);
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 4]);
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 5]);

        // Grupo 2: 2 alumnos
        Inscripcion::create(['grupo_id' => 2, 'user_id' => 3]);
        Inscripcion::create(['grupo_id' => 2, 'user_id' => 4]);
    }
}