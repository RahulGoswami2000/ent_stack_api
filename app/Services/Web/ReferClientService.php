<?php

namespace App\Services\Web;

use App\Repositories\ReferClientRepository;
use App\Services\BaseService;

class ReferClientService extends BaseService
{
    private $ReferClientRepository;

    public function __construct()
    {
        $this->ReferClientRepository = new ReferClientRepository;
    }

    /**
     * List Refer Client
     */
    public function list($userId)
    {
        return $this->ReferClientRepository->list($userId);
    }

    /**
     * Store Refer Client
     */
    public function store($request)
    {
        return $this->ReferClientRepository->store($request);
    }

    /**
     * Details Refer Client
     */
    public function details($request)
    {
        return $this->ReferClientRepository->details($request);
    }

    /**
     * Update Refer Client
     */
    public function update($id, $request)
    {
        return $this->ReferClientRepository->update($id, $request);
    }

    /**
     * Delete Refer Client
     */
    public function destory($id)
    {
        return $this->ReferClientRepository->destroy($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->ReferClientRepository->changeStatus($id, $request);
    }
}
