<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        return User::where('dirID', session('cas_user'))->first();
    }

    // ---- Roles -----
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

    function criteria()
    {
        return $this->belongsToMany('App\Criterion', 'user_criterion')
            ->withPivot('value')
            ->withTimestamps();
    }

    function peerEvaluationsTeam()
    {
        return $this->hasMany('App\PeerEvaluationsTeam');
    }

    function peerEvaluations()
    {
        return $this->belongsToMany('App\PeerEvaluations', 'user_peer_evaluation', 'user_id', 'peer_evaluation_id')
            ->withTimestamps();
    }

    function hasSubmittedCurrentPeerEvaluation()
    {
        return $this->peerEvaluations()->where('active', 1)->count() > 0;
    }
}