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
            $table->bigInteger('company_stack_module_id')->unsigned()->after('id');
            $table->foreign('company_stack_module_id')->references('id')->on('company_stack_modules')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign(['company_stack_module_id']);
            $table->dropColumn('company_stack_module_id');
        });
    }
};
