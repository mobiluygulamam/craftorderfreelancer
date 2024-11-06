@php
    $setting = App\Models\Utility::getAdminPaymentSetting();
    $languages = App\Models\Utility::languages();
    config([
        'captcha.sitekey' => $setting['google_recaptcha_key'],
        'captcha.secret' => $setting['google_recaptcha_secret'],
        'options' => [
            'timeout' => 30,
        ],
    ]);
@endphp

@extends('layouts.guest')

@section('page-title')
    {{ __('Login') }}
@endsection

@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text">{{ ucfirst($languages[$lang]) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $languageCode => $languageFullName)
                    <a href="{{ route('login', $languageCode) }}" tabindex="0"
                        class="dropdown-item {{ $languageCode == $lang ? 'active' : '' }}">
                        <span>{{ ucFirst($languageFullName) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
        </div>
        {{-- @if (env('RECAPTCHA_MODULE') != 'on')
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <span class="text-danger">{{ $error }}</span>
                @endforeach
            @endif
        @endif --}}
        @if (session()->has('error'))
            <div>
                <p class="text-danger">{{ session('error') }}</p>
            </div>
        @endif
        <div class="custom-login-form">
            <form method="POST" id="form_data" action="{{ route('login') }}" class="needs-validation" novalidate>
                @csrf
                <div class="form-group mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control" @error('email') is-invalid @enderror name="email"
                        id="emailaddress" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="{{ __('Enter Your Email') }}" />
                    @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input type="password" class="form-control" @error('password') is-invalid @enderror name="password"
                        required autocomplete="current-password" id="password"
                        placeholder="{{ __('Enter Your Password') }}" />
                    @error('password')
                        <span class="error invalid-password text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <span><a href="{{ route('password.request', $lang) }}"
                                tabindex="0">{{ __('Forgot your password?') }}</a></span>
                    </div>
                </div>

                @if ($setting['recaptcha_module'] == 'on')
                    @if (isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v2-checkbox')
                        <div class="form-group mb-3">
                            {!! NoCaptcha::display($setting['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                            @error('g-recaptcha-response')
                                <span class="small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @else
                        <div class="form-group col-lg-12 col-md-12 mt-3">
                            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"
                                class="form-control">
                            @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif
                @endif

                <div class="d-grid">
                    <button class="btn btn-primary mt-2">
                        {{ __('Login') }}
                    </button>
                </div>

                @if ($setting['signup_button'] == 'on')
                    <p class="my-4 text-center">{{ __("Don't have an account?") }} <a
                            href="{{ route('register', $lang) }}" tabindex="0"> {{ __('Register') }} </a></p>
                @endif
            </form>
            <div class="d-grid mt-3">
                <a href="{{ route('client.login', $lang) }}" type="button" class="btn btn-primary btn-block"
                    style="color:#fff">
                    {{ __('Client Login') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#form_data").submit(function(e) {
                $("#login_button").attr("disabled", true);
                return true;
            });
        });
    </script>
    @if ($setting['recaptcha_module'] == 'on')
        @if (isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v2-checkbox')
            {!! NoCaptcha::renderJs() !!}
        @elseif(isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v3')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $setting['google_recaptcha_key'] }}"></script>
            <script>
                $(document).ready(function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $setting['google_recaptcha_key'] }}', {
                            action: 'submit'
                        }).then(function(token) {
                            $('#g-recaptcha-response').val(token);
                        });
                    });
                });
            </script>
        @endif
    @endif
@endpush
