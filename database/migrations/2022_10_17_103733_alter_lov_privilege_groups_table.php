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
        Schema::table('lov_privilege_groups', function (Blueprint $table) {
            $table->tinyInteger('menu_type')->default(0)->comment('(0 = Both 1 = Admin 2 = Web)')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lov_privilege_groups', function (Blueprint $table) {
            $table->dropColumn('menu_type');
        });
    }
};
