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
        Schema::table('user_stack_access', function (Blueprint $table) {
            $table->unsignedBigInteger('stack_table_id')->nullable()->comment("id for related module transaction")->after('company_stack_category_id');
            $table->string('stack_table_type')->nullable()->after('stack_table_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_stack_access', function (Blueprint $table) {
            $table->dropColumn('stack_table_id');
            $table->dropColumn('stack_table_type');
        });
    }
};
