<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_tech_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('author');
            $table->text('comment');
            $table->dateTime('observed_at');

            $table->timestamps();

            $table->index(['design_id', 'observed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_tech_notes');
    }
};
