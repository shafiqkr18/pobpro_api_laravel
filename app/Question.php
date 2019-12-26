<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
  protected $table = 'qa_questions';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get question creator.
	 * 
	 */ 
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

	/*
	 * Get question status.
	 * 
	 */ 
	public function status()
	{
		return $this->hasOne('App\QuestionStatus', 'id', 'status_id');
	}

	/*
	 * Get question category.
	 * 
	 */ 
	public function category()
	{
		return $this->hasOne('App\QuestionCategory', 'id', 'category_id');
	}

	/*
	 * Get answers.
	 * 
	 */ 
	public function answer()
	{
		return $this->hasMany('App\Answer', 'question_id');
	}

}
