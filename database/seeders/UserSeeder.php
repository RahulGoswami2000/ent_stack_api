<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyMatrix;
use App\Models\LovPrivileges;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Company::truncate();
        CompanyMatrix::truncate();

        $roleIds = LovPrivileges::all()->pluck('id')->toArray() ?? [];
        User::updateOrCreate([
            'id' => 1
        ], [
            'first_name'    => 'Admin',
            'last_name'     => 'User',
            'email'         => 'admin@es.com',
            'mobile_no'     => 11111111,
            'password'      => bcrypt('Test@1234'),
            'role_id'       => 1,
            'user_type'     => 1,
            'is_active'     => 1,
            'date_of_birth' => Carbon::create('2000', '01', '01'),
        ]);

        /*Role Id */
        $superAdminId  = \DB::table('mst_roles')->where('name', config('global.ROLES.SUPER_ADMIN'))->first()->id;
        $adminId       = \DB::table('mst_roles')->where('name', config('global.ROLES.ADMIN'))->first()->id;
        $ownerId       = \DB::table('mst_roles')->where('name', config('global.ROLES.OWNER'))->first()->id;
        $viewerId      = \DB::table('mst_roles')->where('name', config('global.ROLES.VIEWERS'))->first()->id;
        $contributorId = \DB::table('mst_roles')->where('name', config('global.ROLES.CONTRIBUTOR'))->first()->id;

        $webId         = null;
        $companyNameId = 1;
        $companyId = null;
        for ($i = 1; $i <= 99; $i++) {
            $mobileNumber = "";
            if ($i > 9 && $i < 100) {
                $mobileNumber = '123456' . $i;
            } else if ($i < 10) {
                $mobileNumber = '1234567' . $i;
            } else if ($i == 100) {
                $mobileNumber = '12345' . $i;
            }

            if ($i < 50) {
                /* for AdminUsers */
                $email     = "user" . $i . "@es.com";
                $firstName = "User" . $i;
                $lastName  = "Name" . $i;
                $userType  = 1;
            } else {
                /* for WebUsers */
                $email     = "web" . $webId . "@es.com";
                $firstName = "Web" . $webId;
                $lastName  = "Name" . $webId;
                $userType  = 2;

                $webId++;
            }

            $user   = User::updateOrCreate([
                'id' => $i + 1
            ], [
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'email'         => $email,
                'password'      => bcrypt('Test@1234'),
                'mobile_no'     => $mobileNumber,
                'user_type'     => $userType,
                'role_id'       => (($i + 1) == 51 || ($i + 1) == 61 || ($i + 1) == 71 || ($i + 1) == 81 || ($i + 1) == 91) ? $ownerId : (($i + 1) <= 50 ? $superAdminId : $viewerId),
                'is_active'     => 1,
                'date_of_birth' => Carbon::create('2000', '01', '01'),
            ]);
            $userId = $user->id;

            if ($userId <= 50) {
                $roleId = $superAdminId;
            } else if ($userId == 51 || $userId == 61 || $userId == 71 || $userId == 81 || $userId == 91) {
                $roleId = $ownerId;
            } else if ($userId == 52 || $userId == 62 || $userId == 72 || $userId == 82 || $userId == 92) {
                $roleId = $adminId;
            } else if ($userId == 53 || $userId == 63 || $userId == 73 || $userId == 83 || $userId == 93) {
                $roleId = $contributorId;
            } else {
                $roleId = $viewerId;
            }

            if ($roleId == $ownerId) {
                /*Insert In Company*/
                $company = Company::updateOrCreate([
                    'user_id' => $userId,
                ], [
                    'company_name' => "Test" . $companyNameId++,
                ]);
                $companyId = $company->id;

                CompanyMatrix::updateOrCreate([
                    'user_id'     => $userId,
                    'company_id'  => $companyId,
                    'privileges'  => ($userId <= 50) ? "#" . implode("#", $roleIds) . "#" : null,
                    'role_id'     => $roleId,
                    'is_accepted' => 1,
                ]);
            } else if (!empty($companyId)) {
                CompanyMatrix::updateOrCreate([
                    'user_id'     => $userId,
                    'company_id'  => $companyId,
                    'privileges'  => ($userId <= 50) ? "#" . implode("#", $roleIds) . "#" : null,
                    'role_id'     => $roleId,
                    'is_accepted' => 0,
                ]);
            }
        }
    }
}
