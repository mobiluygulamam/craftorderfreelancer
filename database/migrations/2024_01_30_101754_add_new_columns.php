<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->change();
            }

            if (!Schema::hasColumn('users', 'is_enable_login')) {
                $table->integer('is_enable_login')->after('is_register_trial')->default(1);
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'password')) {
                $table->string('password')->nullable()->change();
            }
            if (!Schema::hasColumn('clients', 'is_enable_login')) {
                $table->integer('is_enable_login')->after('telephone')->default(1);
            }
        });

        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'monthly_price')) {
                $table->float('monthly_price', 30, 2)->default('0.00')->nullable()->change();
            }

            if (Schema::hasColumn('plans', 'annual_price')) {
                $table->float('annual_price', 30, 2)->default('0.00')->nullable()->change();
            }
            if (!Schema::hasColumn('plans', 'is_plan_enable')) {
                $table->integer('is_plan_enable')->after('image')->default(1);
            }

            if (!Schema::hasColumn('plans', 'is_trial_disable')) {
                $table->integer('is_trial_disable')->after('is_plan_enable')->default(0);
            }
        });

        Schema::table('workspaces', function (Blueprint $table) {

            if (!Schema::hasColumn('workspaces', 'color_flag')) {
                $table->text('color_flag')->nullable()->after('theme_color');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_refund')) {
                $table->integer('is_refund')->after('user_id')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
