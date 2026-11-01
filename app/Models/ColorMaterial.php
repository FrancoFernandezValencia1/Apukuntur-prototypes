<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColorMaterial extends ModeloBase
{
    protected $table = 'colores_material';

    protected $fillable = [
        'diseno_id', 'color', 'hex', 'codigo', 'empresa', 'cantidad_pedida', 'orden',
    ];

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
