<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('author');
            $table->string('role', 20);   // fabrica (INT) | eu (EU)
            $table->text('comment');
            $table->dateTime('observed_at');

            $table->timestamps();

            $table->index(['design_id', 'observed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_observations');
    }
};
