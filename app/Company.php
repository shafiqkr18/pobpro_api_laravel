<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $table = 'companies';
	public $primaryKey = 'id';
	public $timestamps = false;

	/*
	 * Get modules
	 */
	public function modules()
	{
		return $this->hasMany('App\CompanyModule', 'company_id');
	}

	/*
	 * Get user
	 */
	public function user()
	{
		return $this->hasOne('App\User', 'id', 'ent_admin_id');
	}

    public function company_users()
    {
        return $this->hasMany('App\User', 'company_id');
    }

	/*
	 * Get organization
	 */
	public function organization()
	{
		return $this->hasOne('App\OrganizationManagement', 'company_id', 'id');
	}

	/*
	 * Get total positions count.
	 * 
	 */
  public function getPositionsCount()
	{
		$total = 0;

		if ($this->organization) {
			$organization = $this->organization;

			if ($organization->divisions) {
				foreach ($organization->divisions as $division) {
					foreach ($division->departments as $department) {
						$total += $department->getPositionsCount();
					}
				}
			}
		}

		return $total;
	}

	/*
	 * Get total positions count.
	 * 
	 */
  public function getPositionsCountDirect()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				if ($position->deleted_at == null) {
					$total += $position->total_positions;
				}
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

		if ($this->organization) {
			$organization = $this->organization;

			if ($organization->divisions) {
				foreach ($organization->divisions as $division) {
					foreach ($division->departments as $department) {
						$total += $department->getFilledPositionsCount();
					}
				}
			}
		}

		return $total;
	}

	/*
	 * Get total filled positions count.
	 * 
	 */
  public function getFilledPositionsCountDirect()
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

	/*
	 * Get positions.
	 *
	 */
  public function positions()
	{
		return $this->hasMany('App\PositionManagement', 'company_id')->where('deleted_at', null);
	}

	/*
	 * Get positions direct from company.
	 *
	 */
  public function positionsDirectFromCompany()
	{
		return $this->hasMany('App\PositionManagement', 'company_id')
								->where('deleted_at', null)
								->where(function ($query) {
									$query->where('div_id', 0)
												->orWhere('div_id', null);
								})
								->where(function ($query) {
									$query->where('department_id', 0)
												->orWhere('department_id', null);
								})
								->where(function ($query) {
									$query->where('section_id', 0)
												->orWhere('section_id', null);
								});
	}

	/*
	 * Get filled positions direct from company count.
	 *
	 */
  public function filledPositionsDirectFromCompanyCount()
	{
		$total = 0;

		if ($this->positionsDirectFromCompany) {
			foreach ($this->positionsDirectFromCompany as $position) {
				foreach ($position->offers as $offer) {
					if ($offer->accepted == 1) {
						$total = $total + 1;
					}
				}
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
                if ($position->company_id == $this->id && $position->deleted_at == null) {
                    $total += $position->local_positions;
                }
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
                if ($position->company_id == $this->id && $position->deleted_at == null) {
                    $total += $position->expat_positions;
                }
			}
		}

		return $total;
	}

	/*
	 * Get expat offers.
	 *
	 */
  public function expatOffers()
	{
		return $this->hasMany('App\Offer', 'company_id')->where('type', 0)->where('hire_type', 2);
	}

	/*
	 * Get local accepted offers.
	 *
	 */
  public function localAcceptedOffers()
	{
		return $this->hasMany('App\Offer', 'company_id')->where('type', 0)->where('accepted', 1)->where('hire_type', 1);
	}

	/*
	 * Get expat accepted offers.
	 *
	 */
  public function expatAcceptedOffers()
	{
		return $this->hasMany('App\Offer', 'company_id')->where('type', 0)->where('accepted', 1)->where('hire_type', 2);
	}

	/*
	 * Get department approvals.
	 *
	 */
  public function departmentApprovals()
	{
		return $this->hasMany('App\DepartmentApproval', 'company_id')->where('deleted_at', null)->where('status', 0)->groupBy('department_id');
	}

}
