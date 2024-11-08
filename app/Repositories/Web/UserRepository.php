<?php

namespace App\Repositories\Web;

use App\Library\FunctionUtils;
use App\Models\Company;
use App\Models\CompanyMatrix;
use App\Models\CompanyStackModules;
use App\Models\LovPrivilegeGroups;
use App\Models\LovPrivileges;
use App\Models\Role;
use App\Models\ScorecardStack;
use App\Models\TeamStack;
use App\Models\User;
use App\Models\UserStackAccess;
use App\Repositories\BaseRepository;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{
    use CommonTrait;

    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function setOtp($postData)
    {
        do {
            $otp      = CommonTrait::getOtpForUser(6);
            $otpFound = \DB::table('password_resets')->where([
                'token' => $otp,
            ])->count();
        } while ($otpFound != 0);

        \DB::table('password_resets')->where([
            'email' => $postData['email'],
        ])->delete();

        \DB::table('password_resets')->insert([
            'email'      => $postData['email'],
            'token'      => $otp,
            'created_at' => \Carbon\Carbon::now(),
        ]);
        $user         = User::where('email', $postData['email'])->first();
        $templateData = [
            'user'    => $user,
            'otpLink' => env('WEB_URL') . "reset-password/" . base64_encode($otp),
            'email'   => $user->email,
        ];

        dispatch(new \App\Jobs\SendTemplateEmailJob(config('global.MAIL_TEMPLATE.FORGOT_PASSWORD'), $templateData));
    }

    /**
     * User List
     */
    public function list()
    {
        $query = \DB::table('mst_users')
            ->where('mst_users.user_type', 2)
            ->whereNull('mst_users.deleted_at');
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    /**
     * User Update
     */
    public function update($id, $postData)
    {
        $user       = $this->user->find($id);
        $updateData = [
            'first_name'   => $postData['first_name'],
            'last_name'    => (!empty($postData['last_name'])) ? $postData['last_name'] : null,
            'mobile_no'    => $postData['mobile_no'],
            'country_code' => $postData['country_code'],
            'email'        => $postData['email'],
            'start_date'   => $postData['start_date'],
            'password'     => bcrypt($postData['password']),
            'role_id'      => $postData['role_id'],
        ];
        if (!empty($postData['profile_image'])) {
            $fileName = FunctionUtils::uploadFileOnS3($postData['profile_image'], config('global.UPLOAD_PATHS.USER_PROFILE'), $user->profile_image);
            if (!empty($fileName)) {
                $updateData['profile_image'] = $fileName;
            }
        }
        $user->update($updateData);
        return $user;
    }

    /**
     * User Details
     */
    public function details($id, $companyId = null)
    {
        $me                  = $this->user->find($id);
        $user                = (object)$me;
        $companyList         = [];
        $activeCompanyList   = [];
        $inactiveCompanyList = [];

        if (!empty($user->profile_image)) {
            $user->profile_image = FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.USER_PROFILE') . $user->profile_image);
        } else {
            $user->profile_image = null;
        }
        $rolePrivilegeData = $me->role()->select(['id', 'name', 'privileges'])->first();
        if ($me->id == 1 || ($me->user_type == 1 && $me->client_assigned == 1)) {
            $companyData  = (object)[];
            $companyAllData = Company::where('id', $companyId)->where('is_active', 1)->first();
            if (!empty($companyAllData)) {
                $companyData->company_id   = $companyAllData->id;
                $companyData->company_name = $companyAllData->company_name;
                $companyData->url          = $companyAllData->website_url;
                if (!empty($companyAllData->company_logo)) {
                    $companyData->company_logo = FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.COMPANY_LOGO') . $companyAllData->company_logo);
                } else {
                    $companyData->company_logo = null;
                }
                $companyData->project        = $this->companyProjects($me, $companyAllData, false);
                $companyData->is_owner       = true;
                $companyData->is_admin       = false;
                $companyData->is_contributor = false;
                $companyData->is_viewer      = false;

                $companyList[]       = $companyData;
                $activeCompanyList[] = $companyData;
            }
        } else {
            $companyMatrixList = $me->companyMatrixList()->whereIn('is_accepted', [0, 1])->where('mst_user_company_matrix.is_active', 1)->select('mst_user_company_matrix.id', 'mst_user_company_matrix.user_id', 'mst_user_company_matrix.company_id', 'mst_user_company_matrix.role_id', 'mst_user_company_matrix.is_accepted')->leftjoin('mst_company', 'mst_company.id', '=', 'mst_user_company_matrix.company_id')->where('mst_company.is_active', 1);

            if (!empty($companyId)) {
                $companyMatrixList = $companyMatrixList->where('company_id', $companyId);
            }
            $companyMatrixList = $companyMatrixList->get();

            foreach ($companyMatrixList as $key => $companyLists) {
                $companyData               = $companyLists;
                $companyAllData            = $companyLists->company()->first();
                $companyData->company_name = $companyAllData->company_name;
                $companyData->url          = $companyAllData->website_url;
                if (!empty($companyAllData->company_logo)) {
                    $companyData->company_logo = FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.COMPANY_LOGO') . $companyAllData->company_logo);
                } else {
                    $companyData->company_logo = null;
                }
                $userAccess           = UserStackAccess::where('user_id', $user->id)->get();
                $user_access_project  = $userAccess->pluck('project_id');
                $user_access_module   = $userAccess->pluck('company_stack_modules_id');
                $user_access_category = $userAccess->pluck('company_stack_category_id');
                $companiesWiseRoles   = $companyLists->roles()->select(['id', 'name', 'privileges'])->first();
                $owner                = (!empty($companiesWiseRoles) && config('global.ROLES.OWNER') == $companiesWiseRoles->name);

                $companyData->project = $this->companyProjects($me, $companyAllData, $owner, $user_access_project, $user_access_module, $user_access_category);

                if ($me->user_type == 1) {
                    $companyData->is_owner       = true;
                    $companyData->is_admin       = false;
                    $companyData->is_contributor = false;
                    $companyData->is_viewer      = false;
                } else {
                    $companyData->is_owner       = (!empty($companiesWiseRoles) && config('global.ROLES.OWNER') == $companiesWiseRoles->name);
                    $companyData->is_admin       = (!empty($companiesWiseRoles) && config('global.ROLES.ADMIN') == $companiesWiseRoles->name);
                    $companyData->is_contributor = (!empty($companiesWiseRoles) && config('global.ROLES.CONTRIBUTOR') == $companiesWiseRoles->name);
                    $companyData->is_viewer      = (!empty($companiesWiseRoles) && config('global.ROLES.VIEWERS') == $companiesWiseRoles->name);
                }
                $companyList[] = $companyData;
                if ($companyData->is_accepted == 1) {
                    $activeCompanyList[] = $companyData;
                } else if ($companyData->is_accepted == 0) {
                    $inactiveCompanyList[] = $companyData;
                }
            }
        }

        $user->totalClientReferrals    = $me->referClient()->count();
        $user->acceptedClientReferrals = $me->referClient()->leftJoin('mst_company', 'refer_client.id', '=', 'mst_company.refer_client_id')->whereNotNull('mst_company.id')->count();
        $user->pendingClientReferrals  = $me->referClient()->leftJoin('mst_company', 'refer_client.id', '=', 'mst_company.refer_client_id')->whereNull('mst_company.id')->count();
        $user->role                    = $rolePrivilegeData;
        $user->company                 = $companyList;
        $user->activeCompany           = $activeCompanyList;
        $user->inactiveCompany         = $inactiveCompanyList;

        return $user;
    }

    public function companyProjects($me, $companyAllData, $owner, $user_access_project = [], $user_access_module = [], $user_access_category = [])
    {
        $projects = [];
        if ($owner == true || $me->user_type == 1) {
            $projectList = $companyAllData->companyProject()->select(['id', 'company_id', 'name', 'sequence'])
                ->orderBy('sequence')->get();
        } else {
            $projectList = $companyAllData->companyProject()->select(['id', 'company_id', 'name', 'sequence'])
                ->whereIn('company_projects.id', $user_access_project)
                ->orderBy('sequence')->get();
        }
        foreach ($projectList as $project) {
            if ($owner == true || $me->user_type == 1) {
                $projectAllData = $project->companyStackModule()
                    ->select(['company_stack_modules.id', 'company_stack_modules.name', 'mst_stack_modules.key', 'company_stack_modules.sequence'])
                    ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
                    ->orderBy('sequence')->get();
            } else {
                $projectAllData = $project->companyStackModule()
                    ->select(['company_stack_modules.id', 'company_stack_modules.name', 'mst_stack_modules.key', 'company_stack_modules.sequence'])
                    ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
                    ->whereIn('company_stack_modules.id', $user_access_module)
                    ->orderBy('sequence')->get();
            }
            $project->stackModule = $projectAllData;
            $projectsCategory = [];
            foreach ($projectAllData as $projectCategory) {
                if ($owner == true || $me->user_type == 1) {
                    $projectCategory->stackCategory = $projectCategory->companyStackCategory()->select(['company_stack_category.id as category_id', 'name', 'sequence', 'scorecard_stack_archive.id as archive_id', 'scorecard_stack_archive.node_id'])
                        ->leftjoin('scorecard_stack_archive', function ($on) {
                            $on->on('scorecard_stack_archive.company_stack_category_id', '=', 'company_stack_category.id');
                            $on->where('scorecard_stack_archive.node_id', NULL);
                            $on->whereNull('scorecard_stack_archive.deleted_at');
                        })
                        ->where('scorecard_stack_archive.id', NULL)
                        ->orderBy('sequence')->get();
                } else {
                    $projectCategory->stackCategory = $projectCategory->companyStackCategory()->select(['company_stack_category.id as category_id', 'name', 'sequence', 'scorecard_stack_archive.id as archive_id', 'scorecard_stack_archive.node_id'])
                        ->whereIn('company_stack_category.id', $user_access_category)
                        ->leftjoin('scorecard_stack_archive', function ($on) {
                            $on->on('scorecard_stack_archive.company_stack_category_id', '=', 'company_stack_category.id');
                            $on->where('scorecard_stack_archive.node_id', NULL);
                            $on->whereNull('scorecard_stack_archive.deleted_at');
                        })
                        ->where('scorecard_stack_archive.id', NULL)
                        ->orderBy('sequence')->get();
                }
                $projectsCategory[] = $projectCategory;
            }
            $projects[] = $project;
        }

        return $projects;
    }

    /**
     * User Status
     */
    public function changeStatus($id, $request)
    {
        $data = $this->user->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }

    public function checkOtpExists($otp)
    {
        return \DB::table('password_resets')->where([
            'token' => $otp,
        ])->first();
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function getUserByPhoneNumber($phoneNumber)
    {
        return User::where('mobile_no', $phoneNumber)->first();
    }

    public function setPassword($user, $password)
    {
        $user->update([
            'password' => bcrypt($password),
        ]);
        return $user;
    }

    public function deleteOtp($otp)
    {
        \DB::table('password_resets')->where([
            'token' => $otp,
        ])->delete();
    }

    public function getUserPermissionById($userId)
    {
        return \DB::table('user_role_matrix')
            ->leftJoin('role_permission_matrix', 'role_permission_matrix.role_id', '=', 'user_role_matrix.role_id')
            ->leftJoin('permission', 'permission.id', '=', 'role_permission_matrix.permission_id')
            ->where('user_id', $userId)
            ->whereNull('user_role_matrix.deleted_at')
            ->whereNull('role_permission_matrix.deleted_at')
            ->get(['permission.id', 'permission.permission_name'])
            ->toArray();
    }

    public function updateProfile($id, $request)
    {
        $data                = $this->user->findOrFail($id);
        $data->first_name    = $request->first_name;
        $data->last_name     = $request->last_name;
        $data->job_role      = $request->job_role;
        $data->date_of_birth = $request->date_of_birth;
        $data->start_date    = $request->start_date;
        $data->email         = $request->email;
        $data->country_code  = $request->country_code;
        $data->mobile_no     = $request->mobile_no;
        $data->save();
        return $data;
    }

    public function leaveOrganization($request)
    {
        $query = CompanyMatrix::leftjoin('mst_users as mu', 'mu.id', '=', 'mst_user_company_matrix.user_id')
            ->where('mu.role_id', '!=', 3)
            ->where('mst_user_company_matrix.id', $request)
            ->delete();

        if (empty($query)) {
            return null;
        }

        return $query;
    }

    public function profileImage($id, $request)
    {
        $data       = $this->user->findOrFail($id);
        $updateData = [];
        if (empty($data)) {
            return null;
        }

        if ($request->has('profile_image') && !empty($request->profile_image)) {
            $fileName = FunctionUtils::uploadFileOnS3($request->profile_image, config('global.UPLOAD_PATHS.USER_PROFILE'), $data->profile_image);
            if (!empty($fileName)) {
                $updateData['profile_image'] = $fileName;
            }
        } else {
            $updateData['profile_image'] = NULL;
        }
        $data->update($updateData);
        return $data;
    }

    public function assignStacks($request)
    {
        UserStackAccess::select('user_stack_access.id')
            ->where('user_stack_access.company_id', $request->company_id)
            ->where('user_stack_access.project_id', $request->project_id)
            ->where('user_stack_access.company_stack_modules_id', $request->company_stack_modules_id)
            ->where('user_stack_access.company_stack_category_id', $request->company_stack_category_id)->delete();

        return $this->assignPermissions($request->user_id, $request->company_id, $request->project_id, $request->company_stack_modules_id, $request->company_stack_category_id);
    }

    public function verifyUserChangePassword($user, $password)
    {
        $user->update([
            'password'  => bcrypt($password),
            'is_active' => 1
        ]);
        return $user;
    }

    public function checkUserIsAssociatedWithCompany($userId, $companyId)
    {
        return CompanyMatrix::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('is_accepted', 1)
            ->where('is_active', 1)->count();
    }
}
