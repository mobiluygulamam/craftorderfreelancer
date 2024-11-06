<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100)->unique();
            $table->float('monthly_price', 25, 2)->default('0.00')->nullable();
            $table->float('annual_price', 25, 2)->default('0.00')->nullable();
            $table->smallInteger('status')->default(0);
            $table->Integer('trial_days')->default(0);
            $table->integer('max_workspaces')->default(0);
            $table->integer('max_users')->default(0);
            $table->integer('max_clients')->default(0);
            $table->integer('max_projects')->default(0);
            $table->string('enable_chatgpt');
            $table->float('storage_limit');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
