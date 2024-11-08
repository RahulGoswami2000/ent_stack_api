<?php

namespace App\Repositories;

use App\Models\ReferClient;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Request;

class ReferClientRepository extends BaseRepository
{
    private ReferClient $referClient;

    public function __construct()
    {
        $this->referClient = new ReferClient;
    }

    /**
     * List Refer Client
     */
    public function list($userId)
    {
        $query = \DB::table('refer_client')
            ->leftJoin('mst_company', 'refer_client.id', '=', 'mst_company.refer_client_id')
            ->leftJoin('mst_users', 'refer_client.created_by', '=', 'mst_users.id')
            ->select(
                'refer_client.id',
                'refer_client.first_name',
                'refer_client.last_name',
                'refer_client.email',
                'refer_client.referal_code',
                'refer_client.is_active',
                'refer_client.is_referred',
                \DB::raw('IF(refer_client.is_active = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status'),
                \DB::raw('(CASE 
                WHEN refer_client.is_referred = 0 THEN "' . __('labels.pending') . '"
                WHEN refer_client.is_referred = 1 THEN "' . __('labels.referred') . '"
                WHEN refer_client.is_referred = 2 THEN "' . __('labels.cancelled') . '"
                END ) AS referrals_status'),
                \DB::raw('IF(mst_company.id IS NULL, CONCAT("https://example.com/registration?byFName=",mst_users.first_name,"&byLName=",mst_users.last_name,"&byEmail=",mst_users.email,"&toFName=",refer_client.first_name,"&toLName=",refer_client.last_name,"&toEmail=",refer_client.email,"&refCode=",refer_client.referal_code), NULL) AS copy_link'),
            )
            ->whereNull('refer_client.deleted_at')
            ->where('refer_client.is_active', 1)
            ->where('refer_client.created_by', $userId)
            ->orderBy('refer_client.updated_at', 'DESC');
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    /**
     * Store Refer Client
     */
    public function store($request)
    {
        do {
            $referralCode      = CommonTrait::getOtpForUser(8);
            $referralCodeFound = \DB::table('password_resets')->where([
                'token' => $referralCode,
            ])->count();
        } while ($referralCodeFound != 0);

        return ReferClient::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'referal_code' => $referralCode,
        ]);
    }

    /**
     * Details Refer Client
     */
    public function details($id)
    {
        $dataDetails = $this->referClient->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    /**
     * Update Refer Client
     */
    public function update($id, $request)
    {
        $data = $this->referClient->find($id);
        $data->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);
        return $data;
    }

    /**
     * Delete Refer Client
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
