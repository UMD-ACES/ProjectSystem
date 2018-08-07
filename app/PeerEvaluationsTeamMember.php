<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeerEvaluationsTeamMember extends Model
{
    protected $fillable = ['grade', 'grade_evaluation'];

    public function teamMember()
    {
        return $this->belongsTo('App\User', 'user_to_id');
    }

    public function peerEvaluation()
    {
        return $this->belongsTo('App\PeerEvaluation');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
