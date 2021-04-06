<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls_results', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->unsignedBigInteger('poll_id');
            $table->foreign('poll_id')->references('id')->on('polls');
            $table->text('choice');
            $table->integer('choice_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls_results');
    }
}
