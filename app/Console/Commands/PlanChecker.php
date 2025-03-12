<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use \App\Models;
class PlanChecker extends Command
{
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'app:plan-checker';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Command description';

     /**
      * Execute the console command.
      */
      public function handle()
      {
          $users = \App\Models\User::whereNotNull('plan_expire_date')->get(); // Planı olan tüm kullanıcılar
  
          foreach ($users as $user) {
              $currentDate = Carbon::now();
              $planExpireDate = Carbon::parse($user->plan_expire_date);
              $diffInHours = $currentDate->diffInHours($planExpireDate, false); // Farkı saat olarak alıyoruz
  
              // Eğer fark 0 veya daha küçükse (plan süresi dolmuşsa)
              if ($diffInHours <= 0) {
                  // Kullanıcıyı Demo Finish planına geçir
                  $demoFinishPlan = \App\Models\Plan::where('name', 'Demo Finish')->first();
                  $user->plan = $demoFinishPlan->id;
                  $user->plan_expire_date = null; // Plan süresi bitmiş, null yapıyoruz
                  $user->is_enable_login = 0; // Kullanıcının girişini kapatıyoruz
                  $user->save();
              }
              // Aylık/Yıllık Paketler için diğer kontroller
              elseif ($user->plan != 1) {
                  $plan = \App\Models\Plan::find($user->plan);
  
                  // Kullanıcı, aylık veya yıllık planı seçmişse, ödeme kontrolünü yapın
                  if ($plan->monthly_price > 0) {
                      // Aylık plan kontrolü
                      $planExpireDate = Carbon::parse($user->plan_expire_date);
                      if ($currentDate->greaterThan($planExpireDate)) {
                          // Aylık plan süresi dolmuşsa, kullanıcıyı Demo Finish planına geçir
                          $user->plan = 1;
                          $user->plan_expire_date = null;
                          $user->is_enable_login = 0; // Kullanıcının girişini kapatıyoruz
                          $user->save();
                      }
                  } elseif ($plan->annual_price > 0) {
                      // Yıllık plan kontrolü
                      if ($currentDate->greaterThan($planExpireDate)) {
                          $user->plan = 1;
                          $user->plan_expire_date = null;
                          $user->is_enable_login = 0; // Kullanıcının girişini kapatıyoruz
                          $user->save();
                      }
                  }
              }
          }
  
          $this->info('User plans have been checked and updated.');
      }
}
