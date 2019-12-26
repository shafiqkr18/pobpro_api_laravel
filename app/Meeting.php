<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
  protected $table = 'meetings';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get user that created the meeting
	 *
	 */
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

	/*
	 * Get host for the meeting
	 *
	 */
	public function host()
	{
		return $this->hasOne('App\User', 'id', 'host_id');
	}

	/*
	 * Get meeting actions
	 *
	 */
	public function actions()
	{
		return $this->hasMany('App\MeetingAction');
	}

	/*
	 * Get meeting attendants
	 *
	 */
	public function attendants()
	{
		return $this->hasMany('App\MeetingAttendant');
	}

	/*
	 * Get meeting topics
	 *
	 */
	public function topics()
	{
		return $this->hasMany('App\MeetingTopic');
	}

	/*
	 * Get department
	 *
	 */
	public function department()
	{
		return $this->hasOne('App\DepartmentManagement', 'id', 'dept_id');
	}

	/*
	* Get topic relationships
	*/
	public function topicRelationships()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 1)->where('topic_id', '!=', 0)->groupBy('topic_id')->orderBy('topic_id', 'asc');
	}

	/*
	* Get task relationships
	*/
	public function taskRelationships()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 1)->where('task_id', '!=', 0)->groupBy('task_id')->orderBy('task_id', 'asc');
	}

}
