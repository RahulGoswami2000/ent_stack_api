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
            $table->tinyInteger('can_delete')->unsigned()->default(1)->comment('(0 = No and 1 = Yes)')->after('expression_data');
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
            $table->dropColumn('can_delete');
        });
    }
};
