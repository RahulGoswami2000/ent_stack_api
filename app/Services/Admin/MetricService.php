<?php

namespace App\Services\Admin;

use App\Repositories\MetricRepository;
use App\Services\BaseService;

class MetricService extends BaseService
{
    private $metricRepository;

    public function __construct()
    {
        $this->metricRepository = new MetricRepository;
    }

    public function list($postData, $page, $perPage, $accessType)
    {
        return $this->metricRepository->list($postData, $page, $perPage, $accessType);
    }

    public function store($request)
    {
        return $this->metricRepository->store($request);
    }

    public function update($id, $request)
    {
        return $this->metricRepository->update($id, $request);
    }

    public function delete($id)
    {
        return $this->metricRepository->delete($id);
    }

    public function details($id)
    {
        return $this->metricRepository->details($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->metricRepository->changeStatus($id, $request);
    }

    public function checkMetric($id)
    {
        return $this->metricRepository->checkMetric($id);
    }
}
