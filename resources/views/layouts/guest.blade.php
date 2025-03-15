@php
    if (isset($currentWorkspace)) {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);

        $SITE_RTL = $setting->site_rtl;

        $color = $setting->theme_color;
    } else {
        $setting = App\Models\Utility::getAdminPaymentSetting();

        $SITE_RTL = $setting['site_rtl'];

        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-4';
        }
        $color = 'theme-4';
    }

    $SITE_RTL = isset($setting['RTL']) ? $setting['RTL'] : $SITE_RTL;
    $logo = \App\Models\Utility::get_file('logo/');
    $meta_images = \App\Models\Utility::get_file('uploads/logo/');
    if (\App::getLocale() == 'ar' || \App::getLocale() == 'he') {
        $SITE_RTL = 'on';
    }
    use App\Models\Utility;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="title" content="{{ $setting['meta_keywords'] }}">
    <meta name="description" content="{{ $setting['meta_description'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $setting['meta_keywords'] }}">
    <meta property="og:description" content="{{ $setting['meta_description'] }}">
    <meta property="og:image" content="{{ asset($meta_images . $setting['meta_image']) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $setting['meta_keywords'] }}">
    <meta property="twitter:description" content="{{ $setting['meta_description'] }}">
    <meta property="twitter:image" content="{{ asset($meta_images . $setting['meta_image']) }}">
<!-- Meta Pixel Code -->
<script>
     !function(f,b,e,v,n,t,s)
     {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
     n.callMethod.apply(n,arguments):n.queue.push(arguments)};
     if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
     n.queue=[];t=b.createElement(e);t.async=!0;
     t.src=v;s=b.getElementsByTagName(e)[0];
     s.parentNode.insertBefore(t,s)}(window, document,'script',
     'https://connect.facebook.net/en_US/fbevents.js');
     fbq('init', '499987013180635');
     fbq('track', 'PageView');
     </script>
     <noscript><img height="1" width="1" style="display:none"
     src="https://www.facebook.com/tr?id=499987013180635&ev=PageView&noscript=1"
     /></noscript>
     <!-- End Meta Pixel Code -->
    <title>
        {{ config('app.name', 'Taskly') }} - @yield('page-title')
    </title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset($logo . 'favicon.png') }}">


    @if ($setting['cust_darklayout'] == 'on')
        @if (isset($SITE_RTL) && $SITE_RTL == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @endif
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        @if (isset($SITE_RTL) && $SITE_RTL == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @endif
    @endif

    @if (isset($SITE_RTL) && $SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css') }}" id="main-style-link">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-dark.css') }}" id="main-style-link">
    @endif
    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }

        .big-logo {
            height: 60px;
            width: 150px;
        }

        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }

        .grecaptcha-badge {
            z-index: 99999999 !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
</head>


@php
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-4';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $color = 'custom-color';
    } else {
        $color = $color;
    }
@endphp

<body class="{{ $color }}">
    <?php
    $dir = base_path() . '/resources/lang/';
    $glob = glob($dir . '*', GLOB_ONLYDIR);
    $arrLang = array_map(function ($value) use ($dir) {
        return str_replace($dir, '', $value);
    }, $glob);
    $arrLang = array_map(function ($value) use ($dir) {
        return preg_replace('/[0-9]+/', '', $value);
    }, $arrLang);
    $arrLang = array_filter($arrLang);
    $currantLang = basename(App::getLocale());
    $client_keyword = Request::route()->getName() == 'client.login' ? 'client.' : '';
    ?>

    <script src="{{ asset('assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>.
    <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>

    <script>
        feather.replace();
    </script>
    
    <script>
        feather.replace();
        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }
        // var custthemebg = document.querySelector("#cust-theme-bg");
        // custthemebg.addEventListener("click", function() {
        //     if (custthemebg.checked) {
        //         document.querySelector(".dash-sidebar").classList.add("transprent-bg");
        //         document
        //             .querySelector(".dash-header:not(.dash-mob-header)")
        //             .classList.add("transprent-bg");
        //     } else {
        //         document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
        //         document
        //             .querySelector(".dash-header:not(.dash-mob-header)")
        //             .classList.remove("transprent-bg");
        //     }
        // });

        // var custdarklayout = document.querySelector("#cust-darklayout");
        // custdarklayout.addEventListener("click", function() {
        //     if (custdarklayout.checked) {
        //         document
        //             .querySelector(".m-header > .b-brand > .logo-lg")
        //             .setAttribute("src", "../assets/images/logo.svg");
        //         document
        //             .querySelector("#main-style-link")
        //             .setAttribute("href", "../assets/css/style-dark.css");
        //     } else {
        //         document
        //             .querySelector(".m-header > .b-brand > .logo-lg")
        //             .setAttribute("src", "../assets/images/logo-dark.svg");
        //         document
        //             .querySelector("#main-style-link")
        //             .setAttribute("href", "../assets/css/style.css");
        //     }
        // });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>
    @stack('custom-scripts')

    @php
        $company_logo = App\Models\Utility::get_logo();
    @endphp

    <div class="custom-login bg-slate-600 relative">
   
        <div class="login-bg-img">
            {{-- <img src="{{ asset('assets/img/loginbg.png') }}" class="login-bg-1">--}}
  
        
            @if ($color == 'custom-color')
                <!-- Show theme-3 image -->
                <img src="{{ asset('assets/img/loginbg.png') }}" class="login-bg-1">
              
               
            @else
        
                <img src="{{ asset('assets/img/loginbg.png') }}" class="login-bg-1   rounded-5">
              
                <div class="login-bg-2 font-[Open_Sans] text-white"><h1 class=" font-[Open_Sans] text-white">{{App\Models\Utility::getRandomMotivationalQuote()}}</h1></div>
                
            @endif
           
        </div>
     
        <div class="bg-login text-right">
      
   

        </div>


        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">
                            <a class="" href="#">
                                <img src="{{ asset("assets/logo-light.png". '?timestamp=' . time()) }}" style="width: 75px; height: 75px; " class="big-logo"
                                    alt="{{config('app.name', 'Taskly Sass')}}" loading="lazy">
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                @include('landingpage::layouts.buttons')
                                @yield('language-bar')
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
            <main class="custom-wrapper ">
                <div class="custom-row right-5">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>


            <div class="row justify-content-center">
                <div class="col-md-4">
                    @if (session()->has('info'))
                        <div class="alert alert-primary">
                            {{ session()->get('info') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
       
    </div>
    

    {{-- @yield('content') --}}



    @if ($setting['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif
</body>

</html>
