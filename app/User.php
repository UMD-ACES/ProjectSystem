<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Subfission\Cas\Facades\Cas;

class User extends Authenticatable
{
    use Notifiable;

    public static $admin = 'Admin';
    public static $student = 'Student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dirID', 'name'
    ];

    /**
     * Returns the current user.
     * @return User
     */
    public static function get()
    {
        if(!Auth::check())
        {
            Cas::authenticate();
        }


        return Auth::user();
    }

    /* --------------- Roles --------------- */
    /**
     * Determines if the user is an Admin
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type == self::$admin;
    }

    /**
     * Determines if the user is a Student
     * @return bool
     */
    public function isStudent()
    {
        return $this->type == self::$student;
    }

    /* --------------- Static Functions ------------------ */
    /**
     * Determines if the user table has students.
     * @return bool
     */
    public static function isSetup()
    {
        return User::where('type', 'Student')->count() > 0;
    }

    public static function getAllStudents()
    {
        return User::query()->where('type', User::$student)->get();
    }

    /* --------------- Defining Relationships --------------- */

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * Pivot Table: user_criterion
     */
    function criteria()
    {
        return $this->belongsToMany('App\Criterion', 'user_criterion')
            ->withPivot('value', 'peer_evaluation_id', 'user_to_id')
            ->withTimestamps();
    }

    /**
     * Peer Evaluations regarding overall team
     */
    function peerEvaluationsTeam()
    {
        return $this->hasMany('App\PeerEvaluationsTeam');
    }

    /**
     * Peer Evaluations regarding specific member of the team
     */
    function peerEvaluationsTeamMembers()
    {
        return $this->hasMany('App\PeerEvaluationsTeamMember');
    }

    /**
     *  Peer Evaluations Table keeping track of all the peer evaluations in the application
     */
    function peerEvaluations()
    {
        return $this->belongsToMany('App\PeerEvaluation', 'user_peer_evaluation', 'user_id', 'peer_evaluation_id')
            ->withPivot('display_to_user')
            ->withTimestamps();
    }

    function individualScore(Collection $peerEvaluations)
    {
        $score = 0;
        /** @var PeerEvaluation $peerEvaluation */
        foreach($peerEvaluations as $peerEvaluation)
        {
            $score += $peerEvaluation->computeTeamMemberScore($this);
        }

        $score = $score / $peerEvaluations->count();

        return $score;
    }

    function meetingMinute()
    {
        return $this->hasMany('App\MeetingMinute');
    }

    function meetingMinuteAttendances()
    {
        return $this->hasMany('App\MeetingMinutesAttendance');
    }


    /* --------------- Filtering Relationships  ---------------
     - could be changed to accept an ID but students should only access their latest peer evaluation;
       this is for security reasons. In addition, students have a copy of their report saved on their computer
       and on ELMS if they need to refer to it.
    */

    /**
     * Students can have many team members
     */
    function getSubmittedActivePeerEvaluationTeamMembers()
    {
        return $this->peerEvaluationsTeamMembers()
            ->where('peer_evaluation_id', PeerEvaluation::active()->id);
    }

    /**
     * Each student only has one team peer evaluation per peer evaluation
     */
    function getSubmittedActivePeerEvaluationTeam()
    {
        return $this->hasMany('App\PeerEvaluationsTeam')
            ->where('peer_evaluation_id', PeerEvaluation::active()->id)
            ->first();
    }

    /**
     * Current Peer Evaluation that the student filled out or will fill out
     */
    function getSubmittedActivePeerEvaluation()
    {
        return $this->peerEvaluations()
            ->where('active', 1)
            ->first();
    }

    function getSubmittedActivePeerEvaluationCriteria()
    {
        return $this->criteria()
            ->wherePivot('peer_evaluation_id', PeerEvaluation::active()->id);
    }

    /* --------------- Filtering Relationships  ---------------
    /**
     * Students can have many team members
     */
    function getSubmittedPeerEvaluationTeamMembers(PeerEvaluation $peerEval)
    {
        return $this->peerEvaluationsTeamMembers()
            ->where('peer_evaluation_id', $peerEval->id);
    }

    /**
     * Students can have many team members
     */
    function getSubmittedPeerEvaluationTeamMember(PeerEvaluation $peerEval, User $teamMemberTo)
    {
        return $this->peerEvaluationsTeamMembers()
            ->where('peer_evaluation_id', $peerEval->id)
            ->where('user_to_id', $teamMemberTo->id)
            ->first();
    }

    /**
     * Students can have many team members
     */
    function getSubmittedPeerEvaluationTeam(PeerEvaluation $peerEval)
    {
        return $this->peerEvaluationsTeam()
            ->where('peer_evaluation_id', $peerEval->id)
            ->first();
    }

    function getSubmittedPeerEvaluationCriteria(PeerEvaluation $peerEval, User $teamMemberTo)
    {
        return $this->criteria()
            ->wherePivot('peer_evaluation_id', $peerEval->id)
            ->where('user_to_id', $teamMemberTo->id)
            ->get();
    }


    /* --------------- Questions on Relationships  --------------- */

    function hasSubmittedActivePeerEvaluation()
    {
        return $this->peerEvaluations()->where('active', 1)->count() > 0;
    }




}