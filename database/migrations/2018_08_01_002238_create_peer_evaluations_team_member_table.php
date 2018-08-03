<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeerEvaluationsTeamMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peer_evaluations_team_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('peer_evaluation_id');
            $table->integer('user_id');
            $table->integer('user_to_id');
            $table->integer('grade');
            $table->text('grade_evaluation');
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
        Schema::dropIfExists('peer_evaluations_team_members');
    }
}
