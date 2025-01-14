<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('score');
            $table->timestamps();



             //relationship questioner
           $table->foreign('question_id')->references('id')->on('questionnaires');

            //relationship user
          $table->foreign('user_id')->references('id')->on('users');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
