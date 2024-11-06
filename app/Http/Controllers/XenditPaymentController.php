<?php

namespace App\Http\Controllers;

use Xendit\Xendit;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\Client;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Utility;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class XenditPaymentController extends Controller
{
    public function planPayWithXendit(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $xendit_api = $payment_setting['xendit_api_key'];
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $user = Auth::user();

        $planID = Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($plan) {
            if ($request->midtrans_payment_frequency == 'annual') {
                $get_amount = $plan->annual_price;
            } else {
                $get_amount = $plan->monthly_price;
            }

            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($get_amount / 100) * $coupons->discount;

                    $get_amount = $get_amount - $discount_value;

                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

                    if ($get_amount <= 0) {
                        $authuser = Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();
                        $assignPlan = $authuser->assignPlan($plan->id, $request->midtrans_payment_frequency);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {
                            if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                try {
                                    $authuser->cancel_subscription($authuser->id);
                                } catch (\Exception $exception) {
                                    \Log::debug($exception->getMessage());
                                }
                            }
                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $authuser->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();
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
                                    'price' => $get_amount == null ? 0 : $get_amount,
                                    'price_currency' => $payment_setting['currency'] ? $payment_setting['currency'] : 'USD',
                                    'txn_id' => '',
                                    'payment_type' => 'PayTr',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id, $request->midtrans_payment_frequency);
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }


            $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'plan' => $plan, 'frequency' => $request->xendit_payment_frequency, 'coupon_id' => !empty($coupons->id) ? $coupons->id : NULL, 'currency' => $currency];
            \App\Xendit\Xendit::setApiKey($xendit_api);
            $params = [
                'external_id' => $orderID,
                'payer_email' => Auth::user()->email,
                'description' => 'Payment for order ' . $orderID,
                'amount' => $get_amount,
                'callback_url' => route('plan.xendit.status'),
                'success_redirect_url' => route('plan.xendit.status', $response),
                'failure_redirect_url' => route('plans.index'),
            ];

            $invoice = \App\Xendit\Invoice::create($params);
            Session::put('invoice', $invoice);

            return redirect($invoice['invoice_url']);
        }
    }
    public function planGetXenditStatus(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $xendit_api = $payment_setting['xendit_api_key'];
        \App\Xendit\Xendit::setApiKey($xendit_api);
        $session = Session::get('invoice');
        $getInvoice = \App\Xendit\Invoice::retrieve($session['id']);

        $user = User::find($request->user);
        $plan = Plan::find($request->plan);
        $getAmount = $request->get_amount;
        $currency = $request->currency;
        $frequency = $request->frequency;
        $couponId = $request->coupon_id;
        $plan = Plan::find($request->plan);
        if ($getInvoice['status'] == 'PAID') {

            Order::create(
                [
                    'order_id' => $request->orderId,
                    'subscription_id' => null,
                    'name' => $user->name,
                    'email' => $user->email,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $getAmount,
                    'price_currency' => $currency,
                    'txn_id' => '',
                    'payer_id' => null,
                    'payment_frequency' => $frequency,
                    'payment_type' => __('Xendit'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );
            $coupons = Coupon::find($couponId);
            if (!empty($request->coupon_id)) {
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $request->orderId;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }
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
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
        }
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
        $this->xendit_api_key = isset($payment_setting['xendit_api_key']) ? $payment_setting['xendit_api_key'] : '';
        $this->xendit_token = isset($payment_setting['xendit_token']) ? $payment_setting['xendit_token'] : '';
        // $this->xendit_api_key,
        // $this->xendit_token);


    }

    public function invoicePayWithXendit(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $get_amount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($invoice_id);
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        try {
            if ($invoice) {
                $xendit_token = $this->xendit_token;
                $xendit_api = $this->xendit_api_key;
                $currency = $this->currancy;

                $response = ['orderId' => $orderID, 'user' => $this->user, 'get_amount' => $get_amount, 'invoice' => $invoice_id, 'currency' => $currency];

                \App\Xendit\Xendit::setApiKey($this->xendit_api_key);

                $params = [
                    'external_id' => $orderID,
                    'payer_email' => $this->user->email,
                    'description' => 'Payment for order ' . $orderID,
                    'amount' => $get_amount,
                    'callback_url' => route($client_keyword . 'invoice.xendit.status', $slug),
                    'success_redirect_url' => route($client_keyword . 'invoice.xendit.status', $slug) . '?' . http_build_query($response),
                ];
                $Xenditinvoice = \App\Xendit\Invoice::create($params);

                Session::put('invoicepay', $Xenditinvoice);

                return redirect($Xenditinvoice['invoice_url']);

            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $session = Session::get('invoicepay');
        $invoiceId = $request->invoice;
        $this->setPaymentDetail_client($invoiceId);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $amount = $request->get_amount;

        if (!empty($invoiceId)) {
            $invoice = Invoice::find($invoiceId);

            if (!empty($invoice)) {
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $request->orderId;
                $invoice_payment->invoice_id = $invoiceId;
                $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                $invoice_payment->amount = $amount;
                $invoice_payment->payment_type = 'Xendit';
                $invoice_payment->receipt = '';
                $invoice_payment->client_id = $this->user->id;
                $invoice_payment->txn_id = '';
                $invoice_payment->payment_status = 'approved';
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
                    'total_amount' => $total_amount,
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
                if (Auth::check()) {
                    return redirect()->route(
                        $client_keyword . 'invoices.show',
                        [
                            $slug,
                            $invoice->id,
                        ]
                    )->with('success', __('Payment added Successfully'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                }


            } else {
                if (\Auth::check()) {
                    return redirect()->route(
                        $client_keyword . 'invoices.show',
                        [
                            $slug,
                            $invoiceId,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoiceId)])->with('error', __('Invoice not found!'));
                }
            }

        } else {
            if (\Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
                    [
                        $slug,
                        $invoiceId,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoiceId)])->with('error', __('Invoice not found!'));
            }

        }
    }
}
