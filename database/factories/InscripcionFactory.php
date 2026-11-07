<?php

namespace Database\Factories;

use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscripcion>
 */
class InscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grupo_id' => Grupo::factory(),
            'user_id' => User::factory(),
        ];
    }
}
