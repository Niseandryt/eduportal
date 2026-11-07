<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Foro;
use App\Models\Grupo;
use App\Models\Respuesta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ForoRespuestaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cualquier_usuario_puede_crear_foro(): void
    {
        $grupo = Grupo::factory()->create();
        $alumno = Alumno::factory()->create();
        Sanctum::actingAs($alumno->user);

        $response = $this->postJson('/api/foros', [
            'grupo_id' => $grupo->id,
            'titulo' => 'Error al ejecutar migraciones',
            'contenido' => 'Alguien sabe por que?',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('foros', ['titulo' => 'Error al ejecutar migraciones', 'user_id' => $alumno->user_id]);
    }

    public function test_crear_foro_valida_campos(): void
    {
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->postJson('/api/foros', [])->assertUnprocessable();
    }

    public function test_show_foro_incluye_respuestas(): void
    {
        $foro = Foro::create([
            'grupo_id' => Grupo::factory()->create()->id,
            'user_id' => Alumno::factory()->create()->user_id,
            'titulo' => 'Duda',
            'contenido' => 'Contenido',
        ]);
        Respuesta::create([
            'foro_id' => $foro->id,
            'user_id' => $foro->user_id,
            'contenido' => 'Respuesta 1',
        ]);
        Sanctum::actingAs($foro->user);

        $this->getJson("/api/foros/{$foro->id}")
            ->assertOk()
            ->assertJsonStructure(['id', 'titulo', 'user', 'respuestas' => [['contenido', 'user']]]);
    }

    public function test_usuario_puede_responder_foro(): void
    {
        $foro = Foro::create([
            'grupo_id' => Grupo::factory()->create()->id,
            'user_id' => Alumno::factory()->create()->user_id,
            'titulo' => 'Duda',
            'contenido' => 'Contenido',
        ]);
        $otro = Alumno::factory()->create();
        Sanctum::actingAs($otro->user);

        $response = $this->postJson('/api/respuestas', [
            'foro_id' => $foro->id,
            'contenido' => 'Revisa el .env',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('respuestas', ['foro_id' => $foro->id, 'user_id' => $otro->user_id]);
    }

    public function test_respuesta_requiere_foro_existente(): void
    {
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->postJson('/api/respuestas', [
            'foro_id' => 999,
            'contenido' => 'X',
        ])->assertUnprocessable();
    }
}
