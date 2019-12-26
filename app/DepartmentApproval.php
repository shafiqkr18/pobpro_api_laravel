<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentApproval extends Model
{

    protected $table = 'department_approvals';
    public $primaryKey = 'id';
    public $timestamps = false;


    /*
     * get created by
     */
    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }

	/**
	 * Get department
	 */
	public function department()
	{
		return $this->belongsTo('App\DepartmentManagement', 'department_id');
	}

	/*
	 * Get department approval relationships.
	 *
	 */
  public function approvalRelationships()
	{
		return $this->hasMany('App\DepartmentApprovalRelationShip', 'dept_approval_id');
	}

}
