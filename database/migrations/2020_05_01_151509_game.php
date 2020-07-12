<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Game extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('started')->default(0);
            $table->tinyInteger('finished')->default(0);
            $table->tinyInteger('in_progress')->default(0);
            $table->tinyInteger('round_in_progress')->default(0);
            $table->integer('round_time')->default(60);
            $table->integer('current_round')->default(0);
            $table->string('selected_letters');
            $table->string('key')->nullable(false);
            $table->text('selected_options');
            $table->integer('rounds');
            $table->text('players');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game');
    }
}
