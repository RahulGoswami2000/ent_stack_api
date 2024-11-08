<?php

namespace App\Services;

use App\Repositories\CommonRepository;

class CommonService extends BaseService
{
    private $commonRepository;

    public function __construct()
    {
        $this->commonRepository = new CommonRepository;
    }

    public function metric($request)
    {
        return $this->commonRepository->metric($request);
    }

    public function metricGroup($request)
    {
        return $this->commonRepository->metricGroup($request);
    }

    public function category($request)
    {
        return $this->commonRepository->category($request);
    }

    public function roles($request)
    {
        return $this->commonRepository->roles($request);
    }

    public function user($request, $page, $perPage)
    {
        return $this->commonRepository->user($request, $page, $perPage);
    }

    public function privilegesList($request)
    {
        return $this->commonRepository->privilegesList($request);
    }

    public function companyList($request)
    {
        return $this->commonRepository->companyList($request);
    }

    public function webUserList($request)
    {
        return $this->commonRepository->webUserList($request);
    }

    public function stackModuleList()
    {
        return $this->commonRepository->stackModuleList();
    }

    public function metricAndMetricGroupList($request)
    {
        return array_merge($this->commonRepository->metricAndMetricGroupList($request),$this->commonRepository->metric($request)['data']);
    }
}
