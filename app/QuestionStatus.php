<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionStatus extends Model
{
  protected $table = 'qa_status';
	public $primaryKey = 'id';
	public $timestamps = false;

}
