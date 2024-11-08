<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_stack', function (Blueprint $table) {
            $table->dropColumn('scorecard_type');
            $table->renameColumn('scorecard_data', 'team_stack_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_stack', function (Blueprint $table) {
            $table->tinyInteger('scorecard_type');
            $table->renameColumn('team_stack_data', 'scorecard_data');
        });
    }
};
