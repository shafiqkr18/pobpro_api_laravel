<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    //
    /*
     * get created by for section
     */
    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }


    /**
     * Get the Departments for the section. (parent)
     */
    public function department()
    {
        return $this->belongsTo('App\DepartmentManagement','dept_id');
    }

    public function positions()
    {
        return $this->hasMany('App\PositionManagement','section_id')->where('deleted_at', null);
    }


    public function getOrg($id)
    {
        $org = OrganizationManagement::select('id', 'org_title')
            ->where('id','=', $id)
            ->first();
        return $org->org_title;
    }


    public function getDivision($id)
    {
        $org = Division::select('id', 'short_name')
            ->where('id','=', $id)
            ->first();
        return $org->short_name;
    }

	/*
	 * Get employees in section.
	 *
	 */
  public function employees()
	{
		return $this->hasMany('App\Employee', 'sec_id');
	}

    /*Get Candidates in divisions*/
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'section_id');
    }

    public function accepted_candidates()
    {
        return $this->hasMany('App\Candidate', 'section_id')->where('offer_accepted',1);
    }

	/*
	 * Get total positions count.
	 *
	 */
  public function getPositionsCount()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				$total += ($position->total_positions ? $position->total_positions : 0);
			}
		}

		return $total;
	}

	/*
	 * Get total filled positions count.
	 *
	 */
  public function getFilledPositionsCount()
	{
		$total = 0;

//		if ($this->positions) {
//			foreach ($this->positions as $position) {
//				$total += ($position->positions_filled ? $position->positions_filled : 0);
//			}
//		}
      if ($this->positions) {
          foreach ($this->positions as $position) {
              foreach ($position->offers as $offer) {
                  if ($offer->accepted == 1) {
                      $total = $total + 1;
                  }
              }
          }
      }

		return $total;
	}


    /**
     * Get the organization for the section. (parent)
     */
    public function organization()
    {
        return $this->belongsTo('App\OrganizationManagement','org_id');
    }
		
	/*
	 * Get section update request
	 */
	public function sectionUpdateRequest()
	{
		return $this->hasOne('App\SectionRequest', 'section_id')->where('action_type', '!=', 0);
	}

	/*
	 * Get position requests
	 */
	public function positionRequests()
	{
		return $this->hasMany('App\PositionRequest', 'section_id')->where('action_type', 0)->where('status', '!=', 1)->where('deleted_at', null);
	}

	/*
	 * Get section delete request
	 */
	public function sectionDeleteRequest()
	{
		return $this->hasOne('App\SectionRequest', 'section_id')->where('action_type', 2)->where('deleted_at');
	}

	/*
	 * Get position delete requests count
	 */
	public function positionDeleteRequestsCount()
	{
		$total = 0;

		if (count($this->positions) > 0) {
			foreach ($this->positions as $position) {
				if ($position->positionUpdateRequest && $position->positionUpdateRequest->action_type == 2 && $position->positionUpdateRequest->deleted_at == null) {
					$total += 1;
				}
			}
		}
		
		return $total;
	}

}
