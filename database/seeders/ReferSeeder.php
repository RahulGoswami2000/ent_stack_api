<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ReferClient;
use App\Models\CompanyMatrix;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ReferClient::truncate();

        for ($i = 1; $i <= 10; $i++) {
            ReferClient::updateOrCreate([
                'company_id'   => 4,
                'first_name'   => "user" . $i,
                'last_name'    => "name" . $i,
                'email'        => "user" . $i . "@yopmail.com",
                'referal_code' => "XYSYSYSYS" . $i,
            ]);
            Schema::enableForeignKeyConstraints();
        };
    }
}
