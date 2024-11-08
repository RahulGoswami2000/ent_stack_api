<?php

namespace App\Services\Web;

use App\Repositories\GoalStackRepository;
use App\Services\BaseService;

class GoalStackService extends BaseService
{
    private $goalStackRepository;

    public function __construct()
    {
        $this->goalStackRepository = new GoalStackRepository;
    }

    public function list()
    {
        return $this->goalStackRepository->list();
    }

    public function details($request)
    {
        return $this->goalStackRepository->details($request);
    }

    public function destroy($id)
    {
        return $this->goalStackRepository->delete($id);
    }

    public function save($request)
    {
        return $this->goalStackRepository->save($request);
    }
}
