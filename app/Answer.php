<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
  protected $table = 'qa_answers';
	public $primaryKey = 'id';
	public $timestamps = false;
	
}
