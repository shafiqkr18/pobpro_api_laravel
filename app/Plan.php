<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
  protected $table = 'plans';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get plan positions.
	 *
	 */
	public function positions()
	{
		return $this->hasMany('App\PlanPosition', 'plan_id');
	}

	/*
	 * Get plan creator.
	 *
	 */
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

    public function vacancies()
    {
        return $this->hasManyThrough('App\Vacancy', 'App\PlanPosition','plan_id','position_id','','position_id');
    }

}
