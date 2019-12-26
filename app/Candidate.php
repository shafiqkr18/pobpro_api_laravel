<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    //
    //
    protected $table = 'candidates';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	* Get user
	*/
	public function user()
	{
		return $this->belongsTo('App\User', 'user_uuid', 'user_uuid');
	}

    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }

    public function position()
    {
        return $this->hasOne('App\PositionManagement','id','position_id');
    }

    public function old_position()
    {
        return $this->hasOne('App\PositionManagement','id','old_position_id');
    }

	/*
	 * Get interviews
	 */
	public function interviews()
	{
		return $this->hasMany('App\Interview', 'candidate_id');
	}

	/*
	 * Get offers
	 */
	public function offers()
	{
		return $this->hasMany('App\Offer', 'candidate_id')->where('type', 0);
	}

	/*
	 * Get sent offers
	 */
	public function sentOffers()
	{
		return $this->hasMany('App\Offer', 'candidate_id')->where('type', 0)->where('sending_status', 1);
	}

	/*
	 * Get contract
	 */
	public function contract()
	{
		return $this->hasOne('App\Offer', 'candidate_id')->where('type', 1)->latest();
	}

	/*
	 * Get all contracts
	 */
	public function contracts()
	{
		return $this->hasMany('App\Offer', 'candidate_id')->where('type', 1);
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
	* Get company
	*/
	public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

    public function ex_department()
    {
        return $this->hasOne('App\ExDepartment','id','ex_dept_id');
    }

	/*
	 * Get full name
	 */
	public function getName()
	{
		return ($this->name ? $this->name : '') . ' ' . ($this->last_name ? $this->last_name : '');
	}
	
}
