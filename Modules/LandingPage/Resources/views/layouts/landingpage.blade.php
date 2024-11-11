<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

    <title>{{__('freelance_crm')}}</title>
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
  
        <section class="saas_price_area saas_price_area_two sec_border sec_padding" data-bg-color="#1E1E1E">
            <img class="price_shap" src="landingassets/img/home-four/price_bg.png" alt="">
            <div class="container">
                <div class="section_title white text-center">
                    <h2 class="wow fadeInUp" data-wow-delay="0.2s">{{__('princing.title')}}</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.3s">{{__('princing.description')}}
                </p>
                </div>
                <ul class="nav nav-pills price_tab justify-content-center mb-5 wow fadeInUp" data-wow-delay="0.4s"
                    id="pills-tab-price" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-mon-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-month" type="button" role="tab" aria-controls="pills-month"
                            aria-selected="true">
                            {{__('freelancerspecial')}}
                        </button>
                    </li>
                
                </ul>
                <div class="tab-content price_content wow fadeInUp" data-wow-delay="0.6s" id="pills-tabContent-price">
                    <div class="tab-pane fade show active" id="pills-month" role="tabpanel" tabindex="0">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>{{__('freelancerKickoff')}}</h2>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$3<small>/{{__('Per week')}}</small></h2>
                                        <p>{{__('Weekly Biling')}}</p>
                                        <a href="#" class="price_btn">{{__('select_this_plan')}}</a>

                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">1 {{__('gb_file_uploading')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">2 {{__('team_members')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">3 {{__('project_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">3 {{__('Contract_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">3 {{__('customer_management')}}</li>

                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_task_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                        <li><img src="landingassets/img/home-four/graph.png" alt="">{{__('AI')}} <a href="#">?</a></li>
                                        
                                 
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>{{__('freelancerEssentials')}}</h2>

                                  
                                        <div class="badge">Popular</div>
                                    </div>
                                    <div class="price_middle">
                                        <h2>$9<small>/{{__('Per month')}}</small></h2>
                                        <p>{{__('Monthly Biling')}}</p>
                                        <a href="#" class="price_btn">{{__('select_this_plan')}}</a>

                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10 {{__('gb_file_uploading')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">10 {{__('team_members')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{ __('project_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('Contract_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('customer_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_task_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                        <li><img src="landingassets/img/home-four/graph.png" alt="">{{__('AI')}} <a href="#">?</a></li>
                                        
                                 
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header">
                                        <h2>{{__('masterFreelance')}}</h2>
                              
                                    </div>
                                    <div class="price_middle">
                                        <h2>$99<small>/{{__('Per Year')}}</small></h2>
                                        <p>{{__('Annual Billing')}}</p>
                                        <a href="#" class="price_btn">{{__('select_this_plan')}}</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="landingassets/img/home-four/check.png" alt="">100 {{__('gb_file_uploading')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('team_members')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{ __('project_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('Contract_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('customer_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_task_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                        <li><img src="landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                        <li><img src="landingassets/img/home-four/graph.png" alt="">{{__('AI')}} <a href="#">?</a></li>
                                        
                                 
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
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">@lang('service_section_title')</h2>
                  <p class="wow fadeInUp" data-wow-delay="0.3s">@lang('service_section_subtitle')</p>
              </div>
              <div class="row">
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.2s">
                          <h4>@lang('service_item_1_title')</h4>
                          <p>@lang('service_item_1_desc')</p>
                      </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.3s">
                          <h4>@lang('service_item_2_title')</h4>
                          <p>@lang('service_item_2_desc')</p>
                      </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.4s">
                          <h4>@lang('service_item_3_title')</h4>
                          <p>@lang('service_item_3_desc')</p>
                      </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.5s">
                          <h4>@lang('service_item_4_title')</h4>
                          <p>@lang('service_item_4_desc')</p>
                      </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.6s">
                          <h4>@lang('service_item_5_title')</h4>
                          <p>@lang('service_item_5_desc')</p>
                      </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                      <div class="service_item service_item_dark wow fadeInUp" data-wow-delay="0.7s">
                          <h4>@lang('service_item_6_title')</h4>
                          <p>@lang('service_item_6_desc')</p>
                      </div>
                  </div>
              </div>
          </div>
      </section>
      
      
      
        <section class="promo_area_dark sec_padding" data-bg-color="#121217">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="promo_content col-xl-7 text-center">
                        <h2 class="title-animation">{{__('ready_to_get_started')}}</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.5s">{{__('try_craftorder_free')}}</p>
                        <a href="#" class="saas_btn">
                            <div class="btn_text"><span>{{__('project_management.create_free_account')}}</span><span>{{__('project_management.create_free_account')}}</span></div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
  
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