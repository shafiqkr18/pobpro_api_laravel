<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusDetail extends Model
{
  protected $table = 'status_details';
	public $primaryKey = 'id';
	public $timestamps = false;
	
	/*
	 * Get creator.
	 * 
	 */
  public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

}
