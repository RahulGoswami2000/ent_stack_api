<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Subscription::truncate();
        $subscription = Subscription::firstOrNew([
            'name' => 'First Subscription',
        ]);
        $subscription->description = 'This is the third subscription';
        $subscription->amount = 11.88;
        $subscription->is_active = 1;

        $subscription->save();
        Schema::enableForeignKeyConstraints();
    }
}
