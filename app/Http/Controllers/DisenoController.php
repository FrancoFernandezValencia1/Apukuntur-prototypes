<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisenoRequest;
use App\Models\Diseno;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DisenoController extends Controller
{
    private const RELACIONES = [
        'idea', 'plasmado', 'prototipo', 'remallado', 'tejido', 'acabado', 'ficha',
        'coloresIdea', 'coloresMaterial', 'medidasPrototipo', 'medidasFicha', 'insumos',
        'notas', 'fotos', 'observaciones', 'revisiones', 'codigosBarras', 'historialNotas',
    ];

    public function index(Request $request): View
    {
        $disenos = Diseno::orderBy('codigo')->get();

        if ($request->boolean('new') && ($request->user()->isFactory() || $request->user()->isAdmin())) {
            $diseno = $this->blank();
        } elseif ($request->filled('diseno')) {
            $diseno = Diseno::findOrFail($request->integer('diseno'));
        } else {
            $diseno = $disenos->first() ?? $this->blank();
        }

        if ($diseno->exists) {
            $diseno->load(self::RELACIONES);
        }

        return view('disenos.index', [
            'disenos' => $disenos,
            'diseno' => $diseno,
            'estados' => Diseno::ESTADOS,
            'tallas' => Diseno::TALLAS,
        ]);
    }

    public function store(DisenoRequest $request): RedirectResponse
    {
        $diseno = $this->persist(new Diseno(), $request);

        return $this->back($diseno, $request, 'Diseño creado correctamente.');
    }

    public function update(DisenoRequest $request, Diseno $diseno): RedirectResponse
    {
        $this->persist($diseno, $request);

        return $this->back($diseno, $request, 'Cambios guardados.');
    }

    public function destroy(Diseno $diseno): RedirectResponse
    {
        $rutas = $diseno->fotos->pluck('ruta')->push($diseno->ruta_miniatura)->filter()->all();
        Storage::disk('public')->delete($rutas);

        $diseno->delete();

        return redirect()->route('disenos.index')->with('status', 'Diseño eliminado.');
    }

    private function blank(): Diseno
    {
        $diseno = new Diseno(['version' => 1, 'estado_aprobacion' => 'en_desarrollo']);

        $diseno->setRelation('idea', new \App\Models\IdeaDiseno());
        $diseno->setRelation('plasmado', new \App\Models\DisenoPlasmado());
        $diseno->setRelation('prototipo', new \App\Models\PrototipoDiseno());
        $diseno->setRelation('remallado', new \App\Models\RemalladoDiseno());
        $diseno->setRelation('tejido', new \App\Models\TejidoDiseno());
        $diseno->setRelation('acabado', new \App\Models\AcabadoDiseno());
        $diseno->setRelation('ficha', new \App\Models\FichaTecnica());

        foreach (['coloresIdea', 'coloresMaterial', 'medidasPrototipo', 'medidasFicha', 'insumos', 'notas', 'fotos', 'observaciones', 'revisiones', 'codigosBarras', 'historialNotas'] as $relacion) {
            $diseno->setRelation($relacion, new EloquentCollection());
        }

        return $diseno;
    }

    private function back(Diseno $diseno, Request $request, string $mensaje): RedirectResponse
    {
        return redirect()
            ->route('disenos.index', [
                'diseno' => $diseno->id,
                'tab' => $request->input('_tab', 'idea'),
                'sub' => $request->input('_sub', 'remallado'),
            ])
            ->with('status', $mensaje);
    }

    private function persist(Diseno $diseno, DisenoRequest $request): Diseno
    {
        $datos = $request->validated();
        $notaFichaAnterior = trim((string) ($diseno->exists ? $diseno->ficha?->observaciones : ''));

        $datosDiseno = Arr::only($datos, ['codigo', 'nombre', 'responsable', 'fecha_inicio', 'version']);

        if (! $diseno->exists) {
            $datosDiseno['estado_aprobacion'] = 'en_desarrollo';
        }

        if ($request->hasFile('miniatura')) {
            if ($diseno->ruta_miniatura) {
                Storage::disk('public')->delete($diseno->ruta_miniatura);
            }
            $datosDiseno['ruta_miniatura'] = $request->file('miniatura')->store('disenos/miniaturas', 'public');
        }

        DB::transaction(function () use ($diseno, $datos, $datosDiseno, $notaFichaAnterior, $request) {
            $diseno->fill($datosDiseno)->save();

            $diseno->idea()->updateOrCreate([], $datos['idea'] ?? []);
            $diseno->plasmado()->updateOrCreate([], $datos['plasmado'] ?? []);
            $diseno->prototipo()->updateOrCreate([], $datos['prototipo'] ?? []);
            $diseno->remallado()->updateOrCreate([], $datos['remallado'] ?? []);
            $diseno->tejido()->updateOrCreate([], $datos['tejido'] ?? []);
            $diseno->acabado()->updateOrCreate([], $datos['acabado'] ?? []);

            $datosFicha = $datos['ficha'] ?? [];
            $datosFicha['actualizado_tecnica_en'] = now();
            $datosFicha['modificado_por'] = $request->user()->nombre;

            $ficha = $diseno->ficha()->updateOrCreate([], $datosFicha);
            $notaFichaNueva = trim((string) ($ficha->observaciones ?? ''));

            if ($notaFichaNueva !== '' && $notaFichaNueva !== $notaFichaAnterior) {
                $diseno->historialNotas()->create([
                    'usuario_id' => $request->user()->id,
                    'autor' => $request->user()->nombre,
                    'comentario' => $notaFichaNueva,
                    'observado_en' => now(),
                ]);
            }

            $this->sincronizarColoresIdea($diseno, $datos['colores_idea'] ?? []);
            $this->sincronizarColoresMaterial($diseno, $datos['colores_material'] ?? []);
            $this->sincronizarMedidasPrototipo($diseno, $datos['medidas_prototipo'] ?? []);
            $this->sincronizarMedidasFicha($diseno, $datos['medidas_ficha'] ?? []);
            $this->sincronizarInsumos($diseno, $datos['insumos'] ?? []);
            $this->sincronizarNotas($diseno, $datos['notas'] ?? []);
            $this->guardarFotos($diseno, $request);
        });

        return $diseno;
    }

    private function sincronizarColoresIdea(Diseno $diseno, array $filas): void
    {
        $diseno->coloresIdea()->delete();

        $registros = [];
        foreach ($this->limpiarFilas($filas) as $orden => $fila) {
            $registros[] = Arr::only($fila, ['color', 'tipo_tejido', 'composicion']) + ['orden' => $orden];
        }

        if ($registros) {
            $diseno->coloresIdea()->createMany($registros);
        }
    }

    private function sincronizarColoresMaterial(Diseno $diseno, array $filas): void
    {
        $diseno->coloresMaterial()->delete();

        $registros = [];
        foreach ($this->limpiarFilas($filas) as $orden => $fila) {
            $registros[] = Arr::only($fila, ['color', 'hex', 'codigo', 'empresa', 'cantidad_pedida']) + ['orden' => $orden];
        }

        if ($registros) {
            $diseno->coloresMaterial()->createMany($registros);
        }
    }

    private function sincronizarMedidasPrototipo(Diseno $diseno, array $filas): void
    {
        $diseno->medidasPrototipo()->delete();

        $registros = [];
        foreach ($this->limpiarFilas($filas) as $orden => $fila) {
            if (blank($fila['nombre'] ?? null)) {
                continue;
            }
            $registros[] = Arr::only($fila, ['nombre', 's', 'm', 'l', 'xl']) + ['orden' => $orden];
        }

        if ($registros) {
            $diseno->medidasPrototipo()->createMany($registros);
        }
    }

    private function sincronizarMedidasFicha(Diseno $diseno, array $filas): void
    {
        $diseno->medidasFicha()->delete();

        $registros = [];
        foreach ($this->limpiarFilas($filas) as $orden => $fila) {
            if (blank($fila['nombre'] ?? null)) {
                continue;
            }
            $registros[] = Arr::only($fila, ['nombre', 'incremento', 's', 'm', 'l', 'xl', 'xxl', 'estandar']) + ['orden' => $orden];
        }

        if ($registros) {
            $diseno->medidasFicha()->createMany($registros);
        }
    }

    private function sincronizarInsumos(Diseno $diseno, array $filas): void
    {
        $diseno->insumos()->delete();

        $registros = [];
        foreach ($this->limpiarFilas($filas) as $orden => $fila) {
            if (blank($fila['nombre'] ?? null)) {
                continue;
            }
            $registros[] = Arr::only($fila, ['nombre', 'cantidad', 'codigo']) + ['orden' => $orden];
        }

        if ($registros) {
            $diseno->insumos()->createMany($registros);
        }
    }

    private function sincronizarNotas(Diseno $diseno, array $notas): void
    {
        foreach (Diseno::SECCIONES_NOTA as $seccion) {
            $contenido = trim((string) ($notas[$seccion] ?? ''));

            if ($contenido === '') {
                $diseno->notas()->where('seccion', $seccion)->delete();
                continue;
            }

            $diseno->notas()->updateOrCreate(
                ['seccion' => $seccion],
                ['contenido' => $contenido],
            );
        }
    }

    private function guardarFotos(Diseno $diseno, Request $request): void
    {
        $archivos = $request->file('fotos', []);

        foreach (Diseno::SECCIONES_FOTO as $seccion) {
            $orden = (int) $diseno->fotos()->where('seccion', $seccion)->whereNull('color')->max('orden');

            foreach ((array) ($archivos[$seccion] ?? []) as $archivo) {
                if (! $archivo instanceof UploadedFile) {
                    continue;
                }

                $diseno->fotos()->create([
                    'seccion' => $seccion,
                    'color' => null,
                    'ruta' => $archivo->store("disenos/fotos/{$seccion}", 'public'),
                    'orden' => ++$orden,
                ]);
            }
        }

        foreach ((array) ($archivos['ficha_colores'] ?? []) as $color => $archivo) {
            if (! $archivo instanceof UploadedFile) {
                continue;
            }

            $anterior = $diseno->fotos()->where('seccion', 'ficha')->where('color', $color)->first();

            if ($anterior) {
                Storage::disk('public')->delete($anterior->ruta);
                $anterior->delete();
            }

            $diseno->fotos()->create([
                'seccion' => 'ficha',
                'color' => $color,
                'ruta' => $archivo->store('disenos/fotos/ficha', 'public'),
                'orden' => 0,
            ]);
        }
    }

    /** Descarta filas vacías; el color por defecto "hex" no cuenta como dato. */
    private function limpiarFilas(array $filas): array
    {
        return array_values(array_filter(
            $filas,
            fn ($fila) => is_array($fila) && collect($fila)->except(['hex'])->contains(fn ($valor) => filled($valor))
        ));
    }
}
