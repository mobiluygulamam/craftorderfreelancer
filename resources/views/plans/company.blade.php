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
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 d-flex align-items-center justify-content-end">
            <div class="text-sm-right status-filter">
                <div class="btn-group  nav nav-tabs">
                    <a data-toggle="tab" href="#current-plan"
                        class="btn_tab btn btn-light bg-primary active  text-white">{{ __('active plan') }}</a>
                    <a data-toggle="tab" class="btn_tab btn btn-light bg-primary  text-white"
                        href="#annual-billing">{{ __('other plans') }}</a>


         
                </div>
            </div>
        </div>
    </div>
@endsection

<style type="text/css">
    .price-card .p-price {
        font-size: 67px !important;
    }

    .nav-tabs {
        border-bottom: unset !important;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="pricing-plan">
                <div class="tab-content mt-3">
                    <div id="current-plan" class="tab-pane in active">
                         @if($currentPlan!=null)
                         <div class="row">
                           
                                 @php
                                     $userFrequency = \Auth::user()->userPlanFrequency($currentPlan);
                                     $currentPlanRequest = \Auth::user()->Planrequest();
                                 @endphp
                                 <div class="col-xl-3 col-lg-4 col-md-6">
                                     <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.4s"
                                         style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                                         <div class="card-body">
                                             <span class="price-badge bg-primary">{{ $currentPlan->name }} </span>
                                                 <div class="d-flex flex-row-reverse m-0 p-0 ">
                                                     <span class="d-flex align-items-center ms-2">
                                                         <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                                         <span class="ms-2"> {{ __('Active') }}</span>
                                                     </span>
                                                 </div>
                                           
                                             <span
                                                 class="mb-4 f-w-600 p-price">{{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $currentPlan->monthly_price }}
                                                 <small class="text-sm">/{{ __('Per month') }}</small>
                                             </span>
                                             <p class="mb-0">
                                                 {{ $currentPlan->description }}
                                             </p>
                                             <ul class="list-unstyled my-5">
                                                 <li>
                                                     <span class="theme-avtar">
                                                         <i class=" text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->trial_days < 0 ? __('Unlimited') : $currentPlan->trial_days }}
                                                     {{ __('Trial Days') }}
                                                 </li>
                                             
                                                 {{-- <li>
                                                     <span class="theme-avtar">
                                                         <i class="text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->storage_limit }}
                                                     {{ __('Storage Limit') }}
                                                 </li> --}}
                                                 <li>
                                                     <span class="theme-avtar">
                                                         <i class="text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->max_workspaces < 0 ? __('Unlimited') : $currentPlan->max_workspaces }}
                                                     {{ __('Workspaces') }}
                                                 </li>
                                                 <li>
                                                     <span class="theme-avtar">
                                                         <i class="text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->max_users < 0 ? __('Unlimited') : $currentPlan->max_users }}
                                                     {{ __('Users Per Workspace') }}
                                                 </li>
                                                 <li>
                                                     <span class="theme-avtar">
                                                         <i class="text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->max_clients < 0 ? __('Unlimited') : $currentPlan->max_clients }}
                                                     {{ __('Clients Per Workspace') }}
                                                 </li>
                                                 <li>
                                                     <span class="theme-avtar">
                                                         <i class="text-primary ti ti-circle-plus"></i>
                                                     </span>
                                                     {{ $currentPlan->max_projects < 0 ? __('Unlimited') : $currentPlan->max_projects }}
                                                     {{ __('Projects Per Workspace') }}
                                                 </li>
                                             </ul>
                                             @if (Auth::user()->type != 'admin')
                                             
                                                <div class="d-grid text-center">
                                                   @if (\Auth::user()->plan == $currentPlan->id && Auth::user()->is_trial_done == 1)
                                                       <button
                                                           class="btn mb-3 btn-light d-flex justify-content-center align-items-center btn-primary text-white mx-sm-5">
                                                           {{ __('Trial Expires on ') }} <b>
                                                               {{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}</b>
                                                       </button>
                                                       <div class="row">
                                                           @if ($currentPlan->id != 1 && \Auth::user()->plan != $currentPlan->id)
                                                               <div class="col-8">
                                                                   <button
                                                                       class="btn mb-3 btn-primary d-flex justify-content-center align-items-center">
                                                                       <a href="{{ route('payment', ['monthly', \Illuminate\Support\Facades\Crypt::encrypt($currentPlan->id)]) }}"
                                                                           id="interested_plan_{{ $currentPlan->id }}"
                                                                           class="text-white">
                                                                           <i
                                                                               class="ti ti-shopping-cart px-2 text-white"></i>{{ __('Subscribe') }}
                                                                       </a>
                                                                   </button>
                                                               </div>
                                                               <div class="col-4">
                                                                   @if (\Auth::user()->requested_plan != $currentPlan->id)
                                                                       <ul class="list-unstyled">
                                                                           <li>
                                                                               <span class="btn btn-primary btn-icon m-1">
                                                                                   <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($currentPlan->id), 'monthly']) }}"
                                                                                       class=""
                                                                                       data-title="{{ __('Send Request') }}"
                                                                                       data-toggle="tooltip">
                                                                                       <span class="">
                                                                                           <i
                                                                                               class="ti ti-arrow-forward-up text-white"></i>
                                                                                       </span>
                                                                                   </a>
                                                                               </span>
                                                                           </li>
                                                                       </ul>
                                                                   @else
                                                                       <ul class="list-unstyled">
                                                                           <li>
                                                                               <span class="btn btn-danger btn-icon m-1">
                                                                                   <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                                                       class=""
                                                                                       data-title="{{ __('Cancle Request') }}"
                                                                                       data-toggle="tooltip">
                                                                                       <span class="">
                                                                                           <i
                                                                                               class="ti ti-x text-white "></i>
                                                                                       </span>
                                                                                   </a>
                                                                               </span>
                                                                           </li>
                                                                       </ul>
                                                                   @endif
                                                               </div>
                                                           @endif
                                                       </div>
                                                   @elseif(
                                                       (\Auth::user()->plan == $currentPlan->id &&
                                                           $userFrequency == 'monthly' &&
                                                           (empty(\Auth::user()->plan_expire_date) || date('Y-m-d') < \Auth::user()->plan_expire_date)) ||
                                                           $currentPlan->id == 1)
                                                       <p class="">
                                                           @if (!empty(\Auth::user()->plan_expire_date) && $currentPlan->id != 1)
                                                               {{ __('Plan Expires on ') }}-
                                                               <b>{{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}</b>
                                                           @elseif($currentPlan->id == 1 && \Auth::user()->plan == 1)
                                                               <b>{{ __('lifetime') }}</b>
                                                           @else
                                                               <b>{{ __('') }}</b>
                                                           @endif
                                                       </p>
                                                   @else
                                                       @if (\Auth::user()->is_trial_done == 0 && $currentPlan->id != 1 && $currentPlan->is_trial_disable == 1)
                                                           <button
                                                               class="btn mb-3 btn-light btn-primary  d-flex justify-content-center align-items-center mx-sm-5">
                                                               <a href="{{ route('take.a.plan.trial', $currentPlan->id) }}"
                                                                   class="text-white">
                                                                   <i
                                                                       class="fas fa-cart-plus mr-2"></i>{{ __('Active Free Trial') }}
                                                               </a>
                                                           </button>
                                                       @endif
                                                       <div class="row">
                                                           @if (($currentPlan->id != 1 && \Auth::user()->plan != $currentPlan->id) || $userFrequency != 'monthly')
                                                               <div class="col-8">
                                                                   <button
                                                                       class="btn mb-3 btn-primary d-flex justify-content-center align-items-center">
                                                                       <a href="{{ route('payment', ['monthly', \Illuminate\Support\Facades\Crypt::encrypt($currentPlan->id)]) }}"
                                                                           id="interested_plan_{{ $currentPlan->id }}"
                                                                           class="text-white">
                                                                           <i
                                                                               class="ti ti-shopping-cart px-2 text-white"></i>{{ __('Subscribe') }}
                                                                       </a>
                                                                   </button>
                                                               </div>
                                                               <div class="col-4">
                                                                   @if (\Auth::user()->requested_plan != $currentPlan->id)
                                                                       <ul class="list-unstyled">
                                                                           <li>
                                                                               <span class="btn btn-primary btn-icon m-1">
                                                                                   <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($currentPlan->id), 'monthly']) }}"
                                                                                       class=""
                                                                                       data-title="{{ __('Send Request') }}"
                                                                                       data-toggle="tooltip">
                                                                                       <span class="">
                                                                                           <i
                                                                                               class="ti ti-arrow-forward-up text-white"></i>
                                                                                       </span>
                                                                                   </a>
                                                                               </span>
                                                                           </li>
                                                                       </ul>
                                                                   @elseif(isset($currentPlanRequest) && $currentPlanRequest->duration == 'monthly')
                                                                       <ul class="list-unstyled">
                                                                           <li>
                                                                               <span class="btn btn-danger btn-icon m-1">
                                                                                   <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                                                       class=""
                                                                                       data-title="{{ __('Cancle Request') }}"
                                                                                       data-toggle="tooltip">
                                                                                       <span class="">
                                                                                           <i
                                                                                               class="ti ti-x text-white "></i>
                                                                                       </span>
                                                                                   </a>
                                                                               </span>
                                                                           </li>
                                                                       </ul>
                                                                   @else
                                                                       <ul class="list-unstyled">
                                                                           <li>
                                                                               <span class="btn btn-primary btn-icon m-1">
                                                                                   <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($currentPlan->id), 'monthly']) }}"
                                                                                       class=""
                                                                                       data-title="{{ __('Send Request') }}"
                                                                                       data-toggle="tooltip">
                                                                                       <span class="">
                                                                                           <i
                                                                                               class="ti ti-arrow-forward-up text-white"></i>
                                                                                       </span>
                                                                                   </a>
                                                                               </span>
                                                                           </li>
                                                                       </ul>
                                                                   @endif
                                                               </div>
                                                           @endif
                                                       </div>
                                                   @endif
                                               </div>
                                                @endif
                                           
                                         </div>
                                     </div>
                                 </div>
                            
                         </div>
                         @endif
                     </div>

                    <div id="annual-billing" class="tab-pane">
                        <div class="row">
                            @foreach ($plans as $key => $plan)
                                @php
                                    $userFrequency = \Auth::user()->userPlanFrequency($plan->id);
                                    $planRequest = \Auth::user()->Planrequest();
                                @endphp
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.4s"
                                        style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                                        <div class="card-body">
                                            <span class="price-badge bg-primary">{{ $plan->name }} </span>
                                            @if (\Auth::user()->type == 'user' && \Auth::user()->plan == $plan->id && $userFrequency == 'annual')
                                                <div class="d-flex flex-row-reverse m-0 p-0 ">
                                                    <span class="d-flex align-items-center ms-2">
                                                        <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                                        <span class="ms-2"> {{ __('Active') }}</span>
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <span
                                                class="mb-4 f-w-600 p-price">{{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}{{ $plan->annual_price }}
                                                <small class="text-sm">/{{ __('Per Year') }}</small>
                                            </span>
                                            <p class="mb-0">
                                                {{ $plan->description }}
                                            </p>
                                            <ul class="list-unstyled my-5">
                                             @if ($plan->trial_days )
                                             <li>
                                                  <span class="theme-avtar">
                                                      <i class=" text-primary ti ti-circle-plus"></i></span>
                                                  {{ $plan->trial_days < 0 ? __('Unlimited') : $plan->trial_days }}
                                                  {{ __('Trial Days') }}
                                              </li>
                                             @endif
                                              
                                                @if($plan->enable_chatgpt == 'on')
                                                <li>
                                                    <span class="theme-avtar">
                                                        <i class="text-primary ti ti-circle-plus"></i></span>
                                                    {{ $plan->enable_chatgpt == 'on' ? __('Chatgpt enabled') : __('Chatgpt disabled') }}
                                                </li>
                                                @endif
                                                {{-- <li>
                                                    <span class="theme-avtar">
                                                        <i class="text-primary ti ti-circle-plus"></i></span>
                                                    {{ $plan->storage_limit }}
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
                                            @if (Auth::user()->type != 'admin')
                                                <div class="d-grid text-center">
                                                    @if (\Auth::user()->plan == $plan->id && Auth::user()->is_trial_done == 1)
                                                        <button
                                                            class="btn mb-3 btn-light d-flex justify-content-center align-items-center btn-primary text-white mx-sm-5">
                                                            {{ __('Trial Expires on ') }} <b>
                                                                {{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}</b>
                                                        </button>
                                                        <div class="row">
                                                            @if ($plan->id != 1 && \Auth::user()->plan != $plan->id)
                                                                <div class="col-8">
                                                                    <button
                                                                        class="btn mb-3 btn-primary d-flex justify-content-center align-items-center">
                                                                        <a href="{{ route('payment', ['annual', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                                            id="interested_plan_{{ $plan->id }}"
                                                                            class="text-white">
                                                                            <i
                                                                                class="ti ti-shopping-cart px-2 text-white"></i>{{ __('Subscribe') }}
                                                                        </a>
                                                                    </button>
                                                                </div>
                                                                <div class="col-4">
                                                                    @if (\Auth::user()->requested_plan != $plan->id)
                                                                        <ul class="list-unstyled">
                                                                            <li>
                                                                                <span class="btn btn-primary btn-icon m-1">
                                                                                    <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id), 'annual']) }}"
                                                                                        class=""
                                                                                        data-title="{{ __('Send Request') }}"
                                                                                        data-toggle="tooltip">
                                                                                        <span class="">
                                                                                            <i
                                                                                                class="ti ti-arrow-forward-up text-white"></i>
                                                                                        </span>
                                                                                    </a>
                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    @else
                                                                        <ul class="list-unstyled">
                                                                            <li>
                                                                                <span class="btn btn-danger btn-icon m-1">
                                                                                    <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                                                        class=""
                                                                                        data-title="{{ __('Cancle Request') }}"
                                                                                        data-toggle="tooltip">
                                                                                        <span class=""><i
                                                                                                class="ti ti-x text-white "></i></span>
                                                                                    </a>
                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @elseif(
                                                        (\Auth::user()->plan == $plan->id &&
                                                            $userFrequency == 'annual' &&
                                                            (empty(\Auth::user()->plan_expire_date) || date('Y-m-d') < \Auth::user()->plan_expire_date)) ||
                                                            $plan->id == 1)
                                                        <p class="">
                                                            @if (!empty(\Auth::user()->plan_expire_date) && $plan->id != 1)
                                                                {{ __('Plan Expires on ') }}
                                                                <b>{{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}</b>
                                                            @elseif($plan->id == 1 && \Auth::user()->plan == 1)
                                                                <b>{{ __('lifetime') }}</b>
                                                            @else
                                                                <b>{{ __('') }}</b>
                                                            @endif
                                                        </p>
                                                    @else
                                                        @if (\Auth::user()->is_trial_done == 0 && $plan->id != 1 && $plan->trial_days != 0)
                                                            <button
                                                                class="btn mb-3 btn-light btn-primary  d-flex justify-content-center align-items-center mx-sm-5">
                                                                <a href="{{ route('take.a.plan.trial', $plan->id) }}"
                                                                    class="text-white">
                                                                    <i
                                                                        class="fas fa-cart-plus mr-2"></i>{{ __('Active Free Trial') }}
                                                                </a>
                                                            </button>
                                                        @endif
                                                        <div class="row">
                                                            @if ($plan->id != 1 && (\Auth::user()->plan != $plan->id || $userFrequency != 'annual'))
                                                                <div class="col-8">
                                                                    <button
                                                                        class="btn mb-3 btn-primary d-flex justify-content-center align-items-center">
                                                                        <a href="{{ route('payment', ['annual', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                                            id="interested_plan_{{ $plan->id }}"
                                                                            class="text-white">
                                                                            <i
                                                                                class="ti ti-shopping-cart px-2 text-white"></i>{{ __('Subscribe') }}
                                                                        </a>
                                                                    </button>
                                                                </div>
                                                                <div class="col-4">
                                                                    @if (\Auth::user()->requested_plan != $plan->id)
                                                                        <ul class="list-unstyled">
                                                                            <li>
                                                                                <span class="btn btn-primary btn-icon m-1">
                                                                                    <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id), 'annual']) }}"
                                                                                        class=""
                                                                                        data-title="{{ __('Send Request') }}"
                                                                                        data-toggle="tooltip">
                                                                                        <span class="">
                                                                                            <i
                                                                                                class="ti ti-arrow-forward-up text-white"></i>
                                                                                        </span>
                                                                                    </a>
                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    @elseif(isset($planRequest) && $planRequest->duration == 'annual')
                                                                        <ul class="list-unstyled">
                                                                            <li>
                                                                                <span class="btn btn-danger btn-icon m-1">
                                                                                    <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                                                        class=""
                                                                                        data-title="{{ __('Cancel Request') }}"
                                                                                        data-toggle="tooltip">
                                                                                        <span class="">
                                                                                            <i
                                                                                                class="ti ti-x text-white "></i>
                                                                                        </span>
                                                                                    </a>
                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    @else
                                                                        <ul class="list-unstyled">
                                                                            <li>
                                                                                <span class="btn btn-primary btn-icon m-1">
                                                                                    <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id), 'annual']) }}"
                                                                                        class=""
                                                                                        data-title="{{ __('Send Request') }}"
                                                                                        data-toggle="tooltip">
                                                                                        <span class="">
                                                                                            <i
                                                                                                class="ti ti-arrow-forward-up text-white"></i>
                                                                                        </span>
                                                                                    </a>
                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



 @include('partials.orders')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var tohref = '';
            @if (Auth::user()->is_register_trial == 1)
                tohref = $('#trial_{{ Auth::user()->interested_plan_id }}').attr("href");
            @elseif (Auth::user()->interested_plan_id != 0)
                tohref = $('#interested_plan_{{ Auth::user()->interested_plan_id }}').attr("href");
            @endif

            if (tohref != '') {
                window.location = tohref;
            }
        });
    </script>
@endpush
