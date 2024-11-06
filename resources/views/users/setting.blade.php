@extends('layouts.admin')
@php
    $languages = \App\Models\Utility::languages();
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSetting();
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $flag = $setting['color_flag'];
        // $flag = ($colorFlag == 0) ? false : true;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
    }

    $settings = App\Models\Utility::getAdminPaymentSetting();
    if ($color == '' || $color == null) {
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp

@section('page-title', __('Settings'))
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Settings') }}</li>
@endsection
<style type="text/css">
    .row>* {
        flex-shrink: 0;
        /* width: 100%; */
        width: none !important;
        max-width: 100% !important;
        padding-right: calc(var(--bs-gutter-x) * .5);
        padding-left: calc(var(--bs-gutter-x) * .5);
        margin-top: var(--bs-gutter-y);
        /* width: auto; */
    }
</style>
@section('content')

    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#workspace-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Workspace Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#task-stage-settings"
                                class="list-group-item list-group-item-action border-0 ">{{ __('Task Stage Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#bug-stage-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Bug Stage Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            


                     


                            <a href="#email-notification-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                          

                       
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">

                    <div id="workspace-settings" class="">
                        {{ Form::open(['route' => ['workspace.settings.store', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="card ">
                                    <div class="card-header">
                                        <h5>{{ __('Workspace Settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Dark Logo') }}</h5>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="logo-content">
                                                                <img src="@if ($currentWorkspace->logo) {{ asset($logo . $currentWorkspace->logo . '?timestamp=' . strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')) }} @else{{ asset($logo . 'logo-light.png') }} @endif"
                                                                    style="filter: drop-shadow(2px 3px 7px #011c4b);"
                                                                    class="small_logo" id="dark_logo" />
                                                            </div>

                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+110%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="logo" id="logo"
                                                                        data-filename="edit-logo">
                                                                </label>
                                                                <p class="edit-logo"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Light Logo') }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
                                                                <img src="@if ($currentWorkspace->logo_white) {{ asset($logo . $currentWorkspace->logo_white . '?timestamp=' . strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')) }} @else{{ asset($logo . 'logo-dark.png') }} @endif"
                                                                    style="filter: drop-shadow(2px 3px 7px #011c4b);"
                                                                    id="image" class="small_logo" />
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo_white">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+110%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="logo_white" id="logo_white"
                                                                        data-filename="edit-logo_white">
                                                                </label>
                                                                <p class="edit-logo_white"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4 ">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Favicon') }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">


                                                                <img src="@if ($currentWorkspace->favicon) {{ asset($logo . $currentWorkspace->favicon . '?timestamp=' . strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')) }} @else{{ asset($logo . 'favicon.png') }} @endif"
                                                                    id="favicon" class="favicon"
                                                                    style="width:60px !important" />
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="small-favicon">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+100%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="favicon" id="small-favicon"
                                                                        data-filename="edit-favicon">
                                                                </label>
                                                                <p class="edit-favicon"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                                    {{ Form::text('name', $currentWorkspace->name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                                    @error('name')
                                                        <span class="invalid-name" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                @php
                                                    $DEFAULT_LANG = $currentWorkspace->lang
                                                        ? $currentWorkspace->lang
                                                        : 'en';
                                                @endphp
                                                <div class="form-group">
                                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                    <div class="changeLanguage">
                                                        <select name="default_language" id="default_language"
                                                            class="form-control select2">
                                                            {{-- @foreach (\App\Models\Utility::languages() as $lang)
                                                                <option value="{{ $lang }}"
                                                                    @if ($DEFAULT_LANG == $lang) selected @endif>
                                                                    {{ ucfirst( \App\Models\Utility::getlang_fullname($lang)) }}
                                                                </option>
                                                            @endforeach --}}
                                                            @foreach ($languages as $languageCode => $languageFullName)
                                                                <option value="{{ $languageCode }}"
                                                                    @if ($DEFAULT_LANG == $languageCode) selected @endif>
                                                                    {{ $languageFullName }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="small-title mb-4">Theme Customizer</h4>
                                            <div class="col-12">
                                                <div class="pct-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="credit-card" class="me-2"></i>Primary
                                                                color settings
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="color-wrp">
                                                                <div class="theme-color themes-color">
                                                                    <input type="hidden" name="color" id="color_value"
                                                                        value="{{ $color }}">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                                        data-value="theme-1"
                                                                        onclick="check_theme('theme-1')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-1"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }} "
                                                                        data-value="theme-2"
                                                                        onclick="check_theme('theme-2')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-2"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                                        data-value="theme-3"
                                                                        onclick="check_theme('theme-3')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-3"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                                        data-value="theme-4"
                                                                        onclick="check_theme('theme-4')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-4"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                                        data-value="theme-5"
                                                                        onclick="check_theme('theme-5')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-5"
                                                                        style="display: none;">
                                                                    <br>
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                                        data-value="theme-6"
                                                                        onclick="check_theme('theme-6')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-6"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                                        data-value="theme-7"
                                                                        onclick="check_theme('theme-7')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-7"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                                        data-value="theme-8"
                                                                        onclick="check_theme('theme-8')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-8"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                                        data-value="theme-9"
                                                                        onclick="check_theme('theme-9')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-9"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                                        data-value="theme-10"
                                                                        onclick="check_theme('theme-10')"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-10"
                                                                        style="display: none;">
                                                                </div>
                                                                <div class="color-picker-wrp ">
                                                                    <input type="color"
                                                                        value="{{ $color ? $color : '' }}"
                                                                        class="colorPicker {{ isset($flag) && $flag == 'true' ? 'active_color' : '' }}"
                                                                        name="custom_color" id="color-picker">
                                                                    <input type='hidden' name="color_flag"
                                                                        value={{ isset($flag) && $flag == 'true' ? 'true' : 'false' }}>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="layout" class="me-2"></i>Sidebar
                                                                settings
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-theme-bg" name="cust_theme_bg"
                                                                    @if ($cust_theme_bg == 'on') checked @endif />
                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-theme-bg">Transparent layout</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="sun" class=""></i>Layout settings
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-darklayout" name="cust_darklayout"
                                                                    @if ($dark_mode == 'on') checked @endif />

                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-darklayout">Dark Layout</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="col switch-width">
                                                                <div class="form-group ml-2 mr-3 ">
                                                                    <label
                                                                        class="form-label mb-1">{{ __('Enable RTL') }}</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="site_rtl" id="site_rtl"
                                                                            {{ !empty($SITE_RTL) && $SITE_RTL == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="site_rtl"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <input type="submit" value="{{ __('Save Changes') }}"
                                                    class="btn btn-primary">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="task-stage-settings" class="">
                        <div class="">
                            <div class="col-md-12">
                                <div class="card task-stages" data-value="{{ json_encode($stages) }}"
                                    style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ __('Task Stage Settings') }}

                                                </h5>
                                                <small
                                                    class="pt-2">{{ __('System will consider the last stage as a completed/done project or task status.') }}</small>
                                            </div>
                                            <div class="col-auto  text-end">

                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary btn-icon "
                                                    data-toggle="tooltip" title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right ">
                                                            <a data-repeater-delete
                                                                class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="bug-stage-settings" class="tab-pane">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card bug-stages" data-value="{{ json_encode($bugStages) }}"
                                    style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ __('Bug Stage Settings') }}
                                                </h5>
                                                <small
                                                    class="">{{ __('System will consider the last stage as a completed/done project or bug status.') }}</small>
                                            </div>
                                            <div class=" col-auto text-end">
                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                                                    title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('bug.stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right">
                                                            <a data-repeater-delete
                                                                class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

              




                


                    @if (Auth::user()->type == 'user')
                        <div class="" id="google-calender">
                            {{ Form::open(['route' => ['google.calender.settings', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row justify-content-between">
                                                <div class="col-10 ">
                                                    <h5 class="">{{ __('Google Calendar') }}</h5>
                                                </div>
                                                <div class="text-end  col-auto">
                                                    <div class="col switch-width">
                                                        <div class="form-group ml-2 mr-3 ">
                                                            <div class="custom-control custom-switch">

                                                                <input type="checkbox" data-toggle="switchbutton"
                                                                    data-onstyle="primary" class=""
                                                                    name="is_googlecalendar_enabled"
                                                                    id="is_googlecalendar_enabled"
                                                                    {{ isset($currentWorkspace->is_googlecalendar_enabled) && $currentWorkspace->is_googlecalendar_enabled == 'on' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                    {{ Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('google_calender_id', !empty($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id']) }}
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                    {{ Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label']) }}
                                                    <input type="file" class="form-control"
                                                        name="google_calender_json_file" id="google_calender_json_file">
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
                                </div>
                            </div>
                        </div>
                    @endif


                    <div id="email-settings" class="tab-pane">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="">
                                        {{ __('Email Settings') }}
                                    </h5>
                                    <small
                                        class="">{{ __('This SMTP will be used for sending your company-level email. If this field is empty, then SuperAdmin SMTP will be used for sending emails.') }}</small>
                                </div>
                                <div class="card-body p-4">
                                    {{ Form::open(['route' => ['company.email.settings.store', $currentWorkspace->slug], 'method' => 'post']) }}
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_driver', $payment_detail['mail_driver'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver'), 'id' => 'mail_driver']) }}
                                            @error('mail_driver')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_host', $payment_detail['mail_host'], ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                            @error('mail_host')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_port', $payment_detail['mail_port'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                            @error('mail_port')
                                                <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_username', $payment_detail['mail_username'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                            @error('mail_username')
                                                <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_password', $payment_detail['mail_password'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                            @error('mail_password')
                                                <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_encryption', $payment_detail['mail_encryption'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                            @error('mail_encryption')
                                                <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_address', $payment_detail['mail_from_address'], ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                            @error('mail_from_address')
                                                <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_name', $payment_detail['mail_from_name'], ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                            @error('mail_from_name')
                                                <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="col-lg-12 ">
                                        <div class="row">

                                            <div class="text-start text-light col-6">
                                                <a data-size="md" data-url="{{ route('test.email') }}"
                                                    data-title="{{ __('Send Test Mail') }}"
                                                    class="btn  btn-primary send_email">
                                                    {{ __('Send Test Mail') }}
                                                </a>

                                            </div>
                                            <div class="text-end col-6">
                                                <input type="submit" value="{{ __('Save Changes') }}"
                                                    class="btn-submit btn btn-primary">
                                            </div>

                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/custom/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/custom/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/custom/js/colorPick.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on("click", '.send_email', function(e) {
            e.preventDefault();
            var title = $(this).attr('data-title');
            var size = 'md';
            var url = $(this).attr('data-url');
            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');

                $.post(url, {
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val()
                }, function(data) {
                    $('#commonModal .body').html(data);
                });


            }
        });

        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            $("#email_sending").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                beforeSend: function() {
                    $('#test_email .btn-create').attr('disabled', 'disabled');
                },
                success: function(data) {
                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sending").hide();
                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        })
    </script>

    <script>
        function cust_theme_bg() {
            var custthemebg = document.querySelector("#cust-theme-bg");

            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }

        }

        function cust_darklayout() {
            var custdarklayout = document.querySelector("#cust-darklayout");

            if (custdarklayout.checked) {
                // document
                //     .querySelector(".m-header > .b-brand > .logo-lg")
                //     .setAttribute("src", "{{ asset('assets/images/logo.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                // document
                //     .querySelector(".m-header > .b-brand > .logo-lg")
                //     .setAttribute("src", "{{ asset('assets/images/logo-dark.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

        }
    </script>

    <script src="{{ asset('assets/js/pages/wow.min.js') }}"></script>
    <script>
        // Start [ Menu hide/show on scroll ]
        let ost = 0;
        document.addEventListener("scroll", function() {
            let cOst = document.documentElement.scrollTop;
            if (cOst == 0) {
                // document.querySelector(".navbar").classList.add("top-nav-collapse");
            } else if (cOst > ost) {
                document.querySelector(".navbar").classList.add("top-nav-collapse");
                document.querySelector(".navbar").classList.remove("default");
            } else {
                document.querySelector(".navbar").classList.add("default");
                document
                    .querySelector(".navbar")
                    .classList.remove("top-nav-collapse");
            }
            ost = cOst;
        });
        // End [ Menu hide/show on scroll ]
        var wow = new WOW({
            animateClass: "animate__animated", // animation css class (default is animated)
        });
        wow.init();
        // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        //     target: "#navbar-example",
        // });
    </script>

    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('iframe').attr('src', '{{ url($currentWorkspace->slug . '/invoices/preview') }}/' + template + '/' +
                color);
        });

        $(document).ready(function() {

            var $dragAndDrop = $("body .task-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeater = $('.task-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var value = $(".task-stages").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

            var $dragAndDropBug = $("body .bug-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeaterBug = $('.bug-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDropBug.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var valuebug = $(".bug-stages").attr('data-value');
            if (typeof valuebug != 'undefined' && valuebug.length != 0) {
                valuebug = JSON.parse(valuebug);
                $repeaterBug.setList(valuebug);
            }
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
        });
    </script>


    <script>
        $('#logo').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#dark_logo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_white').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });



        $('#small-favicon').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#favicon').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });
    </script>


    <script>
        $(document).on('click', 'input[name="theme_color"]', function() {
            var eleParent = $(this).attr('data-theme');
            $('#themefile').val(eleParent);
            var imgpath = $(this).attr('data-imgpath');
            $('.' + eleParent + '_img').attr('src', imgpath);
        });

        $(document).ready(function() {
            setTimeout(function(e) {
                var checked = $("input[type=radio][name='theme_color']:checked");
                $('#themefile').val(checked.attr('data-theme'));
                $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
            }, 300);
        });

        function check_theme(color_val) {
            $('#theme-color').removeClass().addClass(color_val);
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('input[value="' + color_val + '"]').attr('checked', true);
            $('a[data-value]').removeClass('active_color');
            $('a[data-value="' + color_val + '"]').addClass('active_color');

            // $('.theme-color').prop('checked', false);
            // $('.theme_color').classList.add('active_color');
            // $('input[value="' + color_val + '"]').prop('checked', true);
            // $('#color_value').val(color_val);
        }
    </script>

    <script>
        $(document).ready(function() {
            cust_theme_bg();
            cust_darklayout();


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
        });
    </script>

    <script>
        $('.colorPicker').on('click', function(e) {
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass('custom-color');
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            const input = document.getElementById("color-picker");
            setColor();
            input.addEventListener("input", setColor);

            function setColor() {
                $(':root').css('--color-customColor', input.value);
            }

            $(`input[name='color_flag`).val('true');
        });

        $('.themes-color-change').on('click', function() {

            $(`input[name='color_flag`).val('false');

            var color_val = $(this).data('value');
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass(color_val);
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $('.colorPicker').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);
        });

        $.fn.removeClassRegex = function(regex) {
            return $(this).removeClass(function(index, classes) {
                return classes.split(/\s+/).filter(function(c) {
                    return regex.test(c);
                }).join(' ');
            });
        };
    </script>
    <style>
        .active_color {
            /* background-color: #ffffff !important; */
            border: 2px solid #000 !important;
        }
    </style>


    <script>
        $(document).on("click", ".email-template-checkbox", function() {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: chbox.val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('{{ __('Success') }}', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    }
                },
                error: function(response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    } else {
                        show_toastr('{{ __('Error') }}', response, 'error');
                    }
                }
            })
        });
    </script>
@endpush
