<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Material::create([
            'grupo_id' => 1,
            'maestro_id' => 1,
            'titulo' => 'Guia de Postman',
            'descripcion' => 'Como probar una API con Postman',
            'enlace' => 'https://learning.postman.com',
        ]);
    }
}
