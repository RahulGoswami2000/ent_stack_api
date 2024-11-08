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
            $table->tinyInteger('is_editable')->default('1')->comment('(0 = No and 1 = Yes)')->after('privileges');
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
            $table->dropColumn(['is_editable']);
        });
    }
};
