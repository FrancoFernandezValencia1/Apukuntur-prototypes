<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoBarrasDiseno extends ModeloBase
{
    public const ORIGEN_EU = 'eu';
    public const ORIGEN_FABRICA = 'fabrica';

    protected $table = 'codigos_barras_diseno';

    protected $fillable = [
        'diseno_id', 'codigo_color', 'talla', 'origen', 'codigo', 'asignado_por', 'asignado_en',
    ];

    protected function casts(): array
    {
        return ['asignado_en' => 'datetime'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'asignado_por');
    }
}
