<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingMinutesAttendance extends Model
{


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function meetingMinute()
    {
        return $this->belongsTo('App\MeetingMinute');
    }
}
