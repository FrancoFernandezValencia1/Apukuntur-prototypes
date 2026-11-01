<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemalladoDiseno extends ModeloBase
{
    protected $table = 'remallados_diseno';

    protected $fillable = ['diseno_id', 'tipo_remallado', 'operario', 'fecha_remallado'];

    protected function casts(): array
    {
        return ['fecha_remallado' => 'date'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
