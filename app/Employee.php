<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
  /*
	* Get user
	*/
	public function user()
	{
		return $this->belongsTo('App\User', 'user_uuid', 'user_uuid');
	}
	
  /*
	 * Get employee position.
	 * 
	 */ 
	public function position()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'position_id');
	}
	
	/*
	 * Get employee organization.
	 * 
	 */ 
	public function organization()
	{
		return $this->hasOne('App\OrganizationManagement', 'id', 'org_id');
	}

	/*
	 * Get employee division.
	 * 
	 */ 
	public function division()
	{
		return $this->hasOne('App\Division', 'id', 'div_id');
	}

	/*
	 * Get employee department.
	 * 
	 */ 
	public function department()
	{
		return $this->hasOne('App\DepartmentManagement', 'id', 'dept_id');
	}

	/*
	* Get education level
	*/
	public function educationLevel()
	{
		return $this->hasOne('App\EducationLevel', 'id', 'education_level');
	}

	/*
	 * Get rotation type.
	 * 
	 */ 
	public function rotationType()
	{
		return $this->hasOne('App\RotationType', 'id', 'rotation_type_id');
	}

	/*
	 * Get report to.
	 * 
	 */ 
	public function reportTo()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'report_to_id');
	}

	/*
	* Get work type
	*/
	public function workType()
	{
		return $this->hasOne('App\WorkType', 'id', 'work_type');
	}

}
