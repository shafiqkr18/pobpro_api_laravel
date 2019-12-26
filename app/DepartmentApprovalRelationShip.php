<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentApprovalRelationShip extends Model
{
    protected $table = 'dept_approval_relationships';
    public $primaryKey = 'id';
    public $timestamps = false;

	/**
	 * Get department approval
	 */
	public function departmentApproval()
	{
		return $this->belongsTo('App\DepartmentApproval', 'dept_approval_id');
	}

}
