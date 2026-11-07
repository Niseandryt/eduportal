<?php

namespace Database\Factories;

use App\Models\Maestro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maestro>
 */
class MaestroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'departamento' => fake()->randomElement(['Informatica', 'Matematica', 'Ciencias', 'Idiomas']),
        ];
    }
}
