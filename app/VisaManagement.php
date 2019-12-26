<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisaManagement extends Model
{
    //
    protected $table = 'user_visas';
    public $primaryKey = 'id';
    public $timestamps = false;
}
