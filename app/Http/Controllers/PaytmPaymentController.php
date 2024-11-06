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
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use Session;
use App\Models\Client;
use App\Models\Project;

class PaytmPaymentController extends Controller
{
    public $currancy, $user;

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
        // $this->currancy  = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'USD';

        config(
            [
                'services.paytm-wallet.env' => isset($payment_setting['paytm_mode']) ? $payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($payment_setting['paytm_merchant_id']) ? $payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' => isset($payment_setting['paytm_merchant_key']) ? $payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($payment_setting['paytm_industry_type']) ? $payment_setting['paytm_industry_type'] : '',
            ]
        );
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

        config(
            [
                'services.paytm-wallet.env' => isset($payment_setting['paytm_mode']) ? $payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($payment_setting['paytm_merchant_id']) ? $payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' => isset($payment_setting['paytm_merchant_key']) ? $payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($payment_setting['paytm_industry_type']) ? $payment_setting['paytm_industry_type'] : '',
            ]
        );

    }

    public function planPayWithPaytm(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $authuser = Auth::user();
        $coupons_id = '';

        $this->setPaymentDetail();

        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price = $plan->{$request->paytm_payment_frequency . '_price'};
            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;
                    $coupons_id = $coupons->id;

                    if ($usedCoupun >= $coupons->limit) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

                    $price = $price - $discount_value;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id, $request->paytm_payment_frequency);

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
                            'payment_frequency' => $request->paytm_payment_frequency,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => $this->currancy,
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                } else {
                    return response()->json(
                        [
                            'message' => __('Plan fail to upgrade.'),
                        ],
                        401
                    );
                }
            }


            $call_back = route(
                'plan.paytm',
                [
                    $request->plan_id,
                    'payment_frequency=' . $request->paytm_payment_frequency,
                    'coupon_id=' . $coupons_id,
                ]
            );

            $payment = PaytmWallet::with('receive');
            $payment->prepare(
                [
                    'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                    'user' => Auth::user()->id,
                    'mobile_number' => $request->mobile,
                    'email' => Auth::user()->email,
                    'amount' => $price,
                    'plan' => $plan->id,
                    'callback_url' => $call_back,
                ]
            );

            return $payment->receive();
        } else {
            return response()->json(
                [
                    'message' => __('Plan is deleted.'),
                ],
                401
            );
        }
    }

    public function getPaymentStatus(Request $request, $plan)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan = Plan::find($planID);
        $user = Auth::user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $this->setPaymentDetail();

        if ($plan) {
            // try
            // {
            // $transaction = PaytmWallet::with('receive');
            // $response    = $transaction->response();

            //     if($transaction->isSuccessful())
            //     {
            if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                try {
                    $user->cancel_subscription($user->id);
                } catch (\Exception $exception) {
                    \Log::debug($exception->getMessage());
                }
            }

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
            $order->price = isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0;
            $order->price_currency = $this->currancy;
            $order->payment_frequency = $request->payment_frequency;
            $order->txn_id = isset($request->TXNID) ? $request->TXNID : '';
            $order->payment_type = 'Paytm';
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
            //     }
            //     else
            //     {
            //         return redirect()->route(
            //             'payment', [
            //                          $request->payment_frequency,
            //                          \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
            //                      ]
            //         )->with('error', __('Transaction has been failed.'));
            //     }
            // }
            // catch(\Exception $e)
            // {
            //     return redirect()->route(
            //         'payment', [
            //                      $request->payment_frequency,
            //                      \Illuminate\Support\Facades\Crypt::encrypt($plan->id),
            //                  ]
            //     )->with('error', __('Something went wrong.'));
            // }
        } else {
            return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Plan not found!'));
        }
    }

    public function invoicePayWithPaytm(Request $request, $slug, $invoice_id)
    {
        // $this->setPaymentDetail();
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $validatorArray = [
            'amount' => 'required',
            'mobile' => 'required',
        ];

        $validator = Validator::make(
            $request->all(),
            $validatorArray
        )->setAttributeNames(
                [
                    'mobile' => 'Mobile No.',
                ]
            );

        if ($validator->fails()) {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoice = Invoice::find($request->invoice_id);


        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                'client.invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }


        $call_back = route(
            'client.invoice.paytm',
            [
                $slug,
                encrypt($invoice->id),

            ]
        );

        $payment = PaytmWallet::with('receive');

        $payment->prepare(
            [
                'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                'user' => $this->user->id,
                'mobile_number' => $request->mobile,
                'email' => $this->user->email,
                'amount' => $request->amount,
                'invoice_id' => $invoice->id,
                'callback_url' => route($client_keyword . 'invoice.paytm', '_token=' . Session::token() . '&slug=' . $slug . '&invoice_id=' . encrypt($invoice->id)),
            ]
        );

        return $payment->receive();
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $this->setPaymentDetail();

        $invoice_id = $request->invoice_id;
        $slug = $request->slug;
        if (!empty($invoice_id)) {
            $user = Auth::user();
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);

            $invoice_id = decrypt($invoice_id);
            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                // try
                // {
                $transaction = PaytmWallet::with('receive');
                // $response    = $transaction->response();
                // if($transaction->isSuccessful())
                // {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $order_id;
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->currency = $currentWorkspace->currency_code;
                $invoice_payment->amount = isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0;
                $invoice_payment->payment_type = 'Paytm';
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
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully'));
                }
                // }
                // else
                // {
                //     return redirect()->route(
                //         'client.invoices.show', [
                //                                   $slug,
                //                                   $invoice_id,
                //                               ]
                //     )->with('error', __('Transaction fail'));
                // }
                // }
                // catch(\Exception $e)
                // {

                //     return redirect()->route(
                //         'client.invoices.show', [
                //                                   $slug,
                //                                   $invoice_id,
                //                               ]
                //     )->with('error', __('Something went wrong.'));
                // }
            } else {
                if (\Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('success', __('Invoice not found'));
                }

            }
        } else {
            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('success', __('Invoice not found'));
            }

        }
    }
}
