<?php

namespace App\Services\Web;

use App\Repositories\ScorecardStackArchiveRepository;
use App\Services\BaseService;

class ScorecardStackArchiveService extends BaseService
{
    private $scorecardStackArchiveRepository;

    public function __construct()
    {
        $this->scorecardStackArchiveRepository = new ScorecardStackArchiveRepository;
    }

    public function list($postData, $page, $perPage)
    {
        return $this->scorecardStackArchiveRepository->list($postData, $page, $perPage);
    }

    public function create($request)
    {
        return $this->scorecardStackArchiveRepository->create($request);
    }

    public function details($id,$request)
    {
        return $this->scorecardStackArchiveRepository->details($id,$request);
    }

    public function restore($id)
    {
        return $this->scorecardStackArchiveRepository->restore($id);
    }
}
