<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicTaskRelationship extends Model
{
  protected $table = 'topic_task_relationship';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get topic
	 */
	public function topic()
	{
		return $this->hasOne('App\Topic', 'id', 'topic_id');
	}

	/*
	 * Get task
	 */
	public function task()
	{
		return $this->hasOne('App\Task', 'id', 'task_id');
	}

	/*
	 * Get company
	 */
	public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

	/*
	 * Get letter
	 */
	public function letter()
	{
		return $this->hasOne('App\CorrespondenceMessage', 'id', 'listing_id')->where('company_id', $this->company_id);
	}

	/*
	 * Get MOM
	 */
    public function mom()
    {
        return $this->hasOne('App\Meeting', 'id', 'listing_id')->where('company_id', $this->company_id);
    }

    /*
	 * Get Report
	 */
    public function report()
    {
        return $this->hasOne('App\Report', 'id', 'listing_id')->where('company_id', $this->company_id);
    }



}
