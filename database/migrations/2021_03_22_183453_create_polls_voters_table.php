<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls_voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voter_id')->constrained('users');
            $table->foreignId('poll_id')->constrained('polls');
            $table->timestamp('voted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls_voters');
    }
}
