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
        Schema::create('scorecard_stack_nodes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scorecard_stack_id')->unsigned();
            $table->foreign('scorecard_stack_id')->references('id')->on('scorecard_stack')->onDelete('cascade')->onUpdate('cascade');
            $table->string('node_id',255)->unique();
            $table->json('node_data')->nullable();
            $table->tinyInteger('auto_assign_color')->comment('(0 = No and 1 = Yes)')->default(0)->unsigned();
            $table->string('assigned_color',255)->nullable();
            $table->bigInteger('assigned_to')->unsigned()->nullable();
            $table->foreign('assigned_to')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('goal_achieve_in_number')->default(0);
            $table->tinyInteger('reminder')->comment('(0 = No and 1 = Yes)')->default(0)->unsigned();
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
        Schema::dropIfExists('scorecard_stack_nodes');
    }
};
