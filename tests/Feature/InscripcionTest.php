<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InscripcionTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_inscribir_usuario_en_grupo(): void
    {
        $grupo = Grupo::factory()->create();
        $alumno = Alumno::factory()->create();
        Sanctum::actingAs($grupo->maestro->user);

        $response = $this->postJson('/api/inscripciones', [
            'grupo_id' => $grupo->id,
            'user_id' => $alumno->user_id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('inscripciones', ['grupo_id' => $grupo->id, 'user_id' => $alumno->user_id]);
    }

    public function test_no_permite_inscripcion_duplicada(): void
    {
        $grupo = Grupo::factory()->create();
        $alumno = Alumno::factory()->create();
        Inscripcion::create(['grupo_id' => $grupo->id, 'user_id' => $alumno->user_id]);
        Sanctum::actingAs($grupo->maestro->user);

        $this->postJson('/api/inscripciones', [
            'grupo_id' => $grupo->id,
            'user_id' => $alumno->user_id,
        ])->assertUnprocessable();
    }

    public function test_rechaza_grupo_o_usuario_inexistente(): void
    {
        Sanctum::actingAs(Grupo::factory()->create()->maestro->user);

        $this->postJson('/api/inscripciones', [
            'grupo_id' => 999,
            'user_id' => 999,
        ])->assertUnprocessable();
    }

    public function test_puede_eliminar_inscripcion(): void
    {
        $inscripcion = Inscripcion::factory()->create();
        Sanctum::actingAs($inscripcion->grupo->maestro->user);

        $this->deleteJson("/api/inscripciones/{$inscripcion->id}")->assertOk();
        $this->assertDatabaseMissing('inscripciones', ['id' => $inscripcion->id]);
    }
}
