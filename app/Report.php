<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
  protected $table = 'work_reports';
	public $primaryKey = 'id';
	public $timestamps = false;


    /*
    * Get created by
    */
    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /*
	* Get department
	*/
    public function department()
    {
        return $this->belongsTo('App\DepartmentManagement','dept_id');
    }

    /*
	* Get topic relationships
	*/
    public function topicRelationships()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 2)->where('topic_id', '!=', 0)->groupBy('topic_id')->orderBy('topic_id', 'asc');
    }


    /*
	* Get remarks
	*/
    public function remarks()
    {
        return $this->hasMany('App\Remark', 'listing_id')->where('type', 2)->orderBy('created_at', 'desc');
    }


    /*
	* Get task relationships
	*/
    public function taskRelationships()
    {
        return $this->hasMany('App\TopicTaskRelationship', 'listing_id')->where('type', 2)->where('task_id', '!=', 0)->groupBy('task_id')->orderBy('task_id', 'asc');
    }

}
