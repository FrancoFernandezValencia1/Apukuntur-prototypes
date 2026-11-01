<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoDiseno extends ModeloBase
{
    protected $table = 'fotos_diseno';

    protected $fillable = ['diseno_id', 'seccion', 'color', 'ruta', 'orden'];

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
