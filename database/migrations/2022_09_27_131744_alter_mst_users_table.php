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
        Schema::table('mst_users', function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned()->after('password');
            $table->bigInteger('created_by')->unsigned()->nullable()->after('is_active');
            $table->bigInteger('updated_by')->unsigned()->nullable()->after('created_by');
            $table->bigInteger('deleted_by')->unsigned()->nullable()->after('updated_by');

            $table->foreign('role_id')->references('id')->on('mst_roles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('deleted_by')->references('id')->on('mst_users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id']);

            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by']);

            $table->dropForeign(['updated_by']);
            $table->dropColumn(['updated_by']);

            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deleted_by']);
        });
    }
};
