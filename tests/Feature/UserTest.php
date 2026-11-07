<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lista_usuarios_con_perfiles(): void
    {
        Alumno::factory()->count(2)->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([['id', 'name', 'email', 'alumno', 'maestro']]);
    }

    public function test_show_muestra_usuario_con_perfil_y_grupos(): void
    {
        $alumno = Alumno::factory()->create();
        Sanctum::actingAs($alumno->user);

        $this->getJson("/api/users/{$alumno->user_id}")
            ->assertOk()
            ->assertJsonStructure(['id', 'alumno', 'maestro', 'grupos'])
            ->assertJsonPath('alumno.carnet', $alumno->carnet);
    }

    public function test_update_actualiza_datos(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson("/api/users/{$user->id}", ['name' => 'Nombre Nuevo'])
            ->assertOk()
            ->assertJsonPath('user.name', 'Nombre Nuevo');
    }

    public function test_update_rechaza_email_duplicado(): void
    {
        $otro = User::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson("/api/users/{$user->id}", ['email' => $otro->email])->assertUnprocessable();
    }

    public function test_destroy_elimina_usuario_y_su_perfil(): void
    {
        $alumno = Alumno::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/users/{$alumno->user_id}")->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $alumno->user_id]);
        $this->assertDatabaseMissing('alumnos', ['id' => $alumno->id]);
    }
}
