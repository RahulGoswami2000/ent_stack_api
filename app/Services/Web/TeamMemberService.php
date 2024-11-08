<?php

namespace App\Services\Web;

use App\Repositories\TeamMemberRepository;
use App\Services\BaseService;

class TeamMemberService extends BaseService
{
    private $teamMemberRepository;

    public function __construct()
    {
        $this->teamMemberRepository = new TeamMemberRepository;
    }

    public function list($request)
    {
        return $this->teamMemberRepository->list($request);
    }

    public function create($request)
    {
        return $this->teamMemberRepository->create($request);
    }

    public function details($id)
    {
        return $this->teamMemberRepository->detail($id);
    }

    public function update($id, $request)
    {
        return $this->teamMemberRepository->update($id, $request);
    }

    public function companyDetails($id)
    {
        return $this->teamMemberRepository->companyDetails($id);
    }

    public function updateRole($request)
    {
        return $this->teamMemberRepository->updateRole($request);
    }

    public function acceptInvitation($id, $request)
    {
        return $this->teamMemberRepository->acceptInvitation($id, $request);
    }

    public function companyMatrixDetails($id)
    {
        return $this->teamMemberRepository->companyMatrixDetails($id);
    }
}
