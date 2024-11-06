<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoicePayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use App\Models\Project;
use App\Models\User;
use Dflydev\DotAccessData\Util;

class PayfastController extends Controller
{
    public $user;

    public function setPaymentDetail_client($invoice_id)
    {

        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);

    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
            $pfHost = $payment_setting['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
            $planID = Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            $setting = Utility::getAdminPaymentSetting();
            if ($plan) {

                if ($request->payfast_payment_frequency == 'annual') {

                    $plan_amount = $plan->annual_price;
                } else {
                    $plan_amount = $plan->monthly_price;

                }
                // $plan_amount = $plan->price;
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $user = Auth::user();

                if ($request->coupon != null) {
                    // $coupons = Coupon::where('code', $request->coupon_code)->first();
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();


                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $order_id;
                        $userCoupon->save();

                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan_amount / 100) * $coupons->discount;
                        $plan_amount = $plan_amount - $discount_value;
                        $coupons->save();

                        if ($coupons->limit <= $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }

                    }

                }


                if ($plan_amount <= 0) {
                    $user = Auth::user();
                    $planID = Crypt::decrypt($request->plan_id);
                    $plan = Plan::find($planID);
                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                    Order::create(
                        [
                            'order_id' => $order_id,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $request->amount == null ? 0 : $request->amount,
                            'payment_frequency' => $request->payfast_payment_frequency,
                            //'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                            'price_currency' => $setting['currency'] ? $setting['currency'] : 'USD',
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $user->id,
                        ]
                    );
                    $assignPlan = $user->assignPlan($plan->id, $request->payfast_payment_frequency);

                    if ($assignPlan['is_success']) {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                }


                $success = Crypt::encrypt([
                    'plan' => $plan->toArray(),
                    'order_id' => $order_id,
                    'plan_amount' => $plan_amount,
                    'payment_frequency' => $request->payfast_payment_frequency
                ]);

                $data = array(
                    // Merchant details
                    'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.payment.success', $success),
                    'cancel_url' => route('plans.index'),
                    'notify_url' => route('plans.index'),
                    // Buyer details
                    'name_first' => $user->name,
                    'name_last' => '',
                    'email_address' => $user->email,
                    // Transaction details
                    'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
                    'amount' => number_format(sprintf('%.2f', $plan_amount), 2, '.', ''),
                    'item_name' => $plan->name,
                );

                $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $url = "https://$pfHost/eng/process";
                $fields_string = http_build_query($data);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                $response = curl_exec($ch);
                curl_close($ch);
                return $response;
            }
        }
    }

    public function generateSignature($data, $passPhrase = null)
    {

        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function success(Request $request, $success)
    {
        $setting = Utility::getAdminPaymentSetting();
        try {

            $user = Auth::user();
            $data = Crypt::decrypt($success);
            $order = new Order();
            $order->order_id = $data['order_id'];
            $order->name = $user->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $data['plan']['name'];
            $order->plan_id = $data['plan']['id'];
            $order->price = $data['plan_amount'];
            // $order->price_currency = env('CURRENCY');
            $order->price_currency = $setting['currency'] ? $setting['currency'] : 'USD';
            $order->payment_frequency = $data['payment_frequency'];
            $order->txn_id = $data['order_id'];
            $order->payment_type = __('PayFast');
            $order->payment_status = 'approved';
            $order->txn_id = '';
            $order->receipt = '';
            $order->user_id = $user->id;
            $order->save();
            $assignPlan = $user->assignPlan($data['plan']['id'], $data['payment_frequency']);

            if ($assignPlan['is_success']) {
                $planData = [
                    'plan_id' => $data['plan']['id'],
                    'plan_frequency' => $data['payment_frequency'],
                    'plan_price' => Utility::getPlanActualPrice($data['plan']['id'], $data['payment_frequency'])
                ];
                Utility::referraltransaction($planData, $user);
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
            } else {
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
        } catch (Exception $e) {

            return redirect()->route('plans.index')->with('error', __($e));
        }
    }

    public function invoicePayWithpayfast(Request $request, $slug, $invoice_id)
    {


        $invoice = Invoice::find($invoice_id);
        $user1 = Auth::user();
        $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $pfHost = $payment_setting['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';


        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

        $success = Crypt::encrypt([
            'invoice_id' => $invoice->id,
            'order_id' => $order_id,
            'plan_amount' => $request->amount,
            'slug' => $this->user->currentWorkspace->slug
        ]);

        $data = array(
            // Merchant details
            'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
            'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
            'return_url' => route($client_keyword . 'invoice.payfast', $success),
            'cancel_url' => route($client_keyword . 'invoices.show', [$this->user->currentWorkspace->slug, $invoice->id]),
            'notify_url' => route($client_keyword . 'invoices.show', [$this->user->currentWorkspace->slug, $invoice->id]),
            // Buyer details
            'name_first' => $this->user->name,
            'name_last' => '',
            'email_address' => $this->user->email,
            // Transaction details
            'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
            'amount' => number_format(sprintf('%.2f', $request->amount), 2, '.', ''),
            'item_name' => $order_id,
        );

        $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
        $signature = $this->generateSignature($data, $passphrase);
        $data['signature'] = $signature;

        $url = "https://$pfHost/eng/process";
        $fields_string = http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }

    public function getInvoicePaymentStatus($success)
    {

        $data = Crypt::decrypt($success);
        $invoice = Invoice::find($data['invoice_id']);
        ;
        // $currentWorkspace = Utility::getWorkspaceBySlug($data['slug']);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $data['invoice_id']);


        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice['client_id'])->first();
        }

        $invoice_payment = new InvoicePayment();
        $invoice_payment->order_id = $data['order_id'];
        $invoice_payment->invoice_id = $data['invoice_id'];
        $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
        $invoice_payment->amount = $data['plan_amount'];
        $invoice_payment->payment_type = 'PayFast';
        $invoice_payment->receipt = '';
        $invoice_payment->client_id = $this->user->id;
        $invoice_payment->txn_id = $data['order_id'];
        $invoice_payment->payment_status = 'approved';
        $invoice_payment->save();

        if ($invoice) {
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
                'paid_amount' => $data['plan_amount'],
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

            // return redirect()->back()->with('success', __('Payment added Successfully'));

            if (Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $data['slug'],
                        $invoice->id,
                    ]
                )->with('success', __('Payment added Successfully'));
            } else {
                return redirect()->route('pay.invoice', [$data['slug'], \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
            }

        }
    }
}
