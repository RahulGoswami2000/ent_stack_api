<?php

namespace App\Services\Admin;

use App\Repositories\MetricCategoryRepository;
use App\Services\BaseService;

class MetricCategoryService extends BaseService
{
    private $metricCategoryRepository;

    public function __construct()
    {
        $this->metricCategoryRepository = new MetricCategoryRepository;
    }

    public function list($postData, $page, $perPage)
    {
        return $this->metricCategoryRepository->list($postData, $page, $perPage);
    }

    public function store($request)
    {
        return $this->metricCategoryRepository->store($request);
    }

    public function update($id, $request)
    {
        return $this->metricCategoryRepository->update($id, $request);
    }

    public function delete($id)
    {
        return $this->metricCategoryRepository->destroy($id);
    }

    public function removeCategory($id, $request)
    {
        return $this->metricCategoryRepository->removeCategory($id, $request);
    }

    public function addMetric($id, $request)
    {
        return $this->metricCategoryRepository->addMetric($id, $request);
    }

    public function details($id)
    {
        return $this->metricCategoryRepository->details($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->metricCategoryRepository->changeStatus($id, $request);
    }

    public function checkMetricExists($id)
    {
        return $this->metricCategoryRepository->checkMetricExists($id);
    }
}
