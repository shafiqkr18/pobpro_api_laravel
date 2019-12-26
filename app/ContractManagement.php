<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractManagement extends Model
{
    //
    protected $table = 'contracts';
    public $primaryKey = 'id';
    public $timestamps = false;


    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }
}
