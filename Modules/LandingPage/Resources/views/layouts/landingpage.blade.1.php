@php
    use App\Models\Utility;
    $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
    $logo = Utility::get_file('uploads/landing_page_image');
    $sup_logo = Utility::get_file('logo/');
    $adminSettings = Utility::getAdminPaymentSetting();

    $getseo = App\Models\Utility::getAdminPaymentSetting();
    $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
    $metsdesc = isset($getseo['meta_desc']) ? $getseo['meta_desc'] : '';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
    $get_cookie = Utility::getAdminPaymentSetting();

    // $SITE_RTL = env('SITE_RTL');
    $SITE_RTL = $adminSettings['site_rtl'];
    $setting = \App\Models\Utility::colorset();
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
@endphp
<!doctype html>
<html lang="en" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>
    <!-- Required meta tags -->
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />

    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metsdesc }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metsdesc }}">
    <meta property="og:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metsdesc }}">
    <meta property="twitter:image" content="{{ $meta_image . $meta_logo }}">

    <link rel="shortcut icon" href="landingassets/img/favicon.ico">


    <!-- Bootstrap CSS -->
    <link href="landingassets/vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="landingassets/vendors/themify-icon/themify-icons.css" rel="stylesheet">
    <link href="landingassets/vendors/icomoon/style.css" rel="stylesheet">
    <link href="landingassets/css/font-awesome.min.css" rel="stylesheet">
    <link href="landingassets/vendors/slick/slick.css" rel="stylesheet">
    <link href="landingassets/vendors/slick/slick-theme.css" rel="stylesheet">
    <link href="landingassets/vendors/animation/animate.css" rel="stylesheet">
    <link href="landingassets/css/style.css" rel="stylesheet">
    <link href="landingassets/css/responsive.css" rel="stylesheet">

    <title>Pictech - Creative HTML5 Template for Saas, Startup & Agency</title>
</head>

