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
}
