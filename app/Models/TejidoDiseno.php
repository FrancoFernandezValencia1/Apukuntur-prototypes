<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TejidoDiseno extends ModeloBase
{
    protected $table = 'tejidos_diseno';

    protected $fillable = ['diseno_id', 'tipo_tejido', 'galga', 'tension', 'peso_por_metro'];

    protected function casts(): array
    {
        return [
            'galga' => 'decimal:2',
            'tension' => 'decimal:2',
            'peso_por_metro' => 'decimal:2',
        ];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
