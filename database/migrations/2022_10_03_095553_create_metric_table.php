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
        Schema::create('metric', function (Blueprint $table) {
            $table->id();
            $table->string('name',70);
            $table->tinyInteger('type')->default('1')->comment('(1 = single and 2 = calculation)');
            $table->bigInteger('metric_category_id')->unsigned()->nullable();
            $table->foreign('metric_category_id')->references('id')->on('metric_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('format_of_matrix')->unsigned()->nullable();
            $table->text('expression')->nullable();
            $table->json('expression_ids')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('(0 = inactive and 1 = active)');
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
        Schema::dropIfExists('metric');
    }
};
