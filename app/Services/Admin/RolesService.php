<?php

namespace App\Services\Admin;

use App\Repositories\RolesRepository;
use App\Services\BaseService;

class RolesService extends BaseService
{
    private $rolesRepository;

    public function __construct()
    {
        $this->rolesRepository = new RolesRepository();
    }

    /**
     * List Roles
     */
    public function list()
    {
        return $this->rolesRepository->list();
    }

    /**
     * Roles Store
     */
    public function store($request)
    {

        return $this->rolesRepository->store($request);
    }

    /**
     * Roles Details
     */
    public function details($request)
    {
        return $this->rolesRepository->details($request);
    }

    /**
     * Roles Update
     */
    public function update($id, $request)
    {
        return $this->rolesRepository->update($id, $request);
    }

    /**
     * Roles Delete
     */
    public function destory($id)
    {
        return $this->rolesRepository->destroy($id);
    }

    /**
     * Roles Status Change
     */
    public function changeStatus($id, $request)
    {
        return $this->rolesRepository->changeStatus($id, $request);
    }
}
