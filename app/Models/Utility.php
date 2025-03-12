<?php

namespace App\Models;

use App\Models\Mail\CommonEmailTemplate;
use App\Models\Workspace;
use App\Models\Webhook;
use App\Models\Languages;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Pusher\Pusher;
use Spatie\GoogleCalendar\Event;
use App\Models\ReferralTransaction;


class Utility

{

    public static function getRandomMotivationalQuote() {
          // Motivasyon cümlelerinin dizisi
          $quotes = [
               "İşini büyütmek seni özgürleştirir.",
               "Her zaman daha büyük hedefler koy.",
               "Başarı, tutkulu insanların işidir.",
               "İşini severek yap, fark yarat.",
               "Başarı, cesaretle başlar ve kararlılıkla büyür.",
               "Yolculuğun zorlu olabilir, ama sonuçlar buna değecektir.",
               "Her gün yeni bir fırsattır, onu değerlendir.",
               "Hayallerin gerçek olmayı bekliyor.",
               "Girişimcilik, risk alarak kazanmaktır.",
               "Hedeflerini aş, sınırlarını zorla.",
               "Yapabileceğine inandığında, her şey mümkündür.",
               "Kendini geliştir, dünyayı değiştirebilirsin.",
               "Her adım seni başarıya biraz daha yaklaştırır.",
               "Başarı, azim ve süreklilikle elde edilir.",
               "Kendine inan, başarının anahtarı senin içinde."
           ];
          // Rastgele bir cümle seçmek için array_rand() fonksiyonu kullanılıyor
          $randomIndex = array_rand($quotes);
          
          // Rastgele seçilen cümleyi döndürüyoruz
          return $quotes[$randomIndex];
      }

