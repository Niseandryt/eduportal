<?php

namespace Database\Seeders;

use App\Models\Tarea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

         // id 1
        Tarea::create([
            'grupo_id' => 1,
            'maestro_id' => 1,
            'titulo' => 'Practica 11 - API con Laravel',
            'descripcion' => 'Crear una API con index y store',
            'fecha_entrega' => '2026-11-15',
        ]);
    }
}
