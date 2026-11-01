<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();

            // idea | material
            $table->string('context', 20)->index();

            $table->string('color')->nullable();
            $table->string('knit_type')->nullable();
            $table->string('composition')->nullable();
            $table->string('hex', 7)->nullable();               // "Ref." (muestra de color)
            $table->string('code')->nullable();
            $table->string('company')->nullable();
            $table->unsignedInteger('ordered_qty')->nullable(); // "Pedido"

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_colors');
    }
};
