<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
  protected $table = 'work_types';
	public $primaryKey = 'id';
	public $timestamps = false;

}
