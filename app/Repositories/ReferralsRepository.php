<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\CompanyMatrix;
use App\Models\ReferClient;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;
use App\Traits\CommonTrait;

class ReferralsRepository extends BaseRepository
{
    use CommonTrait;

    private ReferClient $referClient;

    public function __construct()
    {
        $this->referClient = new ReferClient();
    }

    /**
     * List Referrals
     */
    public function list($postData, $page, $perPage)
    {
        $query = \DB::table('refer_client')
            ->select(
                'refer_client.id',
                \DB::raw("CONCAT(referring_user.first_name,' ',referring_user.last_name) as referring_name"),
                'refer_client.is_active',
                'refer_client.is_referred',
                \DB::raw('IF(refer_client.is_active = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status'),
                \DB::raw('(CASE 
                WHEN refer_client.is_referred = 0 THEN "' . __('labels.pending') . '"
                WHEN refer_client.is_referred = 1 THEN "' . __('labels.referred') . '"
                WHEN refer_client.is_referred = 2 THEN "' . __('labels.cancelled') . '"
                END ) AS referrals_status'),

                \DB::raw('IF(referred.company_name IS NULL, "N/A", referred.company_name) as referred_company'),
                \DB::raw("CONCAT(refer_client.first_name,' ',refer_client.last_name) as referred_name"),
                'refer_client.email as referred_email',
                'refer_client.created_at'
            )
            ->leftjoin('mst_users AS referring_user', 'refer_client.created_by', '=', 'referring_user.id')
            ->leftjoin('mst_company AS referred', 'refer_client.id', '=', 'referred.refer_client_id')

            ->whereNull('refer_client.deleted_at');

        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["referring_name", "referrals_status", "referred_company", "referred_name", "referred_email"])) {
                    switch ($key) {
                        case "referring_name":
                            $key = \DB::raw("CONCAT(referring_user.first_name,' ',referring_user.last_name)");
                            break;
                        case "referrals_status":
                            $key = \DB::raw('(CASE 
                            WHEN refer_client.is_referred = 0 THEN "' . __('labels.pending') . '"
                            WHEN refer_client.is_referred = 1 THEN "' . __('labels.referred') . '"
                            WHEN refer_client.is_referred = 2 THEN "' . __('labels.cancelled') . '"
                            ) AS referrals_status');
                            break;
                        case "referred_company":
                            $key = \DB::raw('IF(referred.company_name IS NULL, "N/A", referred.company_name)');
                            break;
                        case "referred_name":
                            $key = \DB::raw("CONCAT(refer_client.first_name,' ',refer_client.last_name)");
                            break;
                        case "referred_email":
                            $key = 'refer_client.email';
                            break;
                        default:
                            $key = 'refer_client.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }

                if (in_array($key, ["created_at"])) {
                    $key   = 'refer_client.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'refer_client.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'refer_client.updated_at';
        $orderType = (isset($postData['order_by']) && $postData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($postData['sort_data'])) {
            $orderBy   = $postData['sort_data'][0]['col_id'];
            $orderType = $postData['sort_data'][0]['sort'];
        }
        $query       = $query->orderBy($orderBy, $orderType);
        $count       = $query->count();
        $dataPerPage = $query->skip($page)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function referralAccept($request)
    {
        $data = ReferClient::where('id', $request->id)
            ->whereNull('refer_client.deleted_at')->first();

        if (empty($data)) {
            return null;
        }
        $data->update([
            'is_referred' => $request->is_referred,
        ]);

        return $data;
    }
    /**
     * Details Referrals
     */
    public function details($id)
    {
        $dataDetails = $this->referClient->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        $dataDetails->status = empty($dataDetails->company()->count()) ? 0 : 1;

        return $dataDetails;
    }

    /**
     * Delete Referrals
     */
    public function destroy($id)
    {
        return $this->referClient->find($id)->delete();
    }

    public function changeStatus($id, $request)
    {
        $data = ReferClient::where('id', $id)
            ->whereNull('refer_client.deleted_at')->first();

        if (empty($data)) {
            return null;
        }
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }
}
