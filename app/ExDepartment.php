<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExDepartment extends Model
{
    protected $table = 'ex_departments';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	 * Get positions
	 */
	public function positions()
	{
		return $this->hasMany('App\ExPosition', 'dept_id');
	}

}
