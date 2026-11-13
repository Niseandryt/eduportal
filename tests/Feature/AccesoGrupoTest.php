<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Entrega;
use App\Models\Foro;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Material;
use App\Models\Respuesta;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccesoGrupoTest extends TestCase
{
    use RefreshDatabase;

    public function test_acceso_depende_de_la_inscripcion_y_se_revoca_al_eliminarla(): void
    {
        $grupo = Grupo::factory()->create();
        $otroGrupo = Grupo::factory()->create();
        $alumno = Alumno::factory()->create();
        $tarea = Tarea::create(['grupo_id' => $grupo->id, 'maestro_id' => $grupo->maestro_id, 'titulo' => 'Tarea']);
        $material = Material::create(['grupo_id' => $grupo->id, 'maestro_id' => $grupo->maestro_id, 'titulo' => 'Material']);
        $foro = Foro::create(['grupo_id' => $grupo->id, 'user_id' => $grupo->maestro->user_id, 'titulo' => 'Foro', 'contenido' => 'Contenido']);
        $respuesta = Respuesta::create(['foro_id' => $foro->id, 'user_id' => $grupo->maestro->user_id, 'contenido' => 'Respuesta']);
        $entrega = Entrega::create(['tarea_id' => $tarea->id, 'alumno_id' => $alumno->id, 'contenido' => 'Entrega']);
        Tarea::create(['grupo_id' => $otroGrupo->id, 'maestro_id' => $otroGrupo->maestro_id, 'titulo' => 'Otra']);

        Sanctum::actingAs($alumno->user);
        foreach (['grupos', 'tareas', 'materiales', 'foros', 'respuestas', 'entregas', 'inscripciones'] as $recurso) {
            $this->getJson("/api/{$recurso}")->assertOk()->assertJsonCount(0);
        }
        foreach (["grupos/{$grupo->id}", "tareas/{$tarea->id}", "materiales/{$material->id}", "foros/{$foro->id}", "respuestas/{$respuesta->id}", "entregas/{$entrega->id}"] as $recurso) {
            $this->getJson("/api/{$recurso}")->assertForbidden();
        }
        $this->postJson('/api/inscripciones', ['grupo_id' => $grupo->id, 'user_id' => $alumno->user_id])->assertForbidden();
        $this->postJson('/api/foros', ['grupo_id' => $grupo->id, 'titulo' => 'X', 'contenido' => 'X'])->assertForbidden();
        $this->postJson('/api/respuestas', ['foro_id' => $foro->id, 'contenido' => 'X'])->assertForbidden();
        $otraTarea = Tarea::create(['grupo_id' => $grupo->id, 'maestro_id' => $grupo->maestro_id, 'titulo' => 'Otra tarea']);
        $this->postJson('/api/entregas', ['tarea_id' => $otraTarea->id, 'contenido' => 'X'])->assertForbidden();

        $inscripcion = Inscripcion::create(['grupo_id' => $grupo->id, 'user_id' => $alumno->user_id]);
        foreach (['grupos', 'materiales', 'foros', 'respuestas', 'entregas'] as $recurso) {
            $this->getJson("/api/{$recurso}")->assertOk()->assertJsonCount(1);
        }
        $this->getJson('/api/tareas')->assertOk()->assertJsonCount(2);
        $this->getJson("/api/tareas?grupo_id={$otroGrupo->id}")->assertOk()->assertJsonCount(0);
        $this->getJson("/api/materiales?grupo_id={$otroGrupo->id}")->assertOk()->assertJsonCount(0);
        $this->getJson("/api/foros?grupo_id={$otroGrupo->id}")->assertOk()->assertJsonCount(0);
        $this->getJson("/api/foros/{$foro->id}")->assertOk();
        $this->postJson('/api/respuestas', ['foro_id' => $foro->id, 'contenido' => 'Nueva'])->assertCreated();
        $this->postJson('/api/entregas', ['tarea_id' => $otraTarea->id, 'contenido' => 'Nueva'])->assertCreated();

        $inscripcion->delete();
        foreach (['grupos', 'tareas', 'materiales', 'foros', 'respuestas', 'entregas'] as $recurso) {
            $this->getJson("/api/{$recurso}")->assertOk()->assertJsonCount(0);
        }
        foreach (["tareas/{$tarea->id}", "materiales/{$material->id}", "foros/{$foro->id}"] as $recurso) {
            $this->getJson("/api/{$recurso}")->assertForbidden();
        }
    }

    public function test_maestro_ve_sus_grupos_y_no_los_ajenos(): void
    {
        $grupo = Grupo::factory()->create();
        $otro = Grupo::factory()->create();
        Sanctum::actingAs($grupo->maestro->user);

        $this->getJson('/api/grupos')->assertOk()->assertJsonCount(1)->assertJsonPath('0.id', $grupo->id);
        $this->getJson("/api/grupos/{$otro->id}")->assertForbidden();
        $this->postJson('/api/tareas', ['grupo_id' => $otro->id, 'titulo' => 'X'])->assertForbidden();
        $this->postJson('/api/materiales', ['grupo_id' => $otro->id, 'titulo' => 'X'])->assertForbidden();
    }
}
