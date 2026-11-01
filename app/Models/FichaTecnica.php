<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichaTecnica extends ModeloBase
{
    protected $table = 'fichas_tecnicas';

    protected $fillable = [
        'diseno_id', 'es_muestra', 'modelo', 'codigo_modelo', 'tipo_tejido', 'tipo_maquina',
        'tension', 'galga', 'punto', 'nombre_maquina', 'composicion', 'titulo_hilado',
        'color_principal', 'observaciones', 'fecha_impresion', 'actualizado_tecnica_en', 'modificado_por',
    ];

    protected function casts(): array
    {
        return [
            'es_muestra' => 'boolean',
            'tension' => 'decimal:2',
            'galga' => 'decimal:2',
            'fecha_impresion' => 'date',
            'actualizado_tecnica_en' => 'date',
        ];
    }

    public function diseno(): BelongsTo
    {
        return $this->belongsTo(Diseno::class);
    }
}
