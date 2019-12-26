<?php


namespace App;
use Illuminate\Database\Eloquent\Model;

class OrganizationTemplate extends Model
{
    protected $table = 'organization_templates';
    public $primaryKey = 'id';
    public $timestamps = false;
}
