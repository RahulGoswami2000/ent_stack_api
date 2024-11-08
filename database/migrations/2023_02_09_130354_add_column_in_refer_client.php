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
        Schema::table('refer_client', function (Blueprint $table) {
            $table->tinyInteger('is_referred')->default(0)->comment('(0 = pending, 1 = referred, 2 = cancelled)')->after('referal_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refer_client', function (Blueprint $table) {
            $table->dropColumn('is_referred');
        });
    }
};
