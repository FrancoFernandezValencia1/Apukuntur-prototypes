<?php

namespace App\Http\Controllers;

use App\Models\CodigoBarrasDiseno;
use App\Models\Diseno;
use App\Models\RevisionDiseno;
use App\Support\ImagenCodigoBarras;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Observaciones: Fábrica (INT) y EU.
 * Decisión, revisiones y códigos de barras: SOLO EU (middleware can:eu en las rutas).
 */
class FlujoDisenoController extends Controller
{
    public function storeObservation(Request $request, Diseno $diseno): RedirectResponse
    {
        $datos = $request->validate([
            'comentario' => ['required', 'string', 'max:2000'],
        ], [], ['comentario' => 'observación']);

        $usuario = $request->user();

        $diseno->observaciones()->create([
            'usuario_id' => $usuario->id,
            'autor' => $usuario->nombre,
            'rol' => $usuario->rol,
            'comentario' => $datos['comentario'],
            'observado_en' => now(),
        ]);

        return $this->aFicha($diseno, 'Observación agregada.');
    }

    public function storeReview(Request $request, Diseno $diseno): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(array_keys(RevisionDiseno::ESTADOS))],
            'comentario' => ['required', 'string', 'max:2000'],
        ], [], ['comentario' => 'comentario de la revisión']);

        $diseno->revisiones()->create([
            'usuario_id' => $request->user()->id,
            'revisor' => $request->user()->nombre,
            'estado' => $datos['estado'],
            'comentario' => $datos['comentario'],
            'revisado_en' => now(),
        ]);

        return $this->aFicha($diseno, 'Revisión EU registrada.');
    }

    public function updateApproval(Request $request, Diseno $diseno): RedirectResponse
    {
        $datos = $request->validate([
            'estado_aprobacion' => ['required', Rule::in(array_keys(Diseno::ESTADOS))],
            'aprobado_para_produccion' => ['nullable', 'boolean'],
        ]);

        $diseno->update([
            'estado_aprobacion' => $datos['estado_aprobacion'],
            'aprobado_para_produccion' => $request->boolean('aprobado_para_produccion'),
        ]);

        return redirect()
            ->route('disenos.index', ['diseno' => $diseno->id, 'tab' => $request->input('_tab', 'idea')])
            ->with('status', 'Decisión de EU guardada.');
    }

    public function storeBarcode(Request $request, Diseno $diseno): RedirectResponse
    {
        return $this->saveBarcode($request, $diseno, CodigoBarrasDiseno::ORIGEN_EU);
    }

    public function storeFactoryBarcode(Request $request, Diseno $diseno): RedirectResponse
    {
        return $this->saveBarcode($request, $diseno, CodigoBarrasDiseno::ORIGEN_FABRICA);
    }

    public function previewBarcode(Request $request): Response
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:120', 'regex:/^[\x20-\x7E]+$/'],
        ]);

        $codigo = trim($datos['codigo']);
        abort_if($codigo === '', 422, 'El código de barras no puede estar vacío.');

        return response(ImagenCodigoBarras::svg($codigo), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function destroyBarcode(Request $request, Diseno $diseno): RedirectResponse
    {
        return $this->removeBarcode($request, $diseno, CodigoBarrasDiseno::ORIGEN_EU);
    }

    public function destroyFactoryBarcode(Request $request, Diseno $diseno): RedirectResponse
    {
        return $this->removeBarcode($request, $diseno, CodigoBarrasDiseno::ORIGEN_FABRICA);
    }

    private function saveBarcode(Request $request, Diseno $diseno, string $origen): RedirectResponse
    {
        $datos = $this->barcodeInput($request, $diseno);

        $codigo = $datos['codigo'] ?: "{$diseno->codigo}-{$datos['codigo_color']}-" . Diseno::TALLAS[$datos['talla']];

        CodigoBarrasDiseno::updateOrCreate(
            [
                'diseno_id' => $diseno->id,
                'codigo_color' => $datos['codigo_color'],
                'talla' => $datos['talla'],
                'origen' => $origen,
            ],
            ['codigo' => $codigo, 'asignado_por' => $request->user()->id, 'asignado_en' => now()],
        );

        $etiqueta = $origen === CodigoBarrasDiseno::ORIGEN_FABRICA ? 'de Fábrica ' : '';

        return $this->aFicha($diseno, "Código de barras {$etiqueta}asignado: {$codigo}", $datos);
    }

    private function removeBarcode(Request $request, Diseno $diseno, string $origen): RedirectResponse
    {
        $datos = $this->barcodeInput($request, $diseno);

        $diseno->codigosBarras()
            ->where('origen', $origen)
            ->where('codigo_color', $datos['codigo_color'])
            ->where('talla', $datos['talla'])
            ->delete();

        $etiqueta = $origen === CodigoBarrasDiseno::ORIGEN_FABRICA ? 'de Fábrica ' : '';

        return $this->aFicha($diseno, "Código de barras {$etiqueta}quitado.", $datos);
    }

    private function barcodeInput(Request $request, Diseno $diseno): array
    {
        $datos = $request->validate([
            'codigo_color' => ['required', 'string', 'max:60'],
            'talla' => ['required', Rule::in(array_keys(Diseno::TALLAS))],
            'codigo' => ['nullable', 'string', 'max:120', 'regex:/^[\x20-\x7E]+$/'],
        ], [
            'codigo.regex' => 'El código de barras solo admite caracteres ASCII imprimibles.',
        ]);

        $datos['codigo'] = trim((string) ($datos['codigo'] ?? ''));

        abort_unless(
            $diseno->coloresMaterial()->where('codigo', $datos['codigo_color'])->exists(),
            422,
            'Ese código de color no existe en este diseño.'
        );

        return $datos;
    }

    private function aFicha(Diseno $diseno, string $mensaje, ?array $codigoBarras = null): RedirectResponse
    {
        $parametros = ['diseno' => $diseno->id, 'tab' => 'ficha'];

        if ($codigoBarras) {
            $parametros['active'] = $codigoBarras['codigo_color'] . '__' . $codigoBarras['talla'];
        }

        return redirect()->route('disenos.index', $parametros)->with('status', $mensaje);
    }
}
