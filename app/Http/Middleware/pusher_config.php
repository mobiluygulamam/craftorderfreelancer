<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class pusher_config
{
    public function handle(Request $request, Closure $next): Response
    {
        if ( \Schema::hasTable('settings') === true) {
            $settings = Utility::getAdminPaymentSetting();
            if ($settings) {
                config([
                    'chatify.pusher.key' => isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '',
                    'chatify.pusher.secret' => isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '',
                    'chatify.pusher.app_id' => isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '',
                    'chatify.pusher.options.cluster' => isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '',
                ]);
            }
        }

        return $next($request);
    }
}
