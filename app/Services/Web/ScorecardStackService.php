<?php

namespace App\Services\Web;

use App\Repositories\ScorecardStackRepository;
use App\Services\BaseService;

class ScorecardStackService extends BaseService
{
    private $ScorecardStackRepository;

    public function __construct()
    {
        $this->ScorecardStackRepository = new ScorecardStackRepository;
    }
    /**
     * List Scorecard Stack
     */
    public function list()
    {
        return $this->ScorecardStackRepository->list();
    }
    /**
     * Store Scorecard Stack
     */
    public function store($request)
    {
        return $this->ScorecardStackRepository->store($request);
    }
    /**
     * Details Scorecard Stack
     */
    public function details($request)
    {
        return $this->ScorecardStackRepository->details($request);
    }
    /**
     * Update Scorecard Stack
     */
    public function update($id, $request)
    {
        return $this->ScorecardStackRepository->update($id, $request);
    }
    /**
     * Delete Scorecard Stack
     */
    public function destory($id)
    {
        return $this->ScorecardStackRepository->destroy($id);
    }
    /**
     * Change Status Scorecard Stack
     */
    public function changeStatus($id, $request)
    {
        return $this->ScorecardStackRepository->changeStatus($id, $request);
    }

    public function save($request)
    {
        return $this->ScorecardStackRepository->save($request);
    }

    public function nodeEntry($request)
    {
        return $this->ScorecardStackRepository->nodeEntry($request);
    }

    public function changeAssignedColor($request)
    {
        return $this->ScorecardStackRepository->changeAssignedColor($request);
    }

    public function updateScorecardNodeData($request)
    {
        return $this->ScorecardStackRepository->updateScorecardNodeData($request);
    }
}
