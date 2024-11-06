<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->integer('currant_workspace')->nullable();
            $table->string('lang',5)->default('en');
            // $table->string('avatar')->default(config('chatify.user_avatar.default'));
            $table->string('avatar')->nullable();
            $table->string('type',20)->default('user');
            $table->integer('plan')->nullable();
            $table->integer('requested_plan')->default(0);
            $table->date('plan_expire_date')->nullable();
            $table->float('storage_limit');
            $table->string('payment_subscription_id', 100)->nullable();
            $table->smallInteger('is_trial_done')->default(0);
            $table->smallInteger('is_plan_purchased')->default(0);
            $table->smallInteger('interested_plan_id')->default(0);
            $table->smallInteger('is_register_trial')->default(0);
            $table->timestamps();
            $table->string('messenger_color')->default('#2180f3');
            $table->boolean('dark_mode')->default(0);
            $table->boolean('active_status')->default(0);
            $table->boolean('is_active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
