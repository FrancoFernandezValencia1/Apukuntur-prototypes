<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ModeloBase extends Model
{
    public const CREATED_AT = 'creado_en';

    public const UPDATED_AT = 'actualizado_en';
}
