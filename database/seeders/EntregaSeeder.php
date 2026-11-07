<?php

namespace Database\Seeders;
use App\Models\Entrega;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntregaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // alumno_id ahora referencia a la tabla alumnos (1 = Daniel, 2 = Karina)
        Entrega::create([
            'tarea_id' => 1,
            'alumno_id' => 1,
            'contenido' => 'https://github.com/alumno/practica11',
            'calificacion' => 9.50,
        ]);

        Entrega::create([
            'tarea_id' => 1,
            'alumno_id' => 2,
            'contenido' => 'https://github.com/alumno2/practica11',
            'calificacion' => 8.00,
        ]);
    }
}
