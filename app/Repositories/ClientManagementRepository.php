<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\Company;
use App\Models\CompanyMatrix;
use App\Models\User;
use App\Services\Admin\UserService;
use App\Traits\CommonTrait;

class ClientManagementRepository extends BaseRepository
{
    private $user, $userDetail;
    use CommonTrait;

    public function __construct()
    {
        $this->user       = new User();
        $this->userDetail = new UserService;
    }

    public function list($listData, $page = 1, $perPage = 10)
    {
        $query = \DB::table('mst_company')
            ->select(
                'user.id',
                \DB::raw("CONCAT(user.first_name,' ',user.last_name) as name"),
                'user.email',
                \DB::raw('IF(mst_company.company_name IS NULL, "N/A", mst_company.company_name) as company_name'),
                'user.mobile_no',
                'user.created_at',
                'user.is_active',
                'mst_company.id as company_id',
                \DB::raw('CONCAT(user.country_code, user.mobile_no) AS display_mobile_no'),
                \DB::raw('CONCAT(user.country_code, user.mobile_no) AS view_as_url'),
                \DB::raw("CONCAT('" . env('WEB_URL') . "login-as-company?email=" . \Auth::user()->email . "','&company=',mst_company.id) as view_as_url"),
                \DB::raw('IF(user.is_active = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status')
            )
            ->leftjoin('mst_users as user', 'user.id', '=', 'mst_company.user_id')
            ->whereNull('mst_company.deleted_at')
            ->whereNull('user.deleted_at');

        if (\Auth::user()->id == 1 || \Auth::user()->client_assigned == 1) {
        } else {
            $query = $query->leftjoin('mst_user_company_matrix', 'mst_company.id', '=', 'mst_user_company_matrix.company_id')
                ->whereNull('mst_user_company_matrix.deleted_at')
                ->where('mst_user_company_matrix.user_id', \Auth::user()->id);
        }

        if (!empty($listData['filter_data'])) {
            foreach ($listData['filter_data'] as $key => $value) {
                if (in_array($key, ["name", "email", "mobile_no", "display_mobile_no"])) {
                    switch ($key) {
                        case "name":
                            $key = \DB::raw("CONCAT(user.first_name,' ',user.last_name)");
                            break;
                        case "display_mobile_no":
                            $key = \DB::raw('CONCAT(user.country_code, user.mobile_no)');
                            break;
                        default:
                            $key = 'user.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["id"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["company_name"])) {
                    $key   = 'mst_company.' . $key;
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
                if (in_array($key, ["created_at"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'user.updated_at';
        $orderType = (isset($listData['order_by']) && $listData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($listData['sort_data'])) {
            $orderBy   = $listData['sort_data'][0]['col_id'];
            $orderType = $listData['sort_data'][0]['sort'];
        }

        $query       = $query->orderBy($orderBy, $orderType);
        $count       = $query->count();
        $dataPerPage = $query->skip($page)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function details($id)
    {
        $dataDetails = $this->user->find($id);
        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    public function update($id, $request)
    {
        $data = $this->user->find($id);
        $data->update([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'mobile_no'    => $request->mobile_no,
            'country_code' => "",
        ]);
        return $data;
    }

    public function changeStatus($id, $request)
    {
        $data = Company::where('user_id', $id)->first();
        if (empty($data)) {
            return null;
        }
        $data->update([
            'is_active' => $request->is_active,
        ]);

        User::where('id', $id)->update([
            'is_active' => $request->is_active,
        ]);
        $clientCompanyId = \DB::table('mst_user_company_matrix')->where('company_id', $request->id)->whereNull('deleted_at')->pluck('company_id')->toArray();
        if (!empty($clientCompanyId)) {
            \DB::table('mst_user_company_matrix')
                ->where('company_id', $request->id)
                ->whereNull('deleted_at')
                ->update(['is_active' => $request->is_active]);
        }
        return $data;
    }

    public function users($id, $listData, $page = 1, $perPage = 100)
    {
        \DB::enableQueryLog();
        $query = \DB::table('mst_user_company_matrix')
            ->select(
                'user.id',
                'user.email',
                'mst_user_company_matrix.company_id',
                \DB::raw("IF(CONCAT(user.first_name,' ',user.last_name) IS NULL, 'N/A', CONCAT(user.first_name,' ',user.last_name)) as name"),
                'user.job_role',
                'roles.id as role_id',
                'mst_user_company_matrix.is_active',
                'roles.name as display_role',
                'mst_user_company_matrix.is_accepted',
                'user.mobile_no',
                \DB::raw('CONCAT(user.country_code, user.mobile_no) AS display_mobile_no'),
                \DB::raw("IF (user.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE')) . "', user.profile_image)") . ', NULL) AS profile_image'),
                \DB::raw("(select count(CASE when is_accepted = '1' then 1 else NULL end) from mst_user_company_matrix where user_id = user.id) as referred"),
                \DB::raw("(select count(CASE when is_accepted = '0' then 1 else NULL end) from mst_user_company_matrix where user_id = user.id) as pending"),
                \DB::raw("(CASE when roles.name = '" . config('global.ROLES.OWNER') . "' then true else false END) as is_owner"),
                \DB::raw("IF(user.password IS NULL AND user.is_active=0,CONCAT('" . env('WEB_URL') . "set-my-password/',user.email),NULL) as copy_link"),
            )
            ->leftjoin('mst_users as user', function ($query) {
                $query->on('user.id', '=', 'mst_user_company_matrix.user_id');
                $query->on('user.user_type', '=', \DB::raw("2"));
            })
            ->leftjoin('mst_roles as roles', 'roles.id', '=', 'mst_user_company_matrix.role_id')
            ->where('mst_user_company_matrix.company_id', $id)
            ->where('user.user_type', 2)
            ->whereNull('mst_user_company_matrix.deleted_at');

        if (!empty($listData['filter_data'])) {
            foreach ($listData['filter_data'] as $key => $value) {
                if (in_array($key, ["name", "email", "mobile_no", "display_mobile_no"])) {
                    switch ($key) {
                        case "name":
                            $key = \DB::raw("IF(CONCAT(user.first_name,' ',user.last_name) IS NULL, 'N/A', CONCAT(user.first_name,' ',user.last_name))");
                            break;
                        case "display_mobile_no":
                            $key = \DB::raw('CONCAT(user.country_code, user.mobile_no)');
                            break;
                        default:
                            $key = 'user.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["id"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
                if (in_array($key, ["created_at"])) {
                    $key   = 'user.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'user.updated_at';
        $orderType = (isset($listData['order_by']) && $listData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($listData['sort_data'])) {
            $orderBy   = $listData['sort_data'][0]['col_id'];
            $orderType = $listData['sort_data'][0]['sort'];
        }

        $query       = $query->orderBy($orderBy, $orderType);
        $count = $query->count();
        $data  = $query->skip($page - 1)->take($perPage)->get()->toArray();

        $companyDetails = $this->companyDetails($id);
        return ['data' => $data, 'count' => $count, 'companyDetails' => $companyDetails];
    }

    public function userChangeStatus($id, $request)
    {
        $query = CompanyMatrix::where('user_id', $id)
            ->where('company_id', $request->company_id)->get();
        if (empty($query)) {
            return null;
        }
        foreach ($query as $dataDetails) {
            $dataDetails->update([
                'is_active' => $request->is_active,
            ]);
            return $dataDetails;
        }
    }

    public function companyDetails($companyId)
    {
        return \DB::table('mst_company')
            ->select(
                'mst_company.id',
                \DB::raw('IF(mst_company.company_name IS NULL, "N/A", mst_company.company_name) as company_name'),
                \DB::raw("IF (mst_company.company_logo IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.COMPANY_LOGO')) . "', mst_company.company_logo)") . ', NULL) AS company_logo'),
                'mst_company.website_url'
            )
            ->where('mst_company.id', $companyId)
            ->whereNull('mst_company.deleted_at')->first();
    }
}
