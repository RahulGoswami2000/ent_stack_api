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
        //
        Schema::table('mst_user_company_matrix', function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned()->nullable()->comment('(by default a owner)')->after('company_id');
            $table->foreign('role_id')->references('id')->on('mst_roles')->onDelete('cascade')->onUpdate('cascade');
            $table->string('privileges')->nullable()->after('role_id');
            $table->tinyInteger('is_accepted')->default(0)->comment('(0=pending and 1=Reffered and 2=Rejected)')->after('privileges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_user_company_matrix', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('privileges');
            $table->dropColumn('is_accepted');
        });
    }
};
