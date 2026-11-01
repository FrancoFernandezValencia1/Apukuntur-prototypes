<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcabadoDiseno extends ModeloBase
{
    protected $table = 'acabados_diseno';

    protected $fillable = ['diseno_id', 'tipo_acabado', 'temperatura_vapor', 'minutos_proceso', 'con_vapor'];

    protected function casts(): array
    {
        return ['con_vapor' => 'boolean'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
