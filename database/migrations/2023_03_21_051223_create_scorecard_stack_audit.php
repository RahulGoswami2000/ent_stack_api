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
        Schema::create('scorecard_stack_audit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scorecard_stack_id')->unsigned();
            $table->foreign('scorecard_stack_id')->references('id')->on('scorecard_stack')->onDelete('cascade')->onUpdate('cascade');
            $table->json('old_scorecard_data')->nullable();
            $table->json('new_scorecard_data')->nullable();
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
        Schema::dropIfExists('scorecard_stack_audit');
    }
};
