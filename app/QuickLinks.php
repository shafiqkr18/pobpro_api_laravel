<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickLinks extends Model
{
  protected $table = 'quick_links';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get permission.
	 *
	 */
	public function permission()
	{
		return $this->hasOne('App\Permission', 'id', 'link_id');
	}

	/*
	 * Get user/creator.
	 *
	 */
	public function createdBy()
	{
		return $this->belongsTo('App\User', 'created_by');
	}

	/*
	 * Get company
	 */
	public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}

}
