<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('design_barcodes', 'origin')) {
            Schema::table('design_barcodes', function (Blueprint $table) {
                $table->string('origin', 20)->default('eu');
            });
        }

        // Crear primero el índice nuevo para que la FK siga teniendo un índice.
        Schema::table('design_barcodes', function (Blueprint $table) {
            $table->unique(['design_id', 'color_code', 'size', 'origin']);
        });

        Schema::table('design_barcodes', function (Blueprint $table) {
            $table->dropUnique(['design_id', 'color_code', 'size']);
        });
    }

    public function down(): void
    {
        Schema::table('design_barcodes', function (Blueprint $table) {
            $table->unique(['design_id', 'color_code', 'size']);
        });

        Schema::table('design_barcodes', function (Blueprint $table) {
            $table->dropUnique(['design_id', 'color_code', 'size', 'origin']);
        });

        Schema::table('design_barcodes', function (Blueprint $table) {
            $table->dropColumn('origin');
        });
    }
};
