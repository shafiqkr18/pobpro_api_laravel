<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendant extends Model
{
  protected $table = 'meeting_attendants';
	public $primaryKey = 'id';
	public $timestamps = false;

	/**
	 * Get the meeting attendant belongs.
	 */
	public function meeting()
	{
		return $this->belongsTo('App\Meeting');
	}

    public function attendant()
    {
        return $this->hasOne('App\User', 'id', 'attendant_id');
    }

}
