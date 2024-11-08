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
        Schema::table('scorecard_stack', function (Blueprint $table) {
            $table->renameColumn('project_category_id', 'company_stack_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scorecard_stack', function (Blueprint $table) {
            $table->renameColumn('company_stack_category_id', 'project_category_id');
        });
    }
};
