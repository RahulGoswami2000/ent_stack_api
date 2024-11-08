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
        Schema::create('mst_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 70);
            $table->string('last_name', 70);
            $table->string('email',100)->unique();
            $table->string('mobile_no',20);
            $table->string('password',255);
            $table->string('job_role',100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('hire_date')->nullable();
            $table->text('privileges')->nullable();
            $table->text('profile_image')->nullable();
            $table->tinyInteger('user_type')->nullable()->default(2)->comment('(1=>AdminUsers, 2 =>WebUsers) default 2')->index();
            $table->tinyInteger('is_active')->nullable()->default(0)->comment('(0 => Inactive, 1 => Active) default 0');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_users');
    }
};
