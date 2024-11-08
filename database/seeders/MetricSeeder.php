<?php

namespace Database\Seeders;

use App\Models\Metric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $j = 0;
        for($i = 1;$i<11;$i++) {
        if($i%2 == 1){
            $j = $j+1;
        }
        $metric =  Metric::firstOrNew([
            'name' => 'Metric '. $i,
            'metric_category_id' => 0+$j,
        ]);
        $metric->type = 1;
        $metric->format_of_matrix = 1;
        $metric->expression = '(#1#*#2#)/#3#';
        $metric->expression_ids = '["1", "2", "3"]';

        $metric->save();
        };
        Schema::enableForeignKeyConstraints();
    }
}
