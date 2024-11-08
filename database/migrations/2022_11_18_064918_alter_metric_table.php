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
            $table->string('expression_readable')->nullable()->after('expression_ids');
            $table->json('expression_data')->after('expression_readable')->nullable();
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
            $table->dropColumn('expression_readable');
            $table->dropColumn('expression_data');
        });
    }
};
