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
        Schema::table('mst_roles', function (Blueprint $table) {
            $table->tinyInteger('role_type')->default(2)->comment('(1 = Admin, 2 = Web)')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_roles', function (Blueprint $table) {
            $table->dropColumn('role_type');
        });
    }
};
