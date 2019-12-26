<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
	protected $table = 'topics';
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
	 * Get tasks
	 */
	public function tasks()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('listing_id', 0)->where('company_id', $this->company_id);
	}

	/*
	 * Get all tasks
	 */
	public function allTasks()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('task_id', '!=',0)
            ->where('company_id', $this->company_id)->groupBy('task_id');
    }
	/*
	 * Get letters
	 */
	public function letters()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('listing_id', '!=', 0)->where('task_id', 0)->where('type', 0)->where('company_id', $this->company_id);
	}

	/*
	 * Get all letters
	 */
	public function allLetters()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('listing_id', '!=', 0)
            ->where('type', 0)->where('company_id', $this->company_id)->groupBy('listing_id');
    }

	/*
	 * Get all entities
	 */

	public function entities()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('company_id', $this->company_id)
            ->where('listing_id', '!=', 0)->groupBy('listing_id');
    }

    /*
     *Linked tasks
     */
    public function linked_tasks()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')
            ->where('task_id', '!=', 0)
            ->where('company_id', $this->company_id);
    }

    /*
     * Get all MOM
     */
    public function allMOM()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('listing_id', '!=', 0)->where('type', 1)->where('company_id', $this->company_id)->groupBy('listing_id');
    }

    /*
     * Get all Reports
     */
    public function allReports()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'topic_id')->where('listing_id', '!=', 0)->where('type', 2)->where('company_id', $this->company_id)->groupBy('listing_id');
    }
}
