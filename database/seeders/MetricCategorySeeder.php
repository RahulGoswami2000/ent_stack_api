<?php

namespace Database\Seeders;

use App\Models\MetricCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MetricCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        for($i = 1;$i<11; $i++){
            $metriccategory = MetricCategory::firstOrNew([
                'name' => 'Metric Category ' . $i,
            ]);
            $metriccategory->is_active = 1; 
            $metriccategory->save();
        }
        Schema::enableForeignKeyConstraints();
    }
}
