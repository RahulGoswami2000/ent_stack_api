<?php

namespace Database\Seeders;

use App\Models\Stack;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MstStackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Stack::truncate();
        $stackModule = config('global.STACK_MODULES');
        foreach ($stackModule as $key => $value) {
            Stack::create([
                'id'       => $value['id'],
                'key'      => $key,
                'name'     => $value['name'],
                'can_copy' => $value['can_copy']
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
