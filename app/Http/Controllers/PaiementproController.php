<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;

class PaiementproController extends Controller
{
    public function planPayWithPaiementpro(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $merchant_id = $payment_setting['paiementpro_merchant_id'];
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        if ($plan) {

            if ($request->paiementpro_payment_frequency == 'annual') {
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

            try {
                $call_back = route('plan.paiementpro.status', [
                    $plan->id,
                ]);
                $data = array(
                    'merchantId' => $merchant_id,
                    'amount' => $get_amount,
                    'description' => "Api PHP",
                    'channel' => $request->channel,
                    'countryCurrencyCode' => $currency,
                    'referenceNumber' => "REF-" . time(),
                    'customerEmail' => $authuser->email,
                    'customerFirstName' => $authuser->name,
                    'customerLastname' => $authuser->name,
                    'customerPhoneNumber' => $request->mobile_number,
                    'notificationURL' => $call_back,
                    'returnURL' => $call_back,
                    'returnContext' => json_encode(['coupon_id' => isset($coupons) ? $coupons->id : '', 'frequency' => $request->paiementpro_payment_frequency]),
                );

                $data = json_encode($data);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response);

                if (isset($response->success) && $response->success == true) {
                    return redirect($response->url);
                } else {
                    return redirect()->back()->with('error', __('Something went wrong'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e);
            }
        }
    }

    public function planGetpaiementproStatus(Request $request, $plan_id)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $user = Auth::user();
        $plan = Plan::find($plan_id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $jsonData = $request->returnContext;
        $dataArray = json_decode($jsonData, true);

        if ($plan) {
            try {
                if ($request->responsecode == -1) {
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
                            'price' => !empty($request->amount) ? $request->amount : 0,
                            'price_currency' => $currency,
                            'txn_id' => '',
                            'payer_id' => null,
                            'payment_frequency' => $dataArray['frequency'],
                            'payment_type' => __('paiementpro'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $user->id,
                        ]
                    );
                    $coupons = Coupon::find($dataArray['coupon_id']);
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
                    $assignPlan = $user->assignPlan($plan->id, $dataArray['frequency']);
                    if ($assignPlan['is_success']) {
                        $planData = [
                            'plan_id' => $plan->id,
                            'plan_frequency' => $dataArray['frequency'],
                            'plan_price' => Utility::getPlanActualPrice($plan->id, $dataArray['frequency'])
                        ];
                        Utility::referraltransaction($planData, $user);
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } else {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
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
        $this->paiementpro_merchant_id = isset($payment_setting['paiementpro_merchant_id']) ? $payment_setting['paiementpro_merchant_id'] : '';
    }

    public function invoicePayWithPaiementpro(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        try {
            if ($invoice) {
                $request->validate(['amount' => 'required|numeric|min:0']);
                $this->setPaymentDetail_client($invoice_id);
                $user_auth = Auth::user();
                $get_amount = $request->amount;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
                $paiementproMerchantId = $this->paiementpro_merchant_id;
                try {
                    $call_back = route($client_keyword . 'invoice.paiementpro.status', $slug);
                    $data = array(
                        'merchantId' => $paiementproMerchantId,
                        'amount' => $get_amount,
                        'description' => "Api PHP",
                        'channel' => $request->channel,
                        'countryCurrencyCode' => $this->currancy ?? 'USD',
                        'referenceNumber' => "REF-" . time(),
                        'customerEmail' => $this->user->email,
                        'customerFirstName' => $this->user->name,
                        'customerLastname' => $this->user->name,
                        'customerPhoneNumber' => $request->mobile_number,
                        'notificationURL' => $call_back,
                        'returnURL' => $call_back,
                        'returnContext' => json_encode(['invoice_id' => $invoice_id, 'orderID' => $orderID]),
                    );

                    $data = json_encode($data);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response);
                    if (isset($response->success) && $response->success == true) {
                        return redirect($response->url);
                    } else {
                        return redirect()->back()->with('error', __('Something went wrong'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e);
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
        $jsonData = $request->returnContext;
        $dataArray = json_decode($jsonData, true);
        $invoiceId = $dataArray['invoice_id'];
        $orderId = $dataArray['orderID'];
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
                $invoice_payment->payment_type = 'Paiementpro';
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
}