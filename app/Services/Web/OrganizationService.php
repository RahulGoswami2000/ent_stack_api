<?php

namespace App\Services\Web;

use App\Repositories\OrganizationRepository;
use App\Services\BaseService;

class OrganizationService extends BaseService
{
    private $organizationRepository;

    public function __construct()
    {
        $this->organizationRepository = new OrganizationRepository;
    }

    public function details($id)
    {
        return $this->organizationRepository->details($id);
    }

    public function update($id, $request)
    {
        return $this->organizationRepository->update($id, $request);
    }

    public function changeLogo($id, $request)
    {
        return $this->organizationRepository->changeLogo($id, $request);
    }
}
