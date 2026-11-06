<?php

namespace Database\Seeders;

use App\Models\Inscripcion;
use Illuminate\Database\Seeder;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        // Grupo 1: maestro 1 y los 3 alumnos
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 1]);
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 3]);
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 4]);
        Inscripcion::create(['grupo_id' => 1, 'user_id' => 5]);

        // Grupo 2: maestro 2 y 2 alumnos
        Inscripcion::create(['grupo_id' => 2, 'user_id' => 2]);
        Inscripcion::create(['grupo_id' => 2, 'user_id' => 3]);
        Inscripcion::create(['grupo_id' => 2, 'user_id' => 4]);
    }
}