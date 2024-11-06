@extends('layouts.admin')

@section('page-title')
    {{ __('Plan-Request') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Plan Request') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="selection-datatable" class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>{{ __('NAME') }}</th>
                                    <th>{{ __('PLAN NAME') }}</th>
                                    <th>{{ __('TOTAL USERS') }}</th>
                                    <th>{{ __('TOTAL CLIENTS') }}</th>
                                    <th>{{ __('DURATION') }}</th>
                                    <th>{{ __('DATE') }}</th>
                                    <th>{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($plan_requests->count() > 0)
                                    @foreach ($plan_requests as $prequest)
                                        @php
                                            $plan = \App\Models\Plan::find($prequest->plan_id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="font-style font-weight-bold">{{ $prequest->user->name }}</div>
                                            </td>
                                            <td>
                                                <div class="font-style font-weight-bold">{{ $prequest->plan->name }}</div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $plan->max_users }}</div>
                                                {{-- <div>{{__('Employee')}}</div> --}}
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $plan->max_clients }}</div>
                                                {{-- <div>{{__('Client')}}</div> --}}
                                            </td>
                                            <td>
                                                <div class="font-style font-weight-bold">
                                                    {{ $prequest->duration == 'monthly' ? __('One Month') : __('One Year') }}
                                                </div>
                                            </td>
                                            <td>{{ \App\Models\Utility::getDateFormated($prequest->created_at, true) }}
                                            </td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('response.request', [$prequest->id, 1]) }}"
                                                        data-toggle="tooltip" title="{{ __('Accept') }}"
                                                        class="action-btn btn-success  btn btn-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-check"></i>
                                                    </a>
                                                    <a href="{{ route('response.request', [$prequest->id, 0]) }}"
                                                        data-toggle="tooltip" title="{{ __('Delete') }}"
                                                        class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <th scope="col" colspan="7">
                                            <h6 class="text-center">{{ __('No Manually Plan Request Found.') }}</h6>
                                        </th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
