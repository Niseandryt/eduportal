<?php

namespace Database\Seeders;

use Database\Seeders\GrupoSeeder;
use Database\Seeders\InscripcionSeeder;
use Database\Seeders\UserSeeder;
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
        ]);
    }
}
