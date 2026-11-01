<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaDiseno extends ModeloBase
{
    protected $table = 'notas_diseno';

    protected $fillable = ['diseno_id', 'seccion', 'contenido'];

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
