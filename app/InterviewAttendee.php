<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterviewAttendee extends Model
{
    //
    protected $table = 'interview_attendees';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	 * Get interviewer.
	 * 
	 */
  public function interviewer()
	{
		return $this->hasOne('App\User', 'id', 'interviewer_id');
	}

}
