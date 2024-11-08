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
        Schema::create('scorecard_stack_archive', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(1)->comment('(1 = Full Stack and 2 = Metric)');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('mst_company')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('company_projects')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('company_stack_modules_id')->unsigned();
            $table->foreign('company_stack_modules_id')->references('id')->on('company_stack_modules')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('company_stack_category_id')->unsigned();
            $table->foreign('company_stack_category_id')->references('id')->on('company_stack_category')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('scorecard_stack_id')->unsigned();
            $table->foreign('scorecard_stack_id')->references('id')->on('scorecard_stack')->onDelete('cascade')->onUpdate('cascade');
            $table->string('node_id')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('deleted_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scorecard_stack_archive');
    }
};
