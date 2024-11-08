<?php

namespace App\Services\Admin;

use App\Repositories\TemplateRepository;
use App\Services\BaseService;

class TemplateService extends BaseService
{
    private $templateRepository;

    public function __construct()
    {
        $this->templateRepository = new TemplateRepository();
    }

    /**
     * List Template
     */
    public function list($postData, $page, $perPage)
    {
        return $this->templateRepository->list($postData, $page, $perPage);
    }

    /**
     * Template Store
     */
    public function store($request)
    {

        return $this->templateRepository->store($request);
    }

    /**
     * Template Details
     */
    public function details($request)
    {
        return $this->templateRepository->details($request);
    }

    /**
     * Template Update
     */
    public function update($id, $request)
    {
        return $this->templateRepository->update($id, $request);
    }

    /**
     * Template Delete
     */
    public function destory($id)
    {
        return $this->templateRepository->destroy($id);
    }

    /**
     * Template Status Change
     */
    public function changeStatus($id, $request)
    {
        return $this->templateRepository->changeStatus($id, $request);
    }
}
