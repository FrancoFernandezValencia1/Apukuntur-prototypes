<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrototipoDiseno extends ModeloBase
{
    protected $table = 'prototipos_diseno';

    protected $fillable = ['diseno_id', 'peso_real', 'fecha_prototipo'];

    protected function casts(): array
    {
        return [
            'peso_real' => 'decimal:2',
            'fecha_prototipo' => 'date',
        ];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
