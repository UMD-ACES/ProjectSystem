<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Incident extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function report(User $user, $description = null)
    {
        $incident = new Incident();
        $incident->url = URL::full();
        $incident->previous_url = URL::previous();
        $incident->description = $description;
        $incident->user()->associate($user);

        $incident->save();
    }
}
