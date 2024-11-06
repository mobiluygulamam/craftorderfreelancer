<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Project;
use Dflydev\DotAccessData\Util;
use Illuminate\Support\Facades\Storage;

class BankTransferController extends Controller
{
    public $email;
    public $is_enabled;
    public $currancy;
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
        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';


    }

    public function bankpayPost(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $price = $plan->{$request->bank_payment_frequency . '_price'};
        $user = Auth::user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        // $check = Order::where('plan_id' , $plan->id)->where('payment_status' , 'pending')->where('user_id', \Auth::user()->id)->first();
        // if($check){
        //     return redirect()->route('plan.index')->with('error', __('You already send Payment request to this plan.'));
        // }

        if ($request->payment_receipt) {
            $request->validate(
                [
                    'payment_receipt' => 'required',
                ]
            );
            $validation = [
                // 'mimes:'.'png',
                'max:' . '20480',
            ];
            $receipt = time() . '_' . 'receipt_image.png';
            $dir = 'uploads/payment_receipt/';
            $path = Utility::upload_file($request, 'payment_receipt', $receipt, $dir, $validation);
            // if($path['flag'] == 1){
            //     $receipt = $path['url'];
            // }else{
            //     return redirect()->back()->with('error', __($path['msg']));
            // }
        }
        if ($plan) {
            if ($request->has('coupon') && $request->coupon != '') {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();

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

            if ($request->coupon) {
                // $price = $price;
                $discount_value = ($price / 100) * $coupons->discount;
                $price = $price - $discount_value;
            }
            $setting = Utility::getAdminPaymentSetting();
            $order = new Order();
            $order->order_id = $orderID;
            $order->name = $user->first_name . '' . $user->last_name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $plan->name;
            $order->plan_id = $plan->id;
            $order->price = $price;
            $order->price_currency = $setting['currency'] ? $setting['currency'] : '';
            $order->txn_id = isset($request->transaction_id) ? $request->transaction_id : '';
            $order->payment_type = __('Bank Transfer');
            $order->payment_frequency = $request->bank_payment_frequency;
            $order->payment_status = __('pending');
            $order->receipt = $receipt;
            $order->user_id = $user->id;
            $order->save();

            return redirect()->route('plans.index')->with('success', __('Plan Sent for approval.'));
        }
    }

    public function show($id)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $order = Order::where('order_id', $id)->first();
        return view('order.orderstatus', compact('order', 'admin_payment_setting'));
    }

    public function destroy($id)
    {
        $setting = Utility::getAdminPaymentSetting();
        $order = Order::where('order_id', $id)->first();

        Storage::disk($setting['storage_setting'])->delete('uploads/payment_receipt/' . $order->receipt);
        $order->delete();
        return redirect()->route('order.index')->with('success', __('Order Deleted Successfully!'));
    }

    public function bankPaymentApproval(Request $request, $order)
    {
        $orders = Order::find($order);
        $user = User::find($orders->user_id);
        if ($request->payment_approval == '1') {
            $assignPlan = $user->assignPlan($orders->plan_id, $orders->payment_frequency);

            $orders->update(
                [
                    'payment_status' => 'succeeded',
                ]
            );

            $planData = [
                'plan_id' => $orders->plan_id,
                'plan_frequency' => $orders->payment_frequency,
                'plan_price' => Utility::getPlanActualPrice($orders->plan_id, $orders->payment_frequency)
            ];
            Utility::referraltransaction($planData, $user);
            return redirect()->route('order.index')->with('success', __('Plan activated Successfully!'));
        } else {
            $orders->update([
                'payment_status' => 'Rejected',
            ]);

            return redirect()->route('order.index')->with('success', __('Plan Rejected'));
        }
    }

    public function invoicePayWithBank($slug, $invoice_id, Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'payment_receipt' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_owner = isset($currentWorkspace) ? $currentWorkspace->owner($currentWorkspace->id) : '';

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice = Invoice::find($invoiceID);
        // $user      = User::find($invoice->created_by);
        if (Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = Client::where('id', $invoice->client_id)->first();
        }
        $this->setPaymentDetail_client($invoiceID);

        // $settings=Utility::settingsById($invoice->created_by);
        if ($invoice) {

            if ($request->payment_receipt) {
                $request->validate(
                    [
                        'payment_receipt' => 'required',
                    ]
                );
                $validation = [
                    // 'mimes:'.'png',
                    'max:' . '20480',
                ];

                // $image_size = $request->file('payment_receipt')->getSize();
                // $result = Utility::updateStorageLimit($workspace_owner->id, $image_size);
                $receipt = time() . '_' . 'receipt_image.png';
                // if($result==1) {
                $dir = 'uploads/invoice_receipt/';
                $path = Utility::upload_file($request, 'payment_receipt', $receipt, $dir, $validation);
                // if($path['flag'] == 1){
                //     $receipt = $path['url'];
                // }else{
                //     return redirect()->back()->with('error', __($path['msg']));
                // }
                // }
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $invoice_payment = new InvoicePayment();
            $invoice_payment->order_id = $orderID;
            $invoice_payment->invoice_id = $invoice->id;
            $invoice_payment->currency = $currentWorkspace->currency_code;
            $invoice_payment->amount = $request->amount;
            $invoice_payment->payment_type = 'Bank Transfer';
            $invoice_payment->receipt = $receipt;
            $invoice_payment->client_id = $user->id;
            $invoice_payment->txn_id = '';
            $invoice_payment->payment_status = 'pending';
            $invoice_payment->save();


            return redirect()->back()->with('success', __('Invoice payment request send successfully.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));

        } else {
            return redirect()->back()->with('danger', __('Invoice Not Found!'));
        }

    }

    public function invoice_status_show($slug, $id)
    {
        $order = InvoicePayment::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $admin_payment_setting = Utility::getPaymentSetting($currentWorkspace->id);

        return view('invoices.invoicestatus', compact('order', 'admin_payment_setting', 'currentWorkspace'));
    }

    public function invoicebankPaymentApproval(Request $request, $id)
    {
        $orders = InvoicePayment::find($id);
        $invoice = Invoice::find($orders->invoice_id);
        $user = User::find($orders->user_id);
        if ($request->payment_approval == '1') {
            $orders->update(
                [
                    'payment_status' => 'succeeded',
                ]
            );

            if (($invoice->getDueAmount()) == 0) {
                $invoice->status = 'paid';
                $invoice->save();
            } else {

                $invoice->status = 'partialy paid';
                $invoice->save();
            }

            return redirect()->back()->with('success', __('Payment Approved Successfully!'));
        } else {
            $orders->update([
                'payment_status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', __('Payment Rejected'));
        }
    }
    public function invoice_payment_destroy($id)
    {

        $setting = Utility::getAdminPaymentSetting();
        $payments = InvoicePayment::find($id);
        if (Storage::disk($setting['storage_setting'])->exists('/uploads/invoice_receipt/' . $payments->receipt)) {
            // $file_path = 'uploads/invoice_receipt/'.$payments->receipt;
            // $result = Utility::changeStorageLimit(\Auth::user()->id, $file_path);
            Storage::disk($setting['storage_setting'])->delete('uploads/invoice_receipt/' . $payments->receipt);
        }
        $payments->delete();

        return redirect()->back()->with('success', __('payment Deleted Successfully!'));
    }

}
