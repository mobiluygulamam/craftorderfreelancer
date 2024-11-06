<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use YooKassa\Client;
use App\Models\Client as AppClient;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\InvoicePayment;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class YooKassaController extends Controller
{
    public function planPayWithYookassa(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shopid'];
        $yookassa_secret_key = $payment_setting['yookassa_secret_key'];
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        if ($currency == 'RUB') {
            if ($plan) {
                if ($request->yookassa_payment_frequency == 'annual') {
                    $get_amount = $plan->annual_price;
                } else {
                    $get_amount = $plan->monthly_price;
                }
                // $get_amount = $plan->price;

                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($get_amount / 100) * $coupons->discount;
                        $get_amount = $get_amount - $discount_value;
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $authuser->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $orderID;
                        $userCoupon->save();
                        if ($coupons->limit == $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                try {

                    if (is_int((int) $yookassa_shop_id)) {
                        $client = new Client();
                        $client->setAuth((int) $yookassa_shop_id, $yookassa_secret_key);
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        $product = !empty($plan->name) ? $plan->name : 'Life time';
                        $payment = $client->createPayment(
                            array(
                                'amount' => array(
                                    'value' => $get_amount,
                                    'currency' => $currency,
                                ),
                                'confirmation' => array(
                                    'type' => 'redirect',
                                    'return_url' => route('plan.get.yookassa.status', [$plan->id, 'order_id' => $orderID, 'price' => $get_amount, 'frequency' => $request->yookassa_payment_frequency]),
                                ),
                                'capture' => true,
                                'description' => 'Заказ №1',
                            ),
                            uniqid('', true)
                        );

                        $authuser = Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();


                        if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                            try {
                                $authuser->cancel_subscription($authuser->id);
                            } catch (\Exception $exception) {
                                Log::debug($exception->getMessage());
                            }
                        }

                        Order::create(
                            [
                                'order_id' => $orderID,
                                'name' => $authuser->name,
                                'email' => $authuser->email,
                                'card_number' => null,
                                'card_exp_month' => null,
                                'card_exp_year' => null,
                                'plan_name' => $plan->name,
                                'plan_id' => $plan->id,
                                'price' => $get_amount == null ? 0 : $get_amount,
                                'price_currency' => $currency,
                                'txn_id' => '',
                                'payer_id' => '',
                                'payment_frequency' => $request->yookassa_payment_frequency,
                                'payment_type' => __('YooKassa'),
                                'payment_status' => 'succeeded',
                                'receipt' => null,
                                'user_id' => $authuser->id,
                            ]
                        );


                        \Session::put('payment_id', $payment['id']);

                        if ($payment['confirmation']['confirmation_url'] != null) {
                            return redirect($payment['confirmation']['confirmation_url']);
                        } else {
                            return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                        }

                        // return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));

                    } else {
                        return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                    }
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', $th->getMessage());
                }
            }
        } else {
            return redirect()->back()->with('error', __('This Currency Is Not Supported.'));
        }
    }

    public function planGetYookassaStatus(Request $request, $planId)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shopid'];
        $yookassa_secret_key = $payment_setting['yookassa_secret_key'];
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';

        if (is_int((int) $yookassa_shop_id)) {
            $client = new Client();
            $client->setAuth((int) $yookassa_shop_id, $yookassa_secret_key);
            $paymentId = \Session::get('payment_id');
            \Session::forget('payment_id');
            if ($paymentId == null) {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            $payment = $client->getPaymentInfo($paymentId);
            if (isset($payment) && $payment->status == "succeeded") {

                $plan = Plan::find($planId);
                $user = auth()->user();
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $Order = Order::where('order_id', $request->order_id)->first();
                    $Order->payment_status = 'succeeded';
                    $Order->save();

                    $assignPlan = $user->assignPlan($plan->id, $request->frequency);
                    $coupons = Coupon::find($request->coupon_id);

                    if (!empty($couponId)) {
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
                            'plan_frequency' => $request->frequency,
                            'plan_price' => Utility::getPlanActualPrice($plan->id, $request->frequency)
                        ];
                        Utility::referraltransaction($planData, $user);
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
            }
        }
    }

    public function setPaymentDetail_client($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = AppClient::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';
        $this->yookassa_shopid = isset($payment_setting['yookassa_shopid']) ? $payment_setting['yookassa_shopid'] : '';
        $this->yookassa_secret_key = isset($payment_setting['yookassa_secret_key']) ? $payment_setting['yookassa_secret_key'] : '';
    }
    public function invoicePayWithYookassa(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $get_amount = $request->amount;
        // $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($invoice_id);
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if ($this->currancy == 'RUB') {
            if ($invoice) {
                if (is_int((int) $this->yookassa_shopid)) {
                    $client = new Client();
                    $client->setAuth((int) $this->yookassa_shopid, $this->yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Session::put('orderID', $orderID);
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $this->currancy,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route($client_keyword . 'invoice.yookassa.status', [
                                    'slug' => $slug,
                                    'invoice_id' => $invoice->id,
                                    'amount' => $get_amount
                                ])
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );
                    Session::put('invoice_payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Currency Is Not Supported...');
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoiceId = $request->invoice_id;
        $this->setPaymentDetail_client($invoiceId);
        $user_auth = Auth::user();
        $invoice = Invoice::find($request->invoice_id);
        $amount = $request->amount;
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if ($invoice) {
            if (is_int((int) $this->yookassa_shopid)) {
                $client = new Client();
                $client->setAuth((int) $this->yookassa_shopid, $this->yookassa_secret_key);
                $paymentId = Session::get('invoice_payment_id');


                if ($paymentId == null) {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
                $payment = $client->getPaymentInfo($paymentId);
                Session::forget('invoice_payment_id');

                if (isset($payment) && $payment->status == "succeeded") {
                    try {
                        $orderId = Session::get('orderID');
                        Session::forget('orderID');

                        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderId;
                        $invoice_payment->invoice_id = $invoiceId;
                        $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                        $invoice_payment->amount = $amount;
                        $invoice_payment->payment_type = 'Yookassa';
                        $invoice_payment->receipt = '';
                        $invoice_payment->client_id = $this->user->id;
                        $invoice_payment->txn_id = '';
                        $invoice_payment->payment_status = 'approved';
                        $invoice_payment->save();

                        if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                            $invoice->status = 2;
                            $invoice->save();
                        } else {
                            $invoice->status = 3;
                            $invoice->save();
                        }

                        $user1 = $currentWorkspace->id;
                        $settings = Utility::getPaymentSetting($user1);
                        $total_amount = $invoice->getDueAmounts($invoice->id);
                        $client = AppClient::find($invoice->client_id);
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
                            )->with('success', __('Payment added Successfully..'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully...'));
                        }
                    } catch (\Exception $e) {
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
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            }
        } else {
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
        }
    }
}
