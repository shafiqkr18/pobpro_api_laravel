<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    //
    protected $table = 'activities';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function interview()
    {
        return $this->hasOne('App\Interview', 'id', 'listing_id');
    }
}
