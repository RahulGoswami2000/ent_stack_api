<?php

namespace App\Repositories;

use App\Http\Requests\Subscription\AddSubscriptionRequest;
use App\Models\User;
use App\Models\CompanyMatrix;
use App\Library\FunctionUtils;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;
use App\Traits\CommonTrait;
use Illuminate\Support\Str;

class MyTeamMemberRepository extends BaseRepository
{
    use CommonTrait;

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * List Team Member
     */
    public function list($postData, $page, $perPage)
    {
        $query = \DB::table('mst_users')
            ->where('mst_users.user_type', 1)
            ->whereNull('mst_users.deleted_at');

        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["first_name", "last_name", "email", "mobile_no", "job_role", "display_mobile_no", "created_by_name", "updated_by_name", "display_status"])) {
                    switch ($key) {
                        case "job_role":
                            $key = \DB::raw("IF(mst_users.job_role IS NULL, 'N/A', mst_users.job_role)");
                            break;
                        case "display_mobile_no":
                            $key = \DB::raw('CONCAT(mst_users.country_code, mst_users.mobile_no)');
                            break;
                        case "created_by_name":
                            $key = \DB::raw("CONCAT(created_by.first_name, ' ', created_by.last_name)");
                            break;
                        case "updated_by_name":
                            $key = \DB::raw("CONCAT(updated_by.first_name, ' ', updated_by.last_name)");
                            break;
                        case "display_status":
                            $key = \DB::raw('IF(mst_users.is_active=1,"' . __('labels.active') . '","' . __('labels.inactive') . '")');
                            break;
                        default:
                            $key = 'mst_users.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }

                if (in_array($key, ["id", "role_id", "client_assigned", "user_type"])) {
                    $key   = 'mst_users.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }

                if (in_array($key, ["date_of_birth", "start_date", "created_at", "updated_at"])) {
                    $key   = 'mst_users.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }

                if (in_array($key, ["is_active"])) {
                    $key   = 'mst_users.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
            }
        }

        $query = $query->leftJoin('mst_users as created_by', 'created_by.id', '=', 'mst_users.created_by')
            ->leftjoin('mst_users as updated_by', 'updated_by.id', '=', 'mst_users.updated_by')
            ->select(
                'mst_users.id',
                'mst_users.first_name',
                'mst_users.last_name',
                'mst_users.email',
                'mst_users.mobile_no',
                'mst_users.role_id',
                \DB::raw('CONCAT(mst_users.country_code, mst_users.mobile_no) AS display_mobile_no'),
                \DB::raw("IF(mst_users.job_role IS NULL, 'N/A', mst_users.job_role) AS job_role"),
                'mst_users.date_of_birth',
                'mst_users.start_date',
                'mst_users.user_type',
                \DB::raw("IF (mst_users.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE')) . "', mst_users.profile_image)") . ', NULL) AS profile_image'),
                \DB::raw('IF(`mst_users`.`is_active` = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status'),
                'mst_users.client_assigned',
                'mst_users.created_by',
                'mst_users.updated_by',
                \DB::raw("CONCAT(created_by.first_name, ' ', created_by.last_name) as created_by_name"),
                \DB::raw("CONCAT(updated_by.first_name, ' ', updated_by.last_name) as updated_by_name")
            );
        $orderBy   = 'mst_users.updated_at';
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

    /**
     * Store Team Member
     */
    public function store($request)
    {
        $storeData = [
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'mobile_no'    => $request->mobile_no,
            'country_code' => "",
            'email'        => $request->email,
            'password'     => bcrypt($request->password),
            'role_id'      => $request->role_id,
            'job_role'     => $request->job_role,
            'user_type'    => 1,
            'is_active'    => 1,
        ];

        if (!empty($request['profile_image'])) {
            $fileName = FunctionUtils::uploadFileOnS3($request['profile_image'], config('global.UPLOAD_PATHS.USER_PROFILE'));
            if (!empty($fileName)) {
                $storeData['profile_image'] = $fileName;
            }
        }

        return User::create($storeData);
    }

    /**
     * Details Team Member
     */
    public function details($id)
    {

        $dataDetails = $this->user->find($id);

        if (empty($dataDetails)) {
            return null;
        }
        $dataDetails->assigned = $dataDetails->companyMatrix()->get();

        if (!empty($dataDetails->profile_image)) {
            $dataDetails->profile_image = FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.USER_PROFILE') . $dataDetails->profile_image);
        } else {
            $dataDetails->profile_image = null;
        }

        return $dataDetails;
    }

    /**
     * Update Team Member
     */
    public function update($id, $request)
    {
        $data       = $this->user->find($id);
        $updateData = [
            'first_name'   => $request['first_name'],
            'last_name'    => $request['last_name'],
            'mobile_no'    => $request['mobile_no'],
            'country_code' => "",
            'email'        => $request['email'],
            'role_id'      => $request['role_id'],
            'job_role'     => $request['job_role'],
        ];

        if (!empty($request['profile_image'])) {
            if (Str::contains($request['profile_image'], $data->profile_image) == 1) {
            } elseif ($request->hasFile('profile_image')) {
                $fileName                    = FunctionUtils::uploadFileOnS3($request['profile_image'], config('global.UPLOAD_PATHS.USER_PROFILE'), $data->profile_image);
                $updateData['profile_image'] = $fileName;
            } else {
                $updateData['profile_image'] = null;
            }
        } else {
            $updateData['profile_image'] = null;
        }
        $data->update($updateData);
        return $data;
    }

    /**
     * Delete Team Member
     */
    public function destroy($id)
    {
        return $this->user->find($id)->delete();
    }

    /**
     * Team Member Status Change
     */
    public function changeStatus($id, $request)
    {
        $data = $this->user->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }

    /**
     * Client Assign Store
     */
    public function clientAssign($request)
    {
        $userId = $request->user_id;
        $data   = $this->user->find($userId);
        $data->update([
            'client_assigned' => $request->client_assign,
        ]);
        $clientAssign = $request->client_assign;
        $companyIds   = $request->company_id;

        CompanyMatrix::where('user_id', $request->user_id)->delete();
        if ($clientAssign == 0) {
            if (!empty($companyIds)) {
                foreach ($companyIds as $companyId) {
                    CompanyMatrix::create([
                        'user_id'     => $userId,
                        'company_id'  => $companyId,
                        'is_accepted' => 1,
                    ]);
                }
            }
        }

        return ["data" => $data];
    }
}
