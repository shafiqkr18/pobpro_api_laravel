<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	protected $table = 'tasks';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	* Get type
	*/
	public function getType()
	{
		$type = '';

		switch ($this->type) {
			case 0:
				$type = 'Letter';
				break;

			case 1:
				$type = 'Minutes of Meeting';
				break;

			case 2:
				$type = 'Report';
				break;

			default:
				# code...
				break;
		}

		return $type;
	}

	/*
	* Get status
	*/
	public function getStatus()
	{
		$status = '';

		switch ($this->status) {
			case 0:
				$status = 'Open';
				break;

			case 1:
				$status = 'Processing';
				break;

			case 2:
				$status = 'Closed';
				break;

			default:
				# code...
				break;
		}

		return $status;
	}

	/*
	 * Get company
	 */
	public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}
    /*
     * get createdBy
     */
    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

	/*
	 * Get users
	 */
	public function users()
	{
		return $this->hasMany('App\TaskUser', 'task_id');
	}

	/*
	 * Get topics
	 */
	public function topics()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('listing_id', 0)->where('company_id', $this->company_id);
	}

    /*
     * Get topics
     */
    public function allTopics()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('topic_id','!=', 0)->where('company_id', $this->company_id);
    }

	/*
	 * Get letters
	 */
	public function letters()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('listing_id', '!=', 0)->where('topic_id', 0)->where('type', 0)->where('company_id', $this->company_id);
	}

    /*
     * Get all letters
     */
    public function allLetters()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('listing_id', '!=', 0)->where('type', 0)->where('company_id', $this->company_id)->groupBy('listing_id');
    }

	/*
	* Get days remaining
	*/
	public function daysRemaining()
	{
		$days_remaining = 0;
		$today = strtotime(date('Y-m-d H:i:s'));
		$due_date = strtotime($this->due_date);

		if ($due_date > $today) {
			$days_remaining = floor(($due_date - $today) / 86400);
		}

		return $days_remaining;
	}

	/*
	* Get days passed
	*/
	public function daysPassed()
	{
		$today = strtotime(date('Y-m-d H:i:s'));
		$created_at = strtotime($this->created_at);

		return floor(($today - $created_at) / 86400);
	}

	/*
	 * Get history
	 */
	public function history()
    {
		return $this->hasMany('App\TaskHistory', 'listing_id')->where('type', 0)->where('company_id', $this->company_id)->orderBy('created_at', 'DESC');
    }

    public function task_history()
    {
        return $this->hasMany('App\TaskHistory', 'listing_id')->orderBy('created_at', 'desc');
    }


    /*
     * Get all MOM
     */
    public function allMOM()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('listing_id', '!=', 0)->where('type', 1)->where('company_id', $this->company_id)->groupBy('listing_id');
    }

    /*
     * Get all Reports
     */
    public function allReports()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('listing_id', '!=', 0)->where('type', 2)->where('company_id', $this->company_id)->groupBy('listing_id');
    }


    /*
	 * Get all entities
	 */

    public function entities()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')->where('company_id', $this->company_id)
            ->where('listing_id', '!=', 0)->groupBy('listing_id');
    }

    /*
     *Linked topics
     */
    public function linked_topics()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'task_id')
            ->where('topic_id', '!=', 0)
            ->where('company_id', $this->company_id);
    }
}
