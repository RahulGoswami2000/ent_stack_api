<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\Company;
use App\Models\CompanyMatrix;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStackAccess;

class TeamMemberRepository extends BaseRepository
{
    private Company $company;
    private User $teamMember;
    private CompanyMatrix $companyMatrix;

    public function __construct()
    {
        $this->teamMember    = new User();
        $this->company       = new Company();
        $this->companyMatrix = new CompanyMatrix();
    }

    public function list($request)
    {
        $query = \DB::table('mst_user_company_matrix')
            ->select(
                'user.id',
                'user.email',
                \DB::raw("CONCAT(user.first_name,' ',user.last_name) as name"),
                'user.job_role',
                'roles.id as role_id',
                'roles.name as display_role',
                'mst_user_company_matrix.is_accepted',
                \DB::raw("IF (user.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE'))  . "', user.profile_image)") . ', NULL) AS profile_image'),
                \DB::raw("(select count(CASE when is_accepted = '1' then 1 else NULL end) from mst_user_company_matrix where user_id = user.id) as referred"),
                \DB::raw("(select count(CASE when is_accepted = '0' then 1 else NULL end) from mst_user_company_matrix where user_id = user.id) as pending"),
                \DB::raw("(CASE when mst_user_company_matrix.role_id = '2' then true else false END) as is_owner"),
                \DB::raw("IF(user.password IS NULL AND user.is_active=0,CONCAT('" . env('WEB_URL') . "set-my-password/',user.email),NULL) as copy_link"),
            )
            ->leftjoin('mst_users as user', function ($query) {
                $query->on('user.id', '=', 'mst_user_company_matrix.user_id');
                $query->on('user.user_type', '=', \DB::raw("2"));
            })
            ->leftjoin('mst_roles as roles', 'roles.id', '=', 'mst_user_company_matrix.role_id')
            ->where('mst_user_company_matrix.company_id', $request->company_id)
            ->where('user.user_type', 2)
            ->whereNull('mst_user_company_matrix.deleted_at');

        $count = $query->count();
        $data  = $query->get()->toArray();
        return ['data' => $data, 'count' => $count];
    }

    public function create($request)
    {
        $data           = $request->all();
        $userEmailCheck = User::where('mst_users.email', $data['email'])->first();
        if (!empty($userEmailCheck)) {
            return CompanyMatrix::create([
                'user_id'     => $userEmailCheck->id,
                'company_id'  => $data['company_id'],
                'role_id'     => $data['role_id'],
                'is_accepted' => 1,
            ]);
        } else {
            $userData      = User::create([
                'email'    => $data['email'],
                'job_role' => $data['job_role'],
                'role_id'  => 5,
            ]);
            $companyMatrix = CompanyMatrix::create([
                'user_id'     => $userData->id,
                'company_id'  => $data['company_id'],
                'role_id'     => $data['role_id'],
                'is_accepted' => 1,
            ]);

            $user = ['User' => $userData, 'Company' => $companyMatrix];
        }
        $userId = !empty($userData->id) || !empty($userEmailCheck->id) ? $userData->id : $userEmailCheck->id;

        if (!empty($data['data'])) {
            UserStackAccess::select('user_stack_access.id')
                ->where('user_stack_access.company_id', $data['company_id'])
                ->where('user_stack_access.user_id', $userId)->delete();

            foreach ($data['data'] as $dataDetails) {
                $this->assignPermissions($userId, $data['company_id'], $dataDetails['project_id'], $dataDetails['company_stack_modules_id'], $dataDetails['company_stack_category_id']);
            }
        }
        $owner = Company::select('mst_company.id as company_id', 'mst_company.user_id', 'mst_users.first_name', 'mst_users.last_name', 'mst_users.email')
            ->where('mst_company.id', $data['company_id'])
            ->where('mst_company.is_active', 1)
            ->leftjoin('mst_users', 'mst_users.id', '=', 'mst_company.user_id')->first();
        $roleName = Role::select('name')->where('id', $data['role_id'])->where('is_active', 1)->first();
        $templateData = [
            'email'            => $data['email'],
            'link'             => env('WEB_URL') . 'set-my-password/' . $data['email'],
            'job_title'        => $data['job_role'],
            'job_role'         => $roleName->name,
            'owner_first_name' => $owner->first_name,
            'owner_last_name'  => $owner->last_name,
            'owner_email'      => $owner->email,
        ];

        dispatch(new \App\Jobs\SendTemplateEmailJob(config('global.MAIL_TEMPLATE.NEW_MEMBER_ADDED'), $templateData));

        return ['data' => $user];
    }

    public function detail($id)
    {
        $dataDetails = $this->teamMember->find($id);
        if (empty($dataDetails)) {
            return null;
        }

        $dataDetails->company           = $dataDetails->companyMatrixList()->first(['id', 'company_id', 'privileges']);
        $dataDetails->stackAccessRights = $dataDetails->stackAccessRights()->get(["id", "user_id", "company_id", "project_id", "company_stack_modules_id", "company_stack_category_id"]);

        return $dataDetails;
    }

    public function update($id, $request)
    {
        $data        = $request->all();
        $dataDetails = $this->teamMember->find($id);
        $dataDetails->update([
            'job_role' => $data['job_role'],
        ]);

        $companyMatrix = CompanyMatrix::where('user_id', $id)->where('company_id', $data['company_id'])->first();
        $companyMatrix->update([
            'role_id' => $data['role_id'],
        ]);

        if (!empty($data['data'])) {
            UserStackAccess::select('user_stack_access.id')
                ->where('user_stack_access.company_id', $data['company_id'])
                ->where('user_stack_access.user_id', $id)->delete();

            foreach ($data['data'] as $userStackDetails) {
                $this->assignPermissions($id, $data['company_id'], $userStackDetails['project_id'], $userStackDetails['company_stack_modules_id'], $userStackDetails['company_stack_category_id']);
            }
        } else {
            UserStackAccess::select('user_stack_access.id')
                ->where('user_stack_access.company_id', $data['company_id'])
                ->where('user_stack_access.user_id', $id)->delete();
        }

        return ['User' => $dataDetails, 'Company' => $companyMatrix];
    }

    public function companyDetails($id)
    {
        $dataDetails = $this->companyMatrix->where('id', $id)->get();

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    public function updateRole($request)
    {
        $userID = $this->companyMatrix->where('user_id', $request->user_id)->where('company_id', $request->company_id)->whereNull('deleted_at')->first();
        if (empty($userID)) {
            return null;
        }
        $userID->update([
            'role_id' => $request->role_id
        ]);
        return $userID;
    }

    public function acceptInvitation($id, $request)
    {
        $dataDetails = CompanyMatrix::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        $dataDetails->update([
            'is_accepted' => $request->is_accepted,
        ]);

        return $dataDetails;
    }

    public function companyMatrixDetails($id)
    {
        $dataDetails = $this->companyMatrix->find($id);

        if (empty($dataDetails)) {
            return null;
        }
        return $dataDetails;
    }
}
