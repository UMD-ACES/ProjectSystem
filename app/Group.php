<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name'];

    public static function isSetup()
    {
        return Group::all()->count() > 0;
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_group')
            ->withPivot('peer_evaluation_id');
    }

    public function technicalLogs()
    {
        return $this->hasMany('App\TechnicalLog');
    }

    public function meetingMinutes()
    {
        return $this->hasMany('App\MeetingMinute');
    }

    public function lastMeetingMinute()
    {
        return $this->meetingMinutes()
            ->latest();
    }
}
