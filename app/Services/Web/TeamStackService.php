<?php

namespace App\Services\Web;

use App\Models\Company;
use App\Repositories\TeamStackRepository;
use App\Services\BaseService;

class TeamStackService extends BaseService
{
    private $teamStack;
    private $company;

    public function __construct()
    {
        $this->teamStack = new TeamStackRepository;
        $this->company = new Company;
    }
    /**
     * List Company Project
     */
    public function list()
    {
        return $this->teamStack->list();
    }
    /**
     * Store Company Project
     */
    public function store($request)
    {
        return $this->teamStack->store($request);
    }
    /**
     * Details Company Project
     */
    public function details($request)
    {
        return $this->teamStack->details($request);
    }

    /**
     * Details Company Project
     */
    public function companyDetails($request)
    {
        return $this->teamStack->companyDetails($request);
    }
    /**
     * Update Company Project
     */
    public function update($id, $request)
    {
        return $this->teamStack->update($id, $request);
    }
    /**
     * Delete Company Project
     */
    public function destory($id)
    {
        return $this->teamStack->destroy($id);
    }

    public function save($request)
    {
        return $this->teamStack->save($request);
    }
}
