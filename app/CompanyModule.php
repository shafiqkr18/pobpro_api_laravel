<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyModule extends Model
{
  protected $table = 'company_modules';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get module
	 */
	public function module()
	{
		return $this->hasOne('App\ProductModule', 'id', 'module_id');
	}

}
