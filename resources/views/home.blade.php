@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@php
    use App\Models\Utility;
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
    $setting = Utility::getAdminPaymentSetting();
@endphp

@section('content')
    <section class="section">
        @if (Auth::user()->type == 'admin')
            <div class="row">

                <div class="col-12">
                    @if (empty($setting['pusher_app_id']) ||
                            empty($setting['pusher_app_key']) ||
                            empty($setting['pusher_app_secret']) ||
                            empty($setting['pusher_app_cluster']))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Pusher Details in Setting Page ') }}<u>
                                <a href="{{ route('settings.index') }}">{{ __(' here') }}</a></u></div>
                    @endif
                    {{-- @if (empty($setting['MAIL_DRIVER']) || empty($setting['MAIL_HOST']) || empty($setting['MAIL_PORT']) || empty($setting['MAIL_USERNAME']) || empty($setting['MAIL_PASSWORD']) || empty($setting['MAIL_PASSWORD']))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif --}}
                    @if (empty($setting['mail_driver']) ||
                            empty($setting['mail_host']) ||
                            empty($setting['mail_port']) ||
                            empty($setting['mail_username']) ||
                            empty($setting['mail_password']) ||
                            empty($setting['mail_encryption']) ||
                            empty($setting['mail_from_address']) ||
                            empty($setting['mail_from_name']))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u>
                                <a href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Paid User') }} : <strong>{{ $totalPaidUsers }}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Users') }}</h6>
                                    <h3 class="mb-0">{{ $totalUsers }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Order Amount') }} :
                                        <strong>{{ (env('CURRENCY_SYMBOL') != '' ? env('CURRENCY_SYMBOL') : '$') . $totalOrderAmount }}</strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Orders') }}</h6>
                                    <h3 class="mb-0">{{ $totalOrders }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body total_plan">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Most purchase plan') }} : <strong>
                                            @if ($mostPlans)
                                                {{ $mostPlans->name }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Plans') }}</h6>
                                    <h3 class="mb-0">{{ $totalPlans }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <h5>{{ __('Recent Orders') }}</h5>
                                </div>
                                <div class=" col-2"><small class="text-end"></small></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="task-area-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($currentWorkspace)
            <div class="row">
                <div class="col-lg-7 col-md-7 ">
                    <div class="row mt-3">
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-primary">
                                        <i class="fas fa-tasks bg-primary text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Total Project') }}</h6>
                                    <h3 class="mb-0">{{ $totalProject }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-tag bg-info text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm "></p>
                                    <h6 class="">{{ __('Total Task') }}</h6>
                                    <h3 class="mb-0">{{ $totalTask }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-bug bg-danger text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Total Bug') }}</h6>
                                    <h3 class="mb-0">{{ $totalBugs }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="fas fa-users bg-success text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Total User') }}</h6>
                                    <h3 class="mb-0">{{ $totalMembers }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-9">
                                    <h5 class="">
                                        {{ __('Tasks') }}
                                    </h5>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <div class="">
                                        <small><b>{{ $completeTask }}</b> {{ __('Tasks completed out of') }}
                                            {{ $totalTask }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0 animated">
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>
                                                    <div class="font-14 my-1 "><a
                                                            href="{{ route($client_keyword . 'projects.task.board', [$currentWorkspace->slug, $task->project_id]) }}"
                                                            class="text-body">{{ $task->title }}</a></div>

                                                    @php($due_date = '<span class="text-' . ($task->due_date < date('D-m-y') ? 'danger' : 'success') . '">' . date('D-m-y', strtotime($task->due_date)) . '</span> ')

                                                    <span class="text-muted font-13">{{ __('Due Date') }} :
                                                        {!! $due_date !!}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted font-13">{{ __('Status') }}</span> <br />
                                                    @if ($task->complete == '1')
                                                        <span
                                                            class="status_badge_dash badge bg-success p-2 px-3 rounded">{{ __($task->status) }}</span>
                                                    @else
                                                        <span
                                                            class="status_badge_dash badge bg-primary p-2 px-3 rounded">{{ __($task->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted font-13">{{ __('Project') }}</span>
                                                    <div class="font-14 mt-1 font-weight-normal">
                                                        {{ $task->project->name }}</div>
                                                </td>
                                                @if ($currentWorkspace->permission == 'Owner' || Auth::user()->getGuard() == 'client')
                                                    <td>
                                                        <span class="text-muted font-13">{{ __('Assigned to') }}</span>
                                                        <div class="font-14 mt-1 font-weight-normal">
                                                            @foreach ($task->taskUsers() as $user)
                                                                <span
                                                                    class="badge p-2 px-2 rounded bg-secondary">{{ isset($tasksUsers[$user]) ? $tasksUsers[$user] : '-' }}</span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if (Auth::user()->getGuard() != 'client')
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Refferals"><i class=""></i></a>
                                </div>

                                <h5>{{ __('Project Status') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div id="projects-chart"></div>
                                    </div>
                                    <div class="col-6 pb-5 px-3">

                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color:#3cb8d9;"></i>
                                                <span class="ms-2 text-sm">{{ __('On Going') }}</span>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color:#545454;"></i>
                                                <span class="ms-2 text-sm">{{ __('On Hold') }}</span>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color: #6095c1; "></i>
                                                <span class="ms-2 text-sm">{{ __('Finished') }}</span>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="row text-center">
                                        {{-- @foreach ($arrProcessPer as $index => $value)
                                            <div class="col-4">
                                                <i class="fas fa-chart {{ $arrProcessClass[$index] }}  h3"></i>
                                                <h6 class="font-weight-bold">
                                                    <span>{{ $value }}%</span>
                                                </h6>
                                                <p class="text-muted">{{ __($arrProcessLabel[$index]) }}</p>
                                            </div>
                                        @endforeach --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-5 col-md-5 ">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>{{ __('Tasks Overview') }}</h5>
                            <div class="text-end"><small class=""></small></div>
                        </div>
                        <div class="card-body">
                            <div id="task-area-chart"></div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-md-5 w-100 ">
                         <div class="card mt-3 ">
                             <div class="card-header">
                                
                                 <div class="text-end"><small class=""></small></div>
                             </div>
                             <div class="card-body">
                            <h5 class="text-center">    {{App\Models\Utility::getRandomMotivationalQuote()}}</h5>
                             </div>
                         </div>

                    @if (Auth::user()->getGuard() == 'client')
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Refferals"><i class=""></i></a>
                                </div>
                                <h5>{{ __('Project Status') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div id="projects-chart"></div>
                                    </div>
                                    <div class="col-6 pb-5 px-3">
                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color:#3cb8d9;"></i>
                                                <span class="ms-2 text-sm">On Going</span>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color: #545454;"></i>
                                                <span class="ms-2 text-sm">On Hold</span>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="f-10 lh-1 fas fa-circle" style="color: #6095c1; "></i>
                                                <span class="ms-2 text-sm">Finished</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        {{-- @foreach ($arrProcessPer as $index => $value)
                                            <div class="col-4">
                                                <i class="fas fa-chart {{ $arrProcessClass[$index] }}  h3"></i>
                                                <h6 class="font-weight-bold">
                                                    <span>{{ $value }}%</span>
                                                </h6>
                                                <p class="text-muted">{{ __($arrProcessLabel[$index]) }}</p>
                                            </div>
                                        @endforeach --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-0 mt-3 text-center text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title mb-0">
                                {{ __('There is no active Workspace. Please create Workspace from right side menu.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection


@push('scripts')
    <script src="{{ asset('assets/custom/js/apexcharts.min.js') }}"></script>
    @if (Auth::user()->type == 'admin')
    @elseif(isset($currentWorkspace) && $currentWorkspace)
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 200,
                        type: 'donut',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                            }
                        }
                    },
                    series: {!! json_encode($arrProcessPer) !!},
                    colors: {!! json_encode($chartData['color']) !!},
                    labels: {!! json_encode($arrProcessLabel) !!},
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    legend: {
                        show: false
                    }
                };
                var chart = new ApexCharts(document.querySelector("#projects-chart"), options);
                chart.render();
            })();
        </script>
    @endif

    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    @if (Auth::user()->type == 'admin')
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 150,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: {!! json_encode($chartData['data']) !!},
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                    },
                    colors: ['#ffa21d', '#FF3A6E'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                        min: 10,
                        max: 70,
                    }
                };
                var chart = new ApexCharts(document.querySelector("#task-area-chart"), options);
                chart.render();
            })();
        </script>
    @elseif(isset($currentWorkspace) && $currentWorkspace)
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 150,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: [
                        @foreach ($chartData['stages'] as $id => $name)
                            {
                                // console.log($id);
                                name: "{{ __($name) }}",
                                data: {!! json_encode($chartData[$id]) !!}
                            },
                        @endforeach
                    ],
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                        title: {
                            text: '{{ __('Days') }}'
                        }
                    },
                    colors: {!! json_encode($chartData['color']) !!},

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                        min: 10,
                        max: 70,
                    },
                    title: {
                        text: '{{ __('Tasks') }}'
                    },
                };
                var chart = new ApexCharts(document.querySelector("#task-area-chart"), options);
                chart.render();
            })();
        </script>

        @if (Auth::user()->getGuard() != 'client')
            <script>
                (function() {
                    var options = {
                        series: [{!! json_encode($storage_limit) !!}],
                        chart: {
                            height: 475,
                            type: 'radialBar',
                            offsetY: -20,
                            parentHeightOffset: 0,
                            sparkline: {
                                enabled: true
                            }
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    height: 260
                                },
                            }
                        }],
                        plotOptions: {
                            radialBar: {
                                startAngle: -90,
                                endAngle: 90,
                                track: {
                                    background: "#e7e7e7",
                                    strokeWidth: '97%',
                                    margin: 5, // margin is in pixels
                                },
                                dataLabels: {
                                    name: {
                                        show: true
                                    },
                                    value: {
                                        offsetY: -60,
                                        fontSize: '20px'
                                    }
                                }
                            }
                        },
                        grid: {
                            padding: {
                                top: -10
                            }
                        },
                        colors: [style.backgroundColor],
                        labels: ['{{ __('Progress') }}'],
                    };
                    var chart = new ApexCharts(document.querySelector("#storage-limit-chart"), options);
                    chart.render();
                })();
            </script>
        @endif
    @endif
@endpush
