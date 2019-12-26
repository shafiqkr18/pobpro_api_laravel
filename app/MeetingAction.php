<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingAction extends Model
{
  protected $table = 'meeting_actions';
	public $primaryKey = 'id';
	public $timestamps = false;

	/**
	 * Get the meeting that owns the action.
	 */
	public function meeting()
	{
		return $this->belongsTo('App\Meeting');
	}
	
}
