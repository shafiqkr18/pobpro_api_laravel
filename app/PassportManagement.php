<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassportManagement extends Model
{
    protected $table = 'user_passports';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	* Get user
	*/
	public function user()
    {
		return $this->belongsTo('App\User');
    }

	public function createdBy()
    {
		return $this->hasOne('App\User','id','created_by');
    }

}
