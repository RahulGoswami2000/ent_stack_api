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
        Schema::rename('project_category', 'company_stack_category');
        Schema::table('company_stack_category', function (Blueprint $table) {
            $table->bigInteger('company_stack_modules_id')->nullable()->unsigned()->after('project_id');
            $table->foreign('company_stack_modules_id')->references('id')->on('company_stack_modules')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_stack_category', function (Blueprint $table) {
            $table->dropForeign(['company_stack_modules_id']);
            $table->dropColumn('company_stack_modules_id');
        });
        Schema::rename('company_stack_category', 'project_category');
    }
};
