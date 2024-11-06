<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;



class ToyyibpayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData, $user;



    public function setPaymentDetail()
    {
        // $user = Auth::user();
        // if($user->getGuard() == 'client')
        // {
        //     $payment_setting = Utility::getPaymentSetting($user->currentWorkspace->id);
        // }
        // else
        // {
        // }
        $payment_setting = Utility::getAdminPaymentSetting();
        $this->currancy = $payment_setting['currency'] ? $payment_setting['currency'] : 'MYR';
        $this->secretKey = isset($payment_setting['toyyibpay_secret_key']) ? $payment_setting['toyyibpay_secret_key'] : '';
        $this->categoryCode = isset($payment_setting['toyyibpay_category_code']) ? $payment_setting['toyyibpay_category_code'] : '';
        $this->is_enabled = isset($payment_setting['is_toyyibpay_enabled']) ? $payment_setting['is_toyyibpay_enabled'] : 'off';
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

        $this->secretKey = isset($payment_setting['toyyibpay_secret_key']) ? $payment_setting['toyyibpay_secret_key'] : '';
        $this->categoryCode = isset($payment_setting['toyyibpay_category_code']) ? $payment_setting['toyyibpay_category_code'] : '';
        $this->is_enabled = isset($payment_setting['is_toyyibpay_enabled']) ? $payment_setting['is_toyyibpay_enabled'] : 'off';

    }

    public function index()
    {
        return view('payment');
    }

    public function charge(Request $request)
    {
        $this->setPaymentDetail();

        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);

            if ($plan) {

                if ($request->toyyibpay_payment_frequency == 'annual') {
                    $get_amount = $plan->annual_price;
                } else {
                    $get_amount = $plan->monthly_price;

                }

                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $get_amount = $plan->price - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                if ($get_amount <= 0) {
                    $user = auth()->user();

                    $user->plan = $plan->id;
                    $user->save();
                    $assignPlan = $user->assignPlan($plan->id, $request->toyyibpay_payment_frequency);


                    if ($assignPlan['is_success'] == true && !empty($plan)) {
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        $order = new Order();
                        $order->order_id = $orderID;
                        $order->name = $user->name;
                        $order->card_number = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year = '';
                        $order->plan_name = $plan->name;
                        $order->plan_id = $plan->id;
                        $order->price = $get_amount;
                        $order->payment_frequency = $request->toyyibpay_payment_frequency;
                        $order->price_currency = $this->currency;
                        $order->payment_type = __('Toyyibpay');
                        $order->payment_status = 'succeeded';
                        $order->receipt = '';
                        $order->user_id = $user->id;
                        $order->save();

                        return redirect()->route('plans.index')->with('success', __('Plan successfully upgraded.'));
                    } else {
                        return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                    }
                }

                $frequency = $request->toyyibpay_payment_frequency;
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $this->callBackUrl = route('plan.status', [$plan->id, $get_amount, $frequency, $coupon]);
                $this->returnUrl = route('plan.status', [$plan->id, $get_amount, $frequency, $coupon]);


                $Date = date('d-m-Y');
                $ammount = $get_amount;

                $billName = $plan->name;
                $description = $plan->name;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";

                $some_data = array(
                    'userSecretKey' => $this->secretKey,
                    'categoryCode' => $this->categoryCode,
                    'billName' => $billName,
                    'billDescription' => $description,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => 100 * $ammount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => Auth::user()->name,
                    'billEmail' => Auth::user()->email,
                    'billPhone' => '0000000000',
                    'billSplitPayment' => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel' => '0',
                    'billContentEmail' => $billContentEmail,
                    'billChargeToCustomer' => 1,
                    'billExpiryDate' => $billExpiryDate,
                    'billExpiryDays' => $billExpiryDays
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function status(Request $request, $planId, $getAmount, $frequency, $couponCode)
    {
        $this->setPaymentDetail();


        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $plan = Plan::find($planId);
        $user = auth()->user();
        // $request['status_id'] = 1;

        // 1=success, 2=pending, 3=fail
        try {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if ($request->status_id == 3) {
                $statuses = 'Fail';
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->payment_frequency = $frequency;
                $order->price_currency = $this->currency;
                $order->payment_type = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                return redirect()->route('plans.index')->with('error', __('Your Transaction is failed, please try again'));
            } else if ($request->status_id == 2) {
                $statuses = 'pending';
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->payment_frequency = $frequency;
                $order->price_currency = $this->currency;
                $order->payment_type = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                return redirect()->route('plans.index')->with('error', __('Your transaction is pending'));
            } else if ($request->status_id == 1) {
                $statuses = 'Succeeded';
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->payment_frequency = $frequency;
                $order->price_currency = $this->currency;
                $order->payment_type = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id);
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
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function invoicepaywithtoyyibpay(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);

        $invoice = Invoice::find($invoice_id);
        $get_amount = $request->amount;
        $user1 = Auth::user();
        $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

        if ($invoice) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                // $name = Utility::invoiceNumberFormat($settings, $invoice->invoice_id);

                $this->callBackUrl = route($client_keyword . 'invoice.toyyibpay', [
                    $slug,
                    $invoice->id,
                    $get_amount
                ]);


                $this->returnUrl = route($client_keyword . 'invoice.toyyibpay', [
                    $slug,
                    $invoice->id,
                    $get_amount
                ]);

                $Date = date('d-m-Y');
                $ammount = $get_amount;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";

                $some_data = array(
                    'userSecretKey' => $this->secretKey,
                    'categoryCode' => $this->categoryCode,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billName' => 'Invoice',
                    'billDescription' => 'Invoice Payment',
                    'billAmount' => 100 * $ammount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => $this->user->name,
                    'billEmail' => $this->user->email,
                    'billPhone' => isset($this->user->telephone) ? $this->user->telephone : '0000000000',
                    'billSplitPayment' => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel' => '0',
                    'billContentEmail' => $billContentEmail,
                    'billChargeToCustomer' => 1,
                    'billExpiryDate' => $billExpiryDate,
                    'billExpiryDays' => $billExpiryDays
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
            }

            return redirect()->back()->with('error', __('Unknown error occurred.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function getInvoicePaymentStatus(Request $request, $slug, $invoice_id, $amount)
    {
        $this->setPaymentDetail_client($invoice_id);

        $user = Auth::user();
        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);


        if ($request->status_id == 3) {
            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Your Transaction is failed, please try again!'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Your Transaction is failed, please try again'));
            }
        } else if ($request->status_id == 2) {

            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Your transaction is pending!'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Your transaction is pending'));
            }

        } else if ($request->status_id == 1) {

            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $request->order_id;
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->currency = $currentWorkspace->currency_code;
                $invoice_payment->amount = isset($amount) ? $amount : 0;
                $invoice_payment->payment_type = 'toyyibpay';
                $invoice_payment->receipt = '';
                $invoice_payment->client_id = $this->user->id;
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
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
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
        }
    }

}
