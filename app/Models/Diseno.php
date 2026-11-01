<?php

namespace App\Models;

use App\Support\ImagenCodigoBarras;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Diseno extends ModeloBase
{
    public const ESTADOS = [
        'en_desarrollo' => 'En desarrollo',
        'en_prototipo' => 'En prototipo',
        'aprobado' => 'Aprobado por EU',
        'rechazado' => 'Rechazado por EU',
    ];

    /** clave => etiqueta */
    public const TALLAS = ['s' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL'];

    public const SECCIONES_FOTO = ['idea', 'diseno', 'final', 'remallado', 'tejido', 'acabado', 'medidas', 'ficha'];

    public const SECCIONES_NOTA = ['diseno', 'general', 'correcciones', 'remallado', 'tejido', 'acabado'];

    protected $table = 'disenos';

    protected $fillable = [
        'codigo', 'nombre', 'responsable', 'fecha_inicio', 'version',
        'estado_aprobacion', 'aprobado_para_produccion', 'ruta_miniatura',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'version' => 'integer',
            'aprobado_para_produccion' => 'boolean',
        ];
    }

    public function idea(): HasOne
    {
        return $this->hasOne(IdeaDiseno::class);
    }

    public function plasmado(): HasOne
    {
        return $this->hasOne(DisenoPlasmado::class);
    }

    public function prototipo(): HasOne
    {
        return $this->hasOne(PrototipoDiseno::class);
    }

    public function remallado(): HasOne
    {
        return $this->hasOne(RemalladoDiseno::class);
    }

    public function tejido(): HasOne
    {
        return $this->hasOne(TejidoDiseno::class);
    }

    public function acabado(): HasOne
    {
        return $this->hasOne(AcabadoDiseno::class);
    }

    public function ficha(): HasOne
    {
        return $this->hasOne(FichaTecnica::class);
    }

    public function coloresIdea(): HasMany
    {
        return $this->hasMany(ColorIdea::class)->orderBy('orden');
    }

    public function coloresMaterial(): HasMany
    {
        return $this->hasMany(ColorMaterial::class)->orderBy('orden');
    }

    public function medidasPrototipo(): HasMany
    {
        return $this->hasMany(MedidaPrototipo::class)->orderBy('orden');
    }

    public function medidasFicha(): HasMany
    {
        return $this->hasMany(MedidaFicha::class)->orderBy('orden');
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(InsumoFicha::class)->orderBy('orden');
    }

    public function notas(): HasMany
    {
        return $this->hasMany(NotaDiseno::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoDiseno::class)->orderBy('orden');
    }

    public function observaciones(): HasMany
    {
        return $this->hasMany(ObservacionDiseno::class)->orderByDesc('observado_en');
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(RevisionDiseno::class)->orderByDesc('revisado_en');
    }

    public function codigosBarras(): HasMany
    {
        return $this->hasMany(CodigoBarrasDiseno::class);
    }

    public function historialNotas(): HasMany
    {
        return $this->hasMany(HistorialNotaFicha::class)->orderByDesc('observado_en');
    }

    public function nota(string $seccion): ?string
    {
        return $this->notas->firstWhere('seccion', $seccion)?->contenido;
    }

    public function fotosSeccion(string $seccion): array
    {
        return $this->fotos
            ->where('seccion', $seccion)
            ->whereNull('color')
            ->sortBy('orden')
            ->pluck('ruta')
            ->values()
            ->all();
    }

    public function fotoFichaColor(string $color): ?string
    {
        return $this->fotos
            ->first(fn (FotoDiseno $foto) => $foto->seccion === 'ficha' && $foto->color === $color)
            ?->ruta;
    }

    /** Matriz "Códigos por talla y color" con el código de barras si ya fue asignado. */
    public function codigosTallaColor(string $origen = CodigoBarrasDiseno::ORIGEN_EU): array
    {
        $asignados = $this->codigosBarras
            ->where('origen', $origen)
            ->keyBy(fn (CodigoBarrasDiseno $codigo) => $codigo->codigo_color . '|' . $codigo->talla);

        return $this->coloresMaterial
            ->filter(fn (ColorMaterial $color) => filled($color->codigo))
            ->map(function (ColorMaterial $color) use ($asignados) {
                $celdas = [];
                foreach (self::TALLAS as $clave => $etiqueta) {
                    $codigoBarras = $asignados->get($color->codigo . '|' . $clave);
                    $celdas[$clave] = [
                        'etiqueta' => $etiqueta,
                        'codigo' => "{$this->codigo}-{$color->codigo}-{$etiqueta}",
                        'codigo_barras' => $codigoBarras?->codigo,
                        'barcode' => $codigoBarras ? ImagenCodigoBarras::dataUri($codigoBarras->codigo) : null,
                    ];
                }

                return [
                    'color' => $color->color,
                    'hex' => $color->hex ?: '#d1d5db',
                    'codigo' => $color->codigo,
                    'celdas' => $celdas,
                ];
            })
            ->values()
            ->all();
    }

    protected function urlMiniatura(): Attribute
    {
        return Attribute::get(fn () => $this->ruta_miniatura
            ? Storage::disk('public')->url($this->ruta_miniatura)
            : null);
    }

    protected function etiquetaEstado(): Attribute
    {
        return Attribute::get(fn () => self::ESTADOS[$this->estado_aprobacion] ?? $this->estado_aprobacion);
    }
}