<body data-scroll-animation="true">

    <div class="body_wrapper">
        <!-- Preloader Start -->
        <div id="preloader" class="preloader">
            <div class="animation-preloader">
                <div class="spinner">
                </div>
                <div class="txt-loading">
                    <span data-text-preloader="C" class="letters-loading">
                        C
                    </span>
                    <span data-text-preloader="O" class="letters-loading">
                        R
                    </span>
                    <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                    <span data-text-preloader="F" class="letters-loading">
                        F
                    </span>
                    <span data-text-preloader="T" class="letters-loading">
                        T
                    </span>
                    <span data-text-preloader="O" class="letters-loading">
                        O
                    </span>
                    <span data-text-preloader="R" class="letters-loading">
                        R
                    </span>
                    <span data-text-preloader="D" class="letters-loading">
                         D
                     </span>
                     <span data-text-preloader="E" class="letters-loading">
                         E
                     </span>
                     <span data-text-preloader="R" class="letters-loading">
                         +
                     </span>
                </div>
                <p class="text-center">Loading</p>
            </div>
            <div class="loader">
                <div class="row">
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- start header  -->
        <nav class="navbar navbar-expand-lg menu_white sticky_nav">
            <div class="container">
                <a class="navbar-brand sticky_logo" href="index.html">
                    <img src="landingassets/img/logo.png" alt="logo" height="50" width="50">
                    <img src="landingassets/img/logo.png" height="50" width="50">
          
                </a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav menu me-lg-auto ms-lg-auto">
                       
     
              
                   
                 
                        <li class="nav-item"><a href="contact.html" class="nav-link">Contact Us</a></li>
                        <li class="nav-item"><a href="contact.html" class="nav-link">Contact Us</a></li>
                        <li class="nav-item"><a href="contact.html" class="nav-link">Contact Us</a></li>
                    </ul>
                    <div class="nav_right">
                        <a href="{{ route('login') }}" class="login_btn">
                            <div class="btn_text"><span>Login</span><span>Login</span></div>
                        </a>
                        <a href="{{ route('register') }}" class="signup_btn hover_effect">
                            <div class="btn_text"><span>Create Free Account</span><span>Create Free Account</span></div>
                        </a>
                    </div>
                </div>
                <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="menu_toggle">
                        <span class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="hamburger-cross">
                            <span></span>
                            <span></span>
                        </span>
                    </span>
                </button>
            </div>
        </nav>
        <!-- End header  -->

        
        <section class="saas_banner_area_five" data-bg-color="#121217">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="app_banner_content">
                            <h2 class="title-animation">Manage your employees with messaging app</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.5s">Pictech is a complete customer service platform that connects with
                                your website visitors in real-time to convert new
                                leads, close more deals, and provide better support to your customers.</p>
                            <a href="#" class="saas_btn wow fadeInUp" data-wow-delay="0.6s">
                                <div class="btn_text">
                                    <span>Start A 15-Day Free Trial</span>
                                    <span>Start A 15-Day Free Trial</span>
                                </div>
                            </a>
                          
                        </div>
                    </div>
                    <div class="col-lg-6">
                         <div class="saas_banner_img wow fadeInRight" data-wow-delay="0.2s">
                             <img src="landingassets/img/home-five/ip_one.png" alt="">
                             
                         </div>
                     </div>
                </div>
            </div>
        </section>

        <section class="app_features_area" data-bg-color="#121217">
            <div class="container">
                <div class="row app_features_item one">
                    <div class="col-lg-6">
                        <div class="saas_features_img wow fadeInLeft" data-wow-delay="0.2s" data-bg-color="#7B71FE">
                            <img src="landingassets/img/home-five/app_f_one.png" alt="">
                            <img class="img_small" data-parallax='{"y": 40}' src="landingassets/img/home-five/app_f_two.png"
                                alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="app_features_content">
                            <h2 class="wow fadeInUp" data-wow-delay="0.2s">Messaging with a simple platform</h2>
                            <div class="features_item_list wow fadeInUp" data-wow-delay="0.3s">
                                <h5>Manage your spending and save</h5>
                                <p>Stay on top of your spending by tracking what’s
                                    left after the bills are paid.</p>
                            </div>
                            <div class="features_item_list wow fadeInUp" data-wow-delay="0.4s">
                                <h5>Easily view and manage your bills</h5>
                                <p>Stay on top of your spending by tracking what’s
                                    left after the bills are paid.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-row-reverse app_features_item two">
                    <div class="col-lg-6">
                        <div class="saas_features_img wow fadeInRight" data-wow-delay="0.3s" data-bg-color="#FFA31A">
                            <img src="landingassets/img/home-five/video_big.png" alt="">
                            <img class="img_small" data-parallax='{"y": 40}' src="landingassets/img/home-five/video.png"
                                alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="app_features_content">
                            <h2 class="wow fadeInUp" data-wow-delay="0.1s">Keep the power of chat in your pocket</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">All Wing clients receive complimentary access
                                to the Wing Task Workspace App – a
                                specialized app built to help you your assistant, manage tasks/projects, </p>
                            <ul class="list-unstyled features_list wow fadeInUp" data-wow-delay="0.4s">
                                <li>Link your bank or financial account</li>
                                <li>Easy way to view your total balance</li>
                                <li>Transaction history in real-time</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="testimonial_area_five black sec_padding" data-bg-color="#0F172A">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp home_5_testi" data-wow-delay="0.2s">Client’s Feedback</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">Property managers, owners, and accountants worldwide
                        use PicmaticWeb<br>
                        to manage any combination of properties.</p>
                </div>
                <div class="testimonial_slider_one wow fadeInUp" data-wow-delay="0.4s">
                    <div class="item">
                        <div class="ratting">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </div>
                        <p>PicmaticWeb is our go-to tool to manage our team. With new features with every update,
                            PicmaticWeb is helping us catalog our day-to-day operating needs in managing loads of
                            projects. PicmaticWeb has become a game-changer for us. From helping us organize every kind
                            of work to improving efficiency and productivity overall has been amazing</p>
                        <div class="quote_icon d-flex align-items-center justify-content-between">
                            <div class="icon">
                                <img src="landingassets/img/home-two/quote.png" alt="">
                            </div>
                        </div>
                        <div class="client_info">
                            <img src="landingassets/img/home-two/author_img_1.png" alt="">
                            <div class="text">
                                <h5>Barly Vallendito</h5>
                                <h6>Co-founder & COO, Picmatic Ltd.</h6>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="ratting">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </div>
                        <p>PicmaticWeb is our go-to tool to manage our team. With new features with every update,
                            PicmaticWeb is helping us catalog our day-to-day operating needs in managing loads of
                            projects. PicmaticWeb has become a game-changer for us. From helping us organize every kind
                            of work to improving efficiency and productivity overall has been amazing</p>
                        <div class="quote_icon d-flex align-items-center justify-content-between">
                            <div class="icon">
                                <img src="landingassets/img/home-two/quote.png" alt="">
                            </div>
                        </div>
                        <div class="client_info">
                            <img src="landingassets/img/home-two/author_img_2.png" alt="">
                            <div class="text">
                                <h5>Syahrul Falah</h5>
                                <h6>Co-founder & COO, Litteweb Ltd.</h6>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="ratting">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </div>
                        <p>PicmaticWeb is our go-to tool to manage our team. With new features with every update,
                            PicmaticWeb is helping us catalog our day-to-day operating needs in managing loads of
                            projects. PicmaticWeb has become a game-changer for us. From helping us organize every kind
                            of work to improving efficiency and productivity overall has been amazing</p>
                        <div class="quote_icon d-flex align-items-center justify-content-between">
                            <div class="icon">
                                <img src="landingassets/img/home-two/quote.png" alt="">
                            </div>
                        </div>
                        <div class="client_info">
                            <img src="landingassets/img/home-two/author_img_2.png" alt="">
                            <div class="text">
                                <h5>Marina Nikiforova</h5>
                                <h6>Co-founder & COO, Litteweb Ltd.</h6>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="ratting">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </div>
                        <p>PicmaticWeb is our go-to tool to manage our team. With new features with every update,
                            PicmaticWeb is helping us catalog our day-to-day operating needs in managing loads of
                            projects. PicmaticWeb has become a game-changer for us. From helping us organize every kind
                            of work to improving efficiency and productivity overall has been amazing</p>
                        <div class="quote_icon d-flex align-items-center justify-content-between">
                            <div class="icon">
                                <img src="landingassets/img/home-two/quote.png" alt="">
                            </div>
                        </div>
                        <div class="client_info">
                            <img src="landingassets/img/home-two/author_img_2.png" alt="">
                            <div class="text">
                                <h5>Syahrul Falah</h5>
                                <h6>Co-founder & COO, Litteweb Ltd.</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custom_nav">
                    <button class="prev"><i class="icon-arrow-left2" aria-hidden="true"></i></button>
                    <button class="next"><i class="icon-arrow-right2" aria-hidden="true"></i></button>
                </div>
            </div>
        </section>
        <section class="saas_accordion_area sec_padding" data-bg-color="#121217">
            <div class="container">
                <div class="section_title_four text-center">
                    <h2 class="title-animation">Pictech managed remote<br> talent experience</h2>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <img class="accordion_img wow fadeInLeft" data-wow-delay="0.4s"
                            src="landingassets/img/home-four/app_two.png" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="accordion saas_accordion_item saas_accordion_item_two wow fadeInRight"
                            data-wow-delay="0.5s" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Up to 80% quicker & cheaper
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body ps-4">
                                        All Wing clients receive complimentary access to the Wing Task Workspace App – a
                                        specialized app built to help you communicate with your assistant, manage
                                        tasks/projects, share files, record screen-sharing videos, and lot more.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Dedicated assistants only
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body ps-4">
                                        All Wing clients receive complimentary access to the Wing Task Workspace App – a
                                        specialized app built to help you communicate with your assistant, manage
                                        tasks/projects, share files, record screen-sharing videos, and lot more.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Free Customer Success Manager
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body ps-4">
                                        All Wing clients receive complimentary access to the Wing Task Workspace App – a
                                        specialized app built to help you communicate with your assistant, manage
                                        tasks/projects, share files, record screen-sharing videos, and lot more.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        Wing Workspace App
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body ps-4">
                                        All Wing clients receive complimentary access to the Wing Task Workspace App – a
                                        specialized app built to help you communicate with your assistant, manage
                                        tasks/projects, share files, record screen-sharing videos, and lot more.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFive" aria-expanded="false"
                                        aria-controls="collapseFive">
                                        Ongoing quality supervision
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body ps-4">
                                        Use 'Workflows' to define complex, repetitive processes.
                                        Use 'Routine' to delegate recurring, regular tasks.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="fun_fact_area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="fact_item wow fadeInUp" data-wow-delay="0.1s">
                            <h3>1.5 billion+</h3>
                            <h5>Email Sent</h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="fact_item wow fadeInUp" data-wow-delay="0.2s">
                            <h3>150x</h3>
                            <h5>Average ROI</h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="fact_item wow fadeInUp" data-wow-delay="0.3s">
                            <h3>99.5%</h3>
                            <h5>Customer Satisfaction</h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="fact_item wow fadeInUp" data-wow-delay="0.4s">
                            <h3>4.8 stars</h3>
                            <h5>Customer Rating</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="service_category_area sec_padding" data-bg-color="#17171C">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">Manage Your Finance. Anytime, Anywhere.</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">Property managers, owners, and accountants worldwide
                        use PicmaticWeb<br>
                        to manage any combination of properties.</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.2s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/mood.png" alt="">
                            </div>
                            <h4>Happy Customer Service</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.3s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/camera.png" alt="">
                            </div>
                            <h4>Expert Professionals</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.4s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/airplay.png" alt="">
                            </div>
                            <h4>Project Sharing</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.5s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/lock.png" alt="">
                            </div>
                            <h4>Secure Payments</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.6s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/album.png" alt="">
                            </div>
                            <h4>Flexible Payment</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.7s">
                            <div class="icon">
                                <img src="landingassets/img/home-five/deskphone.png" alt="">
                            </div>
                            <h4>24/7 Support</h4>
                            <p>Obligations business will frequently occur that pleasures have to be </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="app_promo_area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="app_promo_content">
                            <h2 class="title-animation">Get Started Free For 30-Days <img
                                    src="landingassets/img/home-five/hand.svg" alt=""></h2>
                            <p class="wow fadeInUp" data-wow-delay="0.5s">Amet minim mollit non deserunt ullamco est sit
                                aliqua
                                sint. velit officia consequat duis enim velit mollit.</p>
                            <div class="d-flex wow fadeInUp" data-wow-delay="0.6s">
                                <a href="#" class="playstore_btn">
                                    <img src="landingassets/img/home-five/play-store.svg" alt="">
                                </a>
                                <a href="#" class="playstore_btn">
                                    <img src="landingassets/img/home-five/app-store.svg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img class="wow fadeInRight" data-wow-delay="0.4s" src="landingassets/img/home-five/mobile.png" alt="">
                    </div>
                </div>
            </div>
        </section>
        <footer class="footer_area_two footer_area_three footer_shap" data-bg-color="#0F172A">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-sm-6">
                        <div class="f_widget f_about_widget wow fadeInUp" data-wow-delay="0.1s">
                            <a href="index.html" class="f_logo"><img src="landingassets/img/home-two/f_logo_w.png" alt=""></a>
                            <p>Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia
                                consequat duis enim velit mollit.</p>
                            <ul class="list-unstyled f_social_icon">
                                <li><a href="#"><i class="ti-facebook"></i></a></li>
                                <li><a href="#"><i class="ti-twitter-alt"></i></a></li>
                                <li><a href="#"><i class="ti-vimeo-alt"></i></a></li>
                                <li><a href="#"><i class="ti-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="f_widget f_link_widget wow fadeInUp" data-wow-delay="0.2s">
                            <h3 class="f_title">COMPANY</h3>
                            <ul class="list-unstyled link_widget">
                            <li><a href="about-us.html">About Us</a></li>
                            <li><a href="service.html">Service</a></li>
                            <li><a href="pricing.html">Pricing</a></li>
                            <li><a href="team.html">Team</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="f_widget f_link_widget wow fadeInUp" data-wow-delay="0.3s">
                            <h3 class="f_title">HELP</h3>
                            <ul class="list-unstyled link_widget">
                                <li><a href="#">Customer Support</a></li>
                                <li><a href="#">Delivery Details</a></li>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="f_widget f_newsletter_widget wow fadeInUp" data-wow-delay="0.4s">
                            <h3 class="f_title">SUBSCRIBE TO NEWSLETTER</h3>
                            <form action="#" class="newsletter_form newsletter_form_two">
                                <input class="form-control" type="text" placeholder="Enter your email">
                                <button type="submit" class="theme_btn">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="footer_bottom text-center">
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="mb-0 wow fadeInUp" data-wow-delay="0.3s">© Copyright 2024, All Rights Reserved by <a href="#0">PicmaticWeb</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->
    <script src="landingassets/js/jquery-3.6.0.min.js"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->

    <script src="landingassets/vendors/bootstrap/js/popper.min.js"></script>
    <script src="landingassets/vendors/bootstrap/js/bootstrap.min.js"></script>
    <script src="landingassets/vendors/slick/slick.min.js"></script>
    <script src="landingassets/vendors/parallax/jquery.parallax-scroll.js"></script>
    <script src="landingassets/js/gsap.min.js"></script>
    <script src="landingassets/js/SplitText.js"></script>
    <script src="landingassets/js/ScrollTrigger.min.js"></script>
    <script src="landingassets/js/SmoothScroll.js"></script>
    <script src="landingassets/vendors/wow/wow.min.js"></script>
    <script src="landingassets/js/custom.js"></script>

</body>

</html>