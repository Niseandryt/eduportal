<?php

namespace Database\Seeders;

use App\Models\Respuesta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RespuestaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

           // Respuestas al foro 1
        Respuesta::create([
            'foro_id' => 1,
            'user_id' => 4,
            'contenido' => 'Revisa que tengas bien configurado el archivo .env',
        ]);

        Respuesta::create([
            'foro_id' => 1,
            'user_id' => 1,
            'contenido' => 'Tambien prueba con php artisan migrate:fresh',
        ]);
    }
}
