<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CinetPayController extends Controller
{
    public function planPayWithCinetpay(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($plan) {
            if ($request->cinetpay_payment_frequency == 'annual') {
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
                        $assignPlan = $authuser->assignPlan($plan->id, $request->cinetpay_payment_frequency);
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
                                    'name' => $authuser->name,
                                    'email' => $authuser->email,
                                    'card_number' => null,
                                    'card_exp_month' => null,
                                    'card_exp_year' => null,
                                    'plan_name' => $plan->name,
                                    'plan_id' => $plan->id,
                                    'price' => $get_amount == null ? 0 : $get_amount,
                                    'price_currency' => $payment_setting['currency'] ? $payment_setting['currency'] : 'USD',
                                    'txn_id' => '',
                                    'payment_type' => 'ClinetPay',
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

            try {
                if (
                    $currency != 'XOF' &&
                    $currency != 'CDF' &&
                    $currency != 'USD' &&
                    $currency != 'KMF' &&
                    $currency != 'GNF'
                ) {
                    return redirect()->route('plans.index')->with('error', __('Availabe currencies: XOF, CDF, USD, KMF, GNF'));
                }


                $cinetpay_data = [
                    "amount" => $get_amount,
                    "currency" => $currency,
                    "apikey" => $payment_setting['cinetpay_api_key'],
                    "site_id" => $payment_setting['cinetpay_site_id'],
                    "transaction_id" => $orderID,
                    "description" => "Plan Subscription",
                    "return_url" => route('plan.cinetpay.return'),
                    "notify_url" => route('plan.cinetpay.notify'),
                    "metadata" => "user001",
                    'customer_name' => isset($authuser->name) ? $authuser->name : '',
                    'customer_surname' => isset($authuser->name) ? $authuser->name : '',
                    'customer_email' => isset($authuser->email) ? $authuser->email : '',
                    'customer_phone_number' => '',
                    'customer_address' => '',
                    'customer_city' => 'texas',
                    'customer_country' => 'BF',
                    'customer_state' => 'USA',
                    'customer_zip_code' => '',
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 45,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array(
                        "content-type:application/json"
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                //On recupère la réponse de CinetPay
                $response_body = json_decode($response, true);
                if (isset($response_body['code']) && $response_body['code'] == '201') {
                    $cinetpaySession = [
                        'order_id' => $orderID,
                        'plan_id' => $plan->id,
                        'plan_amount' => $get_amount,
                        'coupon_code' => !empty($request->coupon_code) ? $request->coupon_code : '',
                        'frequency' => $request->cinetpay_payment_frequency,
                    ];

                    $request->session()->put('cinetpaySession', $cinetpaySession);

                    $payment_link = $response_body["data"]["payment_url"]; // Retrieving the payment URL
                    return redirect($payment_link);
                } else {
                    return back()->with('error', isset($response_body["description"]) ? $response_body["description"] : 'Something Went Wrong!!!');
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planCinetPayReturn(Request $request)
    {
        $cinetpaySession = $request->session()->get('cinetpaySession');
        $request->session()->forget('cinetpaySession');
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $plan = Plan::find($cinetpaySession['plan_id']);
        $user = Auth::user();
        if (isset($request->transaction_id) || isset($request->token)) {

            $cinetpay_check = [
                "apikey" => $payment_setting['cinetpay_api_key'],
                "site_id" => $payment_setting['cinetpay_site_id'],
                "transaction_id" => $request->transaction_id
            ];
            $response = $this->getPayStatus($cinetpay_check);
            $response_body = json_decode($response, true);

            if ($response_body['code'] == '00') {
                Order::create(
                    [
                        'order_id' => $cinetpaySession['order_id'],
                        'subscription_id' => null,
                        'name' => $user->name,
                        'email' => $user->email,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => $plan->name,
                        'plan_id' => $plan->id,
                        'price' => isset($cinetpaySession['plan_amount']) ? $cinetpaySession['plan_amount'] : 0,
                        'price_currency' => $currency,
                        'txn_id' => '',
                        'payer_id' => null,
                        'payment_frequency' => $cinetpaySession['frequency'],
                        'payment_type' => __('ClinetPay'),
                        'payment_status' => 'succeeded',
                        'receipt' => null,
                        'user_id' => $user->id,
                    ]
                );

                $coupons = Coupon::find($cinetpaySession['coupon_code']);
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $cinetpaySession['order_id'];
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
                $assignPlan = $user->assignPlan($plan->id, $cinetpaySession['frequency']);
                if ($assignPlan['is_success']) {
                    $planData = [
                        'plan_id' => $plan->id,
                        'plan_frequency' => $cinetpaySession['frequency'],
                        'plan_price' => Utility::getPlanActualPrice($plan->id, $cinetpaySession['frequency'])
                    ];
                    Utility::referraltransaction($planData, $user);
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Your Payment has failed!'));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Your Payment has failed!'));
        }
    }


    public function planCinetPayNotify(Request $request, $id = null)
    {
        /* 1- Recovery of parameters posted on the URL by CinetPay
         * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#les-etapes-pour-configurer-lurl-de-notification
         * */
        if (isset($request->cpm_trans_id)) {
            // Using your transaction identifier, check that the order has not yet been processed
            $VerifyStatusCmd = "1"; // status value to retrieve from your database
            if ($VerifyStatusCmd == '00') {
                //The order has already been processed
                // Scarred you script
                die();
            }
            if ($id == null) {

                $payment_setting = Utility::getAdminPaymentSetting();
            } else {

                $comapnysetting = Utility::getPaymentSetting($id);
            }

            /* 2- Otherwise, we check the status of the transaction in the event of a payment attempt on CinetPay
             * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#2-verifier-letat-de-la-transaction */
            $cinetpay_check = [
                "apikey" => $payment_setting['cinetpay_api_key'],
                "site_id" => $payment_setting['cinetpay_site_id'],
                "transaction_id" => $request->cpm_trans_id
            ];

            $response = $this->getPayStatus($cinetpay_check); // call query function to retrieve status

            //We get the response from CinetPay
            $response_body = json_decode($response, true);
            if ($response_body['code'] == '00') {
                /* correct, on délivre le service
                 * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#3-delivrer-un-service*/
                echo 'Congratulations, your payment has been successfully completed';
            } else {
                // transaction a échoué
                echo 'Failure, code:' . $response_body['code'] . ' Description' . $response_body['description'] . ' Message: ' . $response_body['message'];
            }
            // Update the transaction in your database
            /*  $order->update(); */
        } else {
            print ("cpm_trans_id non found");
        }
    }


    public function getPayStatus($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type:application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err)
            return redirect()->route('plans.index')->with('error', __('Something went wrong!'));
        else
            return ($response);
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
        $this->cinetpay_api_key = isset($payment_setting['cinetpay_api_key'], ) ? $payment_setting['cinetpay_api_key'] : '';
        $this->cinetpay_site_id = isset($payment_setting['cinetpay_site_id']) ? $payment_setting['cinetpay_site_id'] : '';
    }

    public function invoicePayWithCinetpay(Request $request, $slug, $invoice_id)
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
                $cinetpay_api_key = $this->cinetpay_api_key;
                $cinetpay_site_id = $this->cinetpay_site_id;
                $currency = $this->currancy;
                $user_auth = Auth::user();
                $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
                if (
                    $currency != 'XOF' &&
                    $currency != 'CDF' &&
                    $currency != 'USD' &&
                    $currency != 'KMF' &&
                    $currency != 'GNF'
                ) {
                    return redirect()->back()->with('error', __('Availabe currencies: XOF, CDF, USD, KMF, GNF'));
                }


                $cinetpay_data = [
                    "amount" => $get_amount,
                    "currency" => $currency,
                    "apikey" => $cinetpay_api_key,
                    "site_id" => $cinetpay_site_id,
                    "transaction_id" => $orderId,
                    "description" => "Invoice Payment",
                    "return_url" => route($client_keyword . 'invoice.cinetpay.return', [$invoice_id, $get_amount, $slug]),
                    "notify_url" => route($client_keyword . 'invoice.cinetpay.notify', $this->user->currentWorkspace->id),
                    "metadata" => strval($invoice->id),
                    'customer_name' => isset($this->user->name) ? $this->user->name : '',
                    'customer_email' => isset($this->user->email) ? $this->user->email : '',
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 45,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array(
                        "content-type:application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                $response_body = json_decode($response, true);
                if (isset($response_body['code']) && $response_body['code'] == '201') {
                    $cinetpaySession = [
                        'order_id' => $orderId,
                        'amount' => $get_amount,
                    ];

                    $request->session()->put('cinetpaySession', $cinetpaySession);

                    $payment_link = $response_body["data"]["payment_url"];
                    return redirect($payment_link);
                } else {
                    return redirect()->back()->with('error', isset($response_body["description"]) ? $response_body["description"] : 'Something Went Wrong!!!');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice Not Found !!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __($e));
        }
    }


    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount, $slug)
    {
        $cinetpaySession = $request->session()->get('cinetpaySession');
        $request->session()->forget('cinetpaySession');
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $invoice = Invoice::where('id', $invoice_id)->first();
        if (!empty($invoice)) {
            $orderId = $cinetpaySession['order_id'];
            $this->setPaymentDetail_client($invoice_id);
            $amount = $cinetpaySession['amount'];

            if (isset($request->transaction_id) || isset($request->token)) {
                $cinetpay_api_key = $this->cinetpay_api_key;
                $cinetpay_site_id = $this->cinetpay_site_id;
                $cinetpay_check = [
                    "apikey" => $cinetpay_api_key,
                    "site_id" => $cinetpay_site_id,
                    "transaction_id" => $request->transaction_id
                ];
                $response = $this->getPayStatus($cinetpay_check);

                $response_body = json_decode($response, true);
                if ($response_body['code'] == '00') {

                    $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->order_id = $orderId;
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                    $invoice_payment->amount = $amount;
                    $invoice_payment->payment_type = 'CinetPay';
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
                                $invoice->id,
                            ]
                        )->with('error', __('Your Payment has failed!'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Your Payment has failed!'));
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
                    )->with('error', __('Your Payment has failed!'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Your Payment has failed!'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
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

    public function invoiceCinetPayNotify(Request $request, $id = null)
    {
        /* 1- Recovery of parameters posted on the URL by CinetPay
         * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#les-etapes-pour-configurer-lurl-de-notification
         * */
        if (isset($request->cpm_trans_id)) {
            // Using your transaction identifier, check that the order has not yet been processed
            $VerifyStatusCmd = "1"; // status value to retrieve from your database
            if ($VerifyStatusCmd == '00') {
                //The order has already been processed
                // Scarred you script
                die();
            }
            if ($id == null) {

                $payment_setting = Utility::getAdminPaymentSetting();
            } else {

                $comapnysetting = Utility::getPaymentSetting($id);
            }

            /* 2- Otherwise, we check the status of the transaction in the event of a payment attempt on CinetPay
             * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#2-verifier-letat-de-la-transaction */
            $cinetpay_check = [
                "apikey" => $payment_setting['cinetpay_api_key'],
                "site_id" => $payment_setting['cinetpay_site_id'],
                "transaction_id" => $request->cpm_trans_id
            ];

            $response = $this->getPayStatus($cinetpay_check); // call query function to retrieve status

            //We get the response from CinetPay
            $response_body = json_decode($response, true);
            if ($response_body['code'] == '00') {
                /* correct, on délivre le service
                 * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#3-delivrer-un-service*/
                echo 'Congratulations, your payment has been successfully completed';
            } else {
                // transaction a échoué
                echo 'Failure, code:' . $response_body['code'] . ' Description' . $response_body['description'] . ' Message: ' . $response_body['message'];
            }
            // Update the transaction in your database
            /*  $order->update(); */
        } else {
            print ("cpm_trans_id non found");
        }
    }

}
