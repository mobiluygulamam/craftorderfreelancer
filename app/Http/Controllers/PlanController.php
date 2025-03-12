<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function planRefund(Request $request, $orderId, $userId)
    {
        $getOrderData = Order::where('id', $orderId)->first();
        $getOrderData->is_refund = 1;
        $getOrderData->save();
        $user = User::find($userId);
        $assignPlan = $user->assignPlan(1);
        return redirect()->back()->with('success', __('We successfully planned a refund and assigned a free plan.'));
    }
    public function index()
    {
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        $paymentSetting = Utility::getAdminPaymentSetting();
        $objUser = \Auth::user();
    
        if ($objUser->type == 'admin' || $currentWorkspace->creater->id == $objUser->id) {
            $query = Order::select([
                'orders.*',
                'users.name as user_name',
            ])->join('users', 'orders.user_id', '=', 'users.id');
    
            if ($objUser->type !== 'admin') {
                $query->where('users.id', $objUser->id);
            }
    
            // Eager load the relationships
            $orders = $query->with('total_coupon_used.coupon_detail')->orderBy('orders.created_at', 'DESC')->get();
            $userOrders = Order::select('*')
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('orders')
                        ->groupBy('user_id');
                })
                ->orderBy('created_at', 'desc')
                ->get();
    
            if ($objUser->type == 'admin') {
                $plans = Plan::get();
                return view('plans.admin', compact('plans', 'currentWorkspace', 'paymentSetting', 'orders', 'userOrders'));
            } elseif ($currentWorkspace->creater->id == $objUser->id) {
                $plans = Plan::where('status', '1')
                    ->where('is_plan_enable', '=', 1)
                    ->where('id', '!=', $objUser->plan)
                    ->get();
                $currentPlan = Plan::where('id', $objUser->plan)->first();
                return view('plans.company', compact('plans', 'currentWorkspace', 'paymentSetting', 'currentPlan', 'orders', 'userOrders'));
            } else {
                return redirect()->route('home');
            }
        } else {
            return redirect()->route('home');
        }
    }
    
//     public function index()
//     {
//         $currentWorkspace = Utility::getWorkspaceBySlug('');
//         $paymentSetting = Utility::getAdminPaymentSetting();
//         if (\Auth::user()->type == 'admin') {
//             $plans = Plan::get();
//             return view('plans.admin', compact('plans', 'currentWorkspace', 'paymentSetting'));
//         } elseif ($currentWorkspace->creater->id == \Auth::user()->id) {
//           $plans = Plan::where('status', '1')
//           ->where('is_plan_enable', '=', 1)
//           ->where('id', '!=', \Auth::user()->plan)
//           ->get();
//             $currentPlan= Plan::where('id', \Auth::user()->plan)->first();
//             return view('plans.company', compact('plans', 'currentWorkspace', 'paymentSetting','currentPlan'));
//         } else {
//             return redirect()->route('home');
//         }
//     }


//     public function Orderview()
//     {
//         $currentWorkspace = Utility::getWorkspaceBySlug('');
//         $objUser = \Auth::user();

//         if ($objUser->type == 'admin' || $currentWorkspace->creater->id == $objUser->id) {
//             $query = Order::select([
//                 'orders.*',
//                 'users.name as user_name',
//             ])->join('users', 'orders.user_id', '=', 'users.id');

//             if ($objUser->type !== 'admin') {
//                 $query->where('users.id', $objUser->id);
//             }

//             // Eager load the relationships
//             $orders = $query->with('total_coupon_used.coupon_detail')->orderBy('orders.created_at', 'DESC')->get();
//             $userOrders = Order::select('*')
//                 ->whereIn('id', function ($query) {
//                     $query->selectRaw('MAX(id)')
//                         ->from('orders')
//                         ->groupBy('user_id');
//                 })
//                 ->orderBy('created_at', 'desc')
//                 ->get();

//             return view('order.index', compact('currentWorkspace', 'orders', 'userOrders'));
//         } else {
//             return redirect()->route('home');
//         }
//     }
    public function managePlanStatus(Request $request)
    {
        $planId = $request->plan_id;
        $planStatus = $request->enable;
        $findPlan = Plan::where('id', $planId)->first();
        if ($findPlan) {
            $assignPlan = User::where('plan', $findPlan->id)->where('type', '!=', 'admin')->get();
            if (count($assignPlan) > 0) {
                $msg = 'The company has subscribed to this plan, so it cannot be disable.';
                return response()->json(['success' => false, 'message' => $msg]);
            } else {
                $findPlan->is_plan_enable = $planStatus;
                $findPlan->save();

                if ($findPlan->is_plan_enable == '0') {
                    $msg = 'Plan Disable Successfully..';
                } else {
                    $msg = 'Plan Enable Successfully..';
                }
                return response()->json(['success' => true, 'message' => $msg]);
            }
        } else {
            return redirect()->back()->with('error', 'Plan Data Not Found!!!');
        }
    }

    public function destroy(Request $request, $planId)
    {
        $planId = decrypt($planId);
        $objUser = Auth::user();
        $planData = Plan::where('id', $planId)->first();
        if ($planData) {
            $assignPlan = User::where('plan', $planData->id)->where('type', '!=', 'admin')->get();
            if (count($assignPlan) > 0) {
                return redirect()->back()->with('error', 'The Company Has Subscribed to this plan, So it can not be deleted..');
            } else {
                $planData->delete();
                return redirect()->back()->with('success', 'Plan Deleted Successfully...');
            }
        } else {
            return redirect()->back()->with('error', 'Plan Data Not Found!!');
        }
    }

    public function create()
    {
        $plan = new Plan();
        return view('plans.create', compact('plan'));
    }


    public function store(Request $request)
    {
        $validation = [];
        $validation['name'] = 'required|unique:plans';
        $validation['plan_type'] = 'required|string|max:255';
        $validation['weekly_price'] = 'required|numeric|min:0';
        $validation['monthly_price'] = 'required|numeric|min:0';
        $validation['annual_price'] = 'required|numeric|min:0';
        // $validation['trial_days']     = 'required|numeric|min:1';
        $validation['storage_limit'] = 'required|numeric';
        $validation['max_workspaces'] = 'required|numeric';
        $validation['max_users'] = 'required|numeric';
        $validation['max_clients'] = 'required|numeric';
        $validation['max_projects'] = 'required|numeric';
        if ($request->image) {
            $validation['image'] = 'required|image';
        }
        $validator = Validator::make(
            $request->all(),
            $validation
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $post = $request->all();
        if ($request->trial_days == null) {
            $post['trial_days'] = 0;
        }
        $post['is_trial_disable'] = $request->is_trial_disable == 'on' ? 1 : 0;



        if ($request->monthly_price > 0 || $request->annual_price > 0|| $request->weekly_price > 0) {
            $paymentSetting = Utility::getAdminPaymentSetting();
            $post['monthly_price'] = $request->monthly_price;
            $post['annual_price'] = $request->annual_price;
            $post['storage_limit'] = $request->storage_limit;
            $post['weekly_price'] = $request->weekly_price;
            $post['storage_limit'] = $request->storage_limit;
        }
        $post['status'] = $request->has('status') ? 1 : 0;

        if ($request->image) {
            $avatarName = 'plan_' . time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('plans', $avatarName);
            $post['image'] = $avatarName;
        }

        if (Plan::create($post)) {
            return redirect()->back()->with('success', __('Plan created Successfully!'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function show(Plan $plan)
    {
        //
    }


    public function edit($planID)
    {
        $plan = Plan::find($planID);
        return view('plans.edit', compact('plan'));
    }


    public function update($planID, Request $request)
    {
        $plan = Plan::find($planID);
        if ($plan) {
            $validation = [];
            $validation['name'] = 'required|unique:plans,name,' . $planID;

            if ($plan->id != 1) {
                $validation['monthly_price'] = 'required|numeric|min:0';
                $validation['annual_price'] = 'required|numeric|min:0';
                $validation['trial_days'] = 'required|numeric';
                $validation['storage_limit'] = 'required|numeric';
            }

            $validation['max_workspaces'] = 'required|numeric';
            $validation['max_users'] = 'required|numeric';
            $validation['max_clients'] = 'required|numeric';
            $validation['max_projects'] = 'required|numeric';
            if ($request->image) {
                $validation['image'] = 'required|image';
            }

            $validator = \Validator::make(
                $request->all(),
                $validation
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $post = $request->all();
            if ($request->trial_days == null) {
                $post['trial_days'] = 0;
            }
            $post['is_trial_disable'] = $request->is_trial_disable == 'on' ? 1 : 0;
            if ($post['is_trial_disable'] == 0) {
                $post['trial_days'] = 0;
            }

            if (($request->monthly_price > 0 || $request->annual_price > 0) && $plan->id != 1) {
                $paymentSetting = Utility::getAdminPaymentSetting();
                $post['monthly_price'] = $request->monthly_price;
                $post['annual_price'] = $request->annual_price;
                $post['storage_limit'] = $request->storage_limit;
            }

            if ($plan->id != 1) {
                $post['status'] = $request->has('status') ? 1 : 0;
            }

            if ($request->image) {
                $avatarName = 'plan_' . time() . '.' . $request->image->getClientOriginalExtension();
                $dir = 'plans/';
                $path = Utility::upload_file($request, 'image', $avatarName, $dir, []);
                if ($path['flag'] == 1) {
                    $image = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $post['image'] = $avatarName;
            }

            if ($plan->update($post)) {
                return redirect()->back()->with('success', __('Plan updated Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong'));
            }

        } else {
            return redirect()->back()->with('error', __('Plan not found'));
        }
    }

    public function userPlan(Request $request)
    {
        $objUser = \Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan = Plan::find($planID);
        if ($plan) {
            if ($plan->monthly_price <= 0) {
                $objUser->assignPlan($plan->id, 'monthly');

                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong'));
            }
        } else {
            return redirect()->back()->with('error', __('Plan not found'));
        }
    }

    public function payment(Request $request, $frequency, $code)
    {
        if (Auth::user()->type != 'admin') {
            $currentWorkspace = Utility::getWorkspaceBySlug('');
            $paymentSetting = Utility::getAdminPaymentSetting();
            if (
                (isset($paymentSetting['is_manual_enabled']) && $paymentSetting['is_manual_enabled'] == 'on') ||
                (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on') ||
                (isset($paymentSetting['is_stripe_enabled']) && $paymentSetting['is_stripe_enabled'] == 'on') ||
                (isset($paymentSetting['is_paypal_enabled']) && $paymentSetting['is_paypal_enabled'] == 'on') ||
                (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on') ||
                (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on') ||
                (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on') ||
                (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on') ||
                (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on') ||
                (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on') ||
                (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on') ||
                (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on') ||
                (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on') ||
                (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on') ||
                (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on') ||
                (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on') ||
                (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on') ||
                (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on') ||
                (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on') ||
                (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on') ||
                (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on') ||
                (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on') ||
                (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on') ||
                (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on')
            ) {
                try {
                    $planID = \Illuminate\Support\Facades\Crypt::decrypt($code);
                    $plan = Plan::find($planID);
                    if ($plan) {
                        $plan->price = ($paymentSetting['currency_symbol'] ? $paymentSetting['currency_symbol'] : '₺') . $plan->{$frequency . '_price'};
                        return view('plans.payment', compact('plan', 'frequency', 'currentWorkspace', 'paymentSetting'));
                    } else {
                        return redirect()->back()->with('error', __('Plan is deleted.'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', __('Something went wrong!'));
                }
            } else {
                return redirect()->back()->with('error', __('The admin has not set the payment method'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function takeAPlanTrial(Request $request, $plan_id)
    {
        $plan = Plan::find($plan_id);
        $user = Auth::user();
        if ($plan && $user->type == 'user' && $user->is_trial_done == 0) {
            $assignPlan = $user->assignPlan($plan->id);
            if ($assignPlan['is_success']) {
                $days = $plan->trial_days == '-1' ? '36500' : $plan->trial_days;
                $user->is_trial_done = 1;
                $user->plan = $plan->id;
                $user->plan_expire_date = Carbon::now()->addDays($days)->isoFormat('YYYY-MM-DD');
                $user->save();
                return redirect()->route('home')->with('success', __('Your trial has been started'));
            } else {
                return redirect()->route('home')->with('error', __('Your trial can not be started'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
