<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('surveys', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('discription');
            $table->json('question_ids');
            $table->integer('random_question_count')->nullable();
            $table->integer('number_of_answers')->default(0);
            $table->integer('total_answers');
            $table->string('password')->nullable();
            $table->boolean('view_survey');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
