<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_de_alumno_crea_perfil_y_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'carnet' => '20260001',
        ]);

        $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'email', 'alumno']]);
        $this->assertDatabaseHas('alumnos', ['carnet' => '20260001']);
        $this->assertDatabaseMissing('maestros', ['user_id' => $response->json('user.id')]);
    }

    public function test_registro_de_maestro_crea_perfil_y_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Maria Lopez',
            'email' => 'maria@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'departamento' => 'Informatica',
        ]);

        $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'email', 'maestro']]);
        $this->assertDatabaseHas('maestros', ['departamento' => 'Informatica']);
    }

    public function test_registro_rechaza_carnet_y_departamento_a_la_vez(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ambos',
            'email' => 'ambos@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'carnet' => '20260002',
            'departamento' => 'Informatica',
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('users', ['email' => 'ambos@example.com']);
    }

    public function test_registro_rechaza_sin_carnet_ni_departamento(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ninguno',
            'email' => 'ninguno@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('users', ['email' => 'ninguno@example.com']);
    }

    public function test_registro_rechaza_carnet_duplicado(): void
    {
        User::factory()->create()->alumno()->create(['carnet' => '20260001']);

        $response = $this->postJson('/api/register', [
            'name' => 'Duplicado',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'carnet' => '20260001',
        ]);

        $response->assertUnprocessable();
    }

    public function test_login_devuelve_token_y_usuario_con_perfil(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $user->alumno()->create(['carnet' => '20260010']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user' => ['alumno']]);
    }

    public function test_login_rechaza_credenciales_incorrectas(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ])->assertUnauthorized();
    }

    public function test_logout_cierra_sesion(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/logout')->assertOk()->assertJson(['message' => 'Sesion cerrada']);
    }
}
