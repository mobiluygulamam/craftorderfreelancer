<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ReferralSetting;
use App\Models\Utility;
use App\Models\ReferralTransaction;
use App\Models\ReferralTransactionOrder;
use App\Models\User;
use PDO;

class ReferralProgramController extends Controller
{

    public function index()
    {
        if (Auth::user()->type == 'admin') {
            $setting = ReferralSetting::where('created_by', Auth::user()->id)->first();
            $transactions = ReferralTransaction::get();
            $payRequests = ReferralTransactionOrder::where('status', 1)->get();
            $company = User::where('type', 'user')->get();
            return view('referral-program.index', compact('setting', 'transactions', 'payRequests'
        ));
        } else {
            return redirect()->back()->with('error', 'Permission Denied!!!');
        }
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
            'percentage' => 'required',
            'minimum_threshold_amount' => 'required',
            'guideline' => 'required',
        ]);
        if ($request->has('is_enable') && $request->is_enable == 'on') {
            $is_enable = 1;
        } else {
            $is_enable = 0;
        }

        $setting = ReferralSetting::where('created_by', Auth::user()->id)->first();
        if (($request->is_enable == 'on') || ($setting && $setting->is_enable == 1)) {
            if ($setting == null) {
                $setting = new ReferralSetting();
            }
            $setting->percentage = $request->percentage;
            $setting->minimum_threshold_amount = $request->minimum_threshold_amount;
            $setting->is_enable = $is_enable;
            $setting->guideline = $request->guideline;
            $setting->created_by = Auth::user()->id;
            $setting->save();
        } else {
            return redirect()->back()->with('error', 'Please Enable Referal Settings');
        }


        return redirect()->route('referral-programs.index')->with('success', __('Referral Program Setting successfully Updated.'));
    }


    public function companyIndex(Request $request, $slug)
    {
        if (Auth::user()) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $setting = ReferralSetting::where('created_by', 1)->first();

            $objUser = Auth::user();


            $transactions = ReferralTransaction::with('getUserDetails', 'getPlan')->where('referral_code', $objUser->referral_code)->get();

            $transactionsOrder = ReferralTransactionOrder::where('req_user_id', $objUser->id)->get();
            $paidAmount = $transactionsOrder->where('status', 2)->sum('req_amount');

            $paymentRequest = ReferralTransactionOrder::where('status', 1)->where('req_user_id', $objUser->id)->first();

            return view('referral-program.company', compact('setting', 'currentWorkspace', 'transactions', 'transactionsOrder', 'paidAmount', 'paymentRequest'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }


    public function requestedAmountSent($paidAmount)
    {

        $user = Auth::user();

        return view('referral-program.request_amount', compact('user', 'paidAmount'));
    }
    public function requestedAmountStore(Request $request, $id)
    {
        $setting = ReferralSetting::where('created_by', 1)->first();
        $paidCommisonAmount = ReferralTransactionOrder::where('req_user_id', Auth::user()->id)->where('status', 2)->sum('req_amount');
        $totalCommisonAmount = Auth::user()->commission_amount;
        $totalRemaningCommission = $totalCommisonAmount - $paidCommisonAmount;
        if (($request->request_amount > $setting['minimum_threshold_amount'])) {
            if (($request->request_amount <= $totalRemaningCommission)) {
                $getActiveWorkspace = Utility::getWorkspaceBySlug();
                $order = new ReferralTransactionOrder();
                $order->req_amount = $request->request_amount;
                $order->req_user_id = Auth::user()->id;
                $order->status = 1;
                $order->date = date('Y-m-d');
                $order->save();
            } else {
                return redirect()->back()->with('error', 'Please Enter the Valid Amount!!!');
            }
        } else {
            return redirect()->back()->with('error', 'Requested Amount is less than Threshold Amount!!');
        }



        return redirect()->route('referral-program.company', ['slug' => $getActiveWorkspace->slug])->with('success', __('Request Send Successfully.'));
    }

    public function requestCancel($id)
    {
        $getActiveWorkspace = Utility::getWorkspaceBySlug();
        $transaction = ReferralTransactionOrder::where('req_user_id', $id)->orderBy('id', 'desc')->first();
        $transaction->status = 3;
        $transaction->req_user_id = Auth::user()->id;
        $transaction->save();

        return redirect()->route('referral-program.company', ['slug' => $getActiveWorkspace->slug])->with('success', __('Request Cancel Successfully.'));
    }
    public function requestedAmount($id, $status)
    {
        $setting = ReferralSetting::where('created_by', 1)->first();

        $transaction = ReferralTransactionOrder::find($id);


        $paidAmount = ReferralTransactionOrder::where('req_user_id', $transaction->req_user_id)->where('status', 2)->sum('req_amount');
        $user = User::find($transaction->req_user_id);

        $netAmount = $user->commission_amount - $paidAmount;

        if ($transaction->req_amount > $netAmount && $status == 1) {
            $transaction->status = 0;

            $transaction->save();

            return redirect()->route('referral-programs.index')->with('error', __('This request cannot be accepted because it exceeds the commission amount.'));
        } elseif ($transaction->req_amount <= $setting->minimum_threshold_amount && $status == 1) {
            $transaction->status = 0;

            $transaction->save();
            return redirect()->route('referral-programs.index')->with('error', __('This request cannot be accepted because it less than the threshold amount.'));
        } elseif ($status == 0) {
            $transaction->status = 0;

            $transaction->save();

            return redirect()->route('referral-programs.index')->with('error', __('Request Rejected Successfully.'));
        } else {
            $transaction->status = 2;

            $transaction->save();
            return redirect()->route('referral-programs.index')->with('success', __('Request Aceepted Successfully.'));
        }
    }
}
