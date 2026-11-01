<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('reviewer');
            $table->string('status', 20);   // approved | observed
            $table->text('comment');
            $table->dateTime('reviewed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_reviews');
    }
};
