<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeerEvaluations extends Model
{
    protected $fillable = ['name'];

    public static function isOneActive()
    {
        return PeerEvaluations::where('active', 1)->first() != null;
    }

    public static function active()
    {
        return PeerEvaluations::where('active', 1)->first();
    }
}
