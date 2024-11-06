<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Models\Project;



class MolliePaymentController extends Controller
{
    public $api_key;
    public $profile_id;
    public $partner_id;
    public $is_enabled;
    public $currancy;
    public $user;

    public function setPaymentDetail()
    {
        // $user = \Auth::user();
        // if($user->getGuard() == 'client')
        // {
        //     $payment_setting = Utility::getPaymentSetting($user->currentWorkspace->id);
        //     $this->currancy  = (isset($user->currentWorkspace->currency_code)) ? $user->currentWorkspace->currency_code : 'USD';
        // }
        // else
        // {
        // }
        $payment_setting = Utility::getAdminPaymentSetting();
        // $this->currancy = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';

        $this->api_key = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
        $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
        $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
        $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
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

        $this->api_key = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
        $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
        $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
        $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
    }


    public function planPayWithMollie(Request $request)
    {
        $this->setPaymentDetail();

        $authuser = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $coupons_id = '';

        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price = $plan->{$request->mollie_payment_frequency . '_price'};

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
                    $coupons_id = $coupons->id;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id, $request->mollie_payment_frequency);

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
                            'payment_frequency' => $request->mollie_payment_frequency,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => $this->currancy,
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );

                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                }
            }

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => $this->currancy,
                        "value" => str_replace(',', '', number_format($price, 2)),
                    ],
                    "description" => "payment for product",
                    "redirectUrl" => route(
                        'plan.mollie',
                        [
                            $request->plan_id,
                            'payment_frequency=' . $request->mollie_payment_frequency,
                            'coupon_id=' . $coupons_id,
                        ]
                    ),
                ]
            );

            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request, $plan)
    {
        $this->setPaymentDetail();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan = Plan::find($planID);
        $user = Auth::user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $price = $plan->{$request->payment_frequency . '_price'};

        if ($plan) {
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);

                if (session()->has('mollie_payment_id')) {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if ($payment->isPaid()) {
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

                        $user->is_plan_purchased = 1;
                        if ($user->is_trial_done == 1) {
                            $user->is_trial_done = 2;
                            $user->save();
                        }

                        $order = new Order();
                        $order->order_id = $orderID;
                        $order->name = $user->name;
                        $order->card_number = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year = '';
                        $order->plan_name = $plan->name;
                        $order->plan_id = $plan->id;
                        $order->price = isset($price) ? $price : 0;
                        $order->price_currency = $this->currancy;
                        $order->payment_frequency = $request->payment_frequency;
                        $order->txn_id = isset($request->TXNID) ? $request->TXNID : '';
                        $order->payment_type = 'Mollie';
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
                            return redirect()->route(
                                'payment',
                                [
                                    $request->payment_frequency,
                                    \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
                                ]
                            )->with('error', __($assignPlan['error']));
                        }
                    } else {
                        return redirect()->route(
                            'payment',
                            [
                                $request->payment_frequency,
                                \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
                            ]
                        )->with('error', __('Transaction has been failed.'));
                    }
                } else {
                    return redirect()->route(
                        'payment',
                        [
                            $request->payment_frequency,
                            \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
                        ]
                    )->with('error', __('Transaction has been failed!'));
                }
            } catch (\Exception $e) {
                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Plan not found!'));
            }
        }
    }

    public function invoicePayWithMollie(Request $request, $slug, $invoice_id)
    {
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $this->setPaymentDetail_client($invoice_id);

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
        $invoice = Invoice::find($invoice_id);

        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                'invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($this->api_key);

        $payment = $mollie->payments->create(
            [
                "amount" => [
                    "currency" => $this->currancy,
                    "value" => number_format($request->amount, 2),
                ],
                "description" => "payment for invoice",
                "redirectUrl" => route(
                    $client_keyword . 'invoice.mollie',
                    [
                        $slug,
                        encrypt($invoice_id),
                    ]
                ),
            ]
        );
        session()->put('mollie_payment_id', $payment->id);
        return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
    }

    public function getInvoicePaymentStatus(Request $request, $slug, $invoice_id)
    {

        $decryptId = decrypt($invoice_id);
        $this->setPaymentDetail_client($decryptId);

        if (!empty($invoice_id)) {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);

            $invoice_id = decrypt($invoice_id);
            $invoice = Invoice::find($invoice_id);
            if ($invoice && session()->has('mollie_payment_id')) {
                $user = Auth::user();
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);

                try {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if ($payment->isPaid()) {
                        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $order_id;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($payment->amount->value) ? $payment->amount->value : 0;
                        $invoice_payment->payment_type = 'Mollie';
                        $invoice_payment->receipt = '';
                        $invoice_payment->client_id = $invoice->client_id;
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

                        //return redirect()->back()->with('success', __('Payment added Successfully'));
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Invoice paid Successfully..'));
                        }
                    } else {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $slug,
                                $invoice_id,
                            ]
                        )->with('error', __('Transaction fail'));
                    }
                } catch (\Exception $e) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __('Something went wrong.'));
                }
            } else {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Invoice not found.'));
            }
        } else {
            return redirect()->route('client.invoices.index', $slug)->with('error', __('Invoice not found.'));
        }
    }
}
