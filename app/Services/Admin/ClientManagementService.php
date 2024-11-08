<?php

namespace App\Services\Admin;

use App\Repositories\ClientManagementRepository;
use App\Services\BaseService;

class ClientManagementService extends BaseService
{
    private $clientManangementRepository;

    public function __construct()
    {
        $this->clientManangementRepository = new ClientManagementRepository;
    }

    public function list($postData, $page, $perPage)
    {
        return $this->clientManangementRepository->list($postData, $page, $perPage);
    }

    public function details($id)
    {
        return $this->clientManangementRepository->details($id);
    }

    public function update($id, $request)
    {
        return $this->clientManangementRepository->update($id, $request);
    }

    public function changeStatus($id, $request)
    {
        return $this->clientManangementRepository->changeStatus($id, $request);
    }

    public function users($id, $request, $page, $perPage)
    {
        return $this->clientManangementRepository->users($id, $request, $page, $perPage);
    }

    public function userChangeStatus($id, $request)
    {
        return $this->clientManangementRepository->userChangeStatus($id, $request);
    }
}
