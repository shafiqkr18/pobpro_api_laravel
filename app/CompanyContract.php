<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyContract extends Model
{
    protected $table = 'company_contracts';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function contractor()
    {
        return $this->belongsTo('App\CompanyContractor','contractor_id');
    }
	
	/*
	 * Get department.
	 *
	 */
  public function department()
	{
		return $this->belongsTo('App\DepartmentManagement', 'department_id');
	}

	/*
	 * Get company.
	 *
	 */
  public function company()
	{
		return $this->hasOne('App\Company', 'id', 'company_id');
	}
	
}
