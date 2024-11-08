<?php

namespace App\Services\Admin;

use App\Repositories\ReferralsRepository;
use App\Services\BaseService;

class ReferralsService extends BaseService
{
    private ReferralsRepository $referralsRepository;

    public function __construct()
    {
        $this->referralsRepository = new ReferralsRepository();
    }

    /**
     * List Referrals
     */
    public function list($postData, $page, $perPage)
    {
        return $this->referralsRepository->list($postData, $page, $perPage);
    }

    public function referralAccept($request)
    {
        return $this->referralsRepository->referralAccept($request);
    }

    /**
     * Details Referrals
     */
    public function details($request)
    {
        return $this->referralsRepository->details($request);
    }
    /**
     * Delete Referrals
     */
    public function destory($id)
    {
        return $this->referralsRepository->destroy($id);
    }

    public function changeStatus($id, $request)
    {
        return $this->referralsRepository->changeStatus($id, $request);
    }
}
