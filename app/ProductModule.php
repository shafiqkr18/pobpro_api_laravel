<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModule extends Model
{
  protected $table = 'product_modules';
	public $primaryKey = 'id';
	public $timestamps = false;
}
