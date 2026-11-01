<?php

namespace App\Http\Requests;

use App\Models\Diseno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisenoRequest extends FormRequest
{
    /** Solo el perfil Fábrica crea y edita los datos del diseño. */
    public function authorize(): bool
    {
        $usuario = $this->user();

        return $usuario && ($usuario->isFactory() || $usuario->isAdmin());
    }

    public function rules(): array
    {
        $texto = ['nullable', 'string', 'max:255'];
        $largo = ['nullable', 'string', 'max:2000'];
        $numero = ['nullable', 'numeric', 'min:0', 'max:99999'];
        $hex = ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'];

        $reglas = [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('disenos', 'codigo')->ignore($this->route('diseno'))],
            'nombre' => ['required', 'string', 'max:255'],
            'responsable' => $texto,
            'fecha_inicio' => ['nullable', 'date'],
            'version' => ['required', 'integer', 'min:1', 'max:999'],
            'miniatura' => ['nullable', 'image', 'max:4096'],

            'idea' => ['nullable', 'array'],
            'idea.tipo_prenda' => $texto,
            'idea.temporada' => $texto,
            'idea.paleta' => $texto,
            'idea.tipo_tejido' => $texto,
            'idea.descripcion' => $largo,
            'idea.codigo_hilado' => $texto,
            'idea.titulo_hilado' => $texto,
            'idea.composicion' => $texto,
            'idea.nombre_modelo' => $texto,

            'plasmado' => ['nullable', 'array'],
            'plasmado.material' => $texto,
            'plasmado.hilado' => $texto,
            'plasmado.titulo' => $texto,
            'plasmado.galga' => $texto,
            'plasmado.tallas' => $texto,
            'plasmado.peso_estimado' => $numero,

            'prototipo' => ['nullable', 'array'],
            'prototipo.peso_real' => $numero,
            'prototipo.fecha_prototipo' => ['nullable', 'date'],

            'remallado' => ['nullable', 'array'],
            'remallado.tipo_remallado' => $texto,
            'remallado.operario' => $texto,
            'remallado.fecha_remallado' => ['nullable', 'date'],

            'tejido' => ['nullable', 'array'],
            'tejido.tipo_tejido' => $texto,
            'tejido.galga' => $numero,
            'tejido.tension' => $numero,
            'tejido.peso_por_metro' => $numero,

            'acabado' => ['nullable', 'array'],
            'acabado.tipo_acabado' => $texto,
            'acabado.temperatura_vapor' => ['nullable', 'integer', 'min:0', 'max:300'],
            'acabado.minutos_proceso' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'acabado.con_vapor' => ['nullable', 'boolean'],

            'ficha' => ['nullable', 'array'],
            'ficha.es_muestra' => ['nullable', 'boolean'],
            'ficha.modelo' => $texto,
            'ficha.codigo_modelo' => $texto,
            'ficha.tipo_tejido' => $texto,
            'ficha.tipo_maquina' => $texto,
            'ficha.tension' => $numero,
            'ficha.galga' => $numero,
            'ficha.punto' => $texto,
            'ficha.nombre_maquina' => $texto,
            'ficha.composicion' => $texto,
            'ficha.titulo_hilado' => $texto,
            'ficha.color_principal' => $texto,
            'ficha.observaciones' => $largo,
            'ficha.fecha_impresion' => ['nullable', 'date'],

            'notas' => ['nullable', 'array'],
            'fotos' => ['nullable', 'array'],
            'fotos.*' => ['array'],
            'fotos.*.*' => ['image', 'max:5120'],
        ];

        foreach (Diseno::SECCIONES_NOTA as $seccion) {
            $reglas["notas.$seccion"] = $largo;
        }

        $reglas['colores_idea'] = ['nullable', 'array'];
        $reglas['colores_idea.*.color'] = $texto;
        $reglas['colores_idea.*.tipo_tejido'] = $texto;
        $reglas['colores_idea.*.composicion'] = $texto;

        $reglas['colores_material'] = ['nullable', 'array'];
        $reglas['colores_material.*.color'] = $texto;
        $reglas['colores_material.*.hex'] = $hex;
        $reglas['colores_material.*.codigo'] = $texto;
        $reglas['colores_material.*.empresa'] = $texto;
        $reglas['colores_material.*.cantidad_pedida'] = ['nullable', 'integer', 'min:0', 'max:999999'];

        $reglas['insumos'] = ['nullable', 'array'];
        $reglas['insumos.*.nombre'] = $texto;
        $reglas['insumos.*.cantidad'] = $numero;
        $reglas['insumos.*.codigo'] = $texto;

        $reglas['medidas_prototipo'] = ['nullable', 'array'];
        $reglas['medidas_prototipo.*.nombre'] = $texto;
        foreach (['s', 'm', 'l', 'xl'] as $talla) {
            $reglas["medidas_prototipo.*.$talla"] = $numero;
        }

        $reglas['medidas_ficha'] = ['nullable', 'array'];
        $reglas['medidas_ficha.*.nombre'] = $texto;
        foreach (['incremento', 's', 'm', 'l', 'xl', 'xxl', 'estandar'] as $campo) {
            $reglas["medidas_ficha.*.$campo"] = $numero;
        }

        return $reglas;
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre del diseño',
            'responsable' => 'responsable',
            'fecha_inicio' => 'fecha de inicio',
            'version' => 'versión',
            'miniatura' => 'miniatura',
            'prototipo.peso_real' => 'peso real',
            'prototipo.fecha_prototipo' => 'fecha del prototipo',
            'ficha.fecha_impresion' => 'fecha de impresión',
            'ficha.actualizado_tecnica_en' => 'fecha de actualización',
            'ficha.modificado_por' => 'modificado por',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe un diseño con ese código.',
            '*.image' => 'El archivo debe ser una imagen.',
            'colores_material.*.hex.regex' => 'El color debe tener formato #RRGGBB.',
        ];
    }
}
