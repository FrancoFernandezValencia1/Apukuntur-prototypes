<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedidaFicha extends ModeloBase
{
    protected $table = 'medidas_ficha';

    protected $fillable = [
        'diseno_id', 'nombre', 'incremento', 's', 'm', 'l', 'xl', 'xxl', 'estandar', 'orden',
    ];

    protected function casts(): array
    {
        return [
            'incremento' => 'decimal:2',
            's' => 'decimal:2',
            'm' => 'decimal:2',
            'l' => 'decimal:2',
            'xl' => 'decimal:2',
            'xxl' => 'decimal:2',
            'estandar' => 'decimal:2',
        ];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
