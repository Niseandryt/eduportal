<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Foro;

class ForoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupo_id = [1, 2, 3];
        $user_id = [1, 2, 3];

        foreach ($user_id as $u_id) {
            foreach ($grupo_id as $g_id) {
                Foro::create([
                    'user_id' => $u_id,
                    'grupo_id' => $g_id,
                    'titulo' => 'Título de ejemplo',
                    'contenido' => 'Contenido de ejemplo',
                ]);
            }
        }
    }
}
