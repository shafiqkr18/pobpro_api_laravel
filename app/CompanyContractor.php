<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyContractor extends Model
{
    protected $table = 'company_contractors';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function contracts()
    {
        return $this->hasMany('App\CompanyContract', 'contractor_id');
    }

    public function employees()
    {
        return $this->hasMany('App\ContractorEmployee', 'contractor_id');
    }
}
