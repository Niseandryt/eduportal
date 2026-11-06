<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Maestros (id 1 y 2)
        User::create([
            'name' => 'Denis Lue',
            'email' => 'maestro@edutech.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'maestro',
        ]);

        User::create([
            'name' => 'Carlos Martinez',
            'email' => 'maestro2@edutech.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'maestro',
        ]);

        // Alumnos (id 3, 4 y 5)
        User::create([
            'name' => 'Daniel Angel',
            'email' => 'alumno@edutech.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'alumno',
        ]);

        User::create([
            'name' => 'Karina Hidalgo',
            'email' => 'alumno2@edutech.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'alumno',
        ]);

        User::create([
            'name' => 'Ashley Vasquez',
            'email' => 'alumno3@edutech.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'alumno',
        ]);
    }
}