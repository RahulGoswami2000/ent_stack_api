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
        Schema::create('team_stack', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('mst_company')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('company_projects')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('project_category_id')->unsigned();
            $table->foreign('project_category_id')->references('id')->on('project_category')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('scorecard_type')->default(1)->comment('(1 = weekly 2 = bi-weekly 3 = semi-monthly 4 = monthly 5 = quarterly 6 = annually )');
            $table->json('scorecard_data')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('(0 = Inactive 1 = Active)');
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
        Schema::dropIfExists('team_stack');
    }
};
