<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorrespondenceCategory extends Model
{
  protected $table = 'crspndnc_categories';
	public $primaryKey = 'id';
	public $timestamps = false;

	private $colors = [
		'#c3c3c3',
		'#1ab394',
		'#ed5565',
		'#666666',
		'#23c6c8',
		'#f8ac59'
	];

	/*
	* Get color
	*/
	public function getColor()
	{
		return $this->colors[$this->id];
	}

	/*
	* Get messages
	*/
	public function messages()
	{
		return $this->hasMany('App\CorrespondenceMessage', 'category_id');
	}

}
