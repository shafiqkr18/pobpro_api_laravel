<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionRelationship extends Model
{
  protected $table = 'position_relationships';
	public $primaryKey = 'id';
	public $timestamps = false;
		
	/*
	* Get position
	*/
	public function position()
	{
		return $this->belongsTo('App\PositionManagement', 'position_id');
	}

	/*
	 * Get ex-position
	 */
	public function exPosition()
	{
		return $this->hasOne('App\ExPosition', 'id', 'ex_position_id');
	}

	/*
	 * Get ex-department
	 */
	public function exDepartment()
	{
		return $this->hasOne('App\ExDepartment', 'id', 'ex_dept_id');
	}

}
