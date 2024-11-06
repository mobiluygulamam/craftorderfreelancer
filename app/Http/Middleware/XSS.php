<?php

namespace App\Http\Middleware;

use App\Models\LandingPageSection;
use App\Models\Utility;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;


class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // \App::setLocale(env('DEFAULT_LANG'));
            \App::setLocale(Auth::user()->lang);

            if (Auth::user()->type == 'admin') {
                if (Schema::hasTable('messages')) {
                    if (Schema::hasColumn('messages', 'type') == false) {
                        Schema::drop('messages');
                        DB::table('migrations')->where('migration', 'like', '%messages%')->delete();
                    }
                }
                $migrations = $this->getMigrations();
                $messengerMigration = Utility::get_messenger_packages_migration();
                $dbMigrations = $this->getExecutedMigrations();
                $Modulemigrations = glob(base_path() . '/Modules/LandingPage/Database' . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . '*.php');
                $numberOfUpdatesPending = (count($migrations) + count($Modulemigrations) + $messengerMigration) - count($dbMigrations);

                if ($numberOfUpdatesPending > 0) {
                    return redirect()->route('LaravelUpdater::welcome');
                }
            }
        }
        $input = $request->all();
        // array_walk_recursive($input, function (&$input){
        //     $input = strip_tags($input);
        // });

        $request->merge($input);

        return $next($request);
    }
}
