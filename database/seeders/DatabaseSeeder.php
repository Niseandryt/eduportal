<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GrupoSeeder::class,
            InscripcionSeeder::class,
            TareaSeeder::class,
            EntregaSeeder::class,
            MaterialSeeder::class,
            ForoSeeder::class,
            RespuestaSeeder::class,
        ]);
    }
}