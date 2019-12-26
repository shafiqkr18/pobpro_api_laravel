<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /*
     * get created by for division
     */
    public function createdBy()
    {
        return $this->hasOne('App\User','id','created_by');
    }


    /**
     * Get the Organization for the division. (parent)
     */
    public function organization()
    {
        return $this->belongsTo('App\OrganizationManagement','org_id');
    }


    /**
     * Get the Departments for the division.(children)
     */
    public function departments()
    {
        return $this->hasMany('App\DepartmentManagement','div_id');
    }

	/*
	 * Get employees in division.
	 *
	 */
  public function employees()
	{
		return $this->hasMany('App\Employee', 'div_id');
	}

	/*
	 * Get positions in division.
	 *
	 */
  public function positions()
	{
		return $this->hasMany('App\PositionManagement', 'div_id');
	}

	/*Get Candidates in divisions*/
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'div_id');
    }
    public function accepted_candidates()
    {
        return $this->hasMany('App\Candidate', 'div_id')->where('offer_accepted',1);
    }
	/*
	 * Get total positions count.
	 *
	 */
  public function getPositionsCount()
	{
		$total = 0;

		if ($this->departments) {
			foreach ($this->departments as $department) {
				//$total += $department->getPositionsCountNew();
                if ($department->positions) {
                    foreach ($department->positions as $position) {
                        $total += ($position->total_positions ? $position->total_positions : 0);
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

		if ($this->departments) {
			foreach ($this->departments as $department) {
				$total += $department->getFilledPositionsCount();
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

    public function getFilledPositionsCountNew()
    {
        $total = 0;

        if ($this->departments) {
            foreach ($this->departments as $department) {
               if($department->positions)
               {
                   foreach ($department->positions as $position) {
                       foreach ($position->offers as $offer) {
                           if ($offer->accepted == 1) {
                               $total = $total + 1;
                           }
                       }
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

		if ($this->departments) {
			foreach ($this->departments as $department) {
				if ($department->positions) {
					foreach ($department->positions as $position) {
						$total += $position->local_positions ? $position->local_positions : 0;
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
  public function localPositionsCountDirect()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				$total += $position->local_positions ? $position->local_positions : 0;
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

		if ($this->departments) {
			foreach ($this->departments as $department) {
				if ($department->positions) {
					foreach ($department->positions as $position) {
						$total += $position->expat_positions ? $position->expat_positions : 0;
					}
				}
			}
		}

		return $total;
	}

	/*
	 * Get expat positions count.
	 *
	 */
  public function expatPositionsCountDirect()
	{
		$total = 0;

		if ($this->positions) {
			foreach ($this->positions as $position) {
				$total += $position->expat_positions ? $position->expat_positions : 0;
			}
		}

		return $total;
	}

	public function getFilledLocalPositionsCount()
    {
        $total = 0;
        if ($this->departments) {
            foreach ($this->departments as $department) {
                if ($department->positions) {
                    foreach ($department->positions as $position) {
                        foreach ($position->offers as $offer) {
                            if ($offer->accepted == 1 && $offer->hire_type == 1) {
                                $total = $total + 1;
                            }
                        }
                    }
                }
            }
        }
        return $total;
    }
    public function getFilledExpatPositionsCount()
    {
        $total = 0;
        if ($this->departments) {
            foreach ($this->departments as $department) {
                if ($department->positions) {
                    foreach ($department->positions as $position) {
                        foreach ($position->offers as $offer) {
                            if ($offer->accepted == 1 && $offer->hire_type == 2) {
                                $total = $total + 1;
                            }
                        }
                    }
                }
            }
        }
        return $total;
    }


	/*
	 * Get local assigned positions count.
	 *
	 */
  public function getFilledLocalPositionsCountDirect()
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
		
	/*
	 * Get expat assigned positions count.
	 *
	 */
  public function getFilledExpatPositionsCountDirect()
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


}
