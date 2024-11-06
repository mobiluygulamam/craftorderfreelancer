@extends('layouts.admin')

@section('page-title')
    {{ __('Referral Program') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Referral Program') }}</li>
@endsection

@php
    $adminSetting = App\Models\Utility::getAdminPaymentSetting();
    $currency = $adminSetting['currency_symbol'] ? $adminSetting['currency_symbol'] : '$';
@endphp

<style>
    .validation-error-msg {
        color: red;
    }

    .disabledCookie {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#transaction" class="list-group-item list-group-item-action border-0 tab-link active"
                                data-tab="transaction">{{ __('Transaction') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#payout-request" class="list-group-item list-group-item-action border-0 tab-link"
                                data-tab="payout-request">{{ __('Payout Request') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#settings" class="list-group-item list-group-item-action border-0 tab-link"
                                data-tab="settings">{{ __('Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    {{-- Transaction --}}
                    <div id="transaction" class="card tab-content">
                        <div class="card-header">
                            <h5>{{ __('Transaction') }}</h5>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table pc-dt-simple" id="transaction">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Company Name') }}</th>
                                            <th>{{ __('Referral Company Name') }}</th>
                                            <th>{{ __('Plan Name') }}</th>
                                            <th>{{ __('Plan Price') }}</th>
                                            <th>{{ __('Commission (%)') }}</th>
                                            <th>{{ __('Commission Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $key => $transaction)
                                            @php
                                                $matchedCName = '';
                                                foreach ($company as $comp) {
                                                    if ($comp['referral_code'] == $transaction->referral_code) {
                                                        $matchedCName = ucwords($comp['name']);
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td> {{ ++$key }} </td>
                                                <td>{{ !empty($matchedCName) ? $matchedCName : '-' }}</td>
                                                <td>{{ !empty($transaction->getUserDetails) ? $transaction->getUserDetails->name : '-' }}
                                                </td>
                                                <td>{{ !empty($transaction->getPlan) ? $transaction->getPlan->name : '-' }}
                                                </td>
                                                <td>{{ $currency . $transaction->plan_price }}</td>
                                                <td>{{ $transaction->commission ? $transaction->commission : '' }}</td>
                                                <td>{{ $currency . ($transaction->plan_price * $transaction->commission) / 100 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- end --}}

                    {{-- Payout Request --}}
                    <div id="payout-request" class="card tab-content d-none">
                        <div class="card-header">
                            <h5>{{ __('Payout Request') }}</h5>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table pc-dt-simple" id="payout-request">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Company Name') }}</th>
                                            <th>{{ __('Requested Date') }}</th>
                                            <th>{{ __('Requested Amount') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payRequests as $key => $transaction)
                                            <tr>
                                                <td> {{ ++$key }} </td>
                                                <td>{{ !empty($transaction->getCompany) ? $transaction->getCompany->name : '-' }}
                                                </td>
                                                <td>{{ $transaction->date }}</td>
                                                <td>{{ $currency . $transaction->req_amount }}</td>
                                                <td>
                                                    <a href="{{ route('amount.request', [$transaction->id, 1]) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="ti ti-check"></i>
                                                    </a>
                                                    <a href="{{ route('amount.request', [$transaction->id, 0]) }}"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="ti ti-x"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- end --}}

                    {{-- setting --}}
                    <div id="settings" class="card tab-content d-none">
                        {{ Form::open(['route' => 'referral-programs.store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                        <div
                            class="card-header flex-column flex-lg-row d-flex align-items-lg-center gap-2 justify-content-between">
                            <h5>{{ __('Settings') }}</h5>
                            <div class="d-flex align-items-center">

                                <div class="form-check form-switch custom-switch-v1"mt-1 onclick="enablecookie()">
                                    <input type="checkbox" class="form-check-input" name="is_enable" id="is_enable"
                                        {{ isset($setting) && $setting->is_enable == '1' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-body cookieDiv {{ isset($setting) && $setting->is_enable == '0' ? 'disabledCookie ' : '' }}">
                            <div class="row">
                                <div class="row ">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('percentage', __('Commission Percentage (%)'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('percentage', isset($setting) ? $setting->percentage : '', ['class' => 'form-control', 'placeholder' => __('Enter Commission Percentage'), 'required' => 'required']) }}

                                            <span class="validation-error-msg">
                                                @error('percentage')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('minimum_threshold_amount', __('Minimum Threshold Amount'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="input-group">
                                                <span class="input-group-prepend"><span
                                                        class="input-group-text">{{ $currency }}</span></span>
                                                {{ Form::number('minimum_threshold_amount', isset($setting) ? $setting->minimum_threshold_amount : '', ['class' => 'form-control', 'placeholder' => __('Enter Minimum Payout'), 'required' => 'required']) }}
                                            </div>
                                            <span class="validation-error-msg">
                                                @error('minimum_threshold_amount')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        {{ Form::label('guideline', __('GuideLines'), ['class' => 'form-label text-dark']) }}<x-required></x-required>
                                        <textarea name="guideline" class="summernote-simple" required>{{ isset($setting) ? $setting->guideline : null }}</textarea>
                                        <span class="validation-error-msg">
                                            @error('guideline')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn-submit btn btn-primary" type="submit">
                                {{ __('Save Changes') }}
                            </button>
                        </div>

                        {{ Form::close() }}
                    </div>
                    {{-- end --}}

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        function enablecookie() {
            const element = $('#is_enable').is(':checked');
            $('.cookieDiv').addClass('disabledCookie');
            if (element == true) {
                $('.cookieDiv').removeClass('disabledCookie');
            } else {
                $('.cookieDiv').addClass('disabledCookie');
            }
        }
    </script>

    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 200,
        })
        $('.tab-link').on('click', function() {
            var tabId = $(this).data('tab');
            $('.tab-content').addClass('d-none');
            $('#' + tabId).removeClass('d-none');

            $('.tab-link').removeClass('active');
            $(this).addClass('active');
        });
    </script>
@endpush
