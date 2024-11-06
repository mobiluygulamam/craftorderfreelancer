@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection
@php
    $setting = App\Models\Utility::getAdminPaymentSetting();
    $flag = $setting['color_flag'];
    $languages = \App\Models\Utility::languages();
    if ($setting['color']) {
        $color = $setting['color'];
    }
@endphp

@php
    $logo = \App\Models\Utility::get_file('logo/');
    $meta_images = \App\Models\Utility::get_file('uploads/logo/');
    $file_type = config('files_types');

    $local_storage_validation = $setting['local_storage_validation'];
    $local_storage_validations = explode(',', $local_storage_validation);

    $s3_storage_validation = $setting['s3_storage_validation'];
    $s3_storage_validations = explode(',', $s3_storage_validation);

    $wasabi_storage_validation = $setting['wasabi_storage_validation'];
    $wasabi_storage_validations = explode(',', $wasabi_storage_validation);

    $google_recaptcha_version = ['v2-checkbox' => __('v2'), 'v3' => __('v3')];
@endphp

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Settings') }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#brand-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-settings"
                                class="list-group-item list-group-item-action border-0 ">{{ __('Email Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#payment-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#pusher-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Pusher Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#recaptcha-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('ReCaptcha Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#storage-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Storage Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#seo"
                                class="list-group-item list-group-item-action border-0">{{ __('SEO Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#cache"
                                class="list-group-item list-group-item-action border-0">{{ __('Cache settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#cookie-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Cookie Settings ') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#chat-gpt-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('ChatGPT Settings ') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="brand-settings" class="">
                        {{ Form::open(['route' => 'settings.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="card ">
                                    <div class="card-header">
                                        <h5>{{ __('Brand Settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-2">
                                            <div class="col-sm-4">
                                                <div class="card ">
                                                    <div class="card-header">
                                                        <h5>{{ __('Dark Logo') }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="logo-content">
                                                            <img id="dark_logo"
                                                                style="filter: drop-shadow(2px 3px 7px #011c4b);"
                                                                alt="yourimage"
                                                                src="{{ asset($logo . 'logo-light.png' . '?' . time()) }}"
                                                                class="small_logo">
                                                        </div>
                                                        <div class="choose-file mt-5 ">
                                                            <label for="logo_blue">
                                                                <div class="bg-primary "
                                                                    style="cursor: pointer;transform: translateY(+110%);">
                                                                    <i
                                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file"
                                                                    class=" form-control choose_file_custom"
                                                                    name="logo_blue" id="logo_blue "
                                                                    data-filename="edit-logo_blue"
                                                                    onchange="document.getElementById('dark_logo').src = window.URL.createObjectURL(this.files[0])">
                                                            </label>
                                                            <p class="edit-logo_blue"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="card ">
                                                    <div class="card-header">
                                                        <h5>{{ __('Light Logo') }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="logo-content">
                                                            <img id="image" alt="yourimage"
                                                                style="filter: drop-shadow(2px 3px 7px #011c4b);"
                                                                src="{{ asset($logo . 'logo-dark.png' . '?' . time()) }}"
                                                                class="small_logo">
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
                                                                    name="logo_white" id="logo_white "
                                                                    data-filename="edit-logo_white"
                                                                    onchange="document.getElementById('image').src = window.URL.createObjectURL(this.files[0])">
                                                            </label>
                                                            <p class="edit-logo_white"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="card ">
                                                    <div class="card-header">
                                                        <h5>{{ __('Favicon') }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="logo-content">
                                                            <img src="{{ asset($logo . 'favicon.png' . '?' . time()) }}"
                                                                class="small_logo" id="favicon"
                                                                style="width: 60px !important;" />
                                                        </div>
                                                        <div class="choose-file mt-5 ">
                                                            <label for="small-favicon">
                                                                <div class=" bg-primary"
                                                                    style="cursor: pointer;transform: translateY(+110%);">
                                                                    <i
                                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file"
                                                                    class="form-control choose_file_custom" name="favicon"
                                                                    id="small-favicon" data-filename="edit-favicon"
                                                                    onchange="document.getElementById('favicon').src = window.URL.createObjectURL(this.files[0])">
                                                            </label>
                                                            <p class="edit-favicon"></p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    {{ Form::label('app_name', __('App Name'), ['class' => 'form-label']) }}
                                                    {{-- {{ Form::text('app_name', env('APP_NAME'), ['class' => 'form-control', 'placeholder' => __('App Name')]) }} --}}
                                                    {{ Form::text('app_name', !empty($setting['app_name']) ? $setting['app_name'] : '', ['class' => 'form-control', 'placeholder' => __('App Name')]) }}
                                                    @error('app_name')
                                                        <span class="invalid-app_name" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-label']) }}
                                                    {{ Form::text('footer_text', !empty($setting['footer_text']) ? $setting['footer_text'] : '', ['class' => 'form-control', 'placeholder' => __('Footer Text')]) }}
                                                    @error('footer_text')
                                                        <span class="invalid-footer_text" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                @php
                                                    $DEFAULT_LANG = $setting['default_admin_lang']
                                                        ? $setting['default_admin_lang']
                                                        : 'en';
                                                @endphp
                                                <div class="form-group">
                                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                    <div class="changeLanguage">
                                                        <select name="default_language" id="default_language"
                                                            class="form-control">
                                                            {{-- @foreach (\App\Models\Utility::languages() as $lang)
                                                        <option value="{{ $lang }}" @if ($DEFAULT_LANG == $lang) selected @endif>
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
                                            <div class="row">
                                                <div class="col-sm-3 ">
                                                    <div class="col switch-width">
                                                        <div class="form-group ml-2 mr-3">
                                                            <label
                                                                class="form-label mb-1">{{ __('Enable Landing Page') }}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" data-toggle="switchbutton"
                                                                    data-onstyle="primary" class=""
                                                                    name="display_landing" id="display_landing"
                                                                    {{ !empty($setting['display_landing']) && $setting['display_landing'] == 'on' ? 'checked="checked"' : '' }}>
                                                                <label class="custom-control-label mb-1"
                                                                    for="status"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="col switch-width">
                                                        <div class="form-group ml-2 mr-3 ">
                                                            <label class="form-label mb-1">{{ __('Enable RTL') }}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" data-toggle="switchbutton"
                                                                    data-onstyle="primary" class="" name="site_rtl"
                                                                    id="site_rtl"
                                                                    {{ !empty($setting['site_rtl']) && $setting['site_rtl'] == 'on' ? 'checked="checked"' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="site_rtl"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="col switch-width">
                                                        <div class="form-group ml-2 mr-3 ">
                                                            <label
                                                                class="form-label mb-1">{{ __('Enable Sign-Up Page') }}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" data-toggle="switchbutton"
                                                                    data-onstyle="primary" class=""
                                                                    name="SIGNUP_BUTTON" id="SIGNUP_BUTTON"
                                                                    {{ !empty($setting['signup_button']) && $setting['signup_button'] == 'on' ? 'checked="checked"' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="SIGNUP_BUTTON"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="col switch-width">
                                                        <div class="form-group ml-2 mr-3 ">
                                                            <label
                                                                class="form-label mb-1">{{ __('Enable Email Verification') }}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" data-toggle="switchbutton"
                                                                    data-onstyle="primary" class=""
                                                                    name="email_verification" id="email_verification"
                                                                    {{ isset($payment_detail['email_verification']) && $payment_detail['email_verification'] == 'on' ? 'checked' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="email_verification"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h4 class="small-title mb-4">{{ __('Theme Customizer') }}</h4>
                                            <div class="col-12">
                                                <div class="pct-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="credit-card"
                                                                    class="me-2"></i>{{ __('Primary color settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="color-wrp">
                                                                <div class="theme-color themes-color">
                                                                    <input type="hidden" name="color" id="color_value"
                                                                        value="{{ $color }}">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                                        data-value="theme-1"
                                                                        onclick="check_theme('theme-1', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-1"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }} "
                                                                        data-value="theme-2"
                                                                        onclick="check_theme('theme-2', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-2"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                                        data-value="theme-3"
                                                                        onclick="check_theme('theme-3', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-3"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                                        data-value="theme-4"
                                                                        onclick="check_theme('theme-4', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-4"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                                        data-value="theme-5"
                                                                        onclick="check_theme('theme-5', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-5"
                                                                        style="display: none;">
                                                                    <br>
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                                        data-value="theme-6"
                                                                        onclick="check_theme('theme-6', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-6"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                                        data-value="theme-7"
                                                                        onclick="check_theme('theme-7', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-7"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                                        data-value="theme-8"
                                                                        onclick="check_theme('theme-8', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-8"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                                        data-value="theme-9"
                                                                        onclick="check_theme('theme-9', this)"></a>
                                                                    <input type="radio" class="theme_color"
                                                                        name="color" value="theme-9"
                                                                        style="display: none;">
                                                                    <a href="#!"
                                                                        class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                                        data-value="theme-10"
                                                                        onclick="check_theme('theme-10', this)"></a>
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
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="layout"
                                                                    class="me-2"></i>{{ __('Sidebar settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-theme-bg" name="cust_theme_bg"
                                                                    @if ($setting['cust_theme_bg'] == 'on') checked @endif />

                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="sun"
                                                                    class=""></i>{{ __('Layout settings') }}
                                                            </h6>
                                                            <hr class=" my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-darklayout" name="cust_darklayout"
                                                                    @if ($setting['cust_darklayout'] == 'on') checked @endif />

                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
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

                    <div id="email-settings" class="tab-pane">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="">
                                        {{ __('Email Settings') }}
                                    </h5>
                                    <small
                                        class="d-block mt-2">{{ __('This SMTP will be used for system-level email sending. Additionally, if a company user does not set their SMTP, then this SMTP will be used for sending emails.') }}</small>
                                </div>
                                <div class="card-body p-4">
                                    {{ Form::open(['route' => 'email.settings.store', 'method' => 'post']) }}
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_driver', !empty($setting['mail_driver']) ? $setting['mail_driver'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver'), 'id' => 'mail_driver']) }}
                                            @error('mail_driver')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_host', !empty($setting['mail_host']) ? $setting['mail_host'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                            @error('mail_host')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_port', !empty($setting['mail_port']) ? $setting['mail_port'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                            @error('mail_port')
                                                <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_username', !empty($setting['mail_username']) ? $setting['mail_username'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                            @error('mail_username')
                                                <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_password', !empty($setting['mail_password']) ? $setting['mail_password'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                            @error('mail_password')
                                                <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_encryption', !empty($setting['mail_encryption']) ? $setting['mail_encryption'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                            @error('mail_encryption')
                                                <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_address', !empty($setting['mail_from_address']) ? $setting['mail_from_address'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                            @error('mail_from_address')
                                                <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_name', !empty($setting['mail_from_name']) ? $setting['mail_from_name'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                            @error('mail_from_name')
                                                <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 ">
                                        <div class="row">
                                            <div class="text-start col-6">
                                                <a href="#" data-size="md" data-url="{{ route('test.email') }}"
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

                    <div id="payment-settings" class="faq">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Payment Settings') }}
                                        </h5>
                                        <small
                                            class="d-block mt-2">{{ __('These details will be used to collect subscription plan payments.Each subscription plan will have a payment button based on the below configuration.') }}</small>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post" action="{{ route('payment.settings.store') }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="form-group col-md-6">
                                                    {{ Form::label('currency', __('Currency *'), ['class' => 'form-label']) }}
                                                    {{-- {{ Form::text('currency', env('CURRENCY'), ['class' => 'form-control font-style', 'required', 'placeholder' => __('Enter Currency')]) }} --}}
                                                    {{ Form::text('currency', !empty($setting['currency']) ? $setting['currency'] : '', ['class' => 'form-control font-style', 'required', 'placeholder' => __('Enter Currency')]) }}
                                                    <small>
                                                        {{ __('Note: Add currency code as per three-letter ISO code.') }}<br>
                                                        <a href="https://stripe.com/docs/currencies"
                                                            target="_blank">{{ __('You can find out how to do that here.') }}</a></small>
                                                    <br>
                                                    @error('currency')
                                                        <span class="invalid-currency" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {{ Form::label('currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                                                    {{ Form::text('currency_symbol', !empty($setting['currency_symbol']) ? $setting['currency_symbol'] : '', ['class' => 'form-control', 'required', 'placeholder' => __('Enter Currency Symbol')]) }}
                                                    @error('currency_symbol')
                                                        <span class="invalid-currency_symbol" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="accordion accordion-flush setting-accordion"
                                                        id="payment-gateways">

                                                    

                                                        <div id="" class="accordion-item card">
                                                            <!-- IyziPay -->

                                                            <h2 class="accordion-header" id="iyzipay">
                                                                <button class="accordion-button collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseiyzipay"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseiyzipay">
                                                                    <span class="d-flex align-items-center">
                                                                        {{ __('Iyzipay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_iyzipay_enabled"
                                                                                id="is_iyzipay_enabled"
                                                                                {{ isset($payment_detail['is_iyzipay_enabled']) && $payment_detail['is_iyzipay_enabled'] == 'on' ? 'checked' : '' }}><label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_iyzipay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseiyzipay"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="paypal"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2 form-group">
                                                                            <label class="pb-2"
                                                                                for="iyzipay_mode">{{ __('Iyzipay Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "
                                                                                            name="iyzipay_mode"
                                                                                            value="sandbox"
                                                                                            {{ !isset($payment_detail['iyzipay_mode']) || empty($payment_detail['iyzipay_mode']) || $payment_detail['iyzipay_mode'] == 'sandbox' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "
                                                                                            name="iyzipay_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['iyzipay_mode']) && $payment_detail['iyzipay_mode'] == 'live' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('iyzipay_api_key', __('Api Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('iyzipay_api_key', isset($payment_detail['iyzipay_api_key']) ? $payment_detail['iyzipay_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Api Key')]) }}

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('iyzipay_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('iyzipay_secret_key', isset($payment_detail['iyzipay_secret_key']) ? $payment_detail['iyzipay_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                          <!-- PayTr -->
                                                          <div id="" class="accordion-item card">
                                                            <h2 class="accordion-header" id="headingTwentyfour">
                                                                <button class="accordion-button collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseTwentyfive"
                                                                    aria-expanded="true"
                                                                    aria-controls="collapseTwentyfive">
                                                                    <span class="d-flex align-items-center">
                                                                        {{ __('PayTR') }}
                                                                    </span>

                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_paytr_enabled" value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_paytr_enabled"
                                                                                id="is_paytr_enabled"
                                                                                {{ isset($payment_detail['is_paytr_enabled']) && $payment_detail['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paytr_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseTwentyfive"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="headingTwentyfour"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row pt-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_id', isset($payment_detail['paytr_merchant_id']) ? $payment_detail['paytr_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_id'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_id') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_key', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_key', isset($payment_detail['paytr_merchant_key']) ? $payment_detail['paytr_merchant_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_salt', __('Merchant Salt'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_salt', isset($payment_detail['paytr_merchant_salt']) ? $payment_detail['paytr_merchant_salt'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Salt')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_salt'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_salt') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>




                                          

                                                       

                                                    
                                             
                                                
                                                    </div>

                                                    <div class="accordion accordion-flush setting-accordion">
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end py-3">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pusher-settings" class="card">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('pusher.settings.store') }}"
                                accept-charset="UTF-8">
                                @csrf
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="">{{ __('Pusher Settings') }}</h5>
                                        </div>
                                        <div class=" col-6 text-end">
                                            <div class="col switch-width">
                                                <div class="form-group ml-2 mr-3 ">
                                                    <div class="custom-control custom-switch">
                                                        <label class="custom-control-label form-control-label px-2"
                                                            for="enable_chat ">{{ __('Enable Chat') }}</label>
                                                        <input type="hidden" name="enable_chat" value="off">
                                                        <input type="checkbox" data-toggle="switchbutton"
                                                            data-onstyle="primary" class="" name="enable_chat"
                                                            id="enable_chat"
                                                            {{ !empty($setting['enable_chat']) && $setting['enable_chat'] == 'on' ? 'checked="checked"' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="pusher_app_id"
                                                class="form-label">{{ __('Pusher App Id') }}</label>
                                            <input class="form-control" placeholder="Enter Pusher App Id"
                                                name="pusher_app_id" type="text"
                                                value="{{ !empty($setting['pusher_app_id']) ? $setting['pusher_app_id'] : '' }}"
                                                id="pusher_app_id" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="pusher_app_key"
                                                class="form-label">{{ __('Pusher App Key') }}</label>
                                            <input class="form-control " placeholder="Enter Pusher App Key"
                                                name="pusher_app_key" type="text"
                                                value="{{ !empty($setting['pusher_app_key']) ? $setting['pusher_app_key'] : '' }}"
                                                id="pusher_app_key" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="pusher_app_secret"
                                                class="form-label">{{ __('Pusher App Secret') }}</label>
                                            <input class="form-control " placeholder="Enter Pusher App Secret"
                                                name="pusher_app_secret" type="text"
                                                value="{{ !empty($setting['pusher_app_secret']) ? $setting['pusher_app_secret'] : '' }}"
                                                id="pusher_app_secret" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="pusher_app_cluster"
                                                class="form-label">{{ __('Pusher App Cluster') }}</label>
                                            <input class="form-control " placeholder="Enter Pusher App Cluster"
                                                name="pusher_app_cluster" type="text"
                                                value="{{ !empty($setting['pusher_app_cluster']) ? $setting['pusher_app_cluster'] : '' }}"
                                                id="pusher_app_cluster" required>
                                        </div>
                                    </div>
                                    <div class="text-end p-2">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn-submit btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="recaptcha-settings" class="card">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('recaptcha.settings.store') }}"
                                accept-charset="UTF-8">
                                @csrf
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="">{{ __('ReCaptcha Settings') }}</h5>
                                            <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                target="_blank" class="text-blue ">
                                                <small
                                                    class="d-block mt-2">({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                            </a>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="col switch-width">
                                                <div class="form-group ml-2 mr-3 ">
                                                    <div class="custom-control custom-switch">
                                                        <input type="hidden" name="recaptcha_module" value="off">
                                                        <input type="checkbox" data-toggle="switchbutton"
                                                            data-onstyle="primary" class=""
                                                            name="recaptcha_module" id="recaptcha_module"
                                                            {{ !empty($setting['recaptcha_module']) && $setting['recaptcha_module'] == 'on' ? 'checked="checked"' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group col switch-width">
                                                {{ Form::label('google_recaptcha_version', __('Google Recaptcha Version'), ['class' => 'col-form-label']) }}
                                                {{ Form::select(
                                                    'google_recaptcha_version',
                                                    $google_recaptcha_version,
                                                    isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v3' ? 'v3' : 'v2-checkbox',
                                                    ['id' => 'google_recaptcha_version', 'class' => 'form-control multi-select', 'searchEnabled' => 'true'],
                                                ) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_key"
                                                class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                            <input class="form-control"
                                                placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                name="google_recaptcha_key" type="text"
                                                value="{{ !empty($setting['google_recaptcha_key']) ? $setting['google_recaptcha_key'] : '' }}"
                                                id="google_recaptcha_key" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_secret"
                                                class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                            <input class="form-control "
                                                placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                name="google_recaptcha_secret" type="text"
                                                value="{{ !empty($setting['google_recaptcha_secret']) ? $setting['google_recaptcha_secret'] : '' }}"
                                                id="google_recaptcha_secret" required>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn-submit btn btn-primary">
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <!--storage Setting-->
                    <div id="storage-settings" class="card mb-3">
                        {{ Form::open(['route' => 'storage.setting.store', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-10 col-md-10 col-sm-10">
                                    <h5 class="">{{ __('Storage Settings') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-sm-row flex-column">
                                <div class="pe-2 pb-3">
                                    <input type="radio" class="btn-check" name="storage_setting"
                                        id="local-outlined" autocomplete="off"
                                        {{ $setting['storage_setting'] == 'local' ? 'checked' : '' }} value="local"
                                        checked>
                                    <label class="btn btn-outline-primary col-12"
                                        for="local-outlined">{{ __('Local') }}</label>
                                </div>
                                <div class="pe-2 pb-3">
                                    <input type="radio" class="btn-check" name="storage_setting" id="s3-outlined"
                                        autocomplete="off" {{ $setting['storage_setting'] == 's3' ? 'checked' : '' }}
                                        value="s3">
                                    <label class="btn btn-outline-primary col-12" for="s3-outlined">
                                        {{ __('AWS S3') }}</label>
                                </div>

                                <div class="pe-2 pb-3">
                                    <input type="radio" class="btn-check" name="storage_setting"
                                        id="wasabi-outlined" autocomplete="off"
                                        {{ $setting['storage_setting'] == 'wasabi' ? 'checked' : '' }} value="wasabi">
                                    <label class="btn btn-outline-primary col-12"
                                        for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="local-setting row ">

                                    <div class="form-group col-8 switch-width">
                                        {{ Form::label('local_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                        <select name="local_storage_validation[]" class="multi-select"
                                            data-toggle="select2" id="local_storage_validation" multiple="multiple">
                                            @foreach ($file_type as $f)
                                                <option @if (in_array($f, $local_storage_validations)) selected @endif>
                                                    {{ $f }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label"
                                                for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                            <input type="number" name="local_storage_max_upload_size"
                                                class="form-control"
                                                value="{{ !isset($setting['local_storage_max_upload_size']) || is_null($setting['local_storage_max_upload_size']) ? '' : $setting['local_storage_max_upload_size'] }}"
                                                placeholder="{{ __('Max upload size') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="s3-setting row {{ $setting['storage_setting'] == 's3' ? ' ' : 'd-none' }}">

                                    <div class=" row ">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_key">{{ __('S3 Key') }}</label>
                                                <input type="text" name="s3_key" class="form-control"
                                                    value="{{ !isset($setting['s3_key']) || is_null($setting['s3_key']) ? '' : $setting['s3_key'] }}"
                                                    placeholder="{{ __('S3 Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_secret">{{ __('S3 Secret') }}</label>
                                                <input type="text" name="s3_secret" class="form-control"
                                                    value="{{ !isset($setting['s3_secret']) || is_null($setting['s3_secret']) ? '' : $setting['s3_secret'] }}"
                                                    placeholder="{{ __('S3 Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_region">{{ __('S3 Region') }}</label>
                                                <input type="text" name="s3_region" class="form-control"
                                                    value="{{ !isset($setting['s3_region']) || is_null($setting['s3_region']) ? '' : $setting['s3_region'] }}"
                                                    placeholder="{{ __('S3 Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                <input type="text" name="s3_bucket" class="form-control"
                                                    value="{{ !isset($setting['s3_bucket']) || is_null($setting['s3_bucket']) ? '' : $setting['s3_bucket'] }}"
                                                    placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_url">{{ __('S3 URL') }}</label>
                                                <input type="text" name="s3_url" class="form-control"
                                                    value="{{ !isset($setting['s3_url']) || is_null($setting['s3_url']) ? '' : $setting['s3_url'] }}"
                                                    placeholder="{{ __('S3 URL') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_endpoint">{{ __('S3 Endpoint') }}</label>
                                                <input type="text" name="s3_endpoint" class="form-control"
                                                    value="{{ !isset($setting['s3_endpoint']) || is_null($setting['s3_endpoint']) ? '' : $setting['s3_endpoint'] }}"
                                                    placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            {{ Form::label('s3_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                            <select name="s3_storage_validation[]" class="multi-select"
                                                data-toggle="select2" id="s3_storage_validation" multiple="multiple">
                                                @foreach ($file_type as $f)
                                                    <option @if (in_array($f, $s3_storage_validations)) selected @endif>
                                                        {{ $f }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                <input type="number" name="s3_max_upload_size" class="form-control"
                                                    value="{{ !isset($setting['s3_max_upload_size']) || is_null($setting['s3_max_upload_size']) ? '' : $setting['s3_max_upload_size'] }}"
                                                    placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div
                                    class="wasabi-setting row {{ $setting['storage_setting'] == 'wasabi' ? ' ' : 'd-none' }}">
                                    <div class=" row ">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_key">{{ __('Wasabi Key') }}</label>
                                                <input type="text" name="wasabi_key" class="form-control"
                                                    value="{{ !isset($setting['wasabi_key']) || is_null($setting['wasabi_key']) ? '' : $setting['wasabi_key'] }}"
                                                    placeholder="{{ __('Wasabi Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                <input type="text" name="wasabi_secret" class="form-control"
                                                    value="{{ !isset($setting['wasabi_secret']) || is_null($setting['wasabi_secret']) ? '' : $setting['wasabi_secret'] }}"
                                                    placeholder="{{ __('Wasabi Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="s3_region">{{ __('Wasabi Region') }}</label>
                                                <input type="text" name="wasabi_region" class="form-control"
                                                    value="{{ !isset($setting['wasabi_region']) || is_null($setting['wasabi_region']) ? '' : $setting['wasabi_region'] }}"
                                                    placeholder="{{ __('Wasabi Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                <input type="text" name="wasabi_bucket" class="form-control"
                                                    value="{{ !isset($setting['wasabi_bucket']) || is_null($setting['wasabi_bucket']) ? '' : $setting['wasabi_bucket'] }}"
                                                    placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="wasabi_url">{{ __('Wasabi URL') }}</label>
                                                <input type="text" name="wasabi_url" class="form-control"
                                                    value="{{ !isset($setting['wasabi_url']) || is_null($setting['wasabi_url']) ? '' : $setting['wasabi_url'] }}"
                                                    placeholder="{{ __('Wasabi URL') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="wasabi_root">{{ __('Wasabi Root') }}</label>
                                                <input type="text" name="wasabi_root" class="form-control"
                                                    value="{{ !isset($setting['wasabi_root']) || is_null($setting['wasabi_root']) ? '' : $setting['wasabi_root'] }}"
                                                    placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            {{ Form::label('wasabi_storage_validation', __('Only Upload Files'), ['class' => 'form-label']) }}

                                            <select name="wasabi_storage_validation[]" class="multi-select"
                                                data-toggle="select2" id="wasabi_storage_validation"
                                                multiple='multiple'>
                                                @foreach ($file_type as $f)
                                                    <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>
                                                        {{ $f }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="wasabi_root">{{ __('Max upload size ( In KB)') }}</label>
                                                <input type="number" name="wasabi_max_upload_size"
                                                    class="form-control"
                                                    value="{{ !isset($setting['wasabi_max_upload_size']) || is_null($setting['wasabi_max_upload_size']) ? '' : $setting['wasabi_max_upload_size'] }}"
                                                    placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class=" text-end">
                                {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    <!--seo-->
                    <div class="" id="seo">
                        {{ Form::open(['route' => ['settings.seo.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <h5 class="col-6">
                                                {{ __('SEO Settings') }}
                                            </h5>
                                            <div class="text-end col-6">
                                                <a data-size="lg" data-ajax-popup-over="true"
                                                    class="btn btn-sm text-white btn-primary"
                                                    data-url="{{ route('generate', ['seo']) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Generate with AI') }}"
                                                    data-title="{{ __('Generate Meta Keywords and Meta Description') }}">
                                                    <i
                                                        class="fas fa-robot text-white px-1"></i>{{ __('Generate with AI') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row ">
                                            <div class="col-12 row">
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                        {{ Form::label('Telegram Access Token', __('Meta Keywords'), ['class' => 'form-label']) }}
                                                        {{ Form::text('meta_keywords', isset($payment_detail['meta_keywords']) ? $payment_detail['meta_keywords'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Meta Keywords'), 'required' => 'required']) }}
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                        {{ Form::label('Telegram ChatID', __('Meta Description'), ['class' => 'form-label']) }}
                                                        {{ Form::textarea('meta_description', isset($payment_detail['meta_description']) ? $payment_detail['meta_description'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Meta Description'), 'required' => 'required']) }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <h6>{{ __('Meta Image') }}</h6>
                                                    <div class="logo-content">
                                                        <img src="@if ($payment_detail['meta_image']) {{ asset($meta_images . $payment_detail['meta_image']) }} @else{{ asset($meta_images . 'meta_image.png') }} @endif"
                                                            class="col-9" id="meta" />
                                                    </div>
                                                    <div class="choose-file mt-5 ">
                                                        <label for="meta_image">
                                                            <div class=" bg-primary"> <i
                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file" name="meta_image" id="meta_image"
                                                                class="custom-input-file choose_file_custom"
                                                                onchange="document.getElementById('meta').src = window.URL.createObjectURL(this.files[0])" />
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class=" text-end">
                                                {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <!--cache-->
                    <div class="" id="cache">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Cache Settings') }}
                                        </h5>
                                        <small class="text-secondary font-weight-bold">This is a page meant for more
                                            advanced users, simply ignore
                                            it if you don't
                                            understand what cache is.</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12 py-3">
                                                {{ Form::label('cache', __('Current cache size'), ['class' => 'col-form-label']) }}
                                                <div class="input-group ">
                                                    <input type="text" name="cache"
                                                        value="{{ App\Models\Utility::GetCacheSize() }}"
                                                        class="form-control" disabled>
                                                    <span
                                                        class="input-group-text bg-transparent">{{ __('MB') }}</span>
                                                </div>
                                            </div>
                                            <div class="  text-end">
                                                <a href="{{ url('config-cache') }}"
                                                    class="btn  btn-primary">{{ __('Clear Cache') }}</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--cookie-settings-->
                    <div class="" id="cookie-settings">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card" id="">
                                    {{ Form::open(['route' => 'cookie.setting', 'method' => 'post']) }}
                                    <div
                                        class="card-header flex-column flex-lg-row  d-flex align-items-lg-center gap-2 justify-content-between">
                                        <h5>{{ __('Cookie Settings') }}</h5>
                                        <div class="d-flex align-items-center">
                                            {{ Form::label('enable_cookie', __('Enable cookie'), ['class' => 'col-form-label p-0 fw-bold me-3']) }}
                                            <div class="custom-control custom-switch" id="cookie_dis">
                                                <input type="checkbox" data-toggle="switchbutton"
                                                    data-onstyle="primary" name="enable_cookie"
                                                    class="form-check-input input-primary " id="enable_cookie"
                                                    {{ $payment_detail['enable_cookie'] == 'on' ? ' checked ' : '' }}>
                                                <label class="custom-control-label mb-1" for="enable_cookie"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="text-end col-12">
                                                <a data-size="lg" data-ajax-popup-over="true"
                                                    class="btn btn-sm text-white btn-primary"
                                                    data-url="{{ route('generate', ['cookie']) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Generate with AI') }}"
                                                    data-title="{{ __('Generate Cookie Title & Description') }}">
                                                    <i
                                                        class="fas fa-robot text-white px-1"></i>{{ __('Generate with AI') }}</a>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch custom-switch-v1">
                                                    <input type="checkbox" name="cookie_logging"
                                                        class="form-check-input input-primary" id="todisable"
                                                        {{ $payment_detail['cookie_logging'] == 'on' ? ' checked ' : '' }}>
                                                    <label class="form-check-label"
                                                        for="cookie_logging">{{ __('Enable logging') }}</label>
                                                </div>
                                                <div class="form-group">
                                                    {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('cookie_title', isset($payment_detail['cookie_title']) ? $payment_detail['cookie_title'] : '', ['class' => 'form-control ', 'id' => 'todisable']) }}
                                                </div>
                                                <div class="form-group ">
                                                    {{ Form::label('cookie_description', __('Cookie Description'), ['class' => ' form-label']) }}
                                                    {!! Form::textarea(
                                                        'cookie_description',
                                                        isset($payment_detail['cookie_description']) ? $payment_detail['cookie_description'] : '',
                                                        ['class' => 'form-control ', 'rows' => '2', 'id' => 'todisable'],
                                                    ) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch custom-switch-v1 ">
                                                    <input type="checkbox" name="necessary_cookies"
                                                        class="form-check-input input-primary" id="todisable"
                                                        {{ $payment_detail['necessary_cookies'] == 'on' ? ' checked ' : '' }}
                                                        checked onclick="return false">
                                                    <label class="form-check-label"
                                                        for="necessary_cookies">{{ __('Strictly necessary cookies') }}</label>
                                                </div>
                                                <div class="form-group ">
                                                    {{ Form::label('strictly_cookie_title', __(' Strictly Cookie Title'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('strictly_cookie_title', isset($payment_detail['strictly_cookie_title']) ? $payment_detail['strictly_cookie_title'] : '', ['class' => 'form-control', 'id' => 'todisable']) }}
                                                </div>
                                                <div class="form-group ">
                                                    {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => ' form-label']) }}
                                                    {!! Form::textarea(
                                                        'strictly_cookie_description',
                                                        isset($payment_detail['strictly_cookie_description']) ? $payment_detail['strictly_cookie_description'] : '',
                                                        ['class' => 'form-control ', 'rows' => '2', 'id' => 'todisable'],
                                                    ) !!}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <h5>{{ __('More Information') }}</h5>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group ">
                                                    {{ Form::label('more_information_description', __('Contact Us Description'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('more_information_description', isset($payment_detail['more_information_description']) ? $payment_detail['more_information_description'] : '', ['class' => 'form-control', 'id' => 'todisable']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group ">
                                                    {{ Form::label('contactus_url', __('Contact Us URL'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('contactus_url', isset($payment_detail['contactus_url']) ? $payment_detail['contactus_url'] : '', ['class' => 'form-control', 'id' => 'todisable']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer row">
                                        <div class="text-start col">
                                            @if (file_exists(storage_path() . '/uploads/sample/data.csv') && $setting['cookie_logging'] == 'on')
                                                <label for="file"
                                                    class="form-label">{{ __('Download cookie accepted data') }}</label>
                                                <a href="{{ asset(Storage::url('uploads/sample')) . '/data.csv' }}"
                                                    class="btn  btn-primary">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-end col-auto">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-primary">
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--chat-gtp-->
                    <div class="" id="chat-gpt-settings">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card" id="">
                                    {{ Form::model($payment_detail, ['route' => 'settings.chatgptkey', 'method' => 'post']) }}
                                    <div
                                        class="card-header flex-column flex-lg-row  d-flex align-items-lg-center gap-2 justify-content-between">
                                        <h5>{{ __('Chat GPT Key Settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group ">
                                                    {{ Form::label('chatgpt_key', __('ChatGPT key'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('chatgpt_key', isset($payment_detail['chatgpt_key']) ? $payment_detail['chatgpt_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Chatgpt Key Here')]) }}
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group ">
                                                    {{ Form::label('chatgpt_model', __('ChatGPT Model'), ['class' => 'col-form-label']) }}
                                                    {{ Form::text('chatgpt_model', isset($payment_detail['chatgpt_model']) ? $payment_detail['chatgpt_model'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Chatgpt Model Here')]) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end ">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-primary">
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ sample-page ] end -->
                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> --}}
    {{-- <script src="{{ asset('assets/custom/js/jquery.min(1).js') }}"></script> --}}
    <script src="{{ asset('assets/custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        $("input").click(function() {
            event.stopPropagation() // or
            event.preventDefault
            // console.log(event);
            $(this).parent().SomehowStopCollapse;
        });

        // $(document).on('click', $("input"), function(e){
        //     event1 = document.getElementById("collapse6");
        //     event = $("div.accordion-collapse").attr("data-bs-toggle", "collapse");

        //     $('#accordion').bind('accordionchange',
        //         function() {
        //             alert('Active tab index: ' + $(this).accordion('option', 'active'))
        //         });
        // });


        $("#cookie_dis").click(function() {

            if ($('#enable_cookie').prop('checked')) {
                ele = document.querySelectorAll('[id="todisable"]');
                for (i = 0; i < ele.length; i++) {
                    ele[i].disabled = false;
                }
            } else {
                ele = document.querySelectorAll('[id="todisable"]');
                for (i = 0; i < ele.length; i++) {
                    ele[i].disabled = true;
                }
            }
        });
    </script>
    @if ($payment_detail['enable_cookie'] != 'on')
        <script>
            ele = document.querySelectorAll('[id="todisable"]');
            for (i = 0; i < ele.length; i++) {
                ele[i].disabled = true;
            }
        </script>
    @endif
    <script>
        $(document).ready(function() {

            if ($('.gdpr_fulltime').is(':checked')) {

                $('.fulltime').show();
            } else {
                $('.fulltime').hide();
            }
            $('#gdpr_cookie').on('change', function() {

                if ($('.gdpr_fulltime').is(':checked')) {

                    $('.fulltime').show();
                } else {

                    $('.fulltime').hide();
                }
            });

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

    <script>
        function check_theme(color_val, element) {
            // console.log(color_val);
            // Remove the active_color class from all color options
            $('.theme-color a').removeClass('active_color');
            // Add the active_color class to the clicked color option
            $(element).addClass('active_color');

            // Update the hidden input value
            $('#color_value').val(color_val);

            // You can also update the radio button if needed
            $('input[name="color"]').val([color_val]);
        }

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        });
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


    <script type="text/javascript">
        $('#small-favicon').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#favicon').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_blue').change(function() {

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



        if ($(".multi-select").length > 0) {
            $($(".multi-select")).each(function(index, element) {
                var id = $(element).attr('id');
                var multipleCancelButton = new Choices(
                    '#' + id, {
                        removeItemButton: true,

                    }
                );
            });
        }

        $(document).on('change', '[name=storage_setting]', function() {
            if ($(this).val() == 's3') {
                $('.s3-setting').removeClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').addClass('d-none');
            } else if ($(this).val() == 'wasabi') {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').removeClass('d-none');
                $('.local-setting').addClass('d-none');
            } else {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').removeClass('d-none');
            }
        });
    </script>

    {{-- color picker script --}}
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
@endpush


<style>
    .active_color {
        /* background-color: #ffffff !important; */
        border: 2px solid #000 !important;
    }
</style>

@if ($color == 'theme-3')
    <style>
        .btn-check:checked+.btn-outline-primary,
        .btn-check:active+.btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }


        .btn-outline-primary:hover {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }


        .btn[class*="btn-outline-"]:hover {

            border-color: #6fd943 !important;
        }
    </style>
@endif

@if ($color == 'theme-2')
    <style>
        .btn-check:checked+.btn-outline-primary,
        .btn-check:active+.btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #ffffff;
            background-color: #1f3996 !important;
            border-color: #1f3996 !important;
        }


        body.theme-2 .btn-outline-primary:hover {
            color: #ffffff;
            background-color: #1f3996 !important;
            border-color: #1f3996 !important;
        }
    </style>
@endif

@if ($color == 'theme-4')
    <style>
        .btn-check:checked+.btn-outline-primary,
        .btn-check:active+.btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #ffffff;
            background-color: #51459d !important;
            border-color: #51459d !important;
        }


        body.theme-4 .btn-outline-primary:hover {
            color: #ffffff;
            background-color: #51459d !important;
            border-color: #51459d !important;
        }
    </style>
@endif

@if ($color == 'theme-1')
    <style>
        .btn-check:checked+.btn-outline-primary,
        .btn-check:active+.btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }


        body.theme-1 .btn-outline-primary:hover {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }
    </style>
@endif
