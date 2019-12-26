<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WFGMyRequest extends Model
{
    //
    protected $table = 'wfg_my_requests';
    public $primaryKey = 'id';
    public $timestamps = false;
}
