<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Utility;
use App\Models\User;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use LivePixel\MercadoPago\MP;
use App\Models\UserCoupon;
use App\Models\Client;
use App\Models\Project;



class MercadoPaymentController extends Controller
{
    public $token;
    public $is_enabled;
    public $currancy;
    public $mode;
    public $user;


    public function __construct()
    {
        $this->middleware('XSS');
    }

    public function setPaymentDetail()
    {
        // $user = Auth::user();
        // if ($user->getGuard() == 'client') {
        //     $payment_setting = Utility::getPaymentSetting($user->currentWorkspace->id);
        //     $this->currancy  = (isset($user->currentWorkspace->currency_code)) ? $user->currentWorkspace->currency_code : 'USD';
        // } else {
        // }
        $payment_setting = Utility::getAdminPaymentSetting();
        // $this->currancy  = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';


        $this->token = isset($payment_setting['mercado_access_token']) ? $payment_setting['mercado_access_token'] : '';
        $this->mode = isset($payment_setting['mercado_mode']) ? $payment_setting['mercado_mode'] : '';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
    }

    public function setPaymentDetail_client($invoice_id)
    {

        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';

        $this->token = isset($payment_setting['mercado_access_token']) ? $payment_setting['mercado_access_token'] : '';
        $this->mode = isset($payment_setting['mercado_mode']) ? $payment_setting['mercado_mode'] : '';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
    }


