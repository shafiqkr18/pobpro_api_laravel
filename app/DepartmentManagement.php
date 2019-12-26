<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentManagement extends Model
{
    //
    protected $table = 'departments';
    public $primaryKey = 'id';
    public $timestamps = false;


    /*
     * get created by for department
     */
    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }


    /**
     * Get the Division for the department. (parent)
     */
    public function division()
    {
        return $this->belongsTo('App\Division','div_id');
    }


    /**
     * Get the sections for the department.(children)
     */
    public function sections()
    {
			return $this->hasMany('App\Section','dept_id')->where('deleted_at', null);
    }

		/**
     * Get the organization for the department. (parent)
     */
    public function organization()
    {
        return $this->belongsTo('App\OrganizationManagement','org_id');
    }


    public function positions()
    {
			return $this->hasMany('App\PositionManagement','department_id')->where('deleted_at', null);
    }

    public function getOrg($id)
    {
        $org = OrganizationManagement::select('id', 'org_title')
            ->where('id','=', $id)
            ->first();
        return $org ? $org->org_title : '-';
    }

/*
	 * Get employees in department.
	 *
	 */
public function employees()
{
		return $this->hasMany('App\Employee', 'dept_id');
}
    /*Get Candidates in divisions*/
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'dept_id');
    }

    public function accepted_candidates()
    {
        return $this->hasMany('App\Candidate', 'dept_id')->where('offer_accepted',1);
    }

/*
	 * Get total positions count.
	 *
	 */
public function getPositionsCount()
{
		$total = 0;

		if ($this->sections) {
			foreach ($this->sections as $section) {
				$total += $section->getPositionsCount();
			}
		}

		return $total;
}

public function getPositionsCountNew()
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
	 * Get local positions count.
	 *
	 */
  public function localPositionsCount()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				$total += $position->local_positions;
			}
		}

		return $total;
	}

	/*
	 * Get expat positions count.
	 *
	 */
  public function expatPositionsCount()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				$total += $position->expat_positions;
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

		if ($this->sections) {
			foreach ($this->sections as $section) {
				$total += $section->getFilledPositionsCount();
			}
		}

		return $total;
}

public function getFilledPositionsCountNew()
    {
        $total = 0;
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

    public function getFilledLocalPositionsCount()
    {
        $total = 0;
        if ($this->positions) {
            foreach ($this->positions as $position) {
                foreach ($position->offers as $offer) {
                    if ($offer->accepted == 1 && $offer->hire_type == 1) {
                        $total = $total + 1;
                    }
                }
            }
        }
        return $total;
    }

    public function getFilledExpatPositionsCount()
    {
        $total = 0;
        if ($this->positions) {
            foreach ($this->positions as $position) {
                foreach ($position->offers as $offer) {
                    if ($offer->accepted == 1 && $offer->hire_type == 2) {
                        $total = $total + 1;
                    }
                }
            }
        }
        return $total;
    }
	
	/**
	 * Get requested sections
	 */
	public function requestedSections()
	{
		return $this->hasMany('App\SectionRequest', 'dept_id')->where('deleted_at', null);
	}

	/**
	 * Get requested positions
	 */
	public function requestedPositions()
	{
		return $this->hasMany('App\PositionRequest', 'department_id')->where('deleted_at', null);
	}

	/**
	 * Get requested positions direct from department (without section)
	 */
	public function requestedPositionsDirect()
	{
		return $this->hasMany('App\PositionRequest', 'department_id')->where('deleted_at', null)->where('section_id', 0)->where('action_type', 0);
	}

	/*
	 * Get department approvals.
	 *
	 */
  public function departmentApprovals()
	{
		return $this->hasMany('App\DepartmentApproval', 'department_id');
	}

	/*
	 * Get company contracts.
	 *
	 */
  public function companyContracts()
	{
		return $this->hasMany('App\CompanyContract', 'department_id');
	}

}
