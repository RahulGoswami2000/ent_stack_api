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
        Schema::table('company_stack_modules', function (Blueprint $table) {
            $table->bigInteger('stack_modules_id')->unsigned()->after('id');
            $table->foreign('stack_modules_id')->references('id')->on('mst_stack_modules')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_stack_modules', function (Blueprint $table) {
            $table->dropForeign(['stack_modules_id']);
            $table->dropColumn('stack_modules_id');
        });
    }
};