    public function planPayWithMercado(Request $request)
    {
        $this->setPaymentDetail();

        $authuser = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $coupon_id = '';

        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price = $plan->{$request->mercado_payment_frequency . '_price'};

            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if ($usedCoupun >= $coupons->limit) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

                    $price = $price - $discount_value;
                    $coupon_id = $coupons->id;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }


            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id, $request->mercado_payment_frequency);

                if ($assignPlan['is_success'] == true && !empty($plan)) {
                    if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                        try {
                            $authuser->cancel_subscription($authuser->id);
                        } catch (\Exception $exception) {
                            \Log::debug($exception->getMessage());
                        }
                    }
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => $this->currancy == null ? 'USD' : $this->currancy,
                            'payment_frequency' => $request->mercado_payment_frequency,
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                } else {
                    return response()->json(
                        [
                            'message' => __('Plan fail to upgrade.'),
                        ],
                        401
                    );
                }
            }

            // $preference_data = array(
            //     "items" => array(
            //         array(
            //             "title" => "Plan : " . $plan->name,
            //             "quantity" => 1,
            //             "currency_id" => $this->currancy,
            //             "unit_price" => (float)$price,
            //         ),
            //     ),
            // );
            \MercadoPago\SDK::setAccessToken($this->token);
            try {
                // $mp         = new MP($this->app_id, $this->secret_key);
                // $preference = $mp->create_preference($preference_data);

                // return redirect($preference['response']['init_point']);


                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item = new \MercadoPago\Item();
                $item->title = "Plan : " . $plan->name;
                $item->quantity = 1;
                $item->unit_price = (float) $price;
                $preference->items = array($item);

                $success_url = route('plan.mercado', [$request->plan_id, 'payment_frequency=' . $request->mercado_payment_frequency, 'coupon_id=' . $coupon_id, 'flag' => 'success', 'amount' => (float) $price]);
                $failure_url = route('plan.mercado', [$request->plan_id, 'flag' => 'failure']);
                $pending_url = route('plan.mercado', [$request->plan_id, 'flag' => 'pending']);

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name = \Auth::user()->name;
                $payer->email = \Auth::user()->email;
                $payer->address = array(
                    "street_name" => ''
                );
                if ($this->mode == 'live') {
                    $redirectUrl = $preference->init_point;
                } else {
                    $redirectUrl = $preference->sandbox_init_point;
                }
                return redirect($redirectUrl);
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request, $plan)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan = Plan::find($planID);
        $user = Auth::user();
        $orderID = time();
        if ($plan) {
            // try
            // {

            if ($plan && $request->has('status')) {

                if ($request->status == 'approved' && $request->flag == 'success') {
                    if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                        try {
                            $user->cancel_subscription($user->id);
                        } catch (\Exception $exception) {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    if ($request->has('coupon_id') && $request->coupon_id != '') {
                        $coupons = Coupon::find($request->coupon_id);

                        if (!empty($coupons)) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();

                            $usedCoupun = $coupons->used_coupon();
                            if ($coupons->limit <= $usedCoupun) {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    $order = new Order();
                    $order->order_id = $orderID;
                    $order->name = $user->name;
                    $order->card_number = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year = '';
                    $order->plan_name = $plan->name;
                    $order->plan_id = $plan->id;
                    $order->price = $request->has('amount') ? $request->amount : 0;
                    $order->price_currency = $this->currancy == null ? 'USD' : $this->currancy;
                    $order->txn_id = $request->has('preference_id') ? $request->preference_id : '';
                    $order->payment_frequency = $request->payment_frequency;
                    $order->payment_type = 'Mercado Pago';
                    $order->payment_status = 'succeeded';
                    $order->receipt = '';
                    $order->user_id = $user->id;
                    $order->save();
                    $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);
                    if ($assignPlan['is_success']) {
                        $planData = [
                            'plan_id' => $plan->id,
                            'plan_frequency' => $request->payment_frequency,
                            'plan_price' => Utility::getPlanActualPrice($plan->id, $request->payment_frequency)
                        ];
                        Utility::referraltransaction($planData, $user);
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
            }
            // }
            // catch(\Exception $e)
            // {
            //     return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            // }
        }
    }


    public function invoicePayWithMercado(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);
        $user = Auth::user();

        $validatorArray = [
            'amount' => 'required',
        ];
        $validator = Validator::make(
            $request->all(),
            $validatorArray
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $invoice = Invoice::find($request->invoice_id);

        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                'client.invoices.show',
                [
                    $currentWorkspace->slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }

        // $preference_data = array(
        //     "items" => array(
        //         array(
        //             "title" => "Invoice : " . $request->invoice_id,
        //             "quantity" => 1,
        //             "currency_id" => $this->currancy,
        //             "unit_price" => (float)$request->amount,
        //         ),
        //     ),
        // );


        \MercadoPago\SDK::setAccessToken($this->token);
        try {
            // $mp         = new MP($this->app_id, $this->secret_key);
            // $preference = $mp->create_preference($preference_data);

            // return redirect($preference['response']['init_point']);

            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item = new \MercadoPago\Item();
            $item->title = "Invoice : " . $request->invoice_id;
            $item->quantity = 1;
            $item->unit_price = (float) $request->amount;
            $item->currency_id = $this->currancy;
            $preference->items = array($item);
            $client_keyword = isset($user) ? (($user->getGuard() == 'client') ? 'client.' : '') : '';

            $success_url = route($client_keyword . 'invoice.mercado', [$currentWorkspace->slug, encrypt($invoice->id), 'amount' => (float) $request->amount, 'flag' => 'success']);

            $failure_url = route($client_keyword . 'invoice.mercado', [$currentWorkspace->slug, encrypt($invoice->id), 'flag' => 'failure']);
            $pending_url = route($client_keyword . 'invoice.mercado', [$currentWorkspace->slug, encrypt($invoice->id), 'flag' => 'pending']);
            $preference->back_urls = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url
            );
            $preference->auto_return = "approved";
            $preference->save();

            // Create a customer object
            $payer = new \MercadoPago\Payer();
            // Create payer information
            $payer->name = $this->user->name;
            $payer->email = $this->user->email;
            $payer->address = array(
                "street_name" => ''
            );

            if ($this->mode == 'live') {
                $redirectUrl = $preference->init_point;
            } else {
                $redirectUrl = $preference->sandbox_init_point;
            }
            return redirect($redirectUrl);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function getInvoicePaymentStatus($slug, $invoice_id, Request $request)
    {
        if (!empty($invoice_id)) {
            $invoice_id = decrypt($invoice_id);

            $invoice = Invoice::find($invoice_id);
            $user = Auth::user();
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);

            if (Auth::check()) {
                $user = \Auth::user();
            } else {
                $user = Client::where('id', $invoice->client_id)->first();
            }
            if ($invoice && $request->has('status')) {
                try {

                    if ($request->status == 'approved' && $request->flag == 'success') {
                        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $order_id;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        // $invoice_payment->amount         = isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                        $invoice_payment->amount = $request->has('amount') ? $request->amount : 0;
                        $invoice_payment->payment_type = 'Mercado';
                        $invoice_payment->receipt = '';
                        $invoice_payment->client_id = $user->id;
                        $invoice_payment->txn_id = '';
                        $invoice_payment->payment_status = 'succeeded';
                        $invoice_payment->save();

                        if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                            $invoice->status = 'paid';
                            $invoice->save();
                        } else {

                            $invoice->status = 'partialy paid';
                            $invoice->save();
                        }

                        $user1 = $currentWorkspace->id;
                        $settings = Utility::getPaymentSetting($user1);
                        $total_amount = $invoice->getDueAmounts($invoice->id);
                        $client = Client::find($invoice->client_id);
                        $project_name = Project::where('id', $invoice->project_id)->first();


                        $uArr = [
                            // 'user_name' => $user->name,
                            'project_name' => $project_name->name,
                            'company_name' => User::find($project_name->created_by)->name,
                            'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                            'client_name' => $client->name,
                            'total_amount' => "$total_amount",
                            'paid_amount' => $request->amount,
                        ];

                        if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {

                            Utility::send_slack_msg('Invoice Status Updated', $user1, $uArr);
                        }

                        if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                            Utility::send_telegram_msg('Invoice Status Updated', $uArr, $user1);
                        }

                        //webhook
                        $module = 'Invoice Status Updated';
                        $webhook = Utility::webhookSetting($module, $user1);

                        // $webhook=  Utility::webhookSetting($module);
                        if ($webhook) {
                            $parameter = json_encode($invoice);
                            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                            // if($status == true)
                            // {
                            //     return redirect()->back()->with('success', __('Payment added Successfully!'));
                            // }
                            // else
                            // {
                            //     return redirect()->back()->with('error', __('Webhook call failed.'));
                            // }
                        }

                        $request->session()->forget('invoice_data');

                        // return redirect()->back()->with('success', __('Payment added Successfully'));


                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                        }
                    } else {
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('error', __('Transaction fail'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction fail'));
                        }
                    }
                } catch (\Exception $e) {
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $slug,
                                $invoice_id,
                            ]
                        )->with('error', __('Invoice not found!'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Invoice not found!'));
                    }
                }
            } else {

                if (\Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Invoice not found!'));
                }
            }
        } else {

            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Invoice not found!'));
            }
        }
    }
}
