<?php


namespace App;


use Illuminate\Database\Eloquent\Model;


class PlanExport extends Model
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

    public function recruitmentType()
    {
        return $this->hasOne('App\RecruitmentType', 'id', 'recruitment_type_id');
    }
}