    public function createSlug($table, $title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title, '-');
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($table, $slug, $id);
        // If we haven't used it before then we are all good.
        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }
        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 100; $i++) {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }
        throw new \Exception(__('Can not create a unique slug'));
    }

    public static function GetCacheSize()
    {
        $file_size = 0;
        foreach (\File::allFiles(storage_path('/framework/cache')) as $file) {
            $file_size += $file->getSize();
        }
        $file_size = number_format($file_size / 1000000, 4);
        return $file_size;
    }

    protected function getRelatedSlugs($table, $slug, $id = 0)
    {
        return DB::table($table)->select()->where('slug', 'like', $slug . '%')->where('id', '<>', $id)->get();
    }

    public static function getWorkspaceBySlug($slug = null)
    {
        $objUser = Auth::user();

        if ($objUser && $objUser->currant_workspace) {
            if ($objUser->getGuard() == 'client') {
                $rs = Workspace::select(['workspaces.*'])->join('client_workspaces', 'workspaces.id', '=', 'client_workspaces.workspace_id')->where('workspaces.id', '=', $objUser->currant_workspace)->where('client_id', '=', $objUser->id)->first();
            } else {
                $rs = Workspace::select([
                    'workspaces.*',
                    'user_workspaces.permission',
                ])->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')->where('workspaces.id', '=', $objUser->currant_workspace)->where(
                        'user_id',
                        '=',
                        $objUser->id
                    )->first();
            }
        } elseif ($objUser && !empty($slug)) {
            if ($objUser->getGuard() == 'client') {
                $rs = Workspace::select(['workspaces.*'])->join('client_workspaces', 'workspaces.id', '=', 'client_workspaces.workspace_id')->where('slug', '=', $slug)->where('client_id', '=', $objUser->id)->first();
            } else {
                $rs = Workspace::select([
                    'workspaces.*',
                    'user_workspaces.permission',
                ])->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')->where('slug', '=', $slug)->where('user_id', '=', $objUser->id)->first();
            }
        } elseif ($objUser) {
            if ($objUser->getGuard() == 'client') {
                $rs = Workspace::select(['workspaces.*'])->join('client_workspaces', 'workspaces.id', '=', 'client_workspaces.workspace_id')->where('client_id', '=', $objUser->id)->orderBy('workspaces.id', 'desc')->limit(1)->first();
                $objUser->currant_workspace = $rs->id;
                $objUser->save();
            } else {
                $rs = Workspace::select([
                    'workspaces.*',
                    'user_workspaces.permission',
                ])->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')->where('user_id', '=', $objUser->id)->orderBy('workspaces.id', 'desc')->limit(1)->first();
            }
        } else {
            $rs = Workspace::select(['workspaces.*'])->where('slug', '=', $slug)->limit(1)->first();
        }

        // if(!$rs && $objUser){
        //     $rs = UserWorkspace::select(['user_workspaces.*'])->where('user_id', '=', $objUser->id)->limit(1)->first();
        //     $objUser->currant_workspace = $rs->workspace_id;
        //     $objUser->save();
        // }

        if ($rs) {
            Utility::setLang($rs);
            return $rs;
        }
    }

    public static function getWorkspaceBySlug_copylink($type, $id)
    {
        if ($type == 'invoice') {
            $invoice = Invoice::find($id);
            $rs = Workspace::find($invoice->workspace_id);
        }
        if ($type == 'project') {
            $project = Project::find($id);
            $rs = Workspace::find($project->workspace);
        }
        return $rs;
    }
    public static function success_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "success" : $msg;
        $msg_id = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    public static function error_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "error" : $msg;
        $msg_id = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }
    public static function second_to_time($seconds = 0)
    {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = $seconds % 60;

        $time = sprintf("%02d:%02d:%02d", $H, $i, $s);

        return $time;
    }
    public static function diffance_to_time($start, $end)
    {
        $start = new Carbon($start);
        $end = new Carbon($end);
        $totalDuration = $start->diffInSeconds($end);

        return $totalDuration;
    }
    private static $languages = NULL;
    public static function languages()
    {
        if (self::$languages == null) {
            self::$languages = self::fetchlanguages();
        }
        return self::$languages;
    }
    // public static function fetchlanguages()
    // {
    //     $settings = Utility::getAdminPaymentSetting();
    //     $disablelang = $settings['disable_lang'];

    //     $dir = base_path() . '/resources/lang/';
    //     $glob = glob($dir . "*", GLOB_ONLYDIR);
    //     $arrLang = array_map(
    //         function ($value) use ($dir) {
    //             return str_replace($dir, '', $value);
    //         },
    //         $glob
    //     );
    //     $arrLang = array_map(
    //         function ($value) use ($dir) {
    //             return preg_replace('/[0-9]+/', '', $value);
    //         },
    //         $arrLang
    //     );

    //     $parts = explode(',', $disablelang);
    //     $finalLangArr = array_diff($arrLang, $parts);

    //     return $finalLangArr;
    // }
    public static function fetchlanguages()
    {
        $settings = Utility::getAdminPaymentSetting();
        $disablelang = $settings['disable_lang'];

        // Use Eloquent to fetch all languages from the database, including English
        $languages = Languages::whereNotIn('lang_code', explode(',', $disablelang))
            ->orWhere('lang_code', 'en') // Include English
            ->pluck('lang_fullname', 'lang_code')
            ->all();

        return $languages;
    }



    public static function setUserLang()
    {
        $user = \Auth::user();
        $lang = $user->lang;
        \Date::setLocale(basename($lang));
        \App::setLocale($lang);
    }

    public static function setLang($Workspace)
    {
        $dir = base_path() . '/resources/lang/' . $Workspace->id . "/";
        if (is_dir($dir)) {
            $lang = $Workspace->id . "/" . $Workspace->lang;
        } else {
            $lang = (\Auth::user()) ? \Auth::user()->lang : $Workspace->lang;
        }
        \Date::setLocale(basename($lang));
        \App::setLocale($lang);
    }

    public static function get_timeago($ptime)
    {
        $estimate_time = time() - $ptime;

        $ago = true;

        if ($estimate_time < 1) {
            $ago = false;
            $estimate_time = abs($estimate_time);
        }

        $condition = [
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second',
        ];

        foreach ($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if ($d >= 1) {
                $r = round($d);
                $str = $str . ($r > 1 ? 's' : '');

                return $r . ' ' . __($str) . ($ago ? ' ' . __('ago') : '');
            }
        }

        return $estimate_time;
    }

    public static function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = [
                ' bytes',
                ' KB',
                ' MB',
                ' GB',
                ' TB',
            ];

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }

    public static function invoiceNumberFormat($number)
    {
        return '#INV' . sprintf("%05d", $number);
    }

    public static function contractNumberFormat($number)
    {
        return '#CON' . sprintf("%05d", $number);
    }

    public static function dateFormat($date)
    {
        $lang = \App::getLocale();
        \App::setLocale(basename($lang));
        $date = Date::parse($date)->format('d M Y');

        return $date;
    }

    public static function sendNotification($type, $currentWorkspace, $user_id, $obj)
    {
        $setting = self::fetchAdminPaymentSetting();
        if (is_array($user_id)) {
            foreach ($user_id as $id) {
                $notification = Notification::create([
                    'workspace_id' => $currentWorkspace->id,
                    'user_id' => $id,
                    'project_id' => $obj->project_id,
                    'related_id' => $obj->id,
                    'type' => $type,
                    'data' => json_encode($obj),
                    'is_read' => 0,
                ]);
                $options = array(
                    'cluster' => $setting['pusher_app_cluster'],
                    'useTLS' => true,
                );
                $pusher = new Pusher(
                    $setting['pusher_app_key'],
                    $setting['pusher_app_secret'],
                    $setting['pusher_app_id'],
                    $options
                );
                $data = [];
                $data['html'] = $notification->toHtml();
                $data['user_id'] = $notification->user_id;
                // sending from and to user id when pressed enter

                // if (!empty(env('PUSHER_APP_KEY')) && !empty(env('PUSHER_APP_SECRET')) && !empty(env('PUSHER_APP_ID'))) {
                //     $pusher->trigger($currentWorkspace->slug, 'notification', $data);
                // }
                if (!empty($setting['pusher_app_key']) && !empty($setting['pusher_app_secret']) && !empty($setting['pusher_app_id'])) {
                    $pusher->trigger($currentWorkspace->slug, 'notification', $data);
                }
                // End Push Notification
            }
        } else {
            $notification = Notification::create([
                'workspace_id' => $currentWorkspace->id,
                'user_id' => $user_id,
                'project_id' => $obj->project_id,
                'related_id' => $obj->id,
                'type' => $type,
                'data' => json_encode($obj),
                'is_read' => 0,
            ]);

            $options = array(
                'cluster' => $setting['pusher_app_cluster'],
                'useTLS' => true,
            );
            $pusher = new Pusher(
                $setting['pusher_app_key'],
                $setting['pusher_app_secret'],
                $setting['pusher_app_id'],
                $options
            );
            $data = [];
            $data['html'] = $notification->toHtml();
            $data['user_id'] = $notification->user_id;
            // sending from and to user id when pressed enter
            // if (!empty(env('PUSHER_APP_KEY')) && !empty(env('PUSHER_APP_SECRET')) && !empty(env('PUSHER_APP_ID'))) {
            //     $pusher->trigger($currentWorkspace->slug, 'notification', $data);
            // }
            if (!empty($setting['pusher_app_key']) && !empty($setting['pusher_app_secret']) && !empty($setting['pusher_app_id'])) {
                $pusher->trigger($currentWorkspace->slug, 'notification', $data);
            }

            // End Push Notification
        }
    }

    public static function getFirstSeventhWeekDay($week = null)
    {
        $first_day = $seventh_day = null;

        if (isset($week)) {
            // $first_day = Carbon::now()->addWeeks($week)->startOfWeek();
            // $seventh_day = Carbon::now()->addWeeks($week)->endOfWeek();
            $first_day = Carbon::now()->startOfWeek();
            $seventh_day = Carbon::now()->endOfWeek();
        }

        $dateCollection['first_day'] = $first_day;
        $dateCollection['seventh_day'] = $seventh_day;

        $period = CarbonPeriod::create($first_day, $seventh_day);

        foreach ($period as $key => $dateobj) {
            $dateCollection['datePeriod'][$key] = $dateobj;
        }
        return $dateCollection;
    }

    public static function userprojectcount(){

        $user=Auth::user();
        $currant_workspace = $user->currant_workspace;

   return  Project::where('workspace', '=', $currant_workspace)->count();
    }

    

    public static function isClientRestrictedDemo(){

     if(Utility::isdemopackage()){

          $user=Auth::user();
          $currant_workspace = $user->currant_workspace;
  
      $clientcount= Client::where('currant_workspace', '=', $currant_workspace)->count();
if ($clientcount>=3) {
return true;
}return false;
     }
     else return false;
}
    public static function isProjectRestrictedDemo(){
         if(Utility::isdemopackage()){
if (Utility::userprojectcount()>=3) {
   return true;
}return false;
         }
         else return false;
    }

    public static function isFinishPackageTime(){
     $user=Auth::user();
   
   //Datetime
     $expiredate = $user->plan_expire_date;
$now=\Carbon\Carbon::now();
return $now->gt($expiredate) ? true : false;
  
   
    }
