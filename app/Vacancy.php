<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    //
    protected $table = 'position_vacancies';
    public $primaryKey = 'id';
    public $timestamps = false;


    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }

    public function department()
    {
        return $this->hasOne('App\DepartmentManagement','id','department_id');
    }

		/*
		* Get position
		*/
		public function position()
		{
			return $this->hasOne('App\PositionManagement', 'id', 'position_id');
		}

	/*
	* Get age
	*/
	public function ageRange()
	{
		return $this->hasOne('App\CandidateAge', 'id', 'age');
	}

	/*
	* Get work type
	*/
	public function workType()
	{
		return $this->hasOne('App\WorkType', 'id', 'work_type');
	}

	/*
	* Get education level
	*/
	public function educationLevel()
	{
		return $this->hasOne('App\EducationLevel', 'id', 'education_level');
	}

	/*
	* Get rotation type
	*/
	public function rotationType()
	{
		return $this->hasOne('App\RotationType', 'id', 'rotation_pattern');
	}

	/*
	* Get report to
	*/
	public function reportTo()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'report_to');
	}

}
