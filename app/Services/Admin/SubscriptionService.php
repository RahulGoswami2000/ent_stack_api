<?php

namespace App\Services\Admin;

use App\Repositories\SubscriptionRepository;
use App\Services\BaseService;

class SubscriptionService extends BaseService
{
    private $subcriptionRepository;

    public function __construct()
    {
        $this->subcriptionRepository = new SubscriptionRepository;
    }

    public function list($request)
    {
        return $this->subcriptionRepository->list($request);
    }

    public function store($request)
    {
        return $this->subcriptionRepository->store($request);
    }

    public function details($request)
    {
        return $this->subcriptionRepository->details($request);
    }

    public function update($id, $request)
    {
        return $this->subcriptionRepository->update($id, $request);
    }

    public function destory($id)
    {
        return $this->subcriptionRepository->destroy($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->subcriptionRepository->changeStatus($id, $request);
    }
}
