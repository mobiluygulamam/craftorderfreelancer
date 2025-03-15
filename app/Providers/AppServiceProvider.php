<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
     if (!Request::cookie('ab_test_group')) {
          $group = rand(0, 1) ? 'A' : 'B';
          Cookie::queue('ab_test_group', $group, 43200); // 30 gün boyunca geçerli
      }
       Schema::defaultStringLength(191);
       if($this->app->environment('production')) {
          \URL::forceScheme('https');
      }
    }


}
