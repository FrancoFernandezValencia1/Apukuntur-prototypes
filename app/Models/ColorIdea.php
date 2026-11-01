<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColorIdea extends ModeloBase
{
    protected $table = 'colores_idea';

    protected $fillable = ['diseno_id', 'color', 'tipo_tejido', 'composicion', 'orden'];

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
