<?php

namespace App\Http\Controllers;

use App\Models\Mail\EmailTest;
use App\Models\Utility;
use App\Models\Workspace;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $workspace = new Workspace();
            $payment_detail = Utility::getAdminPaymentSetting();
            $setting = DB::table('admin_payment_settings')->pluck('value', 'name')->toArray();
            $setting['mail_encryption']= '';
            return view('setting', compact('workspace', 'payment_detail', 'setting'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $dir = 'logo/';

            if ($request->favicon) {
                $logo_favicon_Name = 'favicon.png';
                // $request->validate(['favicon' => 'required|image|mimes:png|max:204800']);

                $validator = \Validator::make($request->all(), [
                    'favicon' => 'required|image|mimes:png,jpg,jpeg',
                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }
                //  $request->validate(['favicon' => 'required|image|mimes:png|max:204800']);


                $path = Utility::upload_file($request, 'favicon', $logo_favicon_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $favicon = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            if ($request->logo_blue) {
                $logo_dark_Name = 'logo-light.png';
                // $request->validate(['logo_blue' => 'required|image|mimes:png|max:204800']);

                $validator = \Validator::make($request->all(), [
                    'logo_blue' => 'required|image|mimes:png,jpg,jpeg',

                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }

                $path = Utility::upload_file($request, 'logo_blue', $logo_dark_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $logo_blue = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            if ($request->logo_white) {
                $logo_white_Name = 'logo-dark.png';
                // $request->validate(['logo_white' => 'required|image|mimes:png|max:204800']);
                $validator = \Validator::make($request->all(), [
                    'logo_white' => 'required|image|mimes:png,jpg,jpeg',

                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }
                $path = Utility::upload_file($request, 'logo_white', $logo_white_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $logo_white = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $rules = [
                'app_name' => 'required|string|max:50',
                'default_language' => 'required|string|max:50',
                'footer_text' => 'required|string|max:50',

            ];

            $request->validate($rules);
            $cookie_text = (!isset($request->cookie_text) && empty($request->cookie_text)) ? '' : $request->cookie_text;
            $arrEnv = [
                // 'APP_NAME' => $request->app_name,
                // 'DEFAULT_ADMIN_LANG' => $request->default_language,
                // 'FOOTER_TEXT' => $request->footer_text,
                // 'DISPLAY_LANDING' => $request->display_landing ? 'on' : 'off',
                // 'SITE_RTL' => $request->site_rtl ?? 'off',
                // 'SIGNUP_BUTTON' => !isset($request->SIGNUP_BUTTON) ? 'off' : 'on',
                // 'email_verification' => !isset($request->email_verification) ? 'off' : 'on',
            ];
            // Utility::setEnvironmentValue($arrEnv);
            $post['app_name'] = $request->app_name;
            $post['default_admin_lang'] = $request->default_language;
            $post['footer_text'] = $request->footer_text;
            $post['display_landing'] = $request->display_landing ? 'on' : 'off';
            $post['site_rtl'] = $request->site_rtl ?? 'off';
            $post['signup_button'] = !isset($request->SIGNUP_BUTTON) ? 'off' : 'on';
            $post['email_verification'] = !isset($request->email_verification) ? 'off' : 'on';

            $color = (!empty($request->color)) ? $request->color : 'theme-4';
            $post['color'] = $color;

            if (isset($request->color) && $request->color_flag == 'false') {
                $post['color'] = $request->color;
                $post['color_flag'] = $request->color_flag;
            } else {
                $post['color'] = $request->custom_color;
                $post['color_flag'] = $request->color_flag;
            }


            $cust_theme_bg = (!empty($request->cust_theme_bg)) ? 'on' : 'off';
            $post['cust_theme_bg'] = $cust_theme_bg;

            $cust_darklayout = !empty($request->cust_darklayout) ? 'on' : 'off';
            $post['cust_darklayout'] = $cust_darklayout;

            $email_verification = !empty($request->email_verification) ? 'on' : 'off';
            $post['email_verification'] = $email_verification;

            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                foreach ($post as $key => $data) {
                    \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                        $data,
                        $key,
                        $created_at,
                        $updated_at,
                    ]);
                }
            }

            return redirect()->back()->with('success', __('Setting updated successfully'));
            // if ($this->setEnvironmentValue($arrEnv)) {
            //     return redirect()->back()->with('success', __('Setting updated successfully'));
            // } else {
            //     return redirect()->back()->with('error', __('Something is wrong'));
            // }
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function seosetting(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'meta_keywords' => 'required',
                'meta_description' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->meta_image) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'meta_image' => 'required',
                ]
            );

            $img_name = time() . '_' . 'meta_image.png';
            $dir = 'uploads/logo/';
            $validation = [
                'max:' . '20480',
            ];
            $path = Utility::upload_file($request, 'meta_image', $img_name, $dir, $validation);
            if ($path['flag'] == 1) {
                $logo_dark = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['meta_image'] = $img_name;
        }
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $post['meta_keywords'] = $request->meta_keywords;
        $post['meta_description'] = $request->meta_description;
        foreach ($post as $key => $data) {
            \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function saveCookieSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_description' => 'required',
                'contactus_url' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        $post = $request->all();

        unset($post['_token']);

        if ($request->enable_cookie) {
            $post['enable_cookie'] = 'on';
        } else {
            $post['enable_cookie'] = 'off';
        }

        if ($request->cookie_logging) {
            $post['cookie_logging'] = 'on';
        } else {
            $post['cookie_logging'] = 'off';
        }

        if ($request->cookie_logging) {
            $post['necessary_cookies'] = 'on';
        } else {
            $post['necessary_cookies'] = 'off';
        }

        if ($request->cookie_title) {
            $post['cookie_title'] = $request->cookie_title;
        }
        if ($request->cookie_description) {
            $post['cookie_description'] = $request->cookie_description;
        }

        if ($request->strictly_cookie_title) {
            $post['strictly_cookie_title'] = $request->strictly_cookie_title;
        }

        if ($request->strictly_cookie_description) {
            $post['strictly_cookie_description'] = $request->strictly_cookie_description;
        }

        if ($request->more_information_description) {
            $post['more_information_description'] = $request->more_information_description;
        }

        if ($request->contactus_url) {
            $post['contactus_url'] = $request->contactus_url;
        }

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach ($post as $key => $data) {
            \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', 'Cookie setting successfully saved.');
    }



    public function CookieConsent(Request $request)
    {
        $settings = Utility::getAdminPaymentSetting();

        if ($settings['enable_cookie'] == "on" && $settings['cookie_logging'] == "on") {
            $allowed_levels = ['necessary', 'analytics', 'targeting'];
            $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                return in_array($level, $allowed_levels);
            });
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            // Generate new CSV line
            $browser_name = $whichbrowser->browser->name ?? null;
            $os_name = $whichbrowser->os->name ?? null;
            $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $device_type = Utility::get_device_type($_SERVER['HTTP_USER_AGENT']);

            $ip = $_SERVER['REMOTE_ADDR'];
            // $ip = '49.36.83.154';
            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));


            $date = (new \DateTime())->format('Y-m-d');
            $time = (new \DateTime())->format('H:i:s') . ' UTC';


            $new_line = implode(',', [
                $ip,
                $date,
                $time,
                json_encode($request['cookie']),
                $device_type,
                $browser_language,
                $browser_name,
                $os_name,
                isset($query) ? $query['country'] : '',
                isset($query) ? $query['region'] : '',
                isset($query) ? $query['regionName'] : '',
                isset($query) ? $query['city'] : '',
                isset($query) ? $query['zip'] : '',
                isset($query) ? $query['lat'] : '',
                isset($query) ? $query['lon'] : ''
            ]);

            if (!file_exists(storage_path() . '/uploads/sample/data.csv')) {

                $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                file_put_contents(storage_path() . '/uploads/sample/data.csv', $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            file_put_contents(storage_path() . '/uploads/sample/data.csv', $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);

            return response()->json('success');
        }
        return response()->json('error');
    }

    public function emailSettingStore(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];
            $request->validate($rules);

            $post['mail_driver'] = $request->mail_driver;
            $post['mail_host'] = $request->mail_host;
            $post['mail_port'] = $request->mail_port;
            $post['mail_username'] = $request->mail_username;
            $post['mail_password'] = $request->mail_password;
            $post['mail_encryption'] = '';
            $post['mail_from_address'] = $request->mail_from_address;
            $post['mail_from_name'] = $request->mail_from_name;
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,
                ]);
            }
            return redirect()->back()->with('success', __('Setting updated successfully'));

            // $arrEnv = [

            //     'MAIL_DRIVER' => $request->mail_driver,
            //     'MAIL_HOST' => $request->mail_host,
            //     'MAIL_PORT' => $request->mail_port,
            //     'MAIL_USERNAME' => $request->mail_username,
            //     'MAIL_PASSWORD' => $request->mail_password,
            //     'MAIL_ENCRYPTION' => $request->mail_encryption,
            //     'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            //     'MAIL_FROM_NAME' => $request->mail_from_name,
            // ];
            // Utility::setEnvironmentValue($arrEnv);

            // Artisan::call('config:cache');
            // Artisan::call('config:clear');

            // if ($this->setEnvironmentValue($arrEnv)) {
            //     return redirect()->back()->with('success', __('Setting updated successfully'));
            // }
            // else {
            //     return redirect()->back()->with('error', __('Something is wrong'));
            // }
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function paymentSettingStore(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $validate = [
                'currency' => 'required|string|max:50',
                'currency_symbol' => 'required|string|max:50',
            ];


            if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {
                $validate['bank_details'] = 'required|string';
            }

            if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {
                $validate['stripe_key'] = 'required|string';
                $validate['stripe_secret'] = 'required|string';
            }
            if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {
                $validate['paypal_client_id'] = 'required|string';
                $validate['paypal_secret_key'] = 'required|string';
            }
            if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
                $validate['paystack_public_key'] = 'required|string';
                $validate['paystack_secret_key'] = 'required|string';
            }
            if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
                $validate['flutterwave_public_key'] = 'required|string';
                $validate['flutterwave_secret_key'] = 'required|string';
            }
            if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
                $validate['razorpay_public_key'] = 'required|string';
                $validate['razorpay_secret_key'] = 'required|string';
            }
            if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
                $validate['mercado_access_token'] = 'required|string';
                $validate['mercado_mode'] = 'required|string';
            }
            if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
                $validate['paytm_mode'] = 'required|string';
                $validate['paytm_merchant_id'] = 'required|string';
                $validate['paytm_merchant_key'] = 'required|string';
                $validate['paytm_industry_type'] = 'required|string';
            }
            if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
                $validate['mollie_api_key'] = 'required|string';
                $validate['mollie_profile_id'] = 'required|string';
                $validate['mollie_partner_id'] = 'required|string';
            }
            if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
                $validate['skrill_email'] = 'required|email';
            }
            if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
                $validate['coingate_mode'] = 'required|string';
                $validate['coingate_auth_token'] = 'required|string';
            }

            if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {
                $validate['paymentwall_public_key'] = 'required|string';
                $validate['paymentwall_private_key'] = 'required|string';
            }

            if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {
                $validate['toyyibpay_secret_key'] = 'required|string';
                $validate['toyyibpay_category_code'] = 'required|string';
            }

            if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {
                $validate['payfast_merchant_key'] = 'required|string';
                $validate['payfast_merchant_id'] = 'required|string';
                $validate['payfast_signature'] = 'required|string';
                $validate['payfast_mode'] = 'required|string';
            }

            if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {
                $validate['iyzipay_api_key'] = 'required|string';
                $validate['iyzipay_secret_key'] = 'required|string';
                $validate['iyzipay_mode'] = 'required|string';
            }

            if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {
                $validate['sspay_secret_key'] = 'required|string';
                $validate['sspay_category_code'] = 'required|string';
            }

            if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {
                $validate['paytabs_region'] = 'required|string';
                $validate['paytab_server_key'] = 'required|string';
                $validate['paytabs_profile_id'] = 'required|string';
            }

            if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
                $validate['benefit_secret_key'] = 'required|string';
                $validate['benefit_publishable_key'] = 'required|string';
            }

            if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
                $validate['cashfree_api_key'] = 'required|string';
                $validate['cashfree_secret_key'] = 'required|string';
            }

            if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
                $validate['aamarpay_mode'] = 'required|string';
                $validate['aamarpay_store_id'] = 'required|string';
                $validate['aamarpay_signature_key'] = 'required|string';
                $validate['aamarpay_description'] = 'required|string';
            }

            if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
                $validate['paytr_merchant_id'] = 'required|string';
                $validate['paytr_merchant_key'] = 'required|string';
                $validate['paytr_merchant_salt'] = 'required|string';
            }

            if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
                $validate['is_midtrans_enabled'] = 'required|string';
                $validate['midtrans_server_key'] = 'required|string';
            }

            if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
                $validate['is_xendit_enabled'] = 'required|string';
                $validate['xendit_api_key'] = 'required|string';
                $validate['xendit_token'] = 'required|string';
            }


            if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
                $validate['is_yookassa_enabled'] = 'required|string';
                $validate['yookassa_shopid'] = 'required|string';
                $validate['yookassa_secret_key'] = 'required|string';
            }

            if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
                $validate['is_paiementpro_enabled'] = 'required|string';
                $validate['paiementpro_merchant_id'] = 'required|string';
            }

            if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
                $validate['is_nepalste_enabled'] = 'required';
                $validate['nepalste_mode'] = 'required';
                $validate['nepalste_public_key'] = 'required';
            }


            if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
                $validate['is_cinetpay_enabled'] = 'required';
                $validate['cinetpay_api_key'] = 'required';
                $validate['cinetpay_site_id'] = 'required';
            }

            if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
                $validate['is_fedapay_enabled'] = 'required';
                $validate['fedapay_mode'] = 'required';
                $validate['fedapay_public_key'] = 'required';
                $validate['fedapay_secret_key'] = 'required';
            }

            if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
                $validate['is_payhere_enabled'] = 'required';
                $validate['payhere_mode'] = 'required';
                $validate['payhere_merchant_id'] = 'required';
                $validate['payhere_merchant_secret'] = 'required';
                $validate['payhere_app_id'] = 'required';
                $validate['payhere_app_secret'] = 'required';
            }

            $validator = Validator::make(
                $request->all(),
                $validate
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            // $arrEnv = [
            //     'CURRENCY' => $request->currency,
            //     'CURRENCY_SYMBOL' => $request->currency_symbol,
            // ];


            // $this->setEnvironmentValue($arrEnv);


            $post['currency'] = $request->currency;
            $post['currency_symbol'] = $request->currency_symbol;

            if (isset($request->is_manual_enabled) && $request->is_manual_enabled == 'on') {
                $post['is_manual_enabled'] = $request->is_manual_enabled;
            } else {
                $post['is_manual_enabled'] = 'off';
            }

            if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {
                $post['is_bank_enabled'] = $request->is_bank_enabled;
                $post['bank_details'] = $request->bank_details;
            } else {
                $post['is_bank_enabled'] = 'off';
            }

            if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {
                $post['is_stripe_enabled'] = $request->is_stripe_enabled;
                $post['stripe_key'] = $request->stripe_key;
                $post['stripe_secret'] = $request->stripe_secret;
                // $post['stripe_webhook_secret'] = $request->stripe_webhook_secret;
            } else {
                $post['is_stripe_enabled'] = 'off';
            }

            // Save Paypal Detail
            if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {
                $post['is_paypal_enabled'] = $request->is_paypal_enabled;
                $post['paypal_mode'] = $request->paypal_mode;
                $post['paypal_client_id'] = $request->paypal_client_id;
                $post['paypal_secret_key'] = $request->paypal_secret_key;
            } else {
                $post['is_paypal_enabled'] = 'off';
            }

            // Save Paystack Detail
            if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
                $post['is_paystack_enabled'] = $request->is_paystack_enabled;
                $post['paystack_public_key'] = $request->paystack_public_key;
                $post['paystack_secret_key'] = $request->paystack_secret_key;
            } else {
                $post['is_paystack_enabled'] = 'off';
            }

            // Save Fluuterwave Detail
            if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
                $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
                $post['flutterwave_public_key'] = $request->flutterwave_public_key;
                $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
            } else {
                $post['is_flutterwave_enabled'] = 'off';
            }

            // Save Razorpay Detail
            if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
                $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
                $post['razorpay_public_key'] = $request->razorpay_public_key;
                $post['razorpay_secret_key'] = $request->razorpay_secret_key;
            } else {
                $post['is_razorpay_enabled'] = 'off';
            }
            // Save mercado Detail
            if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
                $request->validate(
                    [
                        'mercado_access_token' => 'required|string',
                    ]
                );
                $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                $post['mercado_access_token'] = $request->mercado_access_token;
                $post['mercado_mode'] = $request->mercado_mode;
            } else {
                $post['is_mercado_enabled'] = 'off';
            }

            // Save Paytm Detail
            if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
                $post['is_paytm_enabled'] = $request->is_paytm_enabled;
                $post['paytm_mode'] = $request->paytm_mode;
                $post['paytm_merchant_id'] = $request->paytm_merchant_id;
                $post['paytm_merchant_key'] = $request->paytm_merchant_key;
                $post['paytm_industry_type'] = $request->paytm_industry_type;
            } else {
                $post['is_paytm_enabled'] = 'off';
            }

            // Save Mollie Detail
            if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
                $post['is_mollie_enabled'] = $request->is_mollie_enabled;
                $post['mollie_api_key'] = $request->mollie_api_key;
                $post['mollie_profile_id'] = $request->mollie_profile_id;
                $post['mollie_partner_id'] = $request->mollie_partner_id;
            } else {
                $post['is_mollie_enabled'] = 'off';
            }

            // Save Skrill Detail
            if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
                $post['is_skrill_enabled'] = $request->is_skrill_enabled;
                $post['skrill_email'] = $request->skrill_email;
            } else {
                $post['is_skrill_enabled'] = 'off';
            }

            // Save Coingate Detail
            if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
                $post['is_coingate_enabled'] = $request->is_coingate_enabled;
                $post['coingate_mode'] = $request->coingate_mode;
                $post['coingate_auth_token'] = $request->coingate_auth_token;
            } else {
                $post['is_coingate_enabled'] = 'off';
            }

            if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {

                $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
                $post['paymentwall_public_key'] = $request->paymentwall_public_key;
                $post['paymentwall_private_key'] = $request->paymentwall_private_key;
            } else {
                $post['is_paymentwall_enabled'] = 'off';
            }

            // Save Toyyibpay Detail
            if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {

                $post['is_toyyibpay_enabled'] = $request->is_toyyibpay_enabled;
                $post['toyyibpay_secret_key'] = $request->toyyibpay_secret_key;
                $post['toyyibpay_category_code'] = $request->toyyibpay_category_code;
            } else {
                $post['is_toyyibpay_enabled'] = 'off';
            }

            // Save payfast Detail
            if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {

                $post['is_payfast_enabled'] = $request->is_payfast_enabled;
                $post['payfast_merchant_key'] = $request->payfast_merchant_key;
                $post['payfast_merchant_id'] = $request->payfast_merchant_id;
                $post['payfast_signature'] = $request->payfast_signature;
                $post['payfast_mode'] = $request->payfast_mode;
            } else {
                $post['is_payfast_enabled'] = 'off';
            }

            // Save iyzipay Detail
            if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {
                $post['is_iyzipay_enabled'] = $request->is_iyzipay_enabled;
                $post['iyzipay_api_key'] = $request->iyzipay_api_key;
                $post['iyzipay_secret_key'] = $request->iyzipay_secret_key;
                $post['iyzipay_mode'] = $request->iyzipay_mode;
            } else {
                $post['is_iyzipay_enabled'] = 'off';
            }

            // Save sspay Detail
            if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {
                $post['is_sspay_enabled'] = $request->is_sspay_enabled;
                $post['sspay_secret_key'] = $request->sspay_secret_key;
                $post['sspay_category_code'] = $request->sspay_category_code;
            } else {
                $post['is_sspay_enabled'] = 'off';
            }

            // Save paytab Detail
            if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {
                $post['is_paytab_enabled'] = $request->is_paytab_enabled;
                $post['paytabs_profile_id'] = $request->paytabs_profile_id;
                $post['paytab_server_key'] = $request->paytab_server_key;
                $post['paytabs_region'] = $request->paytabs_region;
            } else {
                $post['is_paytab_enabled'] = 'off';
            }

            // Save benefit Detail
            if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
                $post['is_benefit_enabled'] = $request->is_benefit_enabled;
                $post['benefit_secret_key'] = $request->benefit_secret_key;
                $post['benefit_publishable_key'] = $request->benefit_publishable_key;
            } else {
                $post['is_benefit_enabled'] = 'off';
            }

            // Save cashfree Detail
            if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
                $post['is_cashfree_enabled'] = $request->is_cashfree_enabled;
                $post['cashfree_api_key'] = $request->cashfree_api_key;
                $post['cashfree_secret_key'] = $request->cashfree_secret_key;
            } else {
                $post['is_cashfree_enabled'] = 'off';
            }

            // Save aamarpay Detail
            if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
                $post['is_aamarpay_enabled'] = $request->is_aamarpay_enabled;
                $post['aamarpay_mode'] = $request->aamarpay_mode;
                $post['aamarpay_store_id'] = $request->aamarpay_store_id;
                $post['aamarpay_signature_key'] = $request->aamarpay_signature_key;
                $post['aamarpay_description'] = $request->aamarpay_description;
            } else {
                $post['is_aamarpay_enabled'] = 'off';
            }

            // Save PAYTr Detail
            if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
                $post['is_paytr_enabled'] = $request->is_paytr_enabled;
                $post['paytr_merchant_key'] = $request->paytr_merchant_key;
                $post['paytr_merchant_id'] = $request->paytr_merchant_id;
                $post['paytr_merchant_salt'] = $request->paytr_merchant_salt;
            } else {
                $post['is_paytr_enabled'] = 'off';
            }

            // Save Midtrans Detail
            if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
                $post['is_midtrans_enabled'] = $request->is_midtrans_enabled;
                $post['midtrans_mode'] = $request->midtrans_mode;
                $post['midtrans_server_key'] = $request->midtrans_server_key;
            } else {
                $post['is_midtrans_enabled'] = 'off';
            }


            // save xendit details
            if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
                $post['is_xendit_enabled'] = $request->is_xendit_enabled;
                $post['xendit_api_key'] = $request->xendit_api_key;
                $post['xendit_token'] = $request->xendit_token;
            } else {
                $post['is_xendit_enabled'] = 'off';
            }

            // save yookasa Details
            if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
                $post['is_yookassa_enabled'] = $request->is_yookassa_enabled;
                $post['yookassa_shopid'] = $request->yookassa_shopid;
                $post['yookassa_secret_key'] = $request->yookassa_secret_key;
            } else {
                $post['is_yookassa_enabled'] = 'off';
            }

            // save paiementpro payment details
            if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
                $post['is_paiementpro_enabled'] = $request->is_paiementpro_enabled;
                $post['paiementpro_merchant_id'] = $request->paiementpro_merchant_id;
            } else {
                $post['is_paiementpro_enabled'] = 'off';
            }

            // save Nepalste payment details
            if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
                $post['is_nepalste_enabled'] = $request->is_nepalste_enabled;
                $post['nepalste_mode'] = $request->nepalste_mode;
                $post['nepalste_public_key'] = $request->nepalste_public_key;
            } else {
                $post['is_nepalste_enabled'] = 'off';
            }

            // save Cinetpay payment details
            if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
                $post['is_cinetpay_enabled'] = $request->is_cinetpay_enabled;
                $post['cinetpay_api_key'] = $request->cinetpay_api_key;
                $post['cinetpay_site_id'] = $request->cinetpay_site_id;
            } else {
                $post['is_cinetpay_enabled'] = 'off';
            }

            // save Fedapay payment details
            if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
                $post['is_fedapay_enabled'] = $request->is_fedapay_enabled;
                $post['fedapay_mode'] = $request->fedapay_mode;
                $post['fedapay_public_key'] = $request->fedapay_public_key;
                $post['fedapay_secret_key'] = $request->fedapay_secret_key;
            } else {
                $post['is_fedapay_enabled'] = 'off';
            }

            // save PayHere payment details
            if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
                $post['is_payhere_enabled'] = $request->is_payhere_enabled;
                $post['payhere_mode'] = $request->payhere_mode;
                $post['payhere_merchant_id'] = $request->payhere_merchant_id;
                $post['payhere_merchant_secret'] = $request->payhere_merchant_secret;
                $post['payhere_app_id'] = $request->payhere_app_id;
                $post['payhere_app_secret'] = $request->payhere_app_secret;
            } else {
                $post['is_payhere_enabled'] = 'off';
            }





            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,
                ]);
            }

            // Artisan::call('config:cache');
            // Artisan::call('config:clear');
            return redirect()->back()->with('success', __('Payment Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function pusherSettingStore(Request $request)
    {

        $user = Auth::user();
        if ($user->type == 'admin') {
            $rules = [];

            if ($request->enable_chat == 'on') {
                $rules['pusher_app_id'] = 'required|string|max:50';
                $rules['pusher_app_key'] = 'required|string|max:50';
                $rules['pusher_app_secret'] = 'required|string|max:50';
                $rules['pusher_app_cluster'] = 'required|string|max:50';
            }

            $request->validate($rules);

            // $arrEnv = [
            //     'CHAT_MODULE' => $request->enable_chat,
            //     'PUSHER_APP_ID' => $request->pusher_app_id,
            //     'PUSHER_APP_KEY' => $request->pusher_app_key,
            //     'PUSHER_APP_SECRET' => $request->pusher_app_secret,
            //     'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
            // ];
            // if ($this->setEnvironmentValue($arrEnv)) {
            //     return redirect()->back()->with('success', __('Setting updated successfully'));
            // } else {
            //     return redirect()->back()->with('error', __('Something is wrong'));
            // }

            $post['enable_chat'] = $request->enable_chat;
            $post['pusher_app_id'] = $request->pusher_app_id;
            $post['pusher_app_key'] = $request->pusher_app_key;
            $post['pusher_app_secret'] = $request->pusher_app_secret;
            $post['pusher_app_cluster'] = $request->pusher_app_cluster;


            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,
                ]);
            }

            return redirect()->back()->with('success', __('Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
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

        // Artisan::call('config:cache');
        // Artisan::call('config:clear');

        return file_put_contents($envFile, $str) ? true : false;
    }

    public function testEmail(Request $request)
    {
        // $user = Auth::user();
        // if ($user->type == 'admin' || $user->type == 'user' ) {

        //     $data                      = [];
        //     $data['mail_driver']       = $request->mail_driver;
        //     $data['mail_host']         = $request->mail_host;
        //     $data['mail_port']         = $request->mail_port;
        //     $data['mail_username']     = $request->mail_username;
        //     $data['mail_password']     = $request->mail_password;
        //     $data['mail_encryption']   = $request->mail_encryption;
        //     $data['mail_from_address'] = $request->mail_from_address;
        //     $data['mail_from_name']    = $request->mail_from_name;

        //     return view('users.test_email', compact('data'));
        // } else {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
        $user = Auth::user();
        if ($user->type == 'admin') {
            $settings = Utility::getAdminPaymentSetting();
            $data = [];
            $data['mail_driver'] = $settings['mail_driver'] ? $settings['mail_driver'] : $request->mail_driver;
            $data['mail_host'] = $settings['mail_host'] ? $settings['mail_host'] : $request->mail_host;
            $data['mail_port'] = $settings['mail_port'] ? $settings['mail_port'] : $request->mail_port;
            $data['mail_username'] = $settings['mail_username'] ? $settings['mail_username'] : $request->mail_username;
            $data['mail_password'] = $settings['mail_password'] ? $settings['mail_password'] : $request->mail_password;
            $data['mail_encryption'] = $settings['mail_encryption'] ? $settings['mail_encryption'] : $request->mail_encryption;
            $data['mail_from_address'] = $settings['mail_from_address'] ? $settings['mail_from_address'] : $request->mail_from_address;
            $data['mail_from_name'] = $settings['mail_from_name'] ? $settings['mail_from_name'] : $request->mail_from_name;
            return view('users.test_email', compact('data'));
        } else if ($user->type == 'user') {
            $user = Auth::user();
            $settings = Utility::getPaymentSetting($user->currant_workspace);
            $data = [];
            $data['mail_driver'] = $settings['mail_driver'] ? $settings['mail_driver'] : $request->mail_driver;
            $data['mail_host'] = $settings['mail_host'] ? $settings['mail_host'] : $request->mail_host;
            $data['mail_port'] = $settings['mail_port'] ? $settings['mail_port'] : $request->mail_port;
            $data['mail_username'] = $settings['mail_username'] ? $settings['mail_username'] : $request->mail_username;
            $data['mail_password'] = $settings['mail_password'] ? $settings['mail_password'] : $request->mail_password;
            $data['mail_encryption'] = $settings['mail_encryption'] ? $settings['mail_encryption'] : $request->mail_encryption;
            $data['mail_from_address'] = $settings['mail_from_address'] ? $settings['mail_from_address'] : $request->mail_from_address;
            $data['mail_from_name'] = $settings['mail_from_name'] ? $settings['mail_from_name'] : $request->mail_from_name;
            return view('users.test_email', compact('data'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
     ini_set('max_execution_time',300);
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'mail_driver' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_from_address' => 'required',
                'mail_from_name' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['success' => false, 'message' => 'Please Add The Email Credentails']);
        }

        // if (($request->mail_driver == null) && ($request->mail_host == null) && ($request->mail_port == null) && ($request->mail_username  == null) && ($request->mail_password  == null) && ($request->mail_from_address  == null) && ($request->mail_from_name  == null)) {
        //     return response()->json(['success' => false,'message' => 'Please Add The Email Credentails']);
        // }

        try {
            config([
                'mail.driver' => $request->mail_driver,
                'mail.host' => $request->mail_host,
                'mail.port' => $request->mail_port,
                'mail.username' => $request->mail_username,
                'mail.password' => $request->mail_password,
                'mail.encryption' => $request->mail_encryption,
                'mail.from.address' => $request->mail_from_address,
                'mail.from.name' => $request->mail_from_name,
            ]);

            Mail::to($request->email)->send(new EmailTest());
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'is_success' => true,
            'message' => __('Email send Successfully'),
        ]);
    }

    public function recaptchaSettingStore(Request $request)
    {
        $user = \Auth::user();
        $rules = [];

        if ($request->recaptcha_module == 'on') {
            $rules['google_recaptcha_version'] = 'required';
            $rules['google_recaptcha_key'] = 'required|string|max:50';
            $rules['google_recaptcha_secret'] = 'required|string|max:50';
        }

        $validator = \Validator::make(
            $request->all(),
            $rules
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        // $arrEnv = [
        //     'RECAPTCHA_MODULE'  => $request->recaptcha_module ? 'on' : 'off',
        //     'NOCAPTCHA_SITEKEY' => $request->google_recaptcha_key,
        //     'NOCAPTCHA_SECRET'  => $request->google_recaptcha_secret,
        // ];
        // if ($this->setEnvironmentValue($arrEnv)) {
        //     return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
        // } else {
        //     return redirect()->back()->with('error', __('Something is wrong'));
        // }

        $post['recaptcha_module'] = $request->recaptcha_module;
        $post['google_recaptcha_version'] = $request->google_recaptcha_version;
        $post['google_recaptcha_key'] = $request->google_recaptcha_key;
        $post['google_recaptcha_secret'] = $request->google_recaptcha_secret;


        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach ($post as $key => $data) {
            \DB::insert('insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', __('Setting updated successfully'));
    }


    public function storageSettingStore(Request $request)
    {

        if (isset($request->storage_setting) && $request->storage_setting == 'local') {

            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 's3') {
            $request->validate(
                [
                    's3_key' => 'required',
                    's3_secret' => 'required',
                    's3_region' => 'required',
                    's3_bucket' => 'required',
                    's3_url' => 'required',
                    's3_endpoint' => 'required',
                    's3_max_upload_size' => 'required',
                    's3_storage_validation' => 'required',
                ]
            );
            $post['storage_setting'] = $request->storage_setting;
            $post['s3_key'] = $request->s3_key;
            $post['s3_secret'] = $request->s3_secret;
            $post['s3_region'] = $request->s3_region;
            $post['s3_bucket'] = $request->s3_bucket;
            $post['s3_url'] = $request->s3_url;
            $post['s3_endpoint'] = $request->s3_endpoint;
            $post['s3_max_upload_size'] = $request->s3_max_upload_size;
            $s3_storage_validation = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation'] = $s3_storage_validation;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 'wasabi') {
            $request->validate(
                [
                    'wasabi_key' => 'required',
                    'wasabi_secret' => 'required',
                    'wasabi_region' => 'required',
                    'wasabi_bucket' => 'required',
                    'wasabi_url' => 'required',
                    'wasabi_root' => 'required',
                    'wasabi_max_upload_size' => 'required',
                    'wasabi_storage_validation' => 'required',
                ]
            );
            $post['storage_setting'] = $request->storage_setting;
            $post['wasabi_key'] = $request->wasabi_key;
            $post['wasabi_secret'] = $request->wasabi_secret;
            $post['wasabi_region'] = $request->wasabi_region;
            $post['wasabi_bucket'] = $request->wasabi_bucket;
            $post['wasabi_url'] = $request->wasabi_url;
            $post['wasabi_root'] = $request->wasabi_root;
            $post['wasabi_max_upload_size'] = $request->wasabi_max_upload_size;
            $wasabi_storage_validation = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation'] = $wasabi_storage_validation;
        }
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                $created_at,
                $updated_at,

            ];


            \DB::insert(
                'insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function chatgptkey(Request $request)
    {
        if (\Auth::user()->type == 'admin') {
            $user = \Auth::user();
            $validator = \Validator::make(
                $request->all(),
                [
                    'chatgpt_key' => 'required',
                    'chatgpt_model' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $post['chatgpt_key'] = $request->chatgpt_key;
            $post['chatgpt_model'] = $request->chatgpt_model;

            unset($post['_token']);
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');
            foreach ($post as $key => $data) {
                $arr = [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,

                ];
                \DB::insert(
                    'insert into admin_payment_settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    $arr
                );
            }

            return redirect()->back()->with('success', __('ChatGPT key successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
