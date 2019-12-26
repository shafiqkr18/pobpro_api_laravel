<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
  protected $table = 'qa_categories';
	public $primaryKey = 'id';
	public $timestamps = false;


}
