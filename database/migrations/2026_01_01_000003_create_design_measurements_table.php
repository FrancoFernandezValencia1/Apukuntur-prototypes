<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();

            // prototype → medidas tomadas sobre la muestra (S, M, L, XL)
            // tech      → medidas de la ficha técnica (Inc, S..XXL, Std)
            $table->string('context', 20)->index();

            $table->string('name');
            $table->decimal('inc', 6, 2)->nullable();
            $table->decimal('s', 6, 2)->nullable();
            $table->decimal('m', 6, 2)->nullable();
            $table->decimal('l', 6, 2)->nullable();
            $table->decimal('xl', 6, 2)->nullable();
            $table->decimal('xxl', 6, 2)->nullable();
            $table->decimal('std', 6, 2)->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_measurements');
    }
};
