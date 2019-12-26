<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionRequest extends Model
{
  protected $table = 'positions_requests';
	public $primaryKey = 'id';
	public $timestamps = false;

	/**
	 * Get section.
	 */
	public function section()
	{
		return $this->belongsTo('App\SectionRequest', 'section_id');
	}

    public function department()
    {
        return $this->belongsTo('App\DepartmentManagement','department_id');
    }

	/**
	 * Get position.
	 */
	public function position()
	{
		return $this->belongsTo('App\PositionManagement', 'position_id');
	}

}
