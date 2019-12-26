<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationManagement extends Model
{
    protected $table = 'organizations';
    public $primaryKey = 'id';
    public $timestamps = false;


    /*
     * get created by for organization
     */
    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }


    /**
     * Get the divisions for the organization.
     */
    public function divisions()
    {
        return $this->hasMany('App\Division','org_id')->where('deleted_at',null);
    }
		
	/**
	 * Get company
	 */
	public function company()
	{
		return $this->belongsTo('App\Company');
	}
	
}
