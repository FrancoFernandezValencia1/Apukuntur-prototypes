<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->id();

            // Cabecera
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('responsible')->nullable();
            $table->date('start_date')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('approval_status', 30)->default('en_desarrollo')->index(); // lo decide EU
            $table->boolean('is_approved')->default(false);                            // lo decide EU
            $table->string('thumbnail_path')->nullable();

            // Etapa 1 · Idea
            $table->string('garment_type')->nullable();
            $table->string('season')->nullable();
            $table->string('palette')->nullable();
            $table->string('knit_type')->nullable();
            $table->text('idea_description')->nullable();
            $table->string('yarn_code')->nullable();
            $table->string('yarn_title')->nullable();
            $table->string('composition')->nullable();
            $table->string('model_name')->nullable();

            // Etapa 2 · Diseño plasmado
            $table->string('s2_material')->nullable();
            $table->string('s2_yarn')->nullable();
            $table->string('s2_title')->nullable();
            $table->string('s2_gauge')->nullable();
            $table->string('s2_sizes')->nullable();
            $table->decimal('s2_estimated_weight', 8, 2)->nullable();

            // Etapa 3 · Prototipo
            $table->decimal('real_weight', 8, 2)->nullable();
            $table->date('prototype_date')->nullable();

            // Remallado
            $table->string('seam_type')->nullable();
            $table->string('seam_operator')->nullable();
            $table->date('seam_date')->nullable();

            // Tejido
            $table->string('knit_proto_type')->nullable();
            $table->decimal('knit_gauge', 6, 2)->nullable();
            $table->decimal('knit_tension', 6, 2)->nullable();
            $table->decimal('weight_per_meter', 8, 2)->nullable();

            // Acabado
            $table->string('finish_type')->nullable();
            $table->unsignedSmallInteger('steam_temperature')->nullable();
            $table->unsignedSmallInteger('process_minutes')->nullable();
            $table->boolean('with_steam')->default(false);

            // Etapa 4 · Ficha técnica
            $table->boolean('is_sample')->default(false);
            $table->string('tech_model')->nullable();
            $table->string('tech_model_code')->nullable();
            $table->string('tech_knit_type')->nullable();
            $table->string('tech_machine_type')->nullable();
            $table->decimal('tech_tension', 6, 2)->nullable();
            $table->decimal('tech_gauge', 6, 2)->nullable();
            $table->string('tech_stitch')->nullable();
            $table->string('tech_machine_name')->nullable();
            $table->string('tech_composition')->nullable();
            $table->string('tech_yarn_title')->nullable();
            $table->string('tech_color')->nullable();
            $table->json('supplies')->nullable();
            $table->date('print_date')->nullable();
            $table->date('tech_updated_at')->nullable();
            $table->string('modified_by')->nullable();

            $table->json('notes')->nullable();   // textos por sección
            $table->json('photos')->nullable();  // rutas de imágenes por sección

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
