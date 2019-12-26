<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    //
    protected $table = 'task_history';
    public $primaryKey = 'id';
    public $timestamps = false;

	/*
	 * Get task
	 */
	public function task()
	{
		return $this->belongsTo('App\Task', 'listing_id');
	}

	/*
	* Get created by
	*/
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

}
