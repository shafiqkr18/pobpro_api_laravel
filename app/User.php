<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
	 * Get full name
	 */
    public function getName()
    {
        return ($this->name ? $this->name : '') . ' ' . ($this->last_name ? $this->last_name : '');
    }
    /*
		 * Get organization
		 */
    public function organization()
    {
        return $this->hasOne('App\OrganizationManagement', 'id', 'org_id');
    }

    /*
     * Get department
     */
    public function department()
    {
        return $this->hasOne('App\DepartmentManagement', 'id', 'dept_id');
    }

    /*
     * Get division
     */
    public function division()
    {
        return $this->hasOne('App\Division', 'id', 'div_id');
    }

    /*
     * Get section
     */
    public function section()
    {
        return $this->hasOne('App\Section', 'id', 'sec_id');
    }

    /*
     * Get role
     */
    public function role()
    {
        return $this->hasOne('App\Role', 'id', 'role_id');
    }

    /*
	 * Get company
	 */
    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }
}
