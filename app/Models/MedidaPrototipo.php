<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedidaPrototipo extends ModeloBase
{
    protected $table = 'medidas_prototipo';

    protected $fillable = ['diseno_id', 'nombre', 's', 'm', 'l', 'xl', 'orden'];

    protected function casts(): array
    {
        return [
            's' => 'decimal:2',
            'm' => 'decimal:2',
            'l' => 'decimal:2',
            'xl' => 'decimal:2',
        ];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
