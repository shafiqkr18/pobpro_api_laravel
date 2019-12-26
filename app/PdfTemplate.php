<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
  protected $table = 'pdf_templates';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get template creator.
	 * 
	 */ 
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

}
