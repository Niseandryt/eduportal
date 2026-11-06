<?php

namespace Database\Seeders;

use App\Models\Foro;
use Illuminate\Database\Seeder;

class ForoSeeder extends Seeder
{
    public function run(): void
    {
        // id 1 - creado por un alumno
        Foro::create([
            'grupo_id' => 1,
            'user_id' => 3,
            'titulo' => 'Error al ejecutar migraciones',
            'contenido' => 'Me sale un error al correr php artisan migrate, alguien sabe por que?',
        ]);

        // id 2 - creado por un maestro
        Foro::create([
            'grupo_id' => 1,
            'user_id' => 1,
            'titulo' => 'Dudas sobre la practica 13',
            'contenido' => 'Escriban aqui sus dudas sobre Sanctum',
        ]);
    }
}