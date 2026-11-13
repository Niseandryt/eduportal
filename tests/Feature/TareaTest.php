<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Maestro;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TareaTest extends TestCase
{
    use RefreshDatabase;

    public function test_maestro_puede_crear_tarea(): void
    {
        $maestro = Maestro::factory()->create();
        $grupo = Grupo::factory()->create(['maestro_id' => $maestro->id]);
        Sanctum::actingAs($maestro->user);

        $response = $this->postJson('/api/tareas', [
            'grupo_id' => $grupo->id,
            'titulo' => 'Practica 11',
            'descripcion' => 'Crear una API',
            'fecha_entrega' => '2026-11-15',
        ]);

        $response->assertCreated()->assertJsonPath('tarea.titulo', 'Practica 11');
        $this->assertDatabaseHas('tareas', ['titulo' => 'Practica 11', 'maestro_id' => $maestro->id]);
    }

    public function test_alumno_no_puede_crear_tarea(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->postJson('/api/tareas', [
            'grupo_id' => $grupo->id,
            'titulo' => 'X',
        ])->assertForbidden();
    }

    public function test_crear_tarea_valida_campos_requeridos(): void
    {
        Sanctum::actingAs(Maestro::factory()->create()->user);

        $this->postJson('/api/tareas', [])->assertUnprocessable();
    }

    public function test_index_filtra_por_grupo(): void
    {
        $grupo = Grupo::factory()->create();
        $otroGrupo = Grupo::factory()->create();
        Tarea::create(['grupo_id' => $grupo->id, 'maestro_id' => $grupo->maestro_id, 'titulo' => 'A']);
        Tarea::create(['grupo_id' => $otroGrupo->id, 'maestro_id' => $otroGrupo->maestro_id, 'titulo' => 'B']);
        Sanctum::actingAs($grupo->maestro->user);

        $this->getJson("/api/tareas?grupo_id={$grupo->id}")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.titulo', 'A');
    }

    public function test_maestro_puede_eliminar_tarea(): void
    {
        $grupo = Grupo::factory()->create();
        $tarea = Tarea::create(['grupo_id' => $grupo->id, 'maestro_id' => $grupo->maestro_id, 'titulo' => 'A']);
        Sanctum::actingAs($grupo->maestro->user);

        $this->deleteJson("/api/tareas/{$tarea->id}")->assertOk();
        $this->assertDatabaseMissing('tareas', ['id' => $tarea->id]);
    }
}
