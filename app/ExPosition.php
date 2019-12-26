<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExPosition extends Model
{
    protected $table = 'ex_positions';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	* Get department
	*/
	public function department()
	{
		return $this->belongsTo('App\ExDepartment', 'dept_id');
	}

	/*
	* Get position relationship
	*/
	public function positionRelationship()
	{
		return $this->belongsTo('App\PositionRelationship', 'id', 'ex_position_id')->where('deleted_at', null);
	}

}
