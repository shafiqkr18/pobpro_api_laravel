<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

class Offer extends Model
{
	protected $table = 'offers';
	
	/*
	 * Get candidate
	 * 
	 */
  public function candidate()
	{
		return $this->hasOne('App\Candidate', 'id', 'candidate_id');
	}

	/*
	 * Get position
	 * 
	 */
  public function position()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'position_id');
	}

	/*
	 * Get offer comments.
	 * 
	 */
  public function comments()
	{
		return $this->hasMany('App\StatusDetail', 'listing_id')->where('type', 3)->orderBy('created_at' , 'desc');
	}

	/*
	 * Get offer creator.
	 * 
	 */ 
	public function createdBy()
	{
		return $this->hasOne('App\User', 'id', 'created_by');
	}

	/*
	 * Get DM approval history.
	 * 
	 */
  public function getDMApproval()
	{
		$history = $this->hasMany('App\StatusDetail', 'listing_id')->where('type', 4)->orderBy('created_at', 'DESC')->get();
		
		foreach ($history as $hist) {
			$created_by = $hist->createdBy;
			if ($created_by->hasRole('DM')) {
				return $hist;
			}
		}

		return null;
	}

	/*
	 * Get HRM approval history.
	 * 
	 */
  public function getHRMApproval()
	{
		$history = $this->hasMany('App\StatusDetail', 'listing_id')->where('type', 4)->orderBy('created_at', 'DESC')->get();

		foreach ($history as $hist) {
			$created_by = $hist->createdBy;
			if ($created_by->hasRole('HRM')) {
				return $hist;
			}
		}

		return null;
	}

	/*
	 * Get GM approval history.
	 * 
	 */
  public function getGMApproval()
	{
		$history = $this->hasMany('App\StatusDetail', 'listing_id')->where('type', 4)->orderBy('created_at', 'DESC')->get();

		foreach ($history as $hist) {
			$created_by = $hist->createdBy;
			if ($created_by->hasRole('GM')) {
				return $hist;
			}
		}

		return null;
	}

	/*
	 * Get rotation type.
	 * 
	 */ 
	public function rotationType()
	{
		return $this->hasOne('App\RotationType', 'id', 'rotation_type_id');
	}

	/*
	 * Get contract duration.
	 * 
	 */ 
	public function contractDuration()
	{
		return $this->hasOne('App\ContractDuration', 'id', 'contract_duration_id');
	}

	/*
	 * Get report to.
	 * 
	 */ 
	public function reportTo()
	{
		return $this->hasOne('App\PositionManagement', 'id', 'report_to');
	}

}
