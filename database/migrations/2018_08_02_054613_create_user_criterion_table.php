<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_criterion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('peer_evaluation_id');
            $table->integer('user_id');
            $table->integer('user_to_id');
            $table->integer('criterion_id');
            $table->string('value');
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
        Schema::dropIfExists('user_criterion');
    }
}
