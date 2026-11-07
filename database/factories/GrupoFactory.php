<?php

namespace Database\Factories;

use App\Models\Grupo;
use App\Models\Maestro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grupo>
 */
class GrupoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->bothify('GRP-####'),
            'descripcion' => fake()->sentence(),
            'maestro_id' => Maestro::factory(),
        ];
    }
}
