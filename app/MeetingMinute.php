<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MeetingMinute extends Model
{
    protected $fillable = ['notes', 'action_items', 'next_meeting', 'start', 'end'];

    /** Defining Relationships */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function attendance()
    {
        return $this->hasMany('App\MeetingMinutesAttendance');
    }


    public static function findForUser(User $user)
    {
        $meetingMinutes = new Collection();

        /** @var MeetingMinutesAttendance $meetingMinuteAttendance */
        foreach ($user->meetingMinuteAttendances as $meetingMinuteAttendance)
        {
            $meetingMinutes->push($meetingMinuteAttendance->meetingMinute()->first());
        }

        return $meetingMinutes;
    }

    public function attendanceMembers()
    {
        $members = new Collection();

        /** @var MeetingMinutesAttendance $attendance */
        foreach ($this->attendance()->get() as $attendance)
        {
            $members->push($attendance->user);
        }

        return $members;
    }



}
