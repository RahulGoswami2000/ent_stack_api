<?php

namespace App\Services\Web;

use App\Repositories\CompanyProjectRepository;
use App\Services\BaseService;

class CompanyProjectService extends BaseService
{
    private $CompanyProjectRepository;

    public function __construct()
    {
        $this->CompanyProjectRepository = new CompanyProjectRepository;
    }
    /**
     * List Company Project
     */
    public function list()
    {
        return $this->CompanyProjectRepository->list();
    }
    /**
     * Store Company Project
     */
    public function store($request)
    {
        return $this->CompanyProjectRepository->store($request);
    }
    /**
     * Details Company Project
     */
    public function details($request)
    {
        return $this->CompanyProjectRepository->details($request);
    }
    /**
     * Update Company Project
     */
    public function update($id, $request)
    {
        return $this->CompanyProjectRepository->update($id, $request);
    }
    /**
     * Delete Company Project
     */
    public function destory($id)
    {
        return $this->CompanyProjectRepository->destroy($id);
    }
    /**
     * Change Status Company Project
     */
    public function changeStatus($id, $request)
    {
        return $this->CompanyProjectRepository->changeStatus($id, $request);
    }

    public function bulkUpdate($request)
    {
        return $this->CompanyProjectRepository->bulkUpdate($request);
    }
}
