<?php

namespace Database\Seeders;

use App\Models\LovPrivileges;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
//        Role::truncate();
        $roleIds = LovPrivileges::all()->pluck('id')->toArray() ?? [];

        $rolesInsert = [
            [
                "id"          => 1,
                'name'        => config('global.ROLES.SUPER_ADMIN'),
                'privileges'  => "#" . implode("#", $roleIds) . "#",
                'is_editable' => 0,
                'is_active'   => 1,
                'role_type'   => 1,
                'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                "id"          => 2,
                'name'        => config('global.ROLES.OWNER'),
                'privileges'  => "#" . implode("#", $roleIds) . "#",
                'is_editable' => 1,
                'is_active'   => 1,
                'role_type'   => 2,
                'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                "id"          => 3,
                'name'        => config('global.ROLES.ADMIN'),
                'privileges'  => '#10001#',
                'is_editable' => 1,
                'is_active'   => 1,
                'role_type'   => 2,
                'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                "id"          => 4,
                'name'        => config('global.ROLES.CONTRIBUTOR'),
                'privileges'  => '#10001#',
                'is_editable' => 1,
                'is_active'   => 1,
                'role_type'   => 2,
                'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                "id"          => 5,
                'name'        => config('global.ROLES.VIEWERS'),
                'privileges'  => '#10001#',
                'is_editable' => 1,
                'is_active'   => 1,
                'role_type'   => 2,
                'created_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'  => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];
        foreach ($rolesInsert as $value) {
            $update = Role::upsert($value, ['id'], ['id', 'name', 'privileges', 'is_editable', 'is_active', 'role_type', 'created_at', 'updated_at']);
        }

        Schema::enableForeignKeyConstraints();
    }
}
