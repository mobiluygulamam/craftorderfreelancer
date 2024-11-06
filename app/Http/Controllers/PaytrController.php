<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Twilio\TwiML\Voice\Stop;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;

class PaytrController extends Controller
{
    public function PlanpayWithPaytr(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $paytr_merchant_id = $payment_setting['paytr_merchant_id'];
        $paytr_merchant_key = $payment_setting['paytr_merchant_key'];
        $paytr_merchant_salt = $payment_setting['paytr_merchant_salt'];
        $currency = 'TL';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = \Auth::user();
        $plan = Plan::find($planID);
        if ($plan) {

            if ($request->paytr_payment_frequency == 'annual') {
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
                        $assignPlan = $authuser->assignPlan($plan->id, $request->paytr_payment_frequency);
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
                                    // 'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                                    'price_currency' => $payment_setting['currency'] ? $payment_setting['currency'] : 'USD',
                                    'txn_id' => '',
                                    'payment_type' => 'PayTr',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id, $request->paytr_payment_frequency);
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            try {
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;

                $merchant_id = $paytr_merchant_id;
                $merchant_key = $paytr_merchant_key;
                $merchant_salt = $paytr_merchant_salt;

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $email = $authuser->email;
                $payment_amount = $get_amount;
                $merchant_oid = $orderID;
                $user_name = $authuser->name;
                $user_address = isset($currentWorkspace->address) ? $currentWorkspace->address : 'No Address';
                $user_phone = isset($currentWorkspace->telephone) ? $currentWorkspace->telephone : '0000000000';


                $user_basket = base64_encode(json_encode(array(
                    array("Plan", $payment_amount, 1),
                )));

                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }

                $user_ip = $ip;
                $timeout_limit = "30";
                $debug_on = 1;
                $test_mode = 0;
                $no_installment = 0;
                $max_installment = 0;
                $currency = "TL";
                $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

                $request['orderID'] = $orderID;
                $request['plan_id'] = $plan->id;
                $request['price'] = $get_amount;
                $request['payment_status'] = 'failed';
                $payment_failed = $request->all();
                $request['payment_status'] = 'success';
                $payment_success = $request->all();

                $post_vals = array(
                    'merchant_id' => $merchant_id,
                    'user_ip' => $user_ip,
                    'merchant_oid' => $merchant_oid,
                    'email' => $email,
                    'payment_amount' => $payment_amount,
                    'paytr_token' => $paytr_token,
                    'user_basket' => $user_basket,
                    'debug_on' => $debug_on,
                    'no_installment' => $no_installment,
                    'max_installment' => $max_installment,
                    'user_name' => $user_name,
                    'user_address' => $user_address,
                    'user_phone' => $user_phone,
                    'merchant_ok_url' => route('pay.paytr.success', $payment_success),
                    'merchant_fail_url' => route('pay.paytr.success', $payment_failed),
                    'timeout_limit' => $timeout_limit,
                    'currency' => $currency,
                    'test_mode' => $test_mode
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);


                $result = @curl_exec($ch);

                if (curl_errno($ch)) {
                    die("PAYTR IFRAME connection error. err:" . curl_error($ch));
                }

                curl_close($ch);

                $result = json_decode($result, 1);

                if ($result['status'] == 'success') {
                    $token = $result['token'];
                } else {
                    return redirect()->route('plans.index')->with('error', $result['reason']);
                }

                return view('plans.paytr_payment', compact('token'));

            } catch (\Throwable $th) {
                return redirect()->route('plans.index')->with('error', $th->getMessage());
            }
        }
    }

    public function paytrsuccess(Request $request)
    {
        if ($request->payment_status == "success") {
            try {
                $user = \Auth::user();
                $planID = $request->plan_id;
                $plan = Plan::find($planID);
                $couponCode = $request->coupon;
                $getAmount = $request->price;
                $payment_setting = Utility::getAdminPaymentSetting();

                if ($couponCode != 0) {
                    $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
                    $request['coupon_id'] = $coupons->id;
                } else {
                    $coupons = null;
                }

                $order = new Order();
                $order->order_id = $request->orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = $payment_setting['currency'];
                $order->txn_id = $request->orderID;
                $order->payment_type = __('PayTR');
                $order->payment_status = 'success';
                $order->payment_frequency = $request->paytr_payment_frequency;
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id, $request->paytr_payment_frequency);

                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $request->orderID;
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
                        'plan_frequency' => $request->paytr_payment_frequency,
                        'plan_price' => Utility::getPlanActualPrice($plan->id, $request->paytr_payment_frequency)
                    ];
                    Utility::referraltransaction($planData, $user);
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e));
            }
        } else {
            return redirect()->route('plans.index')->with('success', __('Your Transaction is fail please try again.'));
        }
    }

    public function invoicePayWithPaytr(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);

        $payment_setting = Utility::getPaymentSetting($currentWorkspace->id);
        $paytr_merchant_id = $payment_setting['paytr_merchant_id'];
        $paytr_merchant_key = $payment_setting['paytr_merchant_key'];
        $paytr_merchant_salt = $payment_setting['paytr_merchant_salt'];

        $user_auth = \Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if (\Auth::check()) {
            $authuser = $user = \Auth::user();
        } else {
            $authuser = $user = Client::where('id', $invoice->client_id)->first();
        }

        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                try {

                    $merchant_id = $paytr_merchant_id;
                    $merchant_key = $paytr_merchant_key;
                    $merchant_salt = $paytr_merchant_salt;

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                    $email = $authuser->email;
                    $payment_amount = $get_amount;
                    $merchant_oid = $orderID;
                    $user_name = $authuser->name;
                    $user_address = isset($authuser->address) ? $authuser->address : 'No Address';
                    $user_phone = isset($authuser->telephone) ? $authuser->telephone : '0000000000';


                    $user_basket = base64_encode(json_encode(array(
                        array("Invoice", $payment_amount, 1),
                    )));

                    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                        $ip = $_SERVER["HTTP_CLIENT_IP"];
                    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                    } else {
                        $ip = $_SERVER["REMOTE_ADDR"];
                    }

                    $user_ip = $ip;
                    $timeout_limit = "30";
                    $debug_on = 1;
                    $test_mode = 0;
                    $no_installment = 0;
                    $max_installment = 0;
                    $currency = "TL";
                    $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

                    $request['orderID'] = $orderID;
                    $request['invoice_id'] = $invoice_id;
                    $request['price'] = $get_amount;
                    $request['slug'] = $slug;
                    $request['payment_status'] = 'failed';
                    $payment_failed = $request->all();
                    $request['payment_status'] = 'success';
                    $payment_success = $request->all();

                    $post_vals = array(
                        'merchant_id' => $merchant_id,
                        'user_ip' => $user_ip,
                        'merchant_oid' => $merchant_oid,
                        'email' => $email,
                        'payment_amount' => $payment_amount,
                        'paytr_token' => $paytr_token,
                        'user_basket' => $user_basket,
                        'debug_on' => $debug_on,
                        'no_installment' => $no_installment,
                        'max_installment' => $max_installment,
                        'user_name' => $user_name,
                        'user_address' => $user_address,
                        'user_phone' => $user_phone,
                        'merchant_ok_url' => route($client_keyword . 'invoice.paytr.success', $payment_success, $slug),
                        'merchant_fail_url' => route($client_keyword . 'invoice.paytr.success', $payment_failed, $slug),
                        'timeout_limit' => $timeout_limit,
                        'currency' => $currency,
                        'test_mode' => $test_mode
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);


                    $result = @curl_exec($ch);
                    if (curl_errno($ch)) {
                        die("PAYTR IFRAME connection error. err:" . curl_error($ch));
                    }


                    $result = json_decode($result, 1);

                    if ($result['status'] == 'success') {
                        $token = $result['token'];
                    } else {

                        if (Auth::user()) {
                            return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $result['reason']);
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $result['reason']);
                        }

                    }

                    return view('plans.paytr_payment', compact('token'));

                } catch (\Throwable $th) {
                    if (Auth::user()) {
                        return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $th->getMessage());
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $th->getMessage());
                    }
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $slug = $request->slug;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = \Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if (\Auth::check()) {
                $user = \Auth::user();
            } else {
                $user = Client::where('id', $invoice->client_id)->first();
            }
            if ($invoice) {
                try {
                    if ($request->payment_status == "success") {

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderID;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->payment_type = 'PayTr';
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
                            )->with('error', __('Transaction fail!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));

                        }
                    }
                } catch (\Exception $e) {
                    if (\Auth::user()) {
                        return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $e->getMessage());
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $e->getMessage());
                    }
                }
            } else {
                if (\Auth::user()) {
                    return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', __('Invoice not found'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Transaction fail!'));
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

