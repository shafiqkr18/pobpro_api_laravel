<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RotationType extends Model
{
  protected $table = 'rotation_types';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get company
	 */
	public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

	/*
	 * Get creator
	 */
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

}
