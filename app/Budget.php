<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
  protected $table = 'budgets';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get budget creator.
	 * 
	 */ 
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

}
