@extends('layouts.admin')

@php
    use App\Models\Utility;
    $setting = Utility::getAdminPaymentSetting();
@endphp

@section('page-title')
    {{ __('Plans') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('plans') }}</li>
@endsection

@section('action-button')
    @if (Auth::user()->type == 'admin')
        <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Add Plan') }}"
            data-toggle="tooltip" title="{{ __('Add Plan') }}" data-url="{{ route('plans.create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection

<style type="text/css">
    .price-card .p-price {
        font-size: 44px !important;
    }
</style>

@section('content')
    <div class="row">
        @foreach ($plans as $key => $plan)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                    style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="form-check form-switch pt-2">
                                @if ($plan->monthly_price > 0 && $plan->annual_price > 0)
                                    <input type="hidden" name="plan_enable" value="off">
                                    <input id="switch-shadow" class="form-check-input" name="plan_enable"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="enable/disable"
                                        data-plan-id="{{ $plan->id }}" onclick="togglePlan(this)" type="checkbox"
                                        {{ isset($plan['is_plan_enable']) && $plan['is_plan_enable'] == 1 ? 'checked="checked"' : '' }}>
                                    <label class="form-check-label" for="switch-shadow"></label>
                                @endif
                            </div>

                            <div class="text-end">
                                <span class="btn btn-sm btn-primary px-2 py-2">
                                    <a href="#" class="" data-url="{{ route('plans.edit', $plan->id) }}"
                                        data-ajax-popup="true" data-title="{{ __('Edit Plan') }}" data-toggle="tooltip"
                                        data-size="lg" title="{{ __('Edit') }}">
                                        <span class=""> <i class="ti ti-pencil text-white"></i></span>

                                    </a>
                                </span>
                                @if ($plan->monthly_price > 0 && $plan->annual_price > 0)
                                    <span class="btn btn-sm btn-danger px-2 py-2">
                                        <a href="#" class="dropdown-item bs-pass-para"
                                            data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                            data-confirm-yes="delete_user_{{ $plan->id }}"><i
                                                class="ti ti-trash"></i></span>
                                    </a>
                                    <form action="{{ route('plans.delete', ['planId' => encrypt($plan->id)]) }}"
                                        method="post" id="delete_user_{{ $plan->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="price-badge bg-primary"> {{ $plan->name }}</span>
                        @if($plan->plan_type == "1")
                        <span class="mb-4 f-w-600 p-price">
                            {{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $plan->weekly_price }}
                            <small class="text-sm">/{{ __('Weekly Price') }}</small>
                        </span><br>
                    @elseif($plan->plan_type == "2")
                        <span class="mb-4 f-w-600 p-price">
                            {{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $plan->monthly_price }}
                            <small class="text-sm">/{{ __('Monthly Price') }}</small>
                        </span><br>
                    @elseif($plan->plan_type == "3")
                        <span class="mb-4 f-w-600 p-price">
                            {{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $plan->annual_price }}
                            <small class="text-sm">/{{ __('Annual Price') }}</small>
                        </span>

                        @else 
                      <div></div>
                    @endif
                    
                        <p class="mb-0">
                            {{ $plan->description }}
                        </p>
                        <ul class="list-unstyled my-5">
                            @if ($plan->id != 1)
                                <li>
                                    <span class="theme-avtar">
                                        <i class="text-primary ti ti-circle-plus"></i></span>
                                    {{ $plan->trial_days < 0 ? __('lifetime') : $plan->trial_days }}
                                    {{ __('Trial Days') }}
                                </li>
                            
                            @endif
                            {{-- <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                {{ $plan->storage_limit < 0 ? __('Unlimited') : $plan->storage_limit }}
                                {{ __('Storage Limit') }}
                            </li> --}}
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
        @endforeach
    </div>
@endsection
@push('scripts')
    <script>
        function togglePlan(plan) {
            var planId = $(plan).data('plan-id');
            var enableValue = $(plan).prop('checked') ? '1' : '0';
            // console.log(planId, enableValue);
            $.ajax({
                type: 'POST',
                url: '{{ route('update.plan.status') }}',
                data: {
                    'plan_id': planId,
                    'enable': enableValue
                },
                success: function(response) {
                    // console.log(response);
                    if (response.success == true) {
                        show_toastr('Success', response.message, 'success');
                    } else {
                        show_toastr('error', response.message, 'error');
                    }
                },

            });
        }

        $(document).ready(function() {
            $(document).on('change', '#is_trial_disable', function() {
                if ($(this).is(':checked')) {
                    $('.ps_div').removeClass('d-none');
                    $('#trial_days').attr("required", true);
                } else {
                    $('.ps_div').addClass('d-none');
                    //  $('#trial_days').val(null);
                    $('#trial_days').removeAttr("required");
                }
            });
        });
    </script>
@endpush
