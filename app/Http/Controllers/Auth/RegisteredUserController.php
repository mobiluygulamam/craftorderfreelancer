<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Utility;
use App\Models\UserWorkspace;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\SendLoginDetail;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        // return view('auth.register');
    }

    public function register(Request $request)
    {
        $user = User::create($request->validated());
        event(new Registered($user));
        auth()->login($user);
        return redirect('/')->with('success', "Account successfully registered.");
    }

    public function showRegistrationForm($ref = '', $plan_id = '', $lang = '')
    {
        $setting = Utility::getAdminPaymentSetting();
        if ($lang == '') {
            $lang = $setting['default_admin_lang'] ? $setting['default_admin_lang'] : 'en';
        }
        $langList = Utility::langList();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';
        if (empty($lang)) {
            $lang = Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        if ($ref == '') {
            $ref = 0;
        }
        $refCode = User::where('referral_code', '=', $ref)->first();

        if ($refCode->referral_code != $ref) {
            return redirect()->route('register');
        }

        if ($plan_id == '') {
            $plan_id = null;
        }

        if ($setting['signup_button'] == 'on') {
            return view('auth.register', compact('lang', 'ref', 'plan_id'));
        } else {
            return abort('404', 'Page not found');
        }

        return view('auth.register', compact('lang', 'ref', 'plan_id'));
    }

    // public function store(Request $request)
    // {
    //     if(env('RECAPTCHA_MODULE') == 'on')
    //     {
    //         $validation['g-recaptcha-response'] = 'required|captcha';
    //     }else{
    //         $validation = [];
    //     }
    //     $this->validate($request, $validation);
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'workspace' => 'required', 'string', 'max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => ['required','string', 'min:8', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //          'workspace'=>$request->workspace,
    //         'password' => Hash::make($request->password),
    //         'plan'=>1,
    //     ]);

    //     $objWorkspace = Workspace::create(['created_by'=>$user->id,'name'=>$request->workspace, 'currency_code' => 'USD', 'paypal_mode' => 'sandbox']);

    //     $setting = Utility::getAdminPaymentSetting();

    //      $userWorkspace               =   new UserWorkspace();
    //         $userWorkspace->user_id      =     $user->id;
    //         $userWorkspace->workspace_id =    $objWorkspace->id;
    //         $userWorkspace->permission  = 'Owner';

    //            if(empty($userWorkspace))
    //         {
    //             $errorArray[] = $userWorkspace;
    //         }
    //         else
    //         {
    //             $userWorkspace->save();
    //         }

    //     $user->currant_workspace = $objWorkspace->id;
    //     $user->save();
    //     Auth::login($user);

    //     if($setting['email_verification'] == 'on'){


    //         try{

    //             event(new Registered($user));
    //             // UserWorkspace::create(['user_id'=> $user->id,'workspace_id'=>$objWorkspace->id,'permission'=>'Owner']);
    //             if(empty($lang))
    //             {
    //                 $lang = env('DEFAULT_LANG');
    //             }
    //             \App::setLocale($lang);


    //         }catch(\Exception $e){

    //             $user->delete();

    //             return redirect('/register/lang?')->with('statuss', __('Email SMTP settings does not configure so please contact to your site admin.'));
    //         }

    //         return view('auth.verify-email', compact('lang'));
    //     }else{

    //         $user->email_verified_at = date('h:i:s');
    //         // UserWorkspace::create(['user_id'=> $user->id,'workspace_id'=>$objWorkspace->id,'permission'=>'Owner']);
    //         $user->save();
    //         return redirect(RouteServiceProvider::HOME);
    //     }

    // }

    public function store(Request $request)
    {
        $setting = Utility::getAdminPaymentSetting();
        // if (env('RECAPTCHA_MODULE') == 'on') {
        if ($setting['recaptcha_module'] == 'on') {
            $validation['g-recaptcha-response'] = 'required';
        } else {
            $validation = [];
        }

        $this->validate($request, $validation);
        $request->validate([
            'name' => 'required|string|max:255',
            'workspace' => 'required',
            'string',
            'max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        do {
            $code = rand(100000, 999999);
        } while (DB::table('users')->where('referral_code', $code)->exists());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'workspace' => $request->workspace,
            'password' => Hash::make($request->password),
            'plan' => 1,
            'lang' => $setting['default_admin_lang'] ? $setting['default_admin_lang'] : 'en',
            'referral_code' => $code,
            'referral_used' => !empty($request->ref_code) ? $request->ref_code : '0',
        ]);

        $objWorkspace = Workspace::create(['lang' => $setting['default_admin_lang'] ? $setting['default_admin_lang'] : 'en', 'created_by' => $user->id, 'name' => $request->workspace, 'currency_code' => 'USD', 'paypal_mode' => 'sandbox']);

        $setting = Utility::getAdminPaymentSetting();

        $userWorkspace = new UserWorkspace();
        $userWorkspace->user_id = $user->id;
        $userWorkspace->workspace_id = $objWorkspace->id;
        $userWorkspace->permission = 'Owner';

        if (empty($userWorkspace)) {
            $errorArray[] = $userWorkspace;
        } else {
            $userWorkspace->save();
        }


        $user->currant_workspace = $objWorkspace->id;
        $user->save();

        User::userDefaultDataRegister($user);
        if ($setting['email_verification'] == 'on') {

            Utility::setMailConfig();
            try {
                // event(new Registered($user));
                $user->sendEmailVerificationNotification();
                // UserWorkspace::create(['user_id'=> $user->id,'workspace_id'=>$objWorkspace->id,'permission'=>'Owner']);
                if (empty($lang)) {
                    $lang = $setting['default_admin_lang'] ? $setting['default_admin_lang'] : 'en';
                }
                \App::setLocale($lang);
            } catch (\Exception $e) {
                $user->delete();
                // return redirect('/register/lang?')->with('statuss', __('Email SMTP settings does not configure so please contact to your site admin.'));
                return redirect()->route('register')->with('statuss', __('Email SMTP settings does not configure so please contact to your site admin.'));
            }
            Auth::login($user);
            return view('auth.verify-email', compact('lang'));
        } else {
            $user->email_verified_at = date('h:i:s');
            // UserWorkspace::create(['user_id'=> $user->id,'workspace_id'=>$objWorkspace->id,'permission'=>'Owner']);
            $user->save();
            $user->password = $request->password;
            Utility::setMailConfig();
            try {
                Mail::to($user->email)->send(new SendLoginDetail($user));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            Auth::login($user);

            if ($request->plan_id != null) {
                return redirect()->route('payment', ['monthly', $request->plan_id]);
            }
            return redirect(RouteServiceProvider::HOME);
        }
    }
}
