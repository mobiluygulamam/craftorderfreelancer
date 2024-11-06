<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\User;


class NepalsteController extends Controller
{

    public function planPayWithNepalste(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $api_key = $payment_setting['nepalste_public_key'] ?? '';
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        if ($plan) {
            if ($request->nepalste_payment_frequency == 'annual') {
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
                        $assignPlan = $authuser->assignPlan($plan->id, $request->paiementpro_payment_frequency);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {
                            if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                try {
                                    $authuser->cancel_subscription($authuser->id);
                                } catch (\Exception $exception) {
                                    return redirect()->back()->with('error', $exception->getMessage());
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
                                    'payment_type' => 'Paiementpro',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if (!empty($request->coupon)) {
                $response = ['plan_amount' => $get_amount, 'plan' => $plan, 'coupon_id' => $coupons->id, 'frequency' => $request->nepalste_payment_frequency];
            } else {
                $response = ['plan_amount' => $get_amount, 'plan' => $plan, 'frequency' => $request->nepalste_payment_frequency];
            }
            try {
                $parameters = [
                    'identifier' => 'DFU80XZIKS',
                    'currency' => $currency,
                    'amount' => $get_amount,
                    'details' => $plan->name,
                    'ipn_url' => route('plan.nepalste.status', $response),
                    'cancel_url' => route('plan.nepalste.cancel'),
                    'success_url' => route('plan.nepalste.status', $response),
                    'public_key' => $api_key,
                    'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme' => 'dark',
                    'customer_name' => $authuser->name,
                    'customer_email' => $authuser->email,
                ];


                //live end point
                $liveUrl = "https://nepalste.com.np/payment/initiate";
                //test end point
                $sandboxUrl = "https://nepalste.com.np/sandbox/payment/initiate";

                $url = $payment_setting['nepalste_mode'] == 'live' ? $liveUrl : $sandboxUrl;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result, true);

                if (isset($result['success'])) {
                    return redirect($result['url']);
                } else {
                    return redirect()->back()->with('error', __($result['message']));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan Not Found..'));
        }
    }

    public function planGetNepalsteStatus(Request $request)
    {
        $plan = Plan::find($request->plan);
        if ($plan) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $payment_setting = Utility::getAdminPaymentSetting();
            $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
            $user = Auth::user();
            try {
                Order::create(
                    [
                        'order_id' => $orderID,
                        'subscription_id' => null,
                        'name' => $user->name,
                        'email' => $user->email,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => $plan->name,
                        'plan_id' => $plan->id,
                        'price' => !empty($request->plan_amount) ? $request->plan_amount : 0,
                        'price_currency' => $currency,
                        'txn_id' => '',
                        'payer_id' => null,
                        'payment_frequency' => $request->frequency,
                        'payment_type' => __('Nepalste'),
                        'payment_status' => 'succeeded',
                        'receipt' => null,
                        'user_id' => $user->id,
                    ]
                );

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
                $assignPlan = $user->assignPlan($plan->id, $request->frequency);
                if ($assignPlan['is_success']) {
                    $planData = [
                        'plan_id' => $plan->id,
                        'plan_frequency' => $request->frequency,
                        'plan_price' => Utility::getPlanActualPrice($plan->id, $request->frequency)
                    ];
                    Utility::referraltransaction($planData, $user);
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
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
        $this->nepalste_mode = isset($payment_setting['nepalste_mode']) ? $payment_setting['nepalste_mode'] : '';
        $this->api_key = isset($payment_setting['nepalste_public_key']) ? $payment_setting['nepalste_public_key'] : '';
    }


    public function invoicePayWithNepalste(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        try {
            if ($invoice) {
                $request->validate(['amount' => 'required|numeric|min:0']);
                $this->setPaymentDetail_client($invoice_id);
                $user_auth = Auth::user();
                $get_amount = $request->amount;

                $orderId = strtoupper(str_replace('.', '', uniqid('', true)));
                $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
                $paiementproMerchantId = $this->api_key;

                $response = ['amount' => $get_amount, 'invoice_id' => $invoice->id, 'slug' => $slug, 'orderId' => $orderId];
                $parameters = [
                    'identifier' => 'DFU80XZIKS',
                    'currency' => $this->currancy ?? 'NPR',
                    'amount' => $get_amount,
                    'details' => $invoice->id,
                    'ipn_url' => route($client_keyword . 'invoice.nepalste.status', $response),
                    'cancel_url' => route($client_keyword . 'invoice.nepalste.cancel', $response),
                    'success_url' => route($client_keyword . 'invoice.nepalste.status', $response),
                    'public_key' => $this->api_key,
                    'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme' => 'dark',
                    'customer_name' => $this->user->name,
                    'customer_email' => $this->user->email,
                ];


                //live end point
                $liveUrl = "https://nepalste.com.np/payment/initiate";
                //test end point
                $sandboxUrl = "https://nepalste.com.np/sandbox/payment/initiate";

                $url = $this->nepalste_mode == 'live' ? $liveUrl : $sandboxUrl;


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result, true);

                if (isset($result['success'])) {
                    return redirect($result['url']);
                } else {
                    return redirect()->back()->with('error', __($result['message']));
                }
            } else {
                return redirect()->back()->with('error', 'Invoice Not Found !!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoiceId = $request->invoice_id;
        $orderId = $request->orderId;
        $this->setPaymentDetail_client($invoiceId);
        $amount = $request->amount;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        if (!empty($invoiceId)) {
            $invoice = Invoice::find($invoiceId);
            if (!empty($invoice)) {
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $orderId;
                $invoice_payment->invoice_id = $invoiceId;
                $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                $invoice_payment->amount = $amount;
                $invoice_payment->payment_type = 'Nepalste';
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
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                }
            } else {
                if (Auth::check()) {
                    return redirect()->route(
                        $client_keyword . 'invoices.show',
                        [
                            $slug,
                            $invoiceId,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoiceId)])->with('error', __('Invoice not found!'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
                    [
                        $slug,
                        $invoiceId,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoiceId)])->with('error', __('Invoice not found!'));
            }
        }
    }

    public function planGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has failed'));
    }
    public function invoiceGetNepalsteCancel(Request $request, $slug)
    {
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        if (Auth::check()) {
            return redirect()->route(
                $client_keyword . 'invoices.show',
                [
                    $slug,
                    $request->invoice_id,
                ]
            )->with('error', __('Transaction Has Been Failed.'));
        } else {
            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id)])->with('error', __('Transaction Has Been Failed.'));
        }
        return redirect()->back()->with('error', __('Transaction has failed'));
    }
}
