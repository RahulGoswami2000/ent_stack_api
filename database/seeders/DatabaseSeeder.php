<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LovPrivilegeGroupsSeeder::class);
        $this->call(LovPrivilegeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(MstStackSeeder::class);
    }
}
