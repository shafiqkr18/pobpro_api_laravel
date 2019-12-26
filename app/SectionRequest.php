<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionRequest extends Model
{
  protected $table = 'sections_requests';
	public $primaryKey = 'id';
	public $timestamps = false;

	/**
	 * Get positions
	 */
	public function positions()
	{
		return $this->hasMany('App\PositionRequest', 'section_id')->where('deleted_at', null);
	}

    public function department()
    {
        return $this->belongsTo('App\DepartmentManagement','dept_id');
    }

}
