<?php

namespace Database\Seeders;

use App\Models\MetricGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MetricGroupSeeder extends Seeder
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
        for ($i = 1; $i < 11; $i++) {
            if ($i % 2 == 1) {
                $j = $j + 1;
            }
            $metricgroup = MetricGroup::firstOrNew([
                'name' => 'Metric Group ' . $i,
                'metric_category_id' => 0 + $j,
            ]);
            $metricgroup->is_active = 1;

            $metricgroup->save();
        };

        Schema::enableForeignKeyConstraints();
    }
}
