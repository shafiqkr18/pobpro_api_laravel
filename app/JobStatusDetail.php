<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobStatusDetail extends Model
{
  protected $table = 'job_status_details';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	* Get candidate associated with job
	*/
	public function candidate()
	{
		return $this->hasOne('App\Candidate', 'id', 'candidate_id');
	}
}
