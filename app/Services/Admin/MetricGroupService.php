<?php

namespace App\Services\Admin;

use App\Repositories\MetricGroupRepository;
use App\Services\BaseService;

class MetricGroupService extends BaseService
{
    private $metricGroupRepository;

    public function __construct()
    {
        $this->metricGroupRepository = new MetricGroupRepository;
    }

    public function list($postData, $page, $perPage)
    {
        return $this->metricGroupRepository->list($postData, $page, $perPage);
    }

    public function store($request)
    {
        return $this->metricGroupRepository->store($request);
    }

    public function update($id, $request)
    {
        return $this->metricGroupRepository->update($id, $request);
    }

    public function delete($id)
    {
        return $this->metricGroupRepository->delete($id);
    }

    public function details($id)
    {
        return $this->metricGroupRepository->details($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->metricGroupRepository->changeStatus($id, $request);
    }

    public function changeCategory($request)
    {
        return $this->metricGroupRepository->changeCategory($request);
    }

    public function addMetric($request)
    {
        return $this->metricGroupRepository->addMetric($request);
    }

    public function removeMetric($request)
    {
        return $this->metricGroupRepository->removeMetric($request);
    }

    public function metricList($id,$request)
    {
        return $this->metricGroupRepository->metricList($id,$request);
    }
}
