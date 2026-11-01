<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasTable('usuarios')) {
            DB::table('users')->orderBy('id')->get()->each(function ($usuario) {
                DB::table('usuarios')->insert([
                    'id' => $usuario->id,
                    'nombre' => $usuario->name,
                    'correo' => $usuario->email,
                    'correo_verificado_en' => $usuario->email_verified_at,
                    'contrasena' => $usuario->password,
                    'rol' => $usuario->role ?? 'fabrica',
                    'recordar_token' => $usuario->remember_token,
                    'creado_en' => $usuario->created_at,
                    'actualizado_en' => $usuario->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('designs') && Schema::hasTable('disenos')) {
            DB::table('designs')->orderBy('id')->get()->each(function ($diseno) {
                $notas = $this->json($diseno->notes);
                $fotos = $this->json($diseno->photos);
                $insumos = $this->json($diseno->supplies);

                DB::table('disenos')->insert([
                    'id' => $diseno->id,
                    'codigo' => $diseno->code,
                    'nombre' => $diseno->name,
                    'responsable' => $diseno->responsible,
                    'fecha_inicio' => $diseno->start_date,
                    'version' => $diseno->version,
                    'estado_aprobacion' => $diseno->approval_status,
                    'aprobado_para_produccion' => (bool) $diseno->is_approved,
                    'ruta_miniatura' => $diseno->thumbnail_path,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('ideas_diseno')->insert([
                    'diseno_id' => $diseno->id,
                    'tipo_prenda' => $diseno->garment_type,
                    'temporada' => $diseno->season,
                    'paleta' => $diseno->palette,
                    'tipo_tejido' => $diseno->knit_type,
                    'descripcion' => $diseno->idea_description,
                    'codigo_hilado' => $diseno->yarn_code,
                    'titulo_hilado' => $diseno->yarn_title,
                    'composicion' => $diseno->composition,
                    'nombre_modelo' => $diseno->model_name,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('disenos_plasmados')->insert([
                    'diseno_id' => $diseno->id,
                    'material' => $diseno->s2_material,
                    'hilado' => $diseno->s2_yarn,
                    'titulo' => $diseno->s2_title,
                    'galga' => $diseno->s2_gauge,
                    'tallas' => $diseno->s2_sizes,
                    'peso_estimado' => $diseno->s2_estimated_weight,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('prototipos_diseno')->insert([
                    'diseno_id' => $diseno->id,
                    'peso_real' => $diseno->real_weight,
                    'fecha_prototipo' => $diseno->prototype_date,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('remallados_diseno')->insert([
                    'diseno_id' => $diseno->id,
                    'tipo_remallado' => $diseno->seam_type,
                    'operario' => $diseno->seam_operator,
                    'fecha_remallado' => $diseno->seam_date,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('tejidos_diseno')->insert([
                    'diseno_id' => $diseno->id,
                    'tipo_tejido' => $diseno->knit_proto_type,
                    'galga' => $diseno->knit_gauge,
                    'tension' => $diseno->knit_tension,
                    'peso_por_metro' => $diseno->weight_per_meter,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('acabados_diseno')->insert([
                    'diseno_id' => $diseno->id,
                    'tipo_acabado' => $diseno->finish_type,
                    'temperatura_vapor' => $diseno->steam_temperature,
                    'minutos_proceso' => $diseno->process_minutes,
                    'con_vapor' => (bool) $diseno->with_steam,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                DB::table('fichas_tecnicas')->insert([
                    'diseno_id' => $diseno->id,
                    'es_muestra' => (bool) $diseno->is_sample,
                    'modelo' => $diseno->tech_model,
                    'codigo_modelo' => $diseno->tech_model_code,
                    'tipo_tejido' => $diseno->tech_knit_type,
                    'tipo_maquina' => $diseno->tech_machine_type,
                    'tension' => $diseno->tech_tension,
                    'galga' => $diseno->tech_gauge,
                    'punto' => $diseno->tech_stitch,
                    'nombre_maquina' => $diseno->tech_machine_name,
                    'composicion' => $diseno->tech_composition,
                    'titulo_hilado' => $diseno->tech_yarn_title,
                    'color_principal' => $diseno->tech_color,
                    'observaciones' => $notas['tech'] ?? null,
                    'fecha_impresion' => $diseno->print_date,
                    'actualizado_tecnica_en' => $diseno->tech_updated_at,
                    'modificado_por' => $diseno->modified_by,
                    'creado_en' => $diseno->created_at,
                    'actualizado_en' => $diseno->updated_at,
                ]);

                foreach ([
                    'design' => 'diseno',
                    'general' => 'general',
                    'corrections' => 'correcciones',
                    'remallado' => 'remallado',
                    'tejido' => 'tejido',
                    'acabado' => 'acabado',
                ] as $origen => $seccion) {
                    if (filled($notas[$origen] ?? null)) {
                        DB::table('notas_diseno')->insert([
                            'diseno_id' => $diseno->id,
                            'seccion' => $seccion,
                            'contenido' => $notas[$origen],
                            'creado_en' => $diseno->created_at,
                            'actualizado_en' => $diseno->updated_at,
                        ]);
                    }
                }

                foreach ((array) $insumos as $orden => $insumo) {
                    DB::table('insumos_ficha')->insert([
                        'diseno_id' => $diseno->id,
                        'nombre' => $insumo['name'] ?? null,
                        'cantidad' => $insumo['quantity'] ?? null,
                        'codigo' => $insumo['code'] ?? null,
                        'orden' => $orden,
                        'creado_en' => $diseno->created_at,
                        'actualizado_en' => $diseno->updated_at,
                    ]);
                }

                foreach ([
                    'idea' => 'idea',
                    'design' => 'diseno',
                    'final' => 'final',
                    'remallado' => 'remallado',
                    'tejido' => 'tejido',
                    'acabado' => 'acabado',
                    'medidas' => 'medidas',
                    'tech' => 'ficha',
                ] as $origen => $seccion) {
                    foreach ((array) ($fotos[$origen] ?? []) as $orden => $ruta) {
                        if (blank($ruta)) {
                            continue;
                        }

                        DB::table('fotos_diseno')->insert([
                            'diseno_id' => $diseno->id,
                            'seccion' => $seccion,
                            'color' => null,
                            'ruta' => $ruta,
                            'orden' => $orden,
                            'creado_en' => $diseno->created_at,
                            'actualizado_en' => $diseno->updated_at,
                        ]);
                    }
                }

                foreach ((array) ($fotos['tech_colors'] ?? []) as $color => $ruta) {
                    if (blank($ruta)) {
                        continue;
                    }

                    DB::table('fotos_diseno')->insert([
                        'diseno_id' => $diseno->id,
                        'seccion' => 'ficha',
                        'color' => $color,
                        'ruta' => $ruta,
                        'orden' => 0,
                        'creado_en' => $diseno->created_at,
                        'actualizado_en' => $diseno->updated_at,
                    ]);
                }
            });
        }

        if (Schema::hasTable('design_colors')) {
            DB::table('design_colors')->orderBy('id')->get()->each(function ($color) {
                if ($color->context === 'idea') {
                    DB::table('colores_idea')->insert([
                        'diseno_id' => $color->design_id,
                        'color' => $color->color,
                        'tipo_tejido' => $color->knit_type,
                        'composicion' => $color->composition,
                        'orden' => $color->position,
                        'creado_en' => $color->created_at,
                        'actualizado_en' => $color->updated_at,
                    ]);

                    return;
                }

                DB::table('colores_material')->insert([
                    'diseno_id' => $color->design_id,
                    'color' => $color->color,
                    'hex' => $color->hex,
                    'codigo' => $color->code,
                    'empresa' => $color->company,
                    'cantidad_pedida' => $color->ordered_qty,
                    'orden' => $color->position,
                    'creado_en' => $color->created_at,
                    'actualizado_en' => $color->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('design_measurements')) {
            DB::table('design_measurements')->orderBy('id')->get()->each(function ($medida) {
                if ($medida->context === 'prototype') {
                    DB::table('medidas_prototipo')->insert([
                        'diseno_id' => $medida->design_id,
                        'nombre' => $medida->name,
                        's' => $medida->s,
                        'm' => $medida->m,
                        'l' => $medida->l,
                        'xl' => $medida->xl,
                        'orden' => $medida->position,
                        'creado_en' => $medida->created_at,
                        'actualizado_en' => $medida->updated_at,
                    ]);

                    return;
                }

                DB::table('medidas_ficha')->insert([
                    'diseno_id' => $medida->design_id,
                    'nombre' => $medida->name,
                    'incremento' => $medida->inc,
                    's' => $medida->s,
                    'm' => $medida->m,
                    'l' => $medida->l,
                    'xl' => $medida->xl,
                    'xxl' => $medida->xxl,
                    'estandar' => $medida->std,
                    'orden' => $medida->position,
                    'creado_en' => $medida->created_at,
                    'actualizado_en' => $medida->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('design_observations')) {
            DB::table('design_observations')->orderBy('id')->get()->each(function ($observacion) {
                DB::table('observaciones_diseno')->insert([
                    'diseno_id' => $observacion->design_id,
                    'usuario_id' => $observacion->user_id,
                    'autor' => $observacion->author,
                    'rol' => $observacion->role,
                    'comentario' => $observacion->comment,
                    'observado_en' => $observacion->observed_at,
                    'creado_en' => $observacion->created_at,
                    'actualizado_en' => $observacion->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('design_reviews')) {
            DB::table('design_reviews')->orderBy('id')->get()->each(function ($revision) {
                DB::table('revisiones_diseno')->insert([
                    'diseno_id' => $revision->design_id,
                    'usuario_id' => $revision->user_id,
                    'revisor' => $revision->reviewer,
                    'estado' => $revision->status === 'approved' ? 'aprobada' : 'observada',
                    'comentario' => $revision->comment,
                    'revisado_en' => $revision->reviewed_at,
                    'creado_en' => $revision->created_at,
                    'actualizado_en' => $revision->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('design_barcodes')) {
            DB::table('design_barcodes')->orderBy('id')->get()->each(function ($codigo) {
                DB::table('codigos_barras_diseno')->insert([
                    'diseno_id' => $codigo->design_id,
                    'codigo_color' => $codigo->color_code,
                    'talla' => $codigo->size,
                    'origen' => $codigo->origin,
                    'codigo' => $codigo->code,
                    'asignado_por' => $codigo->assigned_by,
                    'asignado_en' => $codigo->assigned_at,
                    'creado_en' => $codigo->created_at,
                    'actualizado_en' => $codigo->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('design_tech_notes')) {
            DB::table('design_tech_notes')->orderBy('id')->get()->each(function ($nota) {
                DB::table('historial_notas_ficha')->insert([
                    'diseno_id' => $nota->design_id,
                    'usuario_id' => $nota->user_id,
                    'autor' => $nota->author,
                    'comentario' => $nota->comment,
                    'observado_en' => $nota->observed_at,
                    'creado_en' => $nota->created_at,
                    'actualizado_en' => $nota->updated_at,
                ]);
            });
        }

        foreach ([
            'design_tech_notes',
            'design_barcodes',
            'design_reviews',
            'design_observations',
            'design_measurements',
            'design_colors',
            'designs',
            'users',
        ] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    public function down(): void
    {
        // La migración es irreversible sin el respaldo previo de la base de datos.
    }

    private function json($valor): array
    {
        if (is_array($valor)) {
            return $valor;
        }

        if (blank($valor)) {
            return [];
        }

        $decodificado = json_decode($valor, true);

        return is_array($decodificado) ? $decodificado : [];
    }
};
