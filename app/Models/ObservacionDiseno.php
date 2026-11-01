<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservacionDiseno extends ModeloBase
{
    protected $table = 'observaciones_diseno';

    protected $fillable = ['diseno_id', 'usuario_id', 'autor', 'rol', 'comentario', 'observado_en'];

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

    /** INT (fábrica) o EU */
    public function etiqueta(): string
    {
        if ($this->rol === Usuario::ROL_ADMIN) {
            return 'ADMIN';
        }

        return $this->rol === Usuario::ROL_EU ? 'EU' : 'INT';
    }
}
