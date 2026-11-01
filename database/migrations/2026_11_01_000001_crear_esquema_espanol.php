<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->timestamp('correo_verificado_en')->nullable();
            $table->string('contrasena');
            $table->string('rol', 20)->default('fabrica')->index();
            $table->string('recordar_token', 100)->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('disenos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre');
            $table->string('responsable')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('estado_aprobacion', 30)->default('en_desarrollo')->index();
            $table->boolean('aprobado_para_produccion')->default(false);
            $table->string('ruta_miniatura')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('ideas_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->string('tipo_prenda')->nullable();
            $table->string('temporada')->nullable();
            $table->string('paleta')->nullable();
            $table->string('tipo_tejido')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('codigo_hilado')->nullable();
            $table->string('titulo_hilado')->nullable();
            $table->string('composicion')->nullable();
            $table->string('nombre_modelo')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('disenos_plasmados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->string('material')->nullable();
            $table->string('hilado')->nullable();
            $table->string('titulo')->nullable();
            $table->string('galga')->nullable();
            $table->string('tallas')->nullable();
            $table->decimal('peso_estimado', 8, 2)->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('prototipos_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->decimal('peso_real', 8, 2)->nullable();
            $table->date('fecha_prototipo')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('remallados_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->string('tipo_remallado')->nullable();
            $table->string('operario')->nullable();
            $table->date('fecha_remallado')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('tejidos_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->string('tipo_tejido')->nullable();
            $table->decimal('galga', 6, 2)->nullable();
            $table->decimal('tension', 6, 2)->nullable();
            $table->decimal('peso_por_metro', 8, 2)->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('acabados_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->string('tipo_acabado')->nullable();
            $table->unsignedSmallInteger('temperatura_vapor')->nullable();
            $table->unsignedSmallInteger('minutos_proceso')->nullable();
            $table->boolean('con_vapor')->default(false);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('fichas_tecnicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->unique()->constrained('disenos')->cascadeOnDelete();
            $table->boolean('es_muestra')->default(false);
            $table->string('modelo')->nullable();
            $table->string('codigo_modelo')->nullable();
            $table->string('tipo_tejido')->nullable();
            $table->string('tipo_maquina')->nullable();
            $table->decimal('tension', 6, 2)->nullable();
            $table->decimal('galga', 6, 2)->nullable();
            $table->string('punto')->nullable();
            $table->string('nombre_maquina')->nullable();
            $table->string('composicion')->nullable();
            $table->string('titulo_hilado')->nullable();
            $table->string('color_principal')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_impresion')->nullable();
            $table->date('actualizado_tecnica_en')->nullable();
            $table->string('modificado_por')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('colores_idea', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('tipo_tejido')->nullable();
            $table->string('composicion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('colores_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('hex', 7)->nullable();
            $table->string('codigo')->nullable();
            $table->string('empresa')->nullable();
            $table->unsignedInteger('cantidad_pedida')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('medidas_prototipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('nombre');
            $table->decimal('s', 6, 2)->nullable();
            $table->decimal('m', 6, 2)->nullable();
            $table->decimal('l', 6, 2)->nullable();
            $table->decimal('xl', 6, 2)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('medidas_ficha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('nombre');
            $table->decimal('incremento', 6, 2)->nullable();
            $table->decimal('s', 6, 2)->nullable();
            $table->decimal('m', 6, 2)->nullable();
            $table->decimal('l', 6, 2)->nullable();
            $table->decimal('xl', 6, 2)->nullable();
            $table->decimal('xxl', 6, 2)->nullable();
            $table->decimal('estandar', 6, 2)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('insumos_ficha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->decimal('cantidad', 8, 2)->nullable();
            $table->string('codigo')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('notas_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('seccion', 20);
            $table->text('contenido')->nullable();
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
            $table->unique(['diseno_id', 'seccion']);
        });

        Schema::create('fotos_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('seccion', 20)->index();
            $table->string('color')->nullable();
            $table->string('ruta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('observaciones_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('autor');
            $table->string('rol', 20);
            $table->text('comentario');
            $table->dateTime('observado_en');
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
            $table->index(['diseno_id', 'observado_en']);
        });

        Schema::create('revisiones_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('revisor');
            $table->string('estado', 20);
            $table->text('comentario');
            $table->dateTime('revisado_en');
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
        });

        Schema::create('codigos_barras_diseno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->string('codigo_color', 60);
            $table->string('talla', 10);
            $table->string('origen', 20)->default('eu');
            $table->string('codigo');
            $table->foreignId('asignado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->dateTime('asignado_en');
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
            $table->unique(['diseno_id', 'codigo_color', 'talla', 'origen']);
        });

        Schema::create('historial_notas_ficha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diseno_id')->constrained('disenos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('autor');
            $table->text('comentario');
            $table->dateTime('observado_en');
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('actualizado_en')->nullable();
            $table->index(['diseno_id', 'observado_en']);
        });
    }

    public function down(): void
    {
        foreach ([
            'historial_notas_ficha',
            'codigos_barras_diseno',
            'revisiones_diseno',
            'observaciones_diseno',
            'fotos_diseno',
            'notas_diseno',
            'insumos_ficha',
            'medidas_ficha',
            'medidas_prototipo',
            'colores_material',
            'colores_idea',
            'fichas_tecnicas',
            'acabados_diseno',
            'tejidos_diseno',
            'remallados_diseno',
            'prototipos_diseno',
            'disenos_plasmados',
            'ideas_diseno',
            'disenos',
            'usuarios',
        ] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }
};