public static function isdemopackage(){
     $user=Auth::user();
   
   
     $userPlanid = $user->plan;
$planname= Plan::find($userPlanid)->name;

return strtolower($planname) == 'demo'|| strtolower($planname) == 'deneme' ? true : false;
  
}
    public static function isPlanRestricted()
    {
        $user = Auth::user();

        if (!$user || !$user->plan) {
            return false; // Kullanıcı giriş yapmamışsa veya planı yoksa kısıtlama uygulanmaz.
        }

        // Kullanıcının plan adını al
        $planName = optional($user->plan)->name;

        // Eğer plan "demo" değilse, kısıtlama uygulanmaz.
        if ($planName !== 'demo') {
            return false;
        }

        // Kullanıcının sahip olduğu workspace'leri al
        $workspaces = UserWorkspace::where('user_id', $user->id)->get();
        $workspaceCount = $workspaces->pluck('workspace_id')->unique()->count();

        // Eğer kullanıcının yalnızca 1 workspace'i varsa, içinde kaç "Member" olduğunu kontrol et
        $memberCount = 0;
        if ($workspaceCount == 1) {
            $workspaceId = $workspaces->first()->workspace_id;
            $memberCount = UserWorkspace::where('workspace_id', $workspaceId)
                ->where('permission', 'Member')
                ->count();
        }

        // Eğer tek bir workspace varsa ve en az 5 "Member" bulunuyorsa, kısıtlama aktif olur.
        return ($workspaceCount == 1 && $memberCount >= 5);
    }
    public static function calculateTimesheetHours($times)
    {
        $minutes = 0;
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }

        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function delete_directory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [
            $r,
            $g,
            $b,
        ];

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R = $G = $B = $C = $L = $color = '';
        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));
        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];
        for ($i = 0; $i < count($C); ++$i) {
            if ($C[$i] <= 0.03928) {
                $C[$i] = $C[$i] / 12.92;
            } else {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }
        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        $color = $L > 0.179 ? 'black' : 'white';

        return $color;
    }

    public static function get_messenger_packages_migration()
    {
        $totalMigration = 0;
        $messengerPath = glob(base_path() . '/vendor/munafio/chatify/database/migrations' . DIRECTORY_SEPARATOR . '*.php');
        if (!empty($messengerPath)) {
            $messengerMigration = str_replace('.php', '', $messengerPath);
            $totalMigration = count($messengerMigration);
        }

        return $totalMigration;
    }

    public static function getAllPermission()
    {
        return [
            "create milestone",
            "edit milestone",
            "delete milestone",
            "show milestone",
            "create task",
            "edit task",
            "delete task",
            "show task",
            "move task",
            "show activity",
            "show uploading",
            "show timesheet",
            "show bug report",
            "create bug report",
            "edit bug report",
            "delete bug report",
            "move bug report",
            "show gantt",
            "create expenses",
            "edit expenses",
            "delete expenses",
            "show expenses",
        ];
    }

    public static function settings()
    {

        $data = DB::table('settings');

        if (\Auth::check()) {

            $data = $data->where('created_by', '=', \Auth::user()->creatorId())->get();

            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
        } else {

            $data->where('created_by', '=', 1);
            $data = $data->get();
        }
    }
    private static $getAdminPaymentSetting = NULL;
    public static function getAdminPaymentSetting()
    {
        if (self::$getAdminPaymentSetting == null) {
            self::$getAdminPaymentSetting = self::fetchAdminPaymentSetting();
        }
        return self::$getAdminPaymentSetting;
    }
    public static function fetchAdminPaymentSetting()
    {

        $data = DB::table('admin_payment_settings');

        $adminSettings = [
            'is_stripe_enabled' => 'off',
            'stripe_key' => '',
            'stripe_secret' => '',
            'stripe_webhook_secret' => '',
            'is_paypal_enabled' => 'off',
            'paypal_mode' => 'sandbox',
            'paypal_client_id' => '',
            'paypal_secret_key' => '',
            'is_paystack_enabled' => 'off',
            'paystack_public_key' => '',
            'paystack_secret_key' => '',
            'is_flutterwave_enabled' => 'off',
            'flutterwave_public_key' => '',
            'flutterwave_secret_key' => '',
            'is_razorpay_enabled' => 'off',
            'razorpay_public_key' => '',
            'razorpay_secret_key' => '',
            'is_mercado_enabled' => 'off',
            'mercado_app_id' => '',
            'mercado_secret_key' => '',
            'is_paytm_enabled' => 'off',
            'paytm_mode' => 'local',
            'paytm_merchant_id' => '',
            'paytm_merchant_key' => '',
            'paytm_industry_type' => '',
            'is_mollie_enabled' => 'off',
            'mollie_api_key' => '',
            'mollie_profile_id' => '',
            'mollie_partner_id' => '',
            'is_skrill_enabled' => 'off',
            'skrill_email' => '',
            'is_coingate_enabled' => 'off',
            'coingate_mode' => 'sandbox',
            'coingate_auth_token' => '',
            'is_toyyibpay_enabled' => 'off',
            'toyyibpay_secret_key' => '',
            'toyyibpay_category_code' => '',
            'SIGNUP_BUTTON' => 'on',
            'cust_theme_bg' => 'on',
            'cust_darklayout' => 'off',
            'color' => 'theme-4',
            "SITE_RTL" => "off",
            'company_email' => 'test@example.com',
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "meta_image" => '',
            "meta_keywords" => 'Taskly SaaS',
            "meta_description" => 'Taskly is a tool where you can Manage projects, work with clients, and collaborate with team members. All in one place.',
            "is_payfast_enabled" => '',
            "payfast_merchant_id" => '',
            "payfast_merchant_key" => '',
            "payfast_signature" => '',
            "payfast_mode" => '',
            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'is_manual_enabled' => '',
            'is_bank_enabled' => '',
            'contactus_url' => '',
            'chatgpt_key' => '',
            'disable_lang' => '',
            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',
            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'pusher_app_cluster' => '',
            'enable_chat' => 'off',
            'chat_module' => 'off',
            'recaptcha_module' => 'off',
            'google_recaptcha_key' => '',
            'google_recaptcha_secret' => '',
            'currency' => 'TL',
            'currency_symbol' => '₺',
            'app_name' => 'Taskly Saasasd',
            'APP_NAME' => 'Taskly asdasd',
            'default_admin_lang' => 'en',
            'display_landing' => 'on',
            'site_rtl' => 'off',
            'signup_button' => 'on',
            'footer_text' => 'asd Saas',
            'email_verification' => 'off',
            'color_flag' => 'false',
        ];

        $data = $data->get();

        foreach ($data as $row) {
            $adminSettings[$row->name] = $row->value;
        }
        return $adminSettings;
    }

    public static function colorset()
    {
        $setting = DB::table('admin_payment_settings')->pluck('value', 'name')->toArray();
        return $setting;
    }

    // public static function getselectedThemeColor(){
    //     $color = env('THEME_COLOR');
    //     if($color == "" || $color == null){
    //         $color = 'blue';
    //     }
    //     return $color;
    // }

    public static function getDateFormated($date, $time = false)
    {
        if (!empty($date) && $date != '0000-00-00') {
            if ($time == true) {
                return date("d M Y H:i A", strtotime($date));
            } else {
                return date("d M Y", strtotime($date));
            }
        } else {
            return '';
        }
    }

    public static function InsertDataIn_payment_settings_Table($post, $workspace_id)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)',
                [
                    $data,
                    $key,
                    $workspace_id,
                    $created_at,
                    $updated_at,
                ]
            );
        }
    }
    public static function getPaymentSetting($workspace_id)
    {
        $data = DB::table('payment_settings');

        $settings = [
            'is_paystack_enabled' => 'off',
            'paystack_public_key' => '',
            'paystack_secret_key' => '',
            'is_flutterwave_enabled' => 'off',
            'flutterwave_public_key' => '',
            'flutterwave_secret_key' => '',
            'is_razorpay_enabled' => 'off',
            'razorpay_public_key' => '',
            'razorpay_secret_key' => '',
            'is_mercado_enabled' => 'off',
            'mercado_app_id' => '',
            'mercado_secret_key' => '',
            'is_paytm_enabled' => 'off',
            'paytm_mode' => 'local',
            'paytm_merchant_id' => '',
            'paytm_merchant_key' => '',
            'paytm_industry_type' => '',
            'is_mollie_enabled' => 'off',
            'mollie_api_key' => '',
            'mollie_profile_id' => '',
            'mollie_partner_id' => '',
            'is_skrill_enabled' => 'off',
            'skrill_email' => '',
            'is_coingate_enabled' => 'off',
            'coingate_mode' => 'sandbox',
            'coingate_auth_token' => '',
            'is_toyyibpay_enabled' => 'off',
            'toyyibpay_secret_key' => '',
            'toyyibpay_category_code' => '',
            'slack_webhook' => "",
            'project_notificaation' => 0,
            'task_notificaation' => 0,
            'taskmove_notificaation' => 0,
            'milestone_notificaation' => 0,
            'milestonest_notificaation' => 0,
            'taskcom_notificaation' => 0,
            'invoice_notificaation' => 0,
            'invoicest_notificaation' => 0,
            'cust_theme_bg' => 'on',
            'cust_darklayout' => 'off',
            'color' => 'theme-4',
            "SITE_RTL" => "off",
            "meta_image" => '',
            "meta_keywords" => '',
            "meta_description" => '',
            "is_payfast_enabled" => '',
            "payfast_merchant_id" => '',
            "payfast_merchant_key" => '',
            "payfast_signature" => '',
            "payfast_mode" => '',
            "disable_lang" => '',
            "mail_driver" => '',
            "mail_host" => '',
            "mail_port" => '',
            "mail_username" => '',
            "mail_password" => '',
            "mail_encryption" => '',
            "mail_from_address" => '',
            "mail_from_name" => '',
        ];

        if (!empty($workspace_id)) {
            $data = $data->where('created_by', '=', $workspace_id);
            $data = $data->get();

            foreach ($data as $row) {
                $settings[$row->name] = $row->value;
            }
        }
        return $settings;
    }

    public static function project_nm($project_name)
    {
        $taxArr = explode(',', $project_name);
        $lead = 0;
        foreach ($taxArr as $tax) {
            $tax = Project::find($tax);

            $lead = $tax->name;
        }

        return $lead;
    }

    public static function tax_nm($tax_name)
    {
        $taxArr = explode(',', $tax_name);
        $lead = 0;
        foreach ($taxArr as $tax) {
            $tax = Tax::find($tax);

            $lead = $tax->name;
        }

        return $lead;
    }

    public static function taxRate($client)
    {
        $taxArr = explode(',', $client);
        $taxRate = 0;
        foreach ($taxArr as $tax) {
            $tax = Client::find($tax);

            $taxRate = $tax->name;
        }

        return $taxRate;
    }

    public static function send_slack_msg($type, $user, $obj)
    {
        $user_lang = Workspace::where('id', $user)->first();
        $notification_template = NotificationTemplates::where('name', $type)->first();
        if (!empty($notification_template) && !empty($obj)) {
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user_lang->lang)->where('created_by', '=', $user_lang->created_by)->first();
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user_lang->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($type, $curr_noti_tempLang->content, $obj);
            }
        }
        $settings = Utility::getPaymentSetting($user);

        if (isset($settings['slack_webhook']) && !empty($settings['slack_webhook'])) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $settings['slack_webhook']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $msg]));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        }
    }

    public static function send_telegram_msg($type, $obj, $user)
    {
        $user_lang = Workspace::where('id', $user)->first();

        $notification_template = NotificationTemplates::where('name', $type)->first();
        if (!empty($notification_template) && !empty($obj)) {
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user_lang->lang)->where('created_by', '=', \Auth::user()->id)->first();
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user_lang->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($type, $curr_noti_tempLang->content, $obj);
            }
        }

        $settings = Utility::getPaymentSetting($user);

        // $msg = $resp;
        // Set your Bot ID and Chat ID.
        $telegrambot = $settings['telegram_token'];
        $telegramchatid = $settings['telegram_chatid'];
        // Function call with your own text or variable
        $url = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
        $data = array(
            'chat_id' => $telegramchatid,
            'text' => $msg,
        );
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $url = $url;
    }

    public static function get_logo()
    {
        $setting = Utility::getAdminPaymentSetting();

        if ($setting['cust_darklayout'] == 'on') {
            return 'logo-dark.png';
        } else {
            return 'logo-light.png';
        }
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    public static function getAdminPaymentSettings()
    {
        $data = DB::table('admin_payment_settings');
        $adminSettings = [
            'cust_theme_bg' => 'on',
            'cust_darklayout' => 'off',
            'color' => 'theme-4',
            'color_flag' => 'false',
            'enable_cookie' => 'on',
            "meta_image" => '',
            "meta_keywords" => 'Taskly SaaSkkk',
            "meta_description" => 'Taskly is a tool where you can Manage projects, work with clients, and collaborate with team members. All in one place.',

        ];

        $data = $data->get();
        foreach ($data as $row) {
            $adminSettings[$row->name] = $row->value;
        }

        return $adminSettings;
    }

    private static $getcompanySettings = NULL;
    public static function getcompanySettings($slug)
    {
        if (self::$getcompanySettings == null) {
            self::$getcompanySettings = self::fetchgetcompanySettings($slug);
        }
        return self::$getcompanySettings;
    }
    public static function fetchgetcompanySettings($slug)
    {
        $data = Workspace::where('id', $slug)->first();
        return $data;
    }


    private static $getcompanylogo = NULL;
    public static function getcompanylogo($slug)
    {
        if (self::$getcompanylogo == null) {
            self::$getcompanylogo = self::fetchgetcompanylogo($slug);
        }
        return self::$getcompanylogo;
    }
    public static function fetchgetcompanylogo($slug)
    {
        $data = Workspace::where('id', $slug)->first();

        if ($data->cust_darklayout == 'on') {

            if ($data->logo_white) {
                return $data->logo_white;
            } else {
                return 'logo-dark.png';
            }
        } else {

            if ($data->logo) {
                return $data->logo;
            } else {
                return 'logo-light.png';
            }
        }
    }

    public static function getActiveWorkSpace($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = \Auth::user()->id;
        }
        $user = User::find($user_id);

        if ($user) {
            if (!empty($user->currant_workspace)) {
                return $user->currant_workspace;
            } else {
                if ($user->type == 'admin') {
                    return 0;
                } else {
                    $workspace = WorkSpace::where('created_by', $user->id)->first();
                    return $workspace->id;
                }
            }
        }
    }

    public static function sendEmailSetup()
    {
        $settings = self::fetchAdminPaymentSetting();
        config(
            [
                'mail.driver' => $settings['mail_driver'],
                'mail.host' => $settings['mail_host'],
                'mail.port' => $settings['mail_port'],
                'mail.encryption' => $settings['mail_encryption'],
                'mail.username' => $settings['mail_username'],
                'mail.password' => $settings['mail_password'],
                'mail.from.address' => $settings['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'],
            ]
        );
    }

    public static function setMailConfig()
    {
        $usr = Auth::user();

        if ((isset($usr) && $usr->type == 'admin') || ($usr == null)) {
            $settings = self::fetchAdminPaymentSetting();
            $settings =
                [
                    'mail_driver' => $settings['mail_driver'],
                    'mail_host' => $settings['mail_host'],
                    'mail_port' => $settings['mail_port'],
                    'mail_username' => $settings['mail_username'],
                    'mail_password' => $settings['mail_password'],
                    'mail_encryption' => $settings['mail_encryption'],
                    'mail_from_address' => $settings['mail_from_address'],
                    'mail_from_name' => $settings['mail_from_name'],
                ];
        } else {
            $settings = self::getPaymentSetting($usr->currant_workspace);

            if (($settings['mail_host'] == null) || ($settings['mail_port'] == null) || ($settings['mail_username'] == null) || ($settings['mail_password'] == null) || ($settings['mail_encryption'] == null) || ($settings['mail_from_address'] == null) || ($settings['mail_from_name'] == null)) {
                $settings = self::fetchAdminPaymentSetting();

                $settings =
                    [
                        'mail_driver' => $settings['mail_driver'],
                        'mail_host' => $settings['mail_host'],
                        'mail_port' => $settings['mail_port'],
                        'mail_username' => $settings['mail_username'],
                        'mail_password' => $settings['mail_password'],
                        'mail_encryption' => $settings['mail_encryption'],
                        'mail_from_address' => $settings['mail_from_address'],
                        'mail_from_name' => $settings['mail_from_name'],
                    ];
            }
        }

        config(
            [
                'mail.driver' => $settings['mail_driver'],
                'mail.host' => $settings['mail_host'],
                'mail.port' => $settings['mail_port'],
                'mail.encryption' => $settings['mail_encryption'],
                'mail.username' => $settings['mail_username'],
                'mail.password' => $settings['mail_password'],
                'mail.from.address' => $settings['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'],
            ]
        );
    }

    // Email Template Modules Function START
    // Common Function That used to send mail with check all cases
    public static function sendEmailTemplate($emailTemplate, $user_id, $obj)
    {

        $usr = Auth::user();
        if ($user_id != $usr->id) {
            $mailTo = User::find($user_id)->email;
            // $mailTo = 'sovoci7068@vip4e.com';

            // find template is exist or not in our record
            $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

            if (isset($template) && !empty($template)) {
                // check template is active or not by company
                $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->id)->first();

                if ($is_active->is_active == 1) {

                    if (Auth::user()->type == 'admin') {
                        $settings = self::fetchAdminPaymentSetting();
                        $settings =
                            [
                                'mail_driver' => $settings['mail_driver'],
                                'mail_host' => $settings['mail_host'],
                                'mail_port' => $settings['mail_port'],
                                'mail_username' => $settings['mail_encryption'],
                                'mail_password' => $settings['mail_username'],
                                'mail_encryption' => $settings['mail_password'],
                                'mail_from_address' => $settings['mail_from_address'],
                                'mail_from_name' => $settings['mail_from_name'],
                            ];
                    } else {
                        $settings = self::getPaymentSetting($usr->currant_workspace);


                        if (($settings['mail_host'] == null) || ($settings['mail_port'] == null) || ($settings['mail_username'] == null) || ($settings['mail_password'] == null) || ($settings['mail_encryption'] == null) || ($settings['mail_from_address'] == null) || ($settings['mail_from_name'] == null)) {
                            $settings = self::fetchAdminPaymentSetting();

                            $settings =
                                [
                                    'mail_driver' => $settings['mail_driver'],
                                    'mail_host' => $settings['mail_host'],
                                    'mail_port' => $settings['mail_port'],
                                    'mail_username' => $settings['mail_username'],
                                    'mail_password' => $settings['mail_password'],
                                    'mail_encryption' => $settings['mail_encryption'],
                                    'mail_from_address' => $settings['mail_from_address'],
                                    'mail_from_name' => $settings['mail_from_name'],
                                ];
                        }
                    }

                    $user_lang = Workspace::where('id', $usr->currant_workspace)->first();

                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $user_lang->lang)->first();
                    $content->from = $template->from;

                    if (!empty($content->content)) {
                        $content->content = self::replaceVariable($emailTemplate, $content->content, $obj);
                        // send email
                        try {
                            config(
                                [
                                    'mail.driver' => $settings['mail_driver'],
                                    'mail.host' => $settings['mail_host'],
                                    'mail.port' => $settings['mail_port'],
                                    'mail.encryption' => $settings['mail_encryption'],
                                    'mail.username' => $settings['mail_username'],
                                    'mail.password' => $settings['mail_password'],
                                    'mail.from.address' => $settings['mail_from_address'],
                                    'mail.from.name' => $settings['mail_from_name'],
                                ]
                            );

                            Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                        } catch (\Exception $e) {
                            $error = __('E-Mail has been not sent due to SMTP configuration');
                        }

                        if (isset($error)) {
                            $arReturn = [
                                'is_success' => false,
                                'error' => $error,
                            ];
                        } else {
                            $arReturn = [
                                'is_success' => true,
                                'error' => false,
                            ];
                        }
                    } else {
                        $arReturn = [
                            'is_success' => false,
                            'error' => __('Mail not send, email is empty'),
                        ];
                    }

                    return $arReturn;
                } else {
                    return [
                        'is_success' => true,
                        'error' => false,
                    ];
                }
            } else {
                return [
                    'is_success' => false,
                    'error' => __('Mail not send, email not found'),
                ];
            }
        }
    }

    public static function sendclientEmailTemplate($emailTemplate, $user_id, $obj)
    {
        $usr = Auth::user();

        if ($user_id != $usr->id) {
            $mailTo = Client::find($user_id)->email;

            if ($usr->type != 'admin') {
                // find template is exist or not in our record
                $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

                if (isset($template) && !empty($template)) {
                    // check template is active or not by company
                    $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->id)->first();

                    if ($is_active->is_active == 1) {

                        if (Auth::user()->type == 'admin') {
                            $settings = self::fetchAdminPaymentSetting();
                            $settings =
                                [
                                    'mail_driver' => $settings['mail_driver'],
                                    'mail_host' => $settings['mail_host'],
                                    'mail_port' => $settings['mail_port'],
                                    'mail_username' => $settings['mail_encryption'],
                                    'mail_password' => $settings['mail_username'],
                                    'mail_encryption' => $settings['mail_password'],
                                    'mail_from_address' => $settings['mail_from_address'],
                                    'mail_from_name' => $settings['mail_from_name'],
                                ];
                        } else {
                            $settings = self::getPaymentSetting($usr->currant_workspace);


                            if (($settings['mail_host'] == null) || ($settings['mail_port'] == null) || ($settings['mail_username'] == null) || ($settings['mail_password'] == null) || ($settings['mail_encryption'] == null) || ($settings['mail_from_address'] == null) || ($settings['mail_from_name'] == null)) {
                                $settings = self::fetchAdminPaymentSetting();

                                $settings =
                                    [
                                        'mail_driver' => $settings['mail_driver'],
                                        'mail_host' => $settings['mail_host'],
                                        'mail_port' => $settings['mail_port'],
                                        'mail_username' => $settings['mail_username'],
                                        'mail_password' => $settings['mail_password'],
                                        'mail_encryption' => $settings['mail_encryption'],
                                        'mail_from_address' => $settings['mail_from_address'],
                                        'mail_from_name' => $settings['mail_from_name'],
                                    ];
                            }
                        }

                        //  $settings = self::setMailConfig();
                        $user_lang = Workspace::where('id', $usr->currant_workspace)->first();
                        // get email content language base
                        $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $user_lang->lang)->first();
                        $content->from = $template->from;
                        if (!empty($content->content)) {
                            $content->content = self::replaceVariable($emailTemplate, $content->content, $obj);

                            // send email
                            try {

                                config(
                                    [
                                        'mail.driver' => $settings['mail_driver'],
                                        'mail.host' => $settings['mail_host'],
                                        'mail.port' => $settings['mail_port'],
                                        'mail.encryption' => $settings['mail_encryption'],
                                        'mail.username' => $settings['mail_username'],
                                        'mail.password' => $settings['mail_password'],
                                        'mail.from.address' => $settings['mail_from_address'],
                                        'mail.from.name' => $settings['mail_from_name'],
                                    ]
                                );
                                Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                            } catch (\Exception $e) {
                                $error = __('E-Mail has been not sent due to SMTP configuration');
                            }

                            if (isset($error)) {
                                $arReturn = [
                                    'is_success' => false,
                                    'error' => $error,
                                ];
                            } else {
                                $arReturn = [
                                    'is_success' => true,
                                    'error' => false,
                                ];
                            }
                        } else {
                            $arReturn = [
                                'is_success' => false,
                                'error' => __('Mail not send, email is empty'),
                            ];
                        }

                        return $arReturn;
                    } else {
                        return [
                            'is_success' => true,
                            'error' => false,
                        ];
                    }
                } else {
                    return [
                        'is_success' => false,
                        'error' => __('Mail not send, email not found'),
                    ];
                }
            }
        }
    }

    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable( $content, $obj,$name = '')
    {
        $arrVariable = [
            '{project_name}',
            '{project_status}',
            '{app_name}',
            '{company_name}',
            '{email}',
            '{password}',
            '{app_url}',
            '{workspace_name}',
            '{user_name}',
            '{owner_name}',
            '{client_name}',
            '{contract_subject}',
            '{contract_type}',
            '{value}',
            '{start_date}',
            '{end_date}',
            '{task_title}',
            '{old_stage}',
            '{new_stage}',
            '{milestone_title}',
            '{milestone_status}',
            '{invoice_id}',
            '{invoice_status}',
            '{total_amount}',
            '{paid_amount}',
        ];



        $arrValue = [
            'project_name' => '-',
            'project_status' => '-',
            'app_name' => '-',
            'company_name' => '-',
            'email' => '-',
            'password' => '-',
            'app_url' => '-',
            'workspace_name' => '-',
            'user_name' => '-',
            'owner_name' => '-',
            'client_name' => '-',
            'contract_subject' => '-',
            'contract_type' => '-',
            'value' => '-',
            'start_date' => '-',
            'end_date' => '-',
            'task_title' => '-',
            'old_stage' => '-',
            'new_stage' => '-',
            'milestone_title' => '-',
            'milestone_status' => '-',
            'invoice_id' => '-',
            'invoice_status' => '-',
            'total_amount' => '-',
            'paid_amount' => '-',
        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }
        $setting = Utility::fetchAdminPaymentSetting();
        $arrValue['app_name'] = $setting['APP_NAME'];
        $arrValue['app_url'] = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Make Entry in email_tempalte_lang table when create new language
    public static function makeEmailLang($lang)
    {
        $template = EmailTemplate::all();
        foreach ($template as $t) {
            $default_lang = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
            $emailTemplateLang = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang = $lang;
            $emailTemplateLang->subject = $default_lang->subject;
            $emailTemplateLang->from = $default_lang->from;
            $emailTemplateLang->content = $default_lang->content;
            $emailTemplateLang->save();
        }
    }
    // Email Template Modules Function END

    ///========================== for Storage setting ===============================================================////

    public static function upload_file($request, $key_name, $name, $path, $custom_validation = [])
    {
        try {
            $settings = Utility::getAdminPaymentSetting();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';
                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';
                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $request->$key_name;

                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];
                }

                $validator = \Validator::make($request->all(), [
                    $key_name => $validation,
                ]);

                if ($validator->fails()) {

                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {
                    $name = $name;
                    if ($settings['storage_setting'] == 'local') {

                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path . $name;

                        // \Storage::disk()->putFileAs(
                        //     $path,
                        //     $request->file($key_name),
                        //     $name
                        // );
                        //$path = $path.$name;

                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;

                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                        // $path = $path.$name;
                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }
            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }
        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function get_file($path)
    {
        $settings = Utility::getAdminPaymentSetting();

        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                    ]
                );
            } elseif ($settings['storage_setting'] == 's3') {
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function colorCodeData($type)
    {
        if ($type == 'event') {
            return 1;
        } elseif ($type == 'zoom_meeting') {
            return 2;
        } elseif ($type == 'task') {
            return 3;
        } elseif ($type == 'appointment') {
            return 11;
        } elseif ($type == 'rotas') {
            return 3;
        } elseif ($type == 'holiday') {
            return 4;
        } elseif ($type == 'call') {
            return 10;
        } elseif ($type == 'meeting') {
            return 5;
        } elseif ($type == 'leave') {
            return 6;
        } elseif ($type == 'work_order') {
            return 7;
        } elseif ($type == 'lead') {
            return 7;
        } elseif ($type == 'deal') {
            return 8;
        } elseif ($type == 'interview_schedule') {
            return 9;
        } else {
            return 11;
        }
    }

    public static $colorCode = [
        1 => 'event-warning',
        2 => 'event-secondary',
        3 => 'event-success',
        4 => 'event-warning',
        5 => 'event-danger',
        6 => 'event-dark',
        7 => 'event-black',
        8 => 'event-info',
        9 => 'event-secondary',
        10 => 'event-success',
        11 => 'event-warning',

    ];

    public static function googleCalendarConfig($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        // $setting = Utility::settings();
        // $path = storage_path('storage/googlecalender/' . $currentWorkspace['google_calender_json_file']);
        $path = storage_path() . '/' . $currentWorkspace['google_calender_json_file'];
        config([
            'google-calendar.default_auth_profile' => 'service_account',
            'google-calendar.auth_profiles.service_account.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.token_json' => $path,
            'google-calendar.calendar_id' => isset($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '',
            'google-calendar.user_to_impersonate' => '',

        ]);
        // return $arrayJson;
    }


    public static function getCalendarData($slug, $type)
    {

        $a = self::googleCalendarConfig($slug);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $data = Event::get();

        $type = self::colorCodeData($type);
        $arrayJson = [];
        foreach ($data as $val) {
            $end_date = date_create($val->endDateTime);
            date_add($end_date, date_interval_create_from_date_string("1 days"));

            if ($val->colorId == "$type") {

                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->summary,
                    "start" => $val->startDateTime,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => self::$colorCode[$type],
                    "allDay" => true,
                ];
            }
        }
        return $arrayJson;
    }

    public static function addCalendarData($request, $type, $slug)
    {

        $a = self::googleCalendarConfig($slug);
        if ($a) {

            $event = new Event();
            $event->name = $request->title;
            $event->startDateTime = Carbon::parse($request->start_date);
            $event->endDateTime = Carbon::parse($request->end_date);
            $event->colorId = self::colorCodeData($type);
            $event->save();
        } else {
            return 'google calender task not created';
        }
    }

    public static function webhookSetting($module, $user_id = null)
    {
        if (\Auth::check()) {
            $webhook = Webhook::where('module', $module)->where('created_by', '=', Auth::user()->id)->first();
        } else {
            $webhook = Webhook::where('module', $module)->where('created_by', '=', $user_id)->first();
        }

        if (!empty($webhook)) {
            $url = $webhook->url;
            $method = $webhook->method;
            $reference_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $data['method'] = $method;
            $data['reference_url'] = $reference_url;
            $data['url'] = $url;
            return $data;
        }
        return false;
    }

    public static function WebhookCall($url = null, $parameter = null, $method = 'POST')
    {

        if (!empty($url) && !empty($parameter)) {

            try {

                $curlHandle = curl_init($url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                $curlResponse = curl_exec($curlHandle);
                curl_close($curlHandle);
                if (empty($curlResponse)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }

    static function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';

        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {

            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }

    public function extraKeyword()
    {
        $keyArr = [
            __('Sun'),
            __('Mon'),

        ];
    }


    // start for (plans) storage limit - for file upload size
    public static function updateStorageLimit($company_id, $image_size)
    {
        $image_size = number_format($image_size / 1048576, 2);
        $user = User::find($company_id);
        $plan = Plan::find($user->plan);
        $total_storage = $user->storage_limit + $image_size;
        if ($plan->storage_limit <= $total_storage && $plan->storage_limit != -1) {
            $error = __('Plan storage limit is over so please upgrade the plan.');
            return $error;
        } else {
            $user->storage_limit = $total_storage;
        }

        $user->save();
        return 1;
    }

    public static function changeStorageLimit($company_id, $file_path)
    {
        $files = \File::glob(storage_path($file_path));

        $fileSize = 0;
        foreach ($files as $file) {
            $fileSize += \File::size($file);
        }
        $image_size = number_format($fileSize / 1048576, 2);
        $user = User::find($company_id);
        $plan = Plan::find($user->plan);
        $total_storage = $user->storage_limit - $image_size;
        $user->storage_limit = $total_storage;
        $user->save();

        $status = false;
        foreach ($files as $key => $file) {
            if (\File::exists($file)) {
                $status = \File::delete($file);
            }
        }

        return true;
    }
    // end for (plans) storage limit - for file upload size


    public static function flagOfCountry()
    {
        // $arr = [
        //     'ar' => 'AR 🇦🇪',
        //     'da' => 'DA 🇩🇰',
        //     'de' => 'DE 🇩🇪',
        //     'es' => 'ES 🇪🇸',
        //     'fr' => 'FR 🇫🇷',
        //     'it' => 'IT 🇮🇹',
        //     'ja' => 'JA 🇯🇵',
        //     'nl' => 'NL 🇳🇱',
        //     'pl'=> 'PL 🇵🇱',
        //     'ru' => 'RU 🇷🇺',
        //     'pt' => 'PT 🇵🇹',
        //     'en' => 'EN 🇮🇳',
        //     'tr' => 'TR 🇹🇷',
        //     'pt-br' => 'PT-BR 🇵🇹',
        // ];
        $arr = [
            'ar' => 'arabic 🇦🇪',
            'da' => 'danish 🇩🇰',
            'de' => 'german 🇩🇪',
            'en' => 'english 🇮🇳',
            'es' => 'spanish 🇪🇸',
            'fr' => 'french 🇫🇷',
            'it' => 'italian 🇮🇹',
            'ja' => 'japanese 🇯🇵',
            'nl' => 'dutch 🇳🇱',
            'pl' => 'polish 🇵🇱',
            'ru' => 'russian 🇷🇺',
            'pt' => 'portuguese 🇵🇹',
            'tr' => 'turkish 🇹🇷',
            'pt-br' => 'portuguese(BR) 🇵🇹',
        ];
        return $arr;
    }

    // private static $getlang_fullname = NULL;
    // public static function getlang_fullname($code)
    // {
    //     if (self::$getlang_fullname == null) {
    //         self::$getlang_fullname = self::fetchGetLangFullname($code);
    //         return self::$getlang_fullname;
    //     }
    //     return self::$getlang_fullname;
    // }
    public static function getlang_fullname($code)
    {
        $languages = Utility::langList();
        if (\Schema::hasTable('languages')) {
            $lang = Languages::where('lang_code', '=', $code)->first();
            $languages = isset($lang->lang_fullname) ? $lang->lang_fullname : 'English';
            return $languages;
        }
        return $languages;
    }

    public static function langList()
    {
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Türkçe",
            "pt-br" => "Portuguese(BR)",
        ];
        return $languages;
    }

    public static function plancheck($user_id = null)
    {
        if (!empty($user_id)) {
            $user = User::where('id', $user_id)->first();
        } elseif (\Auth::check()) {
            $user = Auth::user();
        }
        // if ($user->type != 'owner') {
        //     $user = User::where('id', $user->created_by)->first();
        // }
        $plan_data = [
            "enable_chatgpt" => 'off',
        ];

        if ($user != null && $user->plan) {
            $plan = Plan::where('id', $user->plan)->first();

            $plan_data = [
                "enable_chatgpt" => $plan->enable_chatgpt,
            ];
        }
        return $plan_data;
    }

    public static function getValByName($key)
    {
        $setting = Utility::getAdminPaymentSetting($key);
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }
        return $setting[$key];
    }
    public static function getPusherSetting()
    {
        $settings = Utility::getAdminPaymentSetting();
        if ($settings) {
            config([
                'chatify.pusher.key' => isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '',
                'chatify.pusher.secret' => isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '',
                'chatify.pusher.app_id' => isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '',
                'chatify.pusher.options.cluster' => isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '',
            ]);

            return $settings;
        }
    }

    // public static function setCaptchaConfig()
    // {
    //     $settings = Utility::getAdminPaymentSetting();
    //     config([
    //         'captcha.secret' => $settings['google_recaptcha_key'],
    //         'captcha.sitekey' => $settings['google_recaptcha_secret'] ,
    //     ]);
    // }
    public static function referralTransaction($planData, $company = '')
    {
        if ($company != '') {
            $objUser = $company;
        } else {
            $objUser = Auth::user();
        }
        $user = ReferralTransaction::where('company_id', $objUser->id)->first();

        $referralSetting = ReferralSetting::where('created_by', 1)->first();


        if ($objUser->referral_used != 0 && $user == null && (isset($referralSetting) && $referralSetting->is_enable == 1)) {
            $transaction = new ReferralTransaction();
            $transaction->company_id = $objUser->id;
            $transaction->plan_id = $planData['plan_id'];
            $transaction->frequency = $planData['plan_frequency'];
            $transaction->plan_price = $planData['plan_price'];
            $transaction->commission = $referralSetting->percentage;
            $transaction->referral_code = $objUser->referral_used;
            $transaction->save();

            $commissionAmount = ($planData['plan_price'] * $referralSetting->percentage) / 100;
            $user = User::where('referral_code', $objUser->referral_used)->first();

            $user->commission_amount = $user->commission_amount + $commissionAmount;
            $user->save();
        }
    }


    public static function getPlanActualPrice($planId, $planFrequency)
    {
        $plan = Plan::find($planId);
        if ($planFrequency == 'monthly') {
            $planPrice = $plan->monthly_price;
        } else {
            $planPrice = $plan->annual_price;
        }
        return $planPrice;
    }
}
