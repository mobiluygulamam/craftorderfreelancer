<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FedapayPaymentController extends Controller
{
    public function planPayWithFedapay(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $fedapay_public_key = $payment_setting['fedapay_public_key'] ?? '';
        $fedapay_secret_key = $payment_setting['fedapay_secret_key'] ?? '';
        $currency = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        if ($plan) {
            if ($request->fedapay_payment_frequency == 'annual') {
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
                                    'payment_type' => 'FedaPay',
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
                $fedapay = !empty($payment_setting['fedapay_secret_key']) ? $payment_setting['fedapay_secret_key'] : '';
                $fedapay_mode = !empty($payment_setting['fedapay_mode']) ? $payment_setting['fedapay_mode'] : 'sandbox';
                \FedaPay\FedaPay::setApiKey($fedapay);
                \FedaPay\FedaPay::setEnvironment($fedapay_mode);
                $transaction = \FedaPay\Transaction::create([
                    "description" => "Fedapay Payment",
                    "amount" => (int) $get_amount,
                    "currency" => ["iso" => $currency],

                    "callback_url" => route('plan.fedapay.status', [
                        'plan_id' => $plan->id,
                        'amount' => $get_amount,
                        'coupon_id' => !empty($coupons) ? $coupons->id : '',
                        'frequency' => $request->fedapay_payment_frequency,
                    ]),
                    "cancel_url" => route('plan.fedapay.status', [
                        'plan_id' => $plan->id,
                        'amount' => $get_amount,
                        'coupon_id' => !empty($coupons) ? $coupons->id : '',
                        'frequency' => $request->fedapay_payment_frequency,
                    ]),

                ]);

                $token = $transaction->generateToken();
                return redirect($token->url);
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan Not Found..'));
        }
    }

    public function planGetFedapayStatus(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        if ($plan) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $payment_setting = Utility::getAdminPaymentSetting();
            $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
            $user = Auth::user();

            if ($request->status == 'approved') {
                try {
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'subscription_id' => !empty($request->id) ? $request->id : '',
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
                            'payment_frequency' => $request->frequency,
                            'payment_type' => __('FedaPay'),
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
                return redirect()->route('plans.index')->with('error', __('Transaction Has Been Failed..'));
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
        $this->fedapay_mode = isset($payment_setting['fedapay_mode']) ? $payment_setting['fedapay_mode'] : '';
        $this->fedapay_public_key = isset($payment_setting['fedapay_public_key']) ? $payment_setting['fedapay_public_key'] : '';
        $this->fedapay_secret_key = isset($payment_setting['fedapay_secret_key']) ? $payment_setting['fedapay_secret_key'] : '';
    }
    public function invoicePayWithFedapay(Request $request, $slug, $invoice_id)
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

                \FedaPay\FedaPay::setApiKey($this->fedapay_secret_key);

                // Create Fedapay transaction
                $transaction = \FedaPay\Transaction::create([
                    "description" => "Invoice Payment",
                    "amount" => (int) $get_amount,
                    "currency" => ["iso" => $this->currancy],
                    "callback_url" => route($client_keyword . 'invoice.fedapay.status', ['slug' => $slug, 'invoice_id' => $invoice_id, 'amount' => $get_amount, 'orderId' => $orderId]),
                    "cancel_url" => route($client_keyword . 'invoice.fedapay.status', ['slug' => $slug, 'invoice_id' => $invoice_id, 'amount' => $get_amount, 'orderId' => $orderId]),
                ]);
                $token = $transaction->generateToken();

                return redirect($token->url);
            } else {
                return redirect()->back()->with('error', 'Invoice Not Found !!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoiceId = $request->invoice_id;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $invoice = Invoice::find($invoiceId);

        if (!empty($invoice)) {
            $orderId = $request->orderId;
            $this->setPaymentDetail_client($invoiceId);
            $amount = $request->amount;
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);

            if ($request->status == 'approved') {
                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $orderId;
                $invoice_payment->invoice_id = $invoiceId;
                $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                $invoice_payment->amount = $amount;
                $invoice_payment->payment_type = 'Fedapay';
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
                    )->with('error', __('Transaction Has Been Failed..'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction Has Been Failed..'));
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
