@extends('layouts.admin')

@php
    use App\Models\Utility;
    $setting = Utility::getAdminPaymentSetting();
@endphp

@section('page-title')
    {{ __('Plan Payment') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"><a href="{{ route('plans.index') }}"> {{ __('plans') }}</a></li>
    <li class="breadcrumb-item"> {{ __('Plan Payment') }}</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class=" sticky-top" style="top:30px">
                        <div class="list-group list-group-flush card" id="useradd-sidenav">
                            @if (isset($paymentSetting['is_manual_enabled']) && $paymentSetting['is_manual_enabled'] == 'on')
                                <a href="#useradd-15" class="list-group-item list-group-item-action active border-0">
                                    {{ __('Manually') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on')
                                <a href="#useradd-14" class="list-group-item list-group-item-action border-0">
                                    {{ __('Bank Transfer') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (isset($paymentSetting['is_stripe_enabled']) && $paymentSetting['is_stripe_enabled'] == 'on')
                                <a href="#useradd-2"
                                    class="list-group-item list-group-item-action border-0">{{ __('Stripe') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_paypal_enabled']) && $paymentSetting['is_paypal_enabled'] == 'on')
                                <a href="#paypal-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paypal') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
                                <a href="#paystack-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paystack') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                                <a href="#flutterwave-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Flutterwave') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                                <a href="#razorpay-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Razorpay') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                                <a href="#paytm-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paytm') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                                <a href="#mercado-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Mercado') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                                <a href="#mollie-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Mollie') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                                <a href="#skrill-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Skrill') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                                <a href="#coingate-billing"
                                    class="list-group-item list-group-item-action border-0">{{ __('Coingate') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on')
                                <a href="#paymentwall_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paymentwall') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on')
                                <a href="#toyyibpay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Toyyibpay') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif

                            @if (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on')
                                <a href="#payfast_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Payfast') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on')
                                <a href="#iyzipay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Iyzipay') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on')
                                <a href="#sspay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('SSpay') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on')
                                <a href="#paytab_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paytab') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on')
                                <a href="#benefit_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Benefit') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on')
                                <a href="#cashfree_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Cashfree') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on')
                                <a href="#aamarpay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Aamarpay') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on')
                                <a href="#paytr_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Pay TR') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif


                            @if (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on')
                                <a href="#midtrans_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Midtrans') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on')
                                <a href="#xendit_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Xendit') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on')
                                <a href="#yookassa_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Yookassa') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on')
                                <a href="#paiementpro_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Paiementpro') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif


                            @if (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on')
                                <a href="#nepalste_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Nepalste') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on')
                                <a href="#cinetpay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Cinetpay') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on')
                                <a href="#fedapay_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Fedapay') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on')
                                <a href="#payhere_payment"
                                    class="list-group-item list-group-item-action border-0">{{ __('Payhere') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                        </div>

                        <div class="mt-5">
                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                                style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                                <div class="card-body">
                                    <span class="price-badge bg-primary">{{ $plan->name }}</span>
                                    <div class="text-end">
                                        <div class="">
                                        </div>
                                    </div>
                                    <h3 class="mb-4 f-w-600  ">
                                        {{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $plan->monthly_price }}
                                        <small class="text-sm">/{{ __('Monthly Price') }}</small>
                                    </h3>
                                    <p class="mb-0">
                                        @if ($plan->id != 1)
                                            {{ $plan->trial_days < 0 ? __('Unlimited') : $plan->trial_days }}
                                            {{ __('Trial Days') }}
                                        @endif
                                    <ul class="list-unstyled my-2">
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $plan->max_workspaces < 0 ? __('Unlimited') : $plan->max_workspaces }}
                                            {{ __('Workspaces') }}
                                        </li>
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $plan->max_users < 0 ? __('Unlimited') : $plan->max_users }}
                                            {{ __('Users Per Workspace') }}
                                        </li>
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $plan->max_clients < 0 ? __('Unlimited') : $plan->max_clients }}
                                            {{ __('Clients Per Workspace') }}
                                        </li>
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $plan->max_projects < 0 ? __('Unlimited') : $plan->max_projects }}
                                            {{ __('Projects Per Workspace') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">

                    @if (isset($paymentSetting['is_manual_enabled']) && $paymentSetting['is_manual_enabled'] == 'on')
                        <div id="useradd-15" class="card">
                            <div class="card-header">
                                <h5>{{ __('Manually') }}</h5>
                                {{-- <small
                                        class="text-muted">{{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}</small> --}}
                            </div>
                            <div class="card-body">
                                @if ($paymentSetting['is_manual_enabled'] == 'on')
                                    <div class="border p-3 mb-3 rounded stripe-payment-div">
                                        <p>{{ __('Requesting manual payment for the planned amount for the subscription plan.') }}
                                        </p>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card-footer text-end py-0 pe-2 border-0">
                                                <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id), $frequency]) }}"
                                                    class="btn bg-primary text-white">
                                                    {{ __('Send Request') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on')
                        <div id="useradd-14" class="card">
                            <div class="card-header">
                                <h5>{{ __('Bank Transfer') }}</h5>
                                {{-- <small
                                        class="text-muted">{{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}</small> --}}
                            </div>
                            <div class="card-body">
                                @if ($paymentSetting['is_bank_enabled'] == 'on')
                                    <form action="{{ route('bankpay.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="border card p-3">
                                                    <div class="form-check">
                                                        <input type="radio" name="bank_payment_frequency"
                                                            class="form-check-input input-primary payment_frequency"
                                                            data-from="bank" value="monthly"
                                                            data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                            autocomplete="off"
                                                            @if ($frequency == 'monthly') checked="" @endif
                                                            id="">
                                                        <label class="form-check-label d-block" for="">
                                                            <span>
                                                                <span class="h5 d-block"><strong class="float-end">
                                                                        {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="border card p-3">
                                                    <div class="form-check">
                                                        <input type="radio" name="bank_payment_frequency"
                                                            data-from="bank" value="annual"
                                                            data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                            autocomplete="off"
                                                            class="form-check-input input-primary payment_frequency"
                                                            @if ($frequency == 'annual') checked="" @endif
                                                            id="">
                                                        <label class="form-check-label d-block" for="">
                                                            <span>
                                                                <span class="h5 d-block"><strong class="float-end">
                                                                        {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6 form-group">
                                                            <label class="form-label" for="bank_details"
                                                                class="form-label">{{ __('Bank Details : ') }}</label><br>
                                                            {!! isset($paymentSetting['bank_details']) ? $paymentSetting['bank_details'] : '' !!}
                                                            <input type="hidden" name="bank_details" id="bank_details"
                                                                value="{{ isset($paymentSetting['bank_details']) ? $paymentSetting['bank_details'] : '' }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="payment_receipt"
                                                                class="form-label">{{ __('Payment Receipt') }}</label>
                                                            <input type="file" name="payment_receipt"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-10">
                                                    <div class="form-group">
                                                        <label class="form-label">Coupon Code</label>
                                                        <input type="text" id="bank_coupon" name="coupon"
                                                            class="form-control coupon" data-from="bank"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mt-4">
                                                        <a href="#" class="btn  btn-primary apply-coupon"
                                                            data-from="bank">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card-footer text-end py-0 pe-2 border-0">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Checkout') }}(<span
                                                            class="coupon-bank">{{ $plan->price }}</span>)</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_stripe_enabled']) && $paymentSetting['is_stripe_enabled'] == 'on')
                        <div id="useradd-2" class="card">
                            <div class="card-header">
                                <h5>{{ __('Stripe') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('stripe.post') }}" method="post" class=""
                                    id="stripe-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="stripe_payment_frequency"
                                                        class="form-check-input input-primary payment_frequency"
                                                        data-from="stripe" value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif
                                                        id="">
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="stripe_payment_frequency"
                                                        data-from="stripe" value="annual"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off"
                                                        class="form-check-input input-primary payment_frequency"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        id="">
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="border card p-3">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input input-primary"
                                                            name="payment_type" id="one_time_type" value="one-time"
                                                            autocomplete="off" checked="">
                                                        <label class="form-check-label d-block" for="">
                                                            <span>
                                                                <span class="h5 d-block"><strong
                                                                        class="float-end"></strong>
                                                                    {{ __('One Time') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="border card p-3">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input input-primary"
                                                            name="payment_type" id="recurring_type" value="recurring"
                                                            autocomplete="off">
                                                        <label class="form-check-label d-block" for="">
                                                            <span>
                                                                <span class="h5 d-block"><strong
                                                                        class="float-end"></strong>
                                                                    {{ __('Reccuring') }}</span>

                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="stripe_coupon" name="coupon"
                                                    class="form-control coupon" data-from="stripe"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="stripe">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn  btn-primary" type="submit" id="pay_with_stripe">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-stripe">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paypal_enabled']) && $paymentSetting['is_paypal_enabled'] == 'on')
                        <div id="paypal-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paypal') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paypal') }}" method="post"
                                    class="" id="paypal-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency"
                                                        name="paypal_payment_frequency" data-from="paypal"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="paypal_payment_frequency"
                                                        class="form-check-input input-primary payment_frequency"
                                                        data-from="paypal" value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>

                                                <input type="text" id="paypal_coupon" name="coupon"
                                                    class="form-control coupon" data-from="paypal"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paypal">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary" type="submit" id="pay_with_paypal">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paypal">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
                        <div id="paystack-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paystack') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paystack') }}" method="post"
                                    class="" id="paystack-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency"
                                                        name="paystack_payment_frequency" data-from="paystack"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency"
                                                        name="paystack_payment_frequency" data-from="paystack"
                                                        @if ($frequency == 'annual') checked="" @endif value="annual"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paystack_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paystack">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="button"
                                                        id="pay_with_paystack">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paystack">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                        <div id="flutterwave-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Flutterwave') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.flaterwave') }}" method="post"
                                    class="" id="flaterwave-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary flaterwave_frequency payment_frequency"
                                                        name="flaterwave_payment_frequency" data-from="flaterwave"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency flaterwave_frequency"
                                                        name="flaterwave_payment_frequency" data-from="flaterwave"
                                                        value="annual" @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="flaterwave_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="flaterwave">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary" type="button"
                                                        id="pay_with_flaterwave">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-flaterwave">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                        <div id="razorpay-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Razorpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.razorpay') }}" method="post"
                                    class="" id="razorpay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency razorpay_frequency"
                                                        name="razorpay_payment_frequency" data-from="razorpay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary razorpay_frequency payment_frequency"
                                                        name="razorpay_payment_frequency" data-from="razorpay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="razorpay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="razorpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="button"
                                                        id="pay_with_razorpay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-razorpay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                        <div id="paytm-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paytm') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paytm') }}" method="post"
                                    class="" id="paytm-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary paytm_frequency payment_frequency"
                                                        name="paytm_payment_frequency" data-from="paytm" value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency paytm_frequency"
                                                        name="paytm_payment_frequency" data-from="paytm" value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Mobile Number') }}</label>
                                                <input type="text" id="mobile" name="mobile"
                                                    class="form-control mobile" data-from="mobile"
                                                    placeholder="{{ __('Enter Mobile Number') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paytm_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paytm">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="submit" id="pay_with_paytm">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paytm">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                        <div id="mercado-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Mercado') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.mercado') }}" method="post"
                                    class="" id="mercado-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency mercado_frequency"
                                                        name="mercado_payment_frequency" data-from="mercado"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency mercado_frequency"
                                                        name="mercado_payment_frequency" data-from="mercado"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="mercado_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="mercado">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="submit" id="pay_with_paytm">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-mercado">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                        <div id="mollie-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Mollie') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.mollie') }}" method="post"
                                    class="" id="mollie-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary mollie_frequency payment_frequency"
                                                        name="mollie_payment_frequency" data-from="mollie"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency mollie_frequency"
                                                        name="mollie_payment_frequency" data-from="mollie" value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="mollie_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="mollie">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit" id="pay_with_mollie">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-mollie">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                        <div id="skrill-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Skrill') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.skrill') }}" method="post"
                                    class="" id="skrill-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency skrill_frequency"
                                                        name="skrill_payment_frequency" data-from="skrill"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency skrill_frequency"
                                                        name="skrill_payment_frequency" data-from="skrill" value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="skrill_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="skrill">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                @php
                                                    $skrill_data = [
                                                        'transaction_id' => md5(
                                                            date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id',
                                                        ),
                                                        'user_id' => 'user_id',
                                                        'amount' => 'amount',
                                                        'currency' => 'currency',
                                                    ];
                                                    session()->put('skrill_data', $skrill_data);
                                                @endphp

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="submit" id="pay_with_skrill">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-skrill">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                        <div id="coingate-billing" class="card">
                            <div class="card-header">
                                <h5>{{ __('Coingate') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.coingate') }}" method="post"
                                    class="" id="coingate-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency coingate_frequency"
                                                        name="coingate_payment_frequency" data-from="coingate"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency coingate_frequency"
                                                        name="coingate_payment_frequency" data-from="coingate"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="coingate_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="coingate">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_coingate">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-coingate">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on')
                        <div id="paymentwall_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paymentwall') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('paymentwall') }}" method="post" class=""
                                    id="coingate-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary paymentwall_frequency payment_frequency"name="paymentwall_payment_frequency"
                                                        data-from="coingate" value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency paymentwall_frequency"
                                                        name="paymentwall_payment_frequency" data-from="paymentwall"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paymentwall_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paymentwall">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paymentwall">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paymentwall">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on')
                        <div id="toyyibpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Toyyibpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.toyyibpaypayment') }}" method="post"
                                    class="" id="toyyibpay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary toyyibpay_frequency payment_frequency"
                                                        name="toyyibpay_payment_frequency" data-from="toyyibpay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency toyyibpay_frequency"
                                                        name="toyyibpay_payment_frequency" data-from="toyyibpay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="toyyibpay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="toyyibpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_toyyibpay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-toyyibpay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on')
                        @php
                            $pfHost =
                                $paymentSetting['payfast_mode'] == 'sandbox'
                                    ? 'sandbox.payfast.co.za'
                                    : 'www.payfast.co.za';
                        @endphp
                        <div id="payfast_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Payfast') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action={{ route('payfast.payment') }} method="post"
                                    class="require-validation" id="payfast-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payfast_frequency payment_frequency"
                                                        name="payfast_payment_frequency" data-from="payfast"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency payfast_frequency"
                                                        name="payfast_payment_frequency" data-from="payfast"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="payfast_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="payfast">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" id="plan_id" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_payfast">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-payfast">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on')
                        <div id="iyzipay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Iyzipay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.iyzipay') }}" method="post"
                                    class="" id="iyzipay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency"
                                                        name="iyzipay_payment_frequency" data-from="iyzipay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="iyzipay_payment_frequency"
                                                        class="form-check-input input-primary payment_frequency"
                                                        data-from="iyzipay" value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>

                                                <input type="text" id="iyzipay_coupon" name="coupon"
                                                    class="form-control coupon" data-from="iyzipay"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="iyzipay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_iyzipay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-iyzipay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on')
                        <div id="sspay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('SSpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.sspay') }}" method="post"
                                    class="" id="sspay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary sspay_frequency payment_frequency"
                                                        name="sspay_payment_frequency" data-from="sspay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency sspay_frequency"
                                                        name="sspay_payment_frequency" data-from="sspay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_sspay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-sspay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on')
                        <div id="paytab_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paytab') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paytab') }}" method="post"
                                    class="" id="paytab-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary paytab_frequency payment_frequency"
                                                        name="paytab_payment_frequency" data-from="paytab"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency paytab_frequency"
                                                        name="paytab_payment_frequency" data-from="paytab"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paytab_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paytab">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paytab">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paytab">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on')
                        <div id="benefit_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Benefit') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.benefit') }}" method="post"
                                    class="" id="benefit-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary benefit_frequency payment_frequency"
                                                        name="benefit_payment_frequency" data-from="benefit"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency benefit_frequency"
                                                        name="benefit_payment_frequency" data-from="benefit"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="benefit_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="benefit">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_benefit">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-benefit">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on')
                        <div id="cashfree_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('cashfree') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.cashfree') }}" method="post"
                                    class="" id="cashfree-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary cashfree_frequency payment_frequency"
                                                        name="cashfree_payment_frequency" data-from="cashfree"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency cashfree_frequency"
                                                        name="cashfree_payment_frequency" data-from="cashfree"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="cashfree_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="cashfree">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_cashfree">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-cashfree">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on')
                        <div id="aamarpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Aamarpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.aamarpay') }}" method="post"
                                    class="" id="aamarpay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary aamarpay_frequency payment_frequency"
                                                        name="aamarpay_payment_frequency" data-from="aamarpay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency aamarpay_frequency"
                                                        name="aamarpay_payment_frequency" data-from="aamarpay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="aamarpay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="aamarpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_aamarpay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-aamarpay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on')
                        <div id="paytr_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Pay TR') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paytr') }}" method="post"
                                    class="" id="paytr-payment-form">
                                    @csrf
                                    <div class="row">
                                        @if($frequency=='monthly')
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary paytr_frequency payment_frequency"
                                                        name="paytr_payment_frequency" data-from="paytr"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   @else
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency paytr_frequency"
                                                        name="paytr_payment_frequency" data-from="paytr"
                                                        value="annual"
                                                        @if ($frequency == 'annually') checked="true" @endif
                                                        data-price="{{ $plan->annual_price.($setting['currency_symbol'] ? $setting['currency_symbol'] : '₺') }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{$plan->annual_price. ($setting['currency_symbol'] ? $setting['currency_symbol'] : '₺') }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            {{-- <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paytr_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div> --}}
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paytr">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paytr">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paytr">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on')
                        <div id="midtrans_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Midtrans') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.midtrans') }}" method="post"
                                    class="" id="midtrans-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary midtrans_frequency payment_frequency"
                                                        name="midtrans_payment_frequency" data-from="midtrans"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency midtrans_frequency"
                                                        name="midtrans_payment_frequency" data-from="midtrans"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="midtrans_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="midtrans">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_midtrans">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-midtrans">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on')
                        <div id="xendit_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Xendit') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.xendit') }}" method="post"
                                    class="" id="xendit-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary xendit_frequency payment_frequency"
                                                        name="xendit_payment_frequency" data-from="xendit"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency xendit_frequency"
                                                        name="xendit_payment_frequency" data-from="xendit"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="xendit_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="xendit">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_xendit">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-xendit">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on')
                        <div id="yookassa_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Yookassa') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.yookassa') }}" method="post"
                                    class="" id="yookassa-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary yookassa_frequency payment_frequency"
                                                        name="yookassa_payment_frequency" data-from="yookassa"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency yookassa_frequency"
                                                        name="yookassa_payment_frequency" data-from="yookassa"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="yookassa_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="yookassa">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_yookassa">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-yookassa">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif



                    {{-- paiementpro --}}
                    @if (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on')
                        <div id="paiementpro_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paiementpro') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.paiementpro') }}"
                                    method="post" class="" id="paiementpro-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary paiementpro_frequency payment_frequency"
                                                        name="paiementpro_payment_frequency" data-from="paiementpro"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency paiementpro_frequency"
                                                        name="paiementpro_payment_frequency" data-from="paiementpro"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mobile_number', __('Mobile Number'), ['class' => 'form-label']) }}
                                            <input type="text" name="mobile_number"
                                                class="form-control font-style mobile_number">
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('channel', __('Channel'), ['class' => 'form-label']) }}
                                            <input type="text" name="channel"
                                                class="form-control font-style channel">
                                            <small
                                                class="text-danger">{{ __('Example : OMCIV2,MOMO,CARD,FLOOZ ,PAYPAL') }}</small>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="paiementpro_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="paiementpro">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paiementpro">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-paiementpro">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- end --}}

                    {{-- Nepalste --}}
                    @if (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on')
                        <div id="nepalste_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Nepalste') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.nepalste') }}" method="post"
                                    class="" id="nepalste-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary nepalste_frequency payment_frequency"
                                                        name="nepalste_payment_frequency" data-from="nepalste"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency nepalste_frequency"
                                                        name="nepalste_payment_frequency" data-from="nepalste"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="nepalste_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="nepalste">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_nepalste">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-nepalste">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- end --}}

                    {{-- Cinetpay --}}
                    @if (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on')
                        <div id="cinetpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Cinetpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.cinetpay') }}" method="post"
                                    class="" id="cinetpay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary cinetpay_frequency payment_frequency"
                                                        name="cinetpay_payment_frequency" data-from="cinetpay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency cinetpay_frequency"
                                                        name="cinetpay_payment_frequency" data-from="cinetpay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="cinetpay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="cinetpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_cinetpay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-cinetpay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- end --}}


                    {{-- Fedapay --}}
                    @if (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on')
                        <div id="fedapay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Fedapay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.fedapay') }}" method="post"
                                    class="" id="fedapay-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary fedapay_frequency payment_frequency"
                                                        name="fedapay_payment_frequency" data-from="fedapay"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency fedapay_frequency"
                                                        name="fedapay_payment_frequency" data-from="fedapay"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="fedapay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="fedapay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_fedapay">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-fedapay">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- end --}}

                    {{-- Payhere --}}
                    @if (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on')
                        <div id="payhere_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Payhere') }}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('plan.pay.with.payhere') }}" method="post"
                                    class="" id="payhere-payment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payhere_frequency payment_frequency"
                                                        name="payhere_payment_frequency" data-from="payhere"
                                                        value="monthly"
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}"
                                                        autocomplete="off"
                                                        @if ($frequency == 'monthly') checked="" @endif>
                                                    <label class="form-check-label d-block" for="">
                                                        <span>
                                                            <span class="h5 d-block"><strong class="float-end">
                                                                    {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->monthly_price }}</strong>{{ __('Monthly Payments') }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio"
                                                        class="form-check-input input-primary payment_frequency payhere_frequency"
                                                        name="payhere_payment_frequency" data-from="payhere"
                                                        value="annual"
                                                        @if ($frequency == 'annual') checked="" @endif
                                                        data-price="{{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}"
                                                        autocomplete="off">
                                                    <span>
                                                        <span class="h5 d-block"><strong class="float-end">
                                                                {{ ($setting['currency_symbol'] ? $setting['currency_symbol'] : '$') . $plan->annual_price }}</strong>{{ __('Annual Payments') }}</span>
                                                    </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label class="form-label">Coupon Code</label>
                                                <input type="text" id="payhere_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <a href="#" class="btn  btn-primary apply-coupon"
                                                    data-from="payhere">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12">

                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">



                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_payhere">
                                                        {{ __('Checkout') }} (<span
                                                            class="coupon-payhere">{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- end --}}
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.nav-tabs li a').first().trigger('click');
            }, 100);

            var type = window.location.hash.substr(1);
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            if (type != '') {
                $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
            } else {
                $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
            }
        });
        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });
    </script>
    <script src="{{ url('assets/custom/js/jquery.form.js') }}"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>


    @if (
        !empty($paymentSetting['is_stripe_enabled']) &&
            isset($paymentSetting['is_stripe_enabled']) &&
            $paymentSetting['is_stripe_enabled'] == 'on')
        <?php $stripe_session = \Session::get('stripe_session'); ?>

        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            var stripe = Stripe('{{ $paymentSetting['stripe_key'] }}');
            stripe.redirectToCheckout({
                sessionId: '{{ $stripe_session->id }}',
            }).then((result) => {});
        </script>
        <?php endif ?>
    @endif


    <script type="text/javascript">
        $(document).on('change', '.payment_frequency', function(e) {
            var price = $(this).attr('data-price');
            var where = $(this).attr('data-from');
            $('.coupon-' + where).text(price);
            if ($('#' + where + '_coupon').val() != null && $('#' + where + '_coupon').val() != '') {
                applyCoupon($('#' + where + '_coupon').val(), where);
            }

        });

        // Apply Coupon test1
        $(document).on('click', '.apply-coupon', function(e) {
            e.preventDefault();
            var ele = $(this);
            var coupon = $('#' + ele.attr('data-from') + '_coupon').val();
            applyCoupon(coupon, ele.attr('data-from'));
        });

        function applyCoupon(coupon_code, where) {
            if (coupon_code != null && coupon_code != '') {
                $.ajax({
                    url: '{{ route('apply.coupon') }}',
                    datType: 'json',
                    data: {
                        plan_id: '{{ $plan->id }}',
                        coupon: coupon_code,
                        frequency: $('input[name="' + where + '_payment_frequency"]:checked').val()
                    },
                    success: function(data) {
                        if (data.is_success) {
                            $('.coupon-' + where).text(data.final_price);

                        } else {
                            $('.final-price').text(data.final_price);
                            show_toastr('Error', data.message, 'error');
                        }
                    }
                })
            } else {
                show_toastr('Error', '{{ __('Invalid Coupon Code.') }}', 'error');
            }
        }




        @if (
            !empty($paymentSetting['is_paystack_enabled']) &&
                isset($paymentSetting['is_paystack_enabled']) &&
                $paymentSetting['is_paystack_enabled'] == 'on')

            // Paystack Payment
            $(document).on("click", "#pay_with_paystack", function() {
                $('#paystack-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;

                        var paystack_callback = "{{ url('/plan/paystack') }}";
                        var order_id = '{{ time() }}';
                        var handler = PaystackPop.setup({
                            key: '{{ $paymentSetting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                // console.log(response.reference, order_id);
                                window.location.href = paystack_callback + '/' + response
                                    .reference + '/' + '{{ encrypt($plan->id) }}' +
                                    '?coupon_id=' + coupon_id + '&payment_frequency=' + res
                                    .payment_frequency
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (
            !empty($paymentSetting['is_flutterwave_enabled']) &&
                isset($paymentSetting['is_flutterwave_enabled']) &&
                $paymentSetting['is_flutterwave_enabled'] == 'on')

            // Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function() {
                $('#flaterwave-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;

                        var API_publicKey = '{{ $paymentSetting['flutterwave_public_key'] }}';
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/plan/flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{ Auth::user()->email }}',
                            amount: res.total_price,
                            currency: res.currency,
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' +
                                {{ date('Y-m-d') }},
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {
                                    window.location.href = flutter_callback + '/' + txref +
                                        '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                        coupon_id + '&payment_frequency=' + res
                                        .payment_frequency;
                                } else {
                                    // redirect to a failure page.
                                }
                                x
                                    .close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (
            !empty($paymentSetting['is_razorpay_enabled']) &&
                isset($paymentSetting['is_razorpay_enabled']) &&
                $paymentSetting['is_razorpay_enabled'] == 'on')
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var razorPay_callback = '{{ url('/plan/razorpay') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "{{ $paymentSetting['razorpay_public_key'] }}", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": res.currency,
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + response
                                    .razorpay_payment_id + '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                    coupon_id + '&payment_frequency=' + res.payment_frequency;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {
                        show_toastr('Success', res.msg, 'success');
                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif
    </script>
    {{-- <script>
        @if ($paymentSetting['is_payfast_enabled'] == 'on' && !empty($paymentSetting['payfast_merchant_id']) && !empty($paymentSetting['payfast_merchant_key']))
        $(document).ready(function() {
            get_payfast_status(amount = 0,coupon = null);
        })

        function get_payfast_status(amount,coupon){
            var plan_id = $('#plan_id').val();
            var payment_frequency = $('input[name="payfast_payment_frequency"]:checked').val();
            $.ajax({
                url: '{{ route('payfast.payment') }}',
                method: 'POST',
                data : {
                    'plan_id' : plan_id,
                    'coupon_amount' : amount,
                    'coupon_code' : coupon,
                    'payment_frequency' : payment_frequency
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {

                    if (data.success == true) {
                        $('#get-payfast-inputs').append(data.inputs);
                        if(data.iszero <= 0){
                            document.getElementById("payfast-payment-form").action = "{{ route('payfast.payment.success',$plan->id) }}";
                        }

                    }else{
                        show_toastr('Error', data.inputs, 'error')
                    }
                }
            });
        }
        @endif
    </script> --}}

    <script>
        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });

        var type = window.location.hash.substr(1);
        $('.list-group-item').removeClass('active');
        $('.list-group-item').removeClass('text-primary');
        if (type != '') {
            $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
        } else {
            $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
        }




        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
