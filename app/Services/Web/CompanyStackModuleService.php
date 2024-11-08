<?php

namespace App\Services\Web;

use App\Repositories\CompanyStackModuleRepository;
use App\Services\BaseService;

class CompanyStackModuleService extends BaseService
{
    private $companyStackModuleRepository;

    public function __construct()
    {
        $this->companyStackModuleRepository = new CompanyStackModuleRepository;
    }
    /**
     * Store Company Stack Module
     */
    public function store($request)
    {
        return $this->companyStackModuleRepository->store($request);
    }
    /**
     * Details Company Stack Module
     */
    public function details($id)
    {
        return $this->companyStackModuleRepository->detail($id);
    }
    /**
     * Update Company Stack Module
     */
    public function update($id, $request)
    {
        return $this->companyStackModuleRepository->update($id, $request);
    }
    /**
     * Delete Company Stack Module
     */
    public function destory($id)
    {
        return $this->companyStackModuleRepository->destroy($id);
    }
    /**
     * Change Status Company Stack Module
     */
    public function changeStatus($id, $request)
    {
        return $this->companyStackModuleRepository->changeStatus($id, $request);
    }

    public function bulkUpdate($request)
    {
        return  $this->companyStackModuleRepository->bulkUpdate($request);
    }

    public function duplicateStack($request)
    {
        return $this->companyStackModuleRepository->duplicateStack($request);
    }
}
