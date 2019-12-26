<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorrespondenceMessage extends Model
{
    protected $table = 'crspndnc_messages';
    public $primaryKey = 'id';
    public $timestamps = false;

    /*
	* Get created by
     */
    public function createdBy()
    {
		return $this->hasOne('App\User', 'id', 'created_by');
    }

	/*
	* Get from
	*/
	public function from()
	{
		if ($this->direction == 'IN') {
			return $this->hasOne('App\CorrespondenceAddress', 'id', 'msg_from_id');
		}
		else {
			return $this->hasOne('App\User', 'id', 'msg_from_id');
		}
	}

	/*
	* Get to
	*/
	public function to()
	{
		if ($this->direction == 'IN') {
			return $this->hasOne('App\User', 'id', 'msg_to_id');
		}
		else {
			return $this->hasOne('App\CorrespondenceAddress', 'id', 'msg_to_id');
		}
	}

	/*
	* Get category
	*/
	public function category()
	{
		return $this->belongsTo('App\CorrespondenceCategory', 'category_id');
	}


    public function parent() {
        return $this->belongsToOne(static::class, 'msg_parent_id');
    }

    //each category might have multiple children
    public function children() {
        return $this->hasMany(static::class, 'msg_parent_id');
    }

	/*
	* Get topic relationships
	*/
	public function topicRelationships()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 0)->where('topic_id', '!=', 0)->groupBy('topic_id')->orderBy('topic_id', 'asc');
	}

	/*
	* Get task relationships
	*/
	public function taskRelationships()
	{
		return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 0)->where('task_id', '!=', 0)->groupBy('task_id')->orderBy('task_id', 'asc');
	}


    public function task()
    {
        return $this->hasOne('App\Task', 'id', 'task_id');
    }

	/*
	* Get status
	*/
	public function getStatus()
	{
		$status = '';

		switch ($this->status) {
			case 0:
				$status = 'New';
				break;

			case 1:
				$status = 'Assigned';
				break;

			case 2:
				$status = 'Processing';
				break;

			case 3:
				$status = 'Replied';
				break;

			case 4:
				$status = 'Closed';
				break;
			
			default:
				# code...
				break;
		}

		return $status;
	}

}
