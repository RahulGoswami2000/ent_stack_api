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
        Schema::table('metric', function (Blueprint $table) {
            $table->bigInteger('company_id')->nullable()->after('type')->unsigned();
            $table->foreign('company_id')->references('id')->on('mst_company')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('is_admin')->default(0)->comment('(0 = Web and 1 = Admin)')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metric', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
