<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Maestro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GrupoTest extends TestCase
{
    use RefreshDatabase;

    public function test_maestro_puede_crear_grupo(): void
    {
        $maestro = Maestro::factory()->create();
        Sanctum::actingAs($maestro->user);

        $response = $this->postJson('/api/grupos', [
            'nombre' => 'TDSV221',
            'descripcion' => 'Desarrollo de Aplicaciones Moviles',
        ]);

        $response->assertCreated()->assertJsonPath('grupo.nombre', 'TDSV221');
        $this->assertDatabaseHas('grupos', ['nombre' => 'TDSV221', 'maestro_id' => $maestro->id]);
    }

    public function test_alumno_no_puede_crear_grupo(): void
    {
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->postJson('/api/grupos', ['nombre' => 'X'])->assertForbidden();
    }

    public function test_invitado_no_puede_ver_grupos(): void
    {
        $this->getJson('/api/grupos')->assertUnauthorized();
    }

    public function test_index_lista_grupos_con_maestro(): void
    {
        $maestro = Maestro::factory()->create();
        Grupo::factory()->count(2)->create(['maestro_id' => $maestro->id]);
        Sanctum::actingAs($maestro->user);

        $this->getJson('/api/grupos')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([['id', 'nombre', 'maestro' => ['user']]]);
    }

    public function test_show_incluye_relaciones(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs($grupo->maestro->user);

        $this->getJson("/api/grupos/{$grupo->id}")
            ->assertOk()
            ->assertJsonStructure(['id', 'nombre', 'maestro', 'users', 'tareas', 'materiales', 'foros']);
    }

    public function test_maestro_puede_actualizar_y_eliminar_grupo(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs($grupo->maestro->user);

        $this->putJson("/api/grupos/{$grupo->id}", ['nombre' => 'Nuevo nombre'])
            ->assertOk()
            ->assertJsonPath('grupo.nombre', 'Nuevo nombre');

        $this->deleteJson("/api/grupos/{$grupo->id}")->assertOk();
        $this->assertDatabaseMissing('grupos', ['id' => $grupo->id]);
    }

    public function test_alumno_no_puede_actualizar_ni_eliminar_grupo(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->putJson("/api/grupos/{$grupo->id}", ['nombre' => 'X'])->assertForbidden();
        $this->deleteJson("/api/grupos/{$grupo->id}")->assertForbidden();
    }
}
