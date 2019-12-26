<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
  protected $table = 'travels';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get position.
	 */
	public function position()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'position_id');
	}

	/*
	 * Get department.
	 */
	public function department()
	{
		return $this->hasOne('App\DepartmentManagement', 'id', 'dept_id');
	}

}
