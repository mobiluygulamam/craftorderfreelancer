<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use App\Models\User;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use App\Models\Client;
use App\Models\Project;
use Dflydev\DotAccessData\Util;
use Srmklive\PayPal\Services\PayPal as PayPalClient;




class PaypalController extends Controller
{
    private $_api_context;
    private $user;

    // public function setApiContext()
    // {
    //     // $user        = \Auth::user();
    //     $paypal_conf = config('paypal');
    //     // if($user->getGuard() == 'client')
    //     // {
    //     //     $paypal_conf['settings']['mode'] = $user->currentWorkspace->paypal_mode;
    //     //     $paypal_conf['client_id']        = $user->currentWorkspace->paypal_client_id;
    //     //     $paypal_conf['secret_key']       = $user->currentWorkspace->paypal_secret_key;
    //     // }
    //     // else
    //     // {
    //     // }
    //     $paymentSetting = Utility::getAdminPaymentSetting();
    //     $paypal_conf['settings']['mode'] = $paymentSetting['paypal_mode'];
    //     $paypal_conf['client_id'] = $paymentSetting['paypal_client_id'];
    //     $paypal_conf['secret_key'] = $paymentSetting['paypal_secret_key'];

    //     $this->_api_context = new ApiContext(
    //         new OAuthTokenCredential(
    //             $paypal_conf['client_id'],
    //             $paypal_conf['secret_key']
    //         )
    //     );
    //     $this->_api_context->setConfig($paypal_conf['settings']);
    // }
    public function setApiContext()
    {
        $payment_setting = Utility::getAdminPaymentSetting();

        if ($payment_setting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }
        return $payment_setting;
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
        $payment_setting['settings']['mode'] = $this->user->currentWorkspace->paypal_mode;
        $payment_setting['client_id'] = $this->user->currentWorkspace->paypal_client_id;
        $payment_setting['secret_key'] = $this->user->currentWorkspace->paypal_secret_key;

        if ($payment_setting['settings']['mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['client_id']) ? $payment_setting['client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['secret_key']) ? $payment_setting['secret_key'] : '',
                'paypal.mode' => isset($payment_setting['settings']['mode']) ? $payment_setting['settings']['mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['client_id']) ? $payment_setting['client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['secret_key']) ? $payment_setting['secret_key'] : '',
                'paypal.mode' => isset($payment_setting['settings']['mode']) ? $payment_setting['settings']['mode'] : '',
            ]);
        }
        return $payment_setting;
    }

    public function planPayWithPaypal(Request $request)
    {
        $this->setApiContext();
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $authuser = \Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $setting = Utility::getAdminPaymentSetting();

        if ($plan) {
            if ($request->paypal_payment_frequency == 'annual') {
                $get_amount = $plan->annual_price;
            } else {
                $get_amount = $plan->monthly_price;
            }
            $coupon_id = null;
            $price = (float) $plan->{$request->paypal_payment_frequency . '_price'};

            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($price / 100) * $coupons->discount;
                    $get_amount = $price - $discount_value;

                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $coupon_id = $coupons->id;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($get_amount <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id, $request->paypal_payment_frequency);

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
                            'name' => $authuser->name,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $get_amount == null ? 0 : $get_amount,
                            'payment_frequency' => $request->paypal_payment_frequency,
                            'price_currency' => $setting['currency'] ? $setting['currency'] : '$',
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );

                    return redirect()->route('plans.index')->with('success', __('Plan successfully upgraded.'));
                } else {
                    return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                }
            }
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('plan.get.payment.status', [$plan->id, 'coupon_id' => $coupon_id, 'frequency' => $request->paypal_payment_frequency]),
                    "cancel_url" => route('plan.get.payment.status', [$plan->id, 'coupon_id' => $coupon_id, 'frequency' => $request->paypal_payment_frequency]),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => $setting['currency'] ? $setting['currency'] : 'usd',
                            "value" => $get_amount,
                        ],
                        "totalAmount" => $get_amount,
                        // Add your custom data here
                    ]
                ]
            ]);
            if (isset($response['id']) && $response['id'] != null) {
                // redirect to approve href

                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }
                return redirect()
                    ->route('plans.index')
                    ->with('error', 'Something went wrong.');
            } else {
                return redirect()
                    ->route('plans.index')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id)
    {
        $this->setApiContext();
        $user = Auth::user();
        $plan = Plan::find($plan_id);

        if ($plan) {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            //$payment_id = Session::get('paypal_payment_id');
            $get_amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $txn_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            $frequency = $request->frequency;
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($request->has('coupon_id') && $request->coupon_id != '') {
                $coupons = Coupon::find($request->coupon_id);
                $discount_value = ($plan->price / 100) * $coupons->discount;
                $discounted_price = $plan->price - $discount_value;
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
            $setting = Utility::getAdminPaymentSetting();
            $order = new Order();
            $order->order_id = $orderID;
            $order->name = '';
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $plan->name;
            $order->plan_id = $plan->id;
            $order->price = $get_amount;
            $order->price_currency = $setting['currency'] ? $setting['currency'] : '$';
            $order->payment_frequency = $frequency;
            $order->txn_id = $txn_id;
            $order->payment_type = 'Paypal';
            $order->payment_status = 'success';
            $order->receipt = '';
            $order->user_id = $user->id;
            $order->save();

            $assignPlan = $user->assignPlan($plan->id, $request->frequency);

            if ($assignPlan['is_success']) {
                $planData = [
                    'plan_id' => $plan->id,
                    'plan_frequency' => $frequency,
                    'plan_price' => Utility::getPlanActualPrice($plan->id, $frequency)
                ];
                Utility::referraltransaction($planData, $user);
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
            } else {
                return redirect()->route('payment', [$request->frequency, \Illuminate\Support\Facades\Crypt::encrypt($plan->id),])->with('error', __($assignPlan['error']));
            }
        } else {
            return redirect()->route('payment', [$request->frequency, \Illuminate\Support\Facades\Crypt::encrypt($plan->id),])->with('error', __('Plan is deleted.'));
        }
    }
    public function clientPayWithPaypal(Request $request, $slug, $invoice_id)
    {
        //$user = $this->user;
        $totalAmount = $request->amount;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        $request->validate(['amount' => 'required|numeric|min:0']);

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($currentWorkspace) {
            $invoice = Invoice::find($invoice_id);

            if ($invoice) {
                if ($totalAmount > $invoice->getDueAmount()) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                } else {
                    $this->setPaymentDetail_client($invoice_id);
                    $provider = new PayPalClient;
                    $provider->setApiCredentials(config('paypal'));
                    $paypalToken = $provider->getAccessToken();
                    $response = $provider->createOrder([
                        "intent" => "CAPTURE",
                        "application_context" => [
                            "return_url" => route($client_keyword . 'get.payment.status', [$currentWorkspace->slug, $invoice->id]),
                            "cancel_url" => route($client_keyword . 'get.payment.status', [$currentWorkspace->slug, $invoice->id]),
                        ],
                        "purchase_units" => [
                            0 => [
                                "amount" => [
                                    "currency_code" => $currentWorkspace['currency_code'],
                                    "value" => $totalAmount
                                ]
                            ]
                        ]
                    ]);

                    if (isset($response['id']) && $response['id'] != null) {
                        // redirect to approve href
                        foreach ($response['links'] as $links) {
                            if ($links['rel'] == 'approve') {
                                return redirect()->away($links['href']);
                            }
                        }
                        return redirect()
                            ->route('invoice.show', \Crypt::encrypt($invoice->id))
                            ->with('error', 'Something went wrong.');
                    } else {
                        return redirect()
                            ->route('invoice.show', \Crypt::encrypt($invoice->id))
                            ->with('error', $response['message'] ?? 'Something went wrong.');
                    }
                    //return redirect()->route('invoice.show', \Crypt::encrypt($invoice_id))->back()->with('error', __('Unknown error occurred'));
                }
            } else {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function clientGetPaymentStatus(Request $request, $slug, $invoice_id)
    {
        $user = Auth::user();
        $invoice = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace && $invoice) {
            $this->setPaymentDetail_client($invoice_id);
            $user = $this->user;
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $totalAmount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $txn_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                if ($response['status'] == 'COMPLETED') {
                    $status = 'succeeded';
                }
                try {
                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->order_id = $order_id;
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->currency = $currentWorkspace->currency_code;
                    $invoice_payment->amount = $totalAmount;
                    $invoice_payment->payment_type = 'PAYPAL';
                    $invoice_payment->receipt = '';
                    $invoice_payment->client_id = $this->user->id;
                    $invoice_payment->txn_id = $txn_id;
                    $invoice_payment->payment_status = $status;
                    $invoice_payment->save();


                    if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                        $invoice->status = 'paid';
                        $invoice->save();
                    } else {
                        $invoice->status = 'partialy paid';
                        $invoice->save();
                    }
                    $total_amount = $invoice->getDueAmount();

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
                    }
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $currentWorkspace->slug,
                                $invoice_id,
                            ]
                        )->with('success', __('Payment Added Successfully.'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully..'));
                    }
                } catch (\Exception $e) {
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $currentWorkspace->slug,
                                $invoice_id,
                            ]
                        )->with('error', __('Transaction has been failed!'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been failed!'));
                    }
                }
            } else {
                if (\Auth::check()) {
                    return redirect()->route('client.invoices.show', [$currentWorkspace->slug, $invoice_id])->with('error', __('Payment failed'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been Faild.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
