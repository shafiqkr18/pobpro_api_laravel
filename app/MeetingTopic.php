<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingTopic extends Model
{
  protected $table = 'meeting_topics';
	public $primaryKey = 'id';
	public $timestamps = false;

	/**
	 * Get the meeting that owns the topic.
	 */
	public function meeting()
	{
		return $this->belongsTo('App\Meeting');
	}

}
