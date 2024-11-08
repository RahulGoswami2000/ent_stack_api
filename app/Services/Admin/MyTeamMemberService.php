<?php

namespace App\Services\Admin;

use App\Repositories\MyTeamMemberRepository;
use App\Services\BaseService;

class MyTeamMemberService extends BaseService
{
    private $teamMemberRepository;

    public function __construct()
    {
        $this->teamMemberRepository = new MyTeamMemberRepository();
    }

    /**
     * List Team Member
     */
    public function list($postData, $page, $perPage)
    {
        return $this->teamMemberRepository->list($postData, $page, $perPage);
    }

    /**
     * Team Member Store
     */

    public function store($request)
    {

        return $this->teamMemberRepository->store($request);
    }

    /**
     * Team Member Details
     */
    public function details($request)
    {
        return $this->teamMemberRepository->details($request);
    }

    /**
     * Team Member Update
     */
    public function update($id, $request)
    {
        return $this->teamMemberRepository->update($id, $request);
    }

    /**
     * Team Member Delete
     */
    public function destory($id)
    {
        return $this->teamMemberRepository->destroy($id);
    }

    /**
     * Team Member Status Change
     */
    public function changeStatus($id, $request)
    {
        return $this->teamMemberRepository->changeStatus($id, $request);
    }


    /**
     * Client Assign Store
     */

    public function clientAssign($request)
    {
        return $this->teamMemberRepository->clientAssign($request);
    }
}
