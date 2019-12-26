<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanPosition extends Model
{
  protected $table = 'plan_positions';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get plan.
	 *
	 */
	public function plan()
	{
		return $this->belongsTo('App\Plan');
	}

	/*
	 * Get position.
	 *
	 */
	public function position()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'position_id');
	}

    public function rotationType()
    {
        return $this->hasOne('App\RotationType','id','rotation_type_id');
    }

    public function nationality()
    {
        return $this->hasOne('App\PlanNationality','id','nationality_id');
    }

}
