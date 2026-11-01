<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdeaDiseno extends ModeloBase
{
    protected $table = 'ideas_diseno';

    protected $fillable = [
        'diseno_id', 'tipo_prenda', 'temporada', 'paleta', 'tipo_tejido', 'descripcion',
        'codigo_hilado', 'titulo_hilado', 'composicion', 'nombre_modelo',
    ];

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
