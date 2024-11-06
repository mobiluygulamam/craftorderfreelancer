<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserWorkspace;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->integer('referral_code')->default(0)->after('is_enable_login');
            }
            if (!Schema::hasColumn('users', 'referral_used')) {
                $table->integer('referral_used')->default(0)->after('referral_code');
            }
            if (!Schema::hasColumn('users', 'commission_amount')) {
                $table->integer('commission_amount')->default(0)->after('referral_used');
            }
        });


        if (Schema::hasColumn('users', 'referral_code')) {
            $users = User::join('user_workspaces', 'users.id', '=', 'user_workspaces.user_id')
                ->where('user_workspaces.permission', 'Owner')
                ->groupBy('user_workspaces.user_id')
                ->get();
            foreach ($users as $user) {
                do {
                    $code = rand(100000, 999999);
                } while (DB::table('users')->where('referral_code', $code)->exists());
                DB::table('users')->where('id', $user->user_id)->update(['referral_code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
