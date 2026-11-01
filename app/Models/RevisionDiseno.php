<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionDiseno extends ModeloBase
{
    public const APROBADA = 'aprobada';
    public const OBSERVADA = 'observada';

    public const ESTADOS = [
        self::APROBADA => 'Revisión aprobada',
        self::OBSERVADA => 'Revisión con observaciones',
    ];

    protected $table = 'revisiones_diseno';

    protected $fillable = ['diseno_id', 'usuario_id', 'revisor', 'estado', 'comentario', 'revisado_en'];

    protected function casts(): array
    {
        return ['revisado_en' => 'datetime'];
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
