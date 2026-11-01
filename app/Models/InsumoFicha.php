<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsumoFicha extends ModeloBase
{
    protected $table = 'insumos_ficha';

    protected $fillable = ['diseno_id', 'nombre', 'cantidad', 'codigo', 'orden'];

    protected function casts(): array
    {
        return ['cantidad' => 'decimal:2'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
