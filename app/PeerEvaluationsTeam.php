<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeerEvaluationsTeam extends Model
{
    protected $fillable = ['team_evaluation'];

    protected $table = 'peer_evaluations_team';

    public function peerEvaluation()
    {
        return $this->belongsTo('App\PeerEvaluation');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
