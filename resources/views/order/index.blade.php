@extends('layouts.admin')

@php
    use App\Models\Utility;
    use App\Models\Order;
    $setting = Utility::getAdminPaymentSetting();
@endphp

@section('page-title')
    {{ __('Orders') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Orders') }}</li>
@endsection

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card table-responsive">
                    <div class="card-body ">
                        <table id="selection-datatable" class="table" width="100%" style="overflow-x: auto">
                            <thead>
                                <tr>
                                    <th>{{ __('Order Id') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Plan Name') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Payment Type') }}</th>
                                    <th>{{ __('Coupon') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Invoice') }}</th>
                                    @if (Auth::user()->type == 'admin')
                                        <th colspan="2">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $user = App\Models\User::find($order->user_id);
                                    @endphp
                                    @php($color = $order->payment_status == 'succeeded' || $order->payment_status == 'approved' || $order->payment_status == 'success' ? 'success' : 'danger')
                                    <tr>
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->user_name }}</td>
                                        <td>{{ $order->plan_name }}</td>
                                        {{-- <td>{{env('CURRENCY_SYMBOL')}}{{number_format($order->price)}}</td> --}}
                                        {{-- <td>{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $order->price }} --}}
                                        <td>{{ (!empty($setting['currency_symbol']) ? $setting['currency_symbol'] : '$') . $order->price }}
                                        <td><i class="fas fa-circle text-{{ $color }}"></i>
                                            {{ __(ucfirst($order->payment_status)) }}</td>
                                        <td>{{ __($order->payment_type) }}</td>
                                        <td>{{ !empty($order->total_coupon_used) ? (!empty($order->total_coupon_used->coupon_detail) ? $order->total_coupon_used->coupon_detail->code : '-') : '-' }}
                                        </td>
                                        <td>{{ App\Models\Utility::dateFormat($order->created_at) }}</td>
                                        <td class="Id sorting_1">
                                            @php($file = \App\Models\Utility::get_file('uploads/payment_receipt/' . $order->receipt))
                                            @if (!empty($order->receipt))
                                                <a href="{{ $file }}" target="_blank">
                                                    <i class="fas fa-print mr-1"></i> {{ __('Invoice') }}
                                                </a>
                                            @endif
                                        </td>
                                        @if (Auth::user()->type == 'admin')
                                            <td>
                                                @if ($order->payment_type == 'Bank Transfer' && $order->payment_status == 'pending')
                                                    <a href="#"
                                                        class="action-btn btn-warning  btn btn-sm d-inline-flex align-items-center"
                                                        data-url="{{ route('bankpays.show', $order->order_id) }}"
                                                        data-toggle="tooltip" title="{{ __('Order Details') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Payment Status') }}">
                                                        <i class="ti ti-caret-right"></i>
                                                    </a>
                                                @endif
                                                <a href="#"
                                                    class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $order->order_id }}"
                                                    data-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['bankpays.destroy', $order->order_id],
                                                    'id' => 'delete-form-' . $order->order_id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                            </td>
                                            <td>
                                                {{--
                                                @if (\App\Models\Order::isLatestOrder($order->user_id) == $order->id && $order->plan_id != 1 && ($order->payment_status == 'succeeded' || $order->payment_status == 'Approved' || $order->payment_status == 'success' || $order->payment_type == 'Bank Transfer') && $order->payment_type !== 'Manually Upgrade By Super Admin')
                                                    <div class="btn bg-primary ms-2">
                                                        <a href="{{ route('order.refund', ['user_id' => $order->user_id, 'payment_frequency' => $order->payment_frequency]) }}"
                                                            title="{{ __('Payment Refund') }}"
                                                            data-title="{{ __('Payment Refund') }}"
                                                            class="d-inline-flex align-items-center text-white">
                                                            {{ __('Refund') }}
                                                        </a>

                                                    </div>
                                                @endif --}}
                                                @foreach ($userOrders as $userOrder)
                                                    @if (
                                                        $user->plan == $order->plan_id &&
                                                            $order->order_id == $userOrder->order_id &&
                                                            $order->is_refund == 0 &&
                                                            $order->payment_type !== 'Manually Upgrade By Super Admin')
                                                        <div class="badge bg-warning rounded p-2 px-3 ms-2">
                                                            <a href="{{ route('order.refund', [$order->id, $order->user_id]) }}"
                                                                class="mx-3 align-items-center" data-bs-toggle="tooltip"
                                                                title="{{ __('Delete') }}"
                                                                data-original-title="{{ __('Delete') }}">
                                                                <span class ="text-white">{{ __('Refund') }}</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
