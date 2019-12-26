<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractorEmployee extends Model
{
    protected $table = 'contractor_employees';
    public $primaryKey = 'id';
    public $timestamps = false;
	
	/*
	 * Get full name
	 */
	public function getName()
	{
		return ($this->first_name ? $this->first_name : '') . ' ' . ($this->last_name ? $this->last_name : '');
	}

	/*
	 * Get contractor
	 */
	public function contractor()
	{
		return $this->belongsTo('App\CompanyContractor', 'contractor_id');
	}

}
