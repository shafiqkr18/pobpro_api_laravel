<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Permission extends \Spatie\Permission\Models\Permission
{
	protected $table = 'permissions';
	public $primaryKey = 'id';
	public $timestamps = false;
	
    public static function defaultPermissions()
    {
        return [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'planning-list',
            'planning-create',
            'planning-edit',
            'planning-delete',
            'organization-mgt-list',
            'organization-mgt-create',
            'organization-mgt-edit',
            'organization-mgt-delete',
            'positions-list',
            'positions-create',
            'positions-edit',
            'positions-delete',
            'recruitment-list',
            'recruitment-create',
            'recruitment-edit',
            'recruitment-delete',
            'mobilization-list',
            'mobilization-create',
            'mobilization-edit',
            'mobilization-delete',
            'insurance-list',
            'insurance-create',
            'insurance-edit',
            'insurance-delete',
        ];
    }

	/*
	 * Get child permissions.
	 * 
	 */ 
	public function children()
	{
		return $this->hasMany('App\Permission', 'parent_id');
	}

	/*
	 * Get parent permission.
	 * 
	 */ 
	public function parent()
	{
		return $this->belongsTo('App\Permission', 'parent_id');
	}

}
