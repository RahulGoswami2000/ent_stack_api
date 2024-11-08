<?php

namespace App\Services\Admin;

use App\Repositories\ApiIntegrationRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\BaseService;

class ApiIntegrationService extends BaseService
{
    private $ApiIntegrationRepository;
    private $subscription;

    public function __construct()
    {
        $this->ApiIntegrationRepository = new ApiIntegrationRepository();
        $this->subscription = new SubscriptionRepository;
    }

    /**
     * Registration
     */
    public function store($request)
    {
        return $this->ApiIntegrationRepository->store($request);
    }

    public function subscriptionPayment($request)
    {
        return $this->ApiIntegrationRepository->subscriptionPayment($request);
    }

    public function subscriptionList()
    {
        return $this->ApiIntegrationRepository->subscriptionList();
    }

    public function subscriptionDetail($id)
    {
        return $this->subscription->details($id);
    }
}
