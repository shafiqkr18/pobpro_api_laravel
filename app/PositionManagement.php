<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionManagement extends Model
{
    //

    protected $table = 'positions';
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

    public function getDivision()
    {
        return $this->hasOneThrough(
            'App\Division',
            'App\DepartmentManagement',
            'department_id', // Foreign key on users table...
            'dept_id', // Foreign key on history table...
            'id', // Local key on suppliers table...
            'id' // Local key on users table...
        );
    }

    public function myDivision()
    {
        return $this->hasOneThrough(
            'App\Division',
            'App\DepartmentManagement',
            'id', // Foreign key on users table...
            'id', // Foreign key on history table...
            'department_id', // Local key on suppliers table...
            'div_id' // Local key on users table...
        );
    }

	/*
	 * Get section.
	 *
	 */
  public function section()
	{
		return $this->hasOne('App\Section', 'id', 'section_id');
	}

	/*
		* Get vacancy
		*/
		public function vacancy()
		{
			return $this->hasOne('App\Vacancy', 'position_id');
		}

	/*
	 * Get employees with this position.
	 *
	 */
  public function employees()
	{
		return $this->hasMany('App\Employee', 'position_id');
	}

    /*Get Candidates in divisions*/
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'position_id');
    }

    public function accepted_candidates()
    {
        return $this->hasMany('App\Candidate', 'position_id')->where('offer_accepted',1);
    }

	/*
	 * Get offers for position.
	 *
	 */
  public function offers()
	{
		return $this->hasMany('App\Offer', 'position_id')->where('type', 0);
	}

	/*
	 * Get company for position.
	 *
	 */
  public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

	/*
	 * Get position relationships.
	 *
	 */
  public function positionRelationships()
	{
		return $this->hasMany('App\PositionRelationship', 'position_id')->where('deleted_at', null);
	}

	/*
	 * Get position update request
	 */
	public function positionUpdateRequest()
	{
		return $this->hasOne('App\PositionRequest', 'position_id')->where('status', 0);
	}

}
