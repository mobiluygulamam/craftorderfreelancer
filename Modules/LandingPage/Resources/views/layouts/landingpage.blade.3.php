<!doctype html>
<html lang="tr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <title>Freelance CRM: Proje Yönetimi ve Takip İçin Kolay ve Güçlü Araç | CraftOrder</title>
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
                    <span data-text-preloader="R" class="letters-loading">
                        R
                    </span>
                    <span data-text-preloader="F" class="letters-loading">
                        A
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
                         R
                     </span>
                </div>
                <p class="text-center">{{__('Uploading')}}</p>
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
                <div class="ms-lg-auto"></div>
              
                         <div class="nav_right">
                              <a href="{{ route('login') }}" class="login_btn">
                                  <div class="btn_text"><span>{{ __('Login') }}</span><span>{{ __('Login') }}</span></div>
                              </a>
                              <a href="{{ route('register') }}" class="signup_btn hover_effect">
                                  <div class="btn_text"><span>{{ __('project_management.create_free_account') }}</span>
                                   <span>{{ __('project_management.create_free_account') }}</span></div>
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
        <section class="saas_banner_area_four text-center" data-bg-color="#121217">
            <img class="banner_shap" src="landingassets/img/home-four/banner_shap.png" alt="">
            <div class="container">
                <div class="saas_banner_content_two">
                    <h2 class="title-animation">
                         <span data-parallax='{"x": -180}'>{{ __('project_management.part1') }}</span>
                         <span class="title-animation" data-parallax='{"x": 120}'>{{ __('project_management.part2') }}</span>
                     </h2>
                     
                    <p class="wow fadeInUp" data-wow-delay="0.3s">{{__('project_management.success_message')}}
                    </p>
                    <form class="mailchimp wow fadeInUp" data-wow-delay="0.6s" method="post">
                        <div class="subcribes subcribes_two  position-relative">
                            <div class="input_group">
                                <input type="text" name="EMAIL" class="form-control memail"
                                    placeholder="{{__('Enter_mail_adress')}}">
                                <img src="landingassets/img/home-three/email.png" alt="">
                            </div>
                            <button class="btn btn_submit" type="submit">{{__('Lets Start')}}</button>
                        </div>
                    </form>
                
                    <img class="banner_img wow fadeInUp" data-wow-delay="0.7s" src="landingassets/img/home-four/desktop.png"
                        alt="">
                </div>
            </div>
        </section>
        <section class="saas_features_area_three sec_padding" data-bg-color="#17171C">
            <div class="container">
               <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">{{__("section_title.title")}}</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">{{__('section_title.description')}}</p>
                </div>
                <div class="features_animation">
                    <div class="feature_item_inner">
                        <div class="row flex-row-reverse">
                            <div class="col-lg-6">
                              
                            </div>
                            <div class="col-lg-6">
                                <div class="saas_feature_content_two pe-5 wow fadeInLeft" data-wow-delay="0.3s">
                                    <h2>{{__('feature_1_title')}}</h2>
                                    <p>{{__('feature_1_description')}}</p>
                                    <ul class="list-unstyled features_list">
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_1_checklist_1')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_1_checklist_2')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_1_checklist_3')}}</li>
                                    </ul>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="feature_item_inner two">
                        <div class="row">
                            <div class="col-lg-6">
                              
                            </div>
                            <div class="col-lg-6">
                                <div class="saas_feature_content_two ps-5 wow fadeInRight" data-wow-delay="0.4s">
                                   <h2>{{__('feature_5_title')}}</h2>
                                   <p>{{__('feature_5_description')}}</p>
                                    <ul class="list-unstyled features_list">
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_5_checklist_1')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_5_checklist_2')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_5_checklist_3')}}</li></ul>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="feature_item_inner three">
                        <div class="row flex-row-reverse">
                            <div class="col-lg-6">
                            
                            </div>
                            <div class="col-lg-6">
                                <div class="saas_feature_content_two pe-5 wow fadeInLeft" data-wow-delay="0.2s">
                                   <h2>{{__('feature_4_title')}}</h2>
                                   <p>{{__('feature_4_description')}}</p>
                                    <ul class="list-unstyled features_list">
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_4_checklist_1')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_4_checklist_2')}}</li>
                                        <li><i class="fa fa-check-circle"></i>{{__('feature_4_checklist_3')}}</li>
                                   </ul>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="saas_accordion_area sec_padding" data-bg-color="#121217">
            <img class="tab_shap" src="landingassets/img/home-four/tab_bg.png" alt="">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">{{__('faq_title')}}</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">{{__('faq_description')}}</p>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <img class="accordion_img wow fadeInLeft" data-wow-delay="0.3s"
                            src="landingassets/img/home-four/app_two.png" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="accordion saas_accordion_item saas_accordion_item_two wow fadeInRight"
                            data-wow-delay="0.3s" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        {{__('faq_1.question')}}
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{__('faq_1.answer')}}
                               </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        {{__('faq_2.question')}}
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{__('faq_2.answer')}}</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        {{__('faq_3.question')}}
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{__('faq_3.answer')}} </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        {{__('faq_4.question')}}
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{__('faq_4.answer')}} </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFive" aria-expanded="false"
                                        aria-controls="collapseFive">
                                        {{__('faq_5.question')}}

                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{__('faq_5.answer')}}</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="testimonial_area_four black sec_padding" data-bg-color="#17171C">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">Helping Digital Agency Teams Feel More Organized</h2>
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
        <section class="saas_price_area saas_price_area_two sec_border sec_padding" data-bg-color="#1E1E1E">
            <img class="price_shap" src="landingassets/img/home-four/price_bg.png" alt="">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">Plans that fit your scale</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">Simple, transparent pricing that grows with you. Try
                        any plan free for 14 days.</p>
                </div>
                <ul class="nav nav-pills price_tab justify-content-center mb-5 wow fadeInUp" data-wow-delay="0.4s"
                    id="pills-tab-price" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-mon-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-month" type="button" role="tab" aria-controls="pills-month"
                            aria-selected="true">
                            Monthly billing
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-year-tab" data-bs-toggle="pill" data-bs-target="#pills-year"
                            type="button" role="tab" aria-controls="pills-year" aria-selected="false">
                            Annual billing
                        </button>
                    </li>
                </ul>
                <div class="tab-content price_content wow fadeInUp" data-wow-delay="0.6s" id="pills-tabContent-price">
                    <div class="tab-pane fade show active" id="pills-month" role="tabpanel" tabindex="0">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Starter plan</h2>
                                        <h3>Ideal for small teams and startups.</h3>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$19<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Business plan</h2>
                                        <h3>Great for agencies and busy sites.</h3>
                                        <div class="badge">Popular</div>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$49<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">5 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Enterprise plan</h2>
                                        <h3>For high traffic sites and fast syncing.</h3>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$119<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">5 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade service_tab_image" id="pills-year" role="tabpanel"
                        aria-labelledby="pills-year-tab" tabindex="0">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Starter plan</h2>
                                        <h3>Ideal for small teams and startups.</h3>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$19<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">5 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Business plan</h2>
                                        <h3>Great for agencies and busy sites.</h3>
                                        <div class="badge">Popular</div>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$69<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">5 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>Enterprise plan</h2>
                                        <h3>For high traffic sites and fast syncing.</h3>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$219<small>/mo</small></h2>
                                        <p>Billed monthly.</p>
                                        <a href="#" class="price_btn">Select this plan</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">5 site</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Real-time sync</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1k synced records<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Basic chat and email
                                            support</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">Virtual assistant support
                                        </li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10k pageviews<a
                                                href="#">?</a></li>
                                        <li><img src="landingassets/img/home-four/check.png" alt=""><img
                                                src="landingassets/img/home-four/graph.png" alt="">Google
                                            Analytics Import<a href="#">?</a></li>
                                    </ul>
                                </div>
                            </div>
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
        <section class="promo_area_dark sec_padding" data-bg-color="#121217">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="promo_content col-xl-7 text-center">
                        <h2 class="title-animation">Ready to Get Started?</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.5s">Try Flowmonk free for 14 days - setting up takes a
                            few minutes.</p>
                        <a href="#" class="saas_btn">
                            <div class="btn_text"><span>Start a Free Trail</span><span>Start a Free Trail</span></div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <footer class="footer_area_two footer_area_three" data-bg-color="#12141D">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-sm-6">
                        <div class="f_widget f_about_widget wow fadeInUp" data-wow-delay="0.2s">
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
                        <div class="f_widget f_link_widget wow fadeInUp" data-wow-delay="0.3s">
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
                        <div class="f_widget f_link_widget wow fadeInUp" data-wow-delay="0.4s">
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
                        <div class="f_widget f_newsletter_widget wow fadeInUp" data-wow-delay="0.5s">
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