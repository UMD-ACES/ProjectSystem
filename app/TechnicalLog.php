<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TechnicalLog extends Model
{
    protected $fillable = ['completed_at', 'description'];

    /* Defining Relationships */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }


    public function category()
    {
        return $this->belongsTo('App\TechnicalCategory', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
