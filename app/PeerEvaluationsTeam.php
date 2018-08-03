<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeerEvaluationsTeam extends Model
{
    protected $fillable = ['peer_evaluation_id', 'user_id', 'team_evaluation'];

    protected $table = 'peer_evaluations_team';

    public function peerEvaluationDetails()
    {
        return $this->belongsTo('App\PeerEvaluations');
    }
}
