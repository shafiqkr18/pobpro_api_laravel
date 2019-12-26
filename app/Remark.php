<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    //
    protected $table = 'remarks';
    public $primaryKey = 'id';
    public $timestamps = false;

    /*
	* Get created by
	*/
    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
	
}
