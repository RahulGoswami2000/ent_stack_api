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
            $table->bigInteger('company_stack_modules_id')->after('project_id')->unsigned();
            $table->foreign('company_stack_modules_id')->references('id')->on('company_stack_modules')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('team_stack', function (Blueprint $table) {
            $table->dropForeign(['company_stack_modules_id']);
            $table->dropColumn('company_stack_modules_id');
            $table->renameColumn('company_stack_category_id', 'project_category_id');
        });
    }
};
