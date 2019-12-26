<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
	/*
	 * Get candidate
	 * 
	 */
  public function candidate()
	{
		return $this->hasOne('App\Candidate', 'id', 'candidate_id');
	}

	/*
	 * Get company
	 * 
	 */
  public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

	/*
	 * Get interview creator.
	 * 
	 */
  public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

	/*
	 * Get interview comments.
	 * 
	 */
  public function comments()
	{
		return $this->hasMany('App\StatusDetail', 'listing_id')->where('type', 1)->orderBy('created_at' , 'desc');
	}

	/*
	 * Get plan.
	 * 
	 */
  public function plan()
	{
		return $this->hasOne('App\Plan', 'id', 'plan_id');
	}

	/*
	 * Get position.
	 * 
	 */
  public function position()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'position_id');
	}

	/*
	 * Get interview attendees.
	 * 
	 */
  public function attendees()
	{
		return $this->hasMany('App\InterviewAttendee', 'interview_id');
	}

}
