<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialNotaFicha extends ModeloBase
{
    protected $table = 'historial_notas_ficha';

    protected $fillable = ['diseno_id', 'usuario_id', 'autor', 'comentario', 'observado_en'];

    protected function casts(): array
    {
        return ['observado_en' => 'datetime'];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
