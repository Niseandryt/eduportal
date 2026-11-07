<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Maestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_maestro_puede_crear_material(): void
    {
        $maestro = Maestro::factory()->create();
        $grupo = Grupo::factory()->create(['maestro_id' => $maestro->id]);
        Sanctum::actingAs($maestro->user);

        $response = $this->postJson('/api/materiales', [
            'grupo_id' => $grupo->id,
            'titulo' => 'Guia de Postman',
            'enlace' => 'https://learning.postman.com',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('materiales', ['titulo' => 'Guia de Postman', 'maestro_id' => $maestro->id]);
    }

    public function test_alumno_no_puede_crear_material(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs(Alumno::factory()->create()->user);

        $this->postJson('/api/materiales', [
            'grupo_id' => $grupo->id,
            'titulo' => 'X',
        ])->assertForbidden();
    }

    public function test_enlace_debe_ser_url_valida(): void
    {
        $grupo = Grupo::factory()->create();
        Sanctum::actingAs($grupo->maestro->user);

        $this->postJson('/api/materiales', [
            'grupo_id' => $grupo->id,
            'titulo' => 'X',
            'enlace' => 'no-es-una-url',
        ])->assertUnprocessable();
    }
}
