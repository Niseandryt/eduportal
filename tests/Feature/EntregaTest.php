<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Entrega;
use App\Models\Grupo;
use App\Models\Maestro;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EntregaTest extends TestCase
{
    use RefreshDatabase;

    private function tareaLista(): Tarea
    {
        $grupo = Grupo::factory()->create();
        return Tarea::create([
            'grupo_id' => $grupo->id,
            'maestro_id' => $grupo->maestro_id,
            'titulo' => 'Practica 11',
        ]);
    }

    public function test_alumno_puede_entregar_tarea(): void
    {
        $tarea = $this->tareaLista();
        $alumno = Alumno::factory()->create();
        Sanctum::actingAs($alumno->user);

        $response = $this->postJson('/api/entregas', [
            'tarea_id' => $tarea->id,
            'contenido' => 'https://github.com/alumno/practica11',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('entregas', ['tarea_id' => $tarea->id, 'alumno_id' => $alumno->id]);
    }

    public function test_maestro_no_puede_entregar_tarea(): void
    {
        $tarea = $this->tareaLista();
        Sanctum::actingAs(Maestro::factory()->create()->user);

        $this->postJson('/api/entregas', [
            'tarea_id' => $tarea->id,
            'contenido' => 'X',
        ])->assertForbidden();
    }

    public function test_alumno_no_puede_entregar_dos_veces_la_misma_tarea(): void
    {
        $tarea = $this->tareaLista();
        $alumno = Alumno::factory()->create();
        Entrega::create(['tarea_id' => $tarea->id, 'alumno_id' => $alumno->id, 'contenido' => 'primera']);
        Sanctum::actingAs($alumno->user);

        $this->postJson('/api/entregas', [
            'tarea_id' => $tarea->id,
            'contenido' => 'segunda',
        ])->assertUnprocessable();
    }

    public function test_maestro_puede_calificar_entrega(): void
    {
        $tarea = $this->tareaLista();
        $entrega = Entrega::create([
            'tarea_id' => $tarea->id,
            'alumno_id' => Alumno::factory()->create()->id,
            'contenido' => 'X',
        ]);
        Sanctum::actingAs(Maestro::factory()->create()->user);

        $this->putJson("/api/entregas/{$entrega->id}", ['calificacion' => 9.5])
            ->assertOk()
            ->assertJsonPath('entrega.calificacion', '9.50');
    }

    public function test_alumno_no_puede_calificar_entrega(): void
    {
        $tarea = $this->tareaLista();
        $alumno = Alumno::factory()->create();
        $entrega = Entrega::create(['tarea_id' => $tarea->id, 'alumno_id' => $alumno->id, 'contenido' => 'X']);
        Sanctum::actingAs($alumno->user);

        $this->putJson("/api/entregas/{$entrega->id}", ['calificacion' => 10])->assertForbidden();
    }

    public function test_calificacion_fuera_de_rango_es_rechazada(): void
    {
        $tarea = $this->tareaLista();
        $entrega = Entrega::create([
            'tarea_id' => $tarea->id,
            'alumno_id' => Alumno::factory()->create()->id,
            'contenido' => 'X',
        ]);
        Sanctum::actingAs(Maestro::factory()->create()->user);

        $this->putJson("/api/entregas/{$entrega->id}", ['calificacion' => 15])->assertUnprocessable();
    }
}
