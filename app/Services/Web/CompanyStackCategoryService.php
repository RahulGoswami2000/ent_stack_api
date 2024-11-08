<?php

namespace App\Services\Web;

use App\Repositories\CompanyStackCategoryRepository;
use App\Services\BaseService;

class CompanyStackCategoryService extends BaseService
{
    private $projectCategoryRepository;

    public function __construct()
    {
        $this->projectCategoryRepository = new CompanyStackCategoryRepository;
    }

    public function list()
    {
        return $this->projectCategoryRepository->list();
    }

    public function store($request)
    {
        return $this->projectCategoryRepository->store($request);
    }

    public function details($id)
    {
        return $this->projectCategoryRepository->details($id);
    }

    public function update($id, $request)
    {
        return $this->projectCategoryRepository->update($id, $request);
    }

    public function delete($id)
    {
        return $this->projectCategoryRepository->delete($id);
    }

    public function updateSequence($request)
    {
        return $this->projectCategoryRepository->updateSequence($request);
    }

    public function duplicateCategory($request)
    {
        return $this->projectCategoryRepository->duplicateCategory($request);
    }

    public function bulkUpdate($request)
    {
        return $this->projectCategoryRepository->bulkUpdate($request);
    }
}
