<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();

            $table->string('color_code', 60);
            $table->string('size', 10);   // s | m | l | xl | xxl
            $table->string('code');       // AK-D-001-VR1240-L

            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at');

            $table->timestamps();

            $table->unique(['design_id', 'color_code', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_barcodes');
    }
};
