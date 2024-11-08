<?php

namespace App\Repositories;

use App\Models\Template;

class TemplateRepository extends BaseRepository
{

    private Template $template;

    public function __construct()
    {
        $this->template = new Template;
    }

    /**
     * List Template
     */
    public function list($postData, $page, $perPage)
    {
        $query = \DB::table('mst_template')
            ->select(
                'mst_template.id', 'mst_template.name', 'mst_template.content', 'mst_template.api_url',
                \DB::raw("(CASE
                WHEN `mst_template`.`key`= 'FORGOT_PASSWORD' THEN '" . json_encode(config('global.MAIL_TEMPLATE.FORGOT_PASSWORD.payload')) . "'
                WHEN `mst_template`.`key`= 'METRIC_ENTRY_REMINDERS' THEN '" . json_encode(config('global.MAIL_TEMPLATE.METRIC_ENTRY_REMINDERS.payload')) . "'
                ELSE null END) AS template_key"),
                \DB::raw('IF(`mst_template`.`is_active` = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status')
            )
            ->where('mst_template.type', $postData['type'])
            ->whereNull('mst_template.deleted_at');
        if (isset($postData['name'])) {
            $query->where('name', 'Like', '%' . $postData['name'] . '%');
        }
        $orderBy     = 'mst_template.updated_at';
        $orderType   = (isset($postData['name']) && $postData['name'] == 1) ? 'asc' : 'desc';
        $query       = $query->orderBy($orderBy, $orderType);
        $dataPerPage = $query->skip($page)->take($perPage)->get()->toArray();
        $count       = $query->count();

        return ['data' => $dataPerPage, 'count' => $count];
    }

    /**
     * Details Template
     */
    public function details($id)
    {
        $dataDetails = $this->template->find($id);
        if (empty($dataDetails)) {
            return null;
        }

        $mailTemplates        = config('global.MAIL_TEMPLATE');
        $dataDetails->payload = null;

        foreach ($mailTemplates as $key => $value) {
            if ($value['key'] == $dataDetails->key) {
                $dataDetails->payload = $value['payload'];
                break;
            }
        }

        return $dataDetails;
    }

    /**
     * Update Template
     */
    public function update($id, $request)
    {
        $data = $this->template->find($id);

        $isActive = 0;
        if (!empty($request->template_url)) {
            $isActive = 1;
        }
        $data->update([
            'api_url'   => $request->template_url,
            'is_active' => $isActive,
        ]);
        return $data;
    }

    /**
     * Template Status Change
     */
    public function changeStatus($id, $request)
    {
        $data = $this->template->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }
}
