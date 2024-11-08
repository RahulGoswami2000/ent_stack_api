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
            $table->string('first_name', 70)->nullable()->change();
            $table->string('last_name', 70)->nullable()->change();
            $table->string('mobile_no',20)->nullable()->change();
            $table->string('password',255)->nullable()->change();
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
            $table->string('first_name', 70)->change();
            $table->string('last_name', 70)->change();
            $table->string('mobile_no',20)->change();
            $table->string('password',255)->change();
        });
    }
};
