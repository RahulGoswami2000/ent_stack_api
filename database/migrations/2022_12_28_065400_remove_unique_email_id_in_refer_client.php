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
        $keys = \DB::select(\DB::raw('SHOW KEYS from refer_client'));
        foreach ($keys as $key) {
            if ($key->Column_name == 'email') {
                Schema::table('refer_client', function (Blueprint $table) {
                    $table->dropUnique(['email']);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
