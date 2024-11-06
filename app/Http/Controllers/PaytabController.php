<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Paytabscom\Laravel_paytabs\Facades\paypage;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Exception;


class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled;

    public function paymentConfig()
    {
        if (\Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
            $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';

            config([
                'paytabs.profile_id' => isset($payment_setting['paytabs_profile_id']) ? $payment_setting['paytabs_profile_id'] : '',
                'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
                'paytabs.region' => isset($payment_setting['paytabs_region']) ? $payment_setting['paytabs_region'] : '',
                'paytabs.currency' => $payment_setting['currency'] ? $payment_setting['currency'] : 'USD',
            ]);
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

        config([
            'paytabs.profile_id' => isset($payment_setting['paytabs_profile_id']) ? $payment_setting['paytabs_profile_id'] : '',
            'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
            'paytabs.region' => isset($payment_setting['paytabs_region']) ? $payment_setting['paytabs_region'] : '',
            'paytabs.currency' => !empty($this->user->currentWorkspace->currency_code) ? $this->user->currentWorkspace->currency_code : 'USD',
        ]);

    }

    public function planPayWithpaytab(Request $request)
    {
        try {
            $planID = Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            $this->paymentconfig();
            $user = Auth::user();
            $setting = Utility::getAdminPaymentSetting();
            if ($plan) {
                // $get_amount = $plan->price;

                if ($request->paytab_payment_frequency == 'annual') {
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
                            $assignPlan = $authuser->assignPlan($plan->id);
                            if ($assignPlan['is_success'] == true && !empty($plan)) {
                                if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                    try {
                                        $authuser->cancel_subscription($authuser->id);
                                    } catch (Exception $exception) {
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
                                        'price_currency' => $setting['currency'] ? $setting['currency'] : 'USD',
                                        'txn_id' => '',
                                        'payment_frequency' => $request->paytab_payment_frequency,
                                        'payment_type' => 'Paytab',
                                        'payment_status' => 'success',
                                        'receipt' => null,
                                        'user_id' => $authuser->id,
                                    ]
                                );
                                $assignPlan = $authuser->assignPlan($plan->id, $request->paytab_payment_frequency);
                                return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated..'));
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }


                if ($setting['currency'] == 'INR') {
                    $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                    $paypage = new \App\PayTab\paypage();

                    $pay = $paypage->sendPaymentCode('all')
                        ->sendTransaction('sale')
                        ->sendCart(1, $get_amount, 'plan payment')
                        ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                        ->sendURLs(
                            route('plan.paytab.success', ['success' => 1, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $get_amount, 'coupon' => $coupon, 'frequency' => $request->paytab_payment_frequency]),
                            route('plan.paytab.success', ['success' => 0, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $get_amount, 'coupon' => $coupon, 'frequency' => $request->paytab_payment_frequency])
                        )
                        ->sendLanguage('en')
                        ->sendFramed($on = false)
                        ->create_pay_page();

                    return $pay;
                } else {
                    return redirect()->back()->with('error', __('Currency Not Supported. Contact To Your Site Admin'));
                }

            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }

    }

    public function PaytabGetPayment(Request $request)
    {
        $planId = $request->plan_id;
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $frequency = $request->frequency;


        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $plan = Plan::find($planId);
        $user = auth()->user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $setting = Utility::getAdminPaymentSetting();
        try {
            // if ($request->success == "1") {
            if ($request->success == 1) {
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = $setting['currency'] ? $setting['currency'] : 'USD';
                $order->payment_type = __('Paytab');
                $order->payment_status = 'success';
                $order->payment_frequency = $frequency;
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id, $frequency);
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
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

                if ($assignPlan['is_success']) {
                    $planData = [
                        'plan_id' => $plan->id,
                        'plan_frequency' => $frequency,
                        'plan_price' => Utility::getPlanActualPrice($plan->id, $frequency)
                    ];
                    Utility::referraltransaction($planData, $user);
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

            } else {
                return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function invoicePayWithpaytab(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $this->setPaymentDetail_client($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = Client::where('id', $invoice->client_id)->first();
        }

        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                if ($currentWorkspace->currency_code == 'INR') {
                    $paypage = new \App\PayTab\paypage();
                    $pay = $paypage->sendPaymentCode('all')
                        ->sendTransaction('sale')
                        ->sendCart(1, $get_amount, 'invoice payment')
                        ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                        ->sendURLs(
                            route($client_keyword . 'invoice.paytab.success', ['success' => 1, 'data' => $request->all(), $slug, 'amount' => $get_amount, 'slug' => $currentWorkspace->slug, 'invoice_id' => $invoice_id]),
                            route($client_keyword . 'invoice.paytab.success', ['success' => 0, 'data' => $request->all(), $slug, 'amount' => $get_amount, 'slug' => $currentWorkspace->slug, 'invoice_id' => $invoice_id])
                        )
                        ->sendLanguage('en')
                        ->sendFramed($on = false)
                        ->create_pay_page();
                    return $pay;
                } else {
                    return redirect()->back()->with('error', __('Currency Not Supported. Contact To Your Site Admin'));
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoice_id = $request->invoice_id;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = Client::where('id', $invoice->client_id)->first();
            }
            if ($invoice) {
                try {
                    // if ($request->respMessage == "Authorised") {
                    if ($request->success == "1") {

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderID;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->payment_type = 'paytab';
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
                        if ($webhook) {
                            $parameter = json_encode($invoice);
                            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                            if ($status != true) {
                                $msg = "Webhook call failed.";
                            }
                        }
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                        }

                    } else {
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('error', __('Transaction fail!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));

                        }
                    }
                } catch (Exception $e) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __($e->getMessage()));
                }

            } else {
                if (Auth::user()) {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Invoice not found'));
                } else {
                    $id = \Crypt::encrypt($invoice_id);

                    return redirect()->route('pay.invoice', $id)->with('error', __('Transaction fail!'));
                }
            }
        } else {
            return redirect()->route(
                'client.invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invoice not found!'));
        }
    }

}
