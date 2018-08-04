<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeerEvaluationsTeamMember extends Model
{
    protected $fillable = ['peer_evaluation_id', 'user_id', 'user_to_id', 'grade', 'grade_evaluation', 'participation_table'];

    public function teamMember()
    {
        return $this->belongsTo('App\User', 'user_to_id');
    }
}
