<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;
use App\Models\Client;
use App\Models\Project;



class SkrillPaymentController extends Controller
{
    public $email;
    public $is_enabled;
    public $currancy;
    public $user;

    public function setPaymentDetail()
    {
        // $user = Auth::user();
        // if($user->getGuard() == 'client')
        // {
        //     $payment_setting = Utility::getPaymentSetting($user->currentWorkspace->id);
        //     $this->currancy  = (isset($user->currentWorkspace->currency_code)) ? $user->currentWorkspace->currency_code : 'USD';
        // }
        // else
        // {
        // }
        $payment_setting = Utility::getAdminPaymentSetting();
        // $this->currancy  = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';

        $this->email = isset($payment_setting['skrill_email']) ? $payment_setting['skrill_email'] : '';
        $this->is_enabled = isset($payment_setting['is_skrill_enabled']) ? $payment_setting['is_skrill_enabled'] : 'off';
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

        $this->email = isset($payment_setting['skrill_email']) ? $payment_setting['skrill_email'] : '';
        $this->is_enabled = isset($payment_setting['is_skrill_enabled']) ? $payment_setting['is_skrill_enabled'] : 'off';

    }

    public function planPayWithSkrill(Request $request)
    {

        $authuser = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $coupons_id = '';

        $this->setPaymentDetail();

        if ($plan) {

            /* Check for code usage */
            $plan->discounted_price = false;
            $price = $plan->{$request->skrill_payment_frequency . '_price'};

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

                $assignPlan = $authuser->assignPlan($plan->id, $request->skrill_payment_frequency);

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
                            'payment_frequency' => $request->skrill_payment_frequency,
                            'price_currency' => $this->currancy,
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );

                    return redirect()->back()->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                }
            }

            $tran_id = md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id');
            $skill = new SkrillRequest();
            $skill->pay_to_email = $this->email;
            $skill->return_url = route(
                'plan.skrill',
                [
                    $request->plan_id,
                    'tansaction_id=' . MD5($tran_id),
                    'payment_frequency=' . $request->skrill_payment_frequency,
                    'coupon_id=' . $coupons_id,
                    'flag' => 'success'
                ]
            );
            $skill->cancel_url = route('plan.skrill', [$request->plan_id, 'flag' => 'error']);

            // create object instance of SkrillRequest
            $skill->transaction_id = MD5($tran_id); // generate transaction id
            $skill->amount = $price;
            $skill->currency = $this->currancy;
            $skill->language = 'EN';
            $skill->prepare_only = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name = Auth::user()->name;
            $skill->customer_email = Auth::user()->email;

            // create object instance of SkrillClient
            $client = new SkrillClient($skill);
            $sid = $client->generateSID(); //return SESSION ID

            // handle error
            $jsonSID = json_decode($sid);
            if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
                return redirect()->back()->with('error', $jsonSID->message);
            }


            // do the payment
            $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
            if ($tran_id) {
                $data = [
                    'amount' => $price,
                    'trans_id' => MD5($request['transaction_id']),
                    'currency' => $this->currancy,
                ];
                session()->put('skrill_data', $data);
            }


            return redirect($redirectUrl);
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request, $plan)
    {

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan = Plan::find($planID);
        $user = Auth::user();

        $this->setPaymentDetail();

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($plan && $request->flag == 'success') {
            try {
                if (session()->has('skrill_data')) {
                    if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                        try {
                            $user->cancel_subscription($user->id);
                        } catch (\Exception $exception) {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    $get_data = session()->get('skrill_data');


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
                    $order->price = isset($get_data['amount']) ? $get_data['amount'] : 0;
                    $order->price_currency = $this->currancy;
                    $order->payment_frequency = $request->payment_frequency;
                    $order->txn_id = isset($request->transaction_id) ? $request->transaction_id : '';
                    $order->payment_type = 'Skrill';
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
            } catch (\Exception $e) {
                return redirect()->route(
                    'payment',
                    [
                        $request->payment_frequency,
                        \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
                    ]
                )->with('error', __('Plan not found!'));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Something went wrong!'));
        }
    }

    public function invoicePayWithSkrill(Request $request, $slug, $invoice_id)
    {
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

        $invoice = Invoice::find($invoice_id);
        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                'client.invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }

        $tran_id = md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id');
        $skill = new SkrillRequest();
        $skill->pay_to_email = $this->email;
        $skill->return_url = route(
            'client.invoice.skrill',
            [
                $slug,
                encrypt($invoice->id),
                'tansaction_id=' . MD5($tran_id),
            ]
        );
        $skill->cancel_url = route(
            'client.invoice.skrill',
            [
                $slug,
                encrypt($invoice->id),
            ]
        );

        // create object instance of SkrillRequest
        $skill->transaction_id = MD5($tran_id); // generate transaction id
        $skill->amount = $request->amount;
        $skill->currency = $this->currancy;
        $skill->language = 'EN';
        $skill->prepare_only = '1';
        $skill->merchant_fields = 'site_name, customer_email';
        $skill->site_name = $this->user->name;
        $skill->customer_email = $this->email;

        // create object instance of SkrillClient
        $client = new SkrillClient($skill);
        $sid = $client->generateSID(); //return SESSION ID

        // handle error
        $jsonSID = json_decode($sid);
        if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
            return redirect()->back()->with('error', $jsonSID->message);
        }

        // do the payment
        $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
        if ($tran_id) {
            $data = [
                'amount' => $request->amount,
                'trans_id' => MD5($request['transaction_id']),
                'currency' => $this->currancy,
            ];
            session()->put('skrill_data', $data);
        }

        return redirect($redirectUrl);
    }

    public function getInvoicePaymentStatus($slug, $invoice_id, Request $request)
    {
        $this->setPaymentDetail_client($invoice_id);


        if (!empty($invoice_id)) {
            $invoice_id = decrypt($invoice_id);
            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                $user = Auth::user();
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);

                try {
                    if (session()->has('skrill_data') && $request->has('tansaction_id')) {
                        $get_data = session()->get('skrill_data');
                        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $order_id;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($get_data['amount']) ? $get_data['amount'] : 0;
                        $invoice_payment->payment_type = 'Skrill';
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

                        session()->forget('skrill_data');

                        return redirect()->back()->with('success', __('Payment added Successfully'));

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
