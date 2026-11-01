<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisenoPlasmado extends ModeloBase
{
    protected $table = 'disenos_plasmados';

    protected $fillable = ['diseno_id', 'material', 'hilado', 'titulo', 'galga', 'tallas', 'peso_estimado'];

    protected function casts(): array
    {
        return ['peso_estimado' => 'decimal:2'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
