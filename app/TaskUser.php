<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
  protected $table = 'task_users';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get user
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/*
	 * Get task
	 */
	public function task()
	{
		return $this->belongsTo('App\Task');
	}
	
}
