<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SourceType extends Model
{
    //
    protected $table = 'source_types';
    public $primaryKey = 'id';
    public $timestamps = false;
}
