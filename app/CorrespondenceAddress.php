<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CorrespondenceAddress extends Model
{
  protected $table = 'crspndnc_addresses';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get full name.
	 *
	 */
	public function getName()
	{
		return ($this->first_name ? $this->first_name : '') . ' '
					. ($this->middle_name ? $this->middle_name : '') . ' '
					. ($this->last_name ? $this->last_name : '');
	}

	/*
	 * Get messages count.
	 *
	 */
	public function getMessagesCount($direction)
	{
		if ($direction == 'IN') {
			if (Auth::user()->hasRole('itfpobadmin')) {
				return $this->hasMany('App\CorrespondenceMessage', 'msg_from_id')->where('direction', 'IN')->count();
			}
			else {
				return $this->hasMany('App\CorrespondenceMessage', 'msg_from_id')
											->where('direction', 'IN')
											->where('company_id', Auth::user()->company_id)
											->count();
			}
		}
		else {
			if (Auth::user()->hasRole('itfpobadmin')) {
				return $this->hasMany('App\CorrespondenceMessage', 'msg_to_id')->where('direction', 'OUT')->count();
			}
			else {
				return $this->hasMany('App\CorrespondenceMessage', 'msg_to_id')
											->where('direction', 'OUT')
											->where('company_id', Auth::user()->company_id)
											->count();
			}
		}
	}
	
}
