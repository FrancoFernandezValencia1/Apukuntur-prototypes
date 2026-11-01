<?php

namespace Tests\Feature;

use App\Models\Diseno;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisenosTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_can_view_the_design_sheet(): void
    {
        $this->seed();

        $fabrica = Usuario::where('correo', 'fabrica@apukuntur.test')->firstOrFail();

        $this->actingAs($fabrica)
            ->get(route('disenos.index'))
            ->assertOk()
            ->assertSee('Sweater Baby Alpaca')
            ->assertSee('AK-D-001');
    }

    public function test_eu_can_view_the_technical_sheet(): void
    {
        $this->seed();

        $eu = Usuario::where('correo', 'eu@apukuntur.test')->firstOrFail();
        $diseno = Diseno::where('codigo', 'AK-D-001')->firstOrFail();

        $this->actingAs($eu)
            ->get(route('disenos.index', ['diseno' => $diseno->id, 'tab' => 'ficha']))
            ->assertOk()
            ->assertSee('Ficha técnica')
            ->assertSee('Colores material');
    }

    public function test_factory_can_add_an_observation(): void
    {
        $this->seed();

        $fabrica = Usuario::where('correo', 'fabrica@apukuntur.test')->firstOrFail();
        $diseno = Diseno::where('codigo', 'AK-D-001')->firstOrFail();

        $this->actingAs($fabrica)
            ->post(route('disenos.observaciones.store', $diseno), ['comentario' => 'Observación de prueba'])
            ->assertRedirect(route('disenos.index', ['diseno' => $diseno->id, 'tab' => 'ficha']));

        $this->assertDatabaseHas('observaciones_diseno', [
            'diseno_id' => $diseno->id,
            'autor' => $fabrica->nombre,
            'comentario' => 'Observación de prueba',
        ]);
    }

    public function test_factory_can_create_a_design_with_its_relations(): void
    {
        $this->seed();

        $fabrica = Usuario::where('correo', 'fabrica@apukuntur.test')->firstOrFail();

        $this->actingAs($fabrica)
            ->post(route('disenos.store'), [
                'codigo' => 'AK-T-001',
                'nombre' => 'Diseño de prueba',
                'version' => 1,
                'idea' => ['tipo_prenda' => 'Chompa', 'temporada' => 'Invierno'],
                'ficha' => ['modelo' => 'MODELO TEST', 'color_principal' => 'ROJO', 'observaciones' => 'Nota inicial'],
                'colores_idea' => [['color' => 'Rojo', 'tipo_tejido' => 'Punto']],
                'colores_material' => [['color' => 'ROJO', 'hex' => '#FF0000', 'codigo' => 'R1', 'cantidad_pedida' => 2]],
                'medidas_prototipo' => [['nombre' => 'Largo', 's' => 10, 'm' => 11]],
                'medidas_ficha' => [['nombre' => 'Largo', 's' => 10, 'm' => 11]],
                'insumos' => [['nombre' => 'Botón', 'cantidad' => 2, 'codigo' => 'B1']],
                'notas' => ['general' => 'Nota general'],
            ])
            ->assertRedirect();

        $diseno = Diseno::where('codigo', 'AK-T-001')->firstOrFail();

        $this->assertDatabaseHas('ideas_diseno', ['diseno_id' => $diseno->id, 'tipo_prenda' => 'Chompa']);
        $this->assertDatabaseHas('colores_material', ['diseno_id' => $diseno->id, 'codigo' => 'R1']);
        $this->assertDatabaseHas('medidas_ficha', ['diseno_id' => $diseno->id, 'nombre' => 'Largo']);
        $this->assertDatabaseHas('insumos_ficha', ['diseno_id' => $diseno->id, 'codigo' => 'B1']);
        $this->assertDatabaseHas('historial_notas_ficha', ['diseno_id' => $diseno->id, 'autor' => $fabrica->nombre]);
    }

    public function test_eu_can_review_and_approve_a_design(): void
    {
        $this->seed();

        $eu = Usuario::where('correo', 'eu@apukuntur.test')->firstOrFail();
        $diseno = Diseno::where('codigo', 'AK-D-001')->firstOrFail();

        $this->actingAs($eu)
            ->post(route('disenos.revisiones.store', $diseno), [
                'estado' => 'aprobada',
                'comentario' => 'Revisión de prueba',
            ])
            ->assertRedirect(route('disenos.index', ['diseno' => $diseno->id, 'tab' => 'ficha']));

        $this->assertDatabaseHas('revisiones_diseno', [
            'diseno_id' => $diseno->id,
            'revisor' => 'Ana Gómez',
            'estado' => 'aprobada',
        ]);

        $this->actingAs($eu)
            ->patch(route('disenos.aprobacion.update', $diseno), [
                'estado_aprobacion' => 'aprobado',
                'aprobado_para_produccion' => 1,
            ])
            ->assertRedirect(route('disenos.index', ['diseno' => $diseno->id, 'tab' => 'idea']));

        $this->assertDatabaseHas('disenos', [
            'id' => $diseno->id,
            'estado_aprobacion' => 'aprobado',
            'aprobado_para_produccion' => 1,
        ]);
    }

    public function test_eu_can_assign_a_barcode_to_a_material_color(): void
    {
        $this->seed();

        $eu = Usuario::where('correo', 'eu@apukuntur.test')->firstOrFail();
        $diseno = Diseno::where('codigo', 'AK-D-001')->firstOrFail();

        $this->actingAs($eu)
            ->post(route('disenos.codigos.store', $diseno), [
                'codigo_color' => 'AM9110',
                'talla' => 'm',
                'codigo' => '',
            ])
            ->assertRedirect(route('disenos.index', ['diseno' => $diseno->id, 'tab' => 'ficha', 'active' => 'AM9110__m']));

        $this->assertDatabaseHas('codigos_barras_diseno', [
            'diseno_id' => $diseno->id,
            'codigo_color' => 'AM9110',
            'talla' => 'm',
            'origen' => 'eu',
            'codigo' => 'AK-D-001-AM9110-M',
        ]);
    }
}

