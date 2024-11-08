<?php

namespace Database\Seeders;

use App\Models\LovPrivileges;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $template = config('global.MAIL_TEMPLATE');
        foreach ($template as $key => $value) {
            $checkTemplate = Template::where('type', $value['type'])
                ->where('key', $value['key'])
                ->first();
            if (empty($checkTemplate)) {
                Template::updateOrCreate([
                    'type'      => $value['type'],
                    'name'      => $value['name'],
                    'key'       => $value['key'],
                    'is_active' => 0,
                ]);
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}
