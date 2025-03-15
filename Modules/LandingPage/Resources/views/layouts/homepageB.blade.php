<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/landingassets/img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="/landingassets/vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/landingassets/vendors/themify-icon/themify-icons.css" rel="stylesheet">
    <link href="/landingassets/vendors/icomoon/style.css" rel="stylesheet">
    <link href="/landingassets/css/font-awesome.min.css" rel="stylesheet">
    <link href="/landingassets/vendors/slick/slick.css" rel="stylesheet">
    <link href="/landingassets/vendors/slick/slick-theme.css" rel="stylesheet">
    <link href="/landingassets/vendors/animation/animate.css" rel="stylesheet">
    <link href="/landingassets/css/style.css" rel="stylesheet">
    <link href="/landingassets/css/responsive.css" rel="stylesheet">

    <!-- Slick Slider CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <title>{{__('freelance_crm')}}</title>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
     new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
     j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
     'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
     })(window,document,'script','dataLayer','GTM-KTMKXSWZ');</script>
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
     <!-- End Google Tag Manager -->

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
                    <img src="/landingassets/img/logo.png" alt="logo" height="50" width="50">
                    <img src="/landingassets/img/logo.png" height="50" width="50">
                   
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
            <img class="banner_shap" src="/landingassets/img/home-four/banner_shap.png" alt="">
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
                                <img src="/landingassets/img/home-three/email.png" alt="">
                            </div>
                            <button class="btn btn_submit" type="submit">{{__('Lets Start')}}</button>
                        </div>
                    </form>
                
                    <img class="banner_img wow fadeInUp" data-wow-delay="0.7s" src="/landingassets/img/home-four/desktop.png"
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
                    <div class="feature_item_inner ">
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
<div class="h-32"></div>
        <section class="testimonial_area_four black sec_padding" data-bg-color="#17171C">
          <div class="container">
              <div class="section_title white text-center mb-6">
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">Craft Order Kullananlar Global Markalarla Çalışıyor</h2>
               
              </div>
      
              <!-- Logo Slider -->
              <div class="logo_slider_container">
                  <div class="slick-slider">
               
                    <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/tesla.png" alt="tesla" class="w-8 h-8 rounded-lg">
                     </div>
                     
                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/google.png" alt="google" class="w-8 h-8 rounded-lg">
                     </div>
              
                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/intel.png" alt="intel" class="w-8 h-8 rounded-lg">
                     </div>

                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/ikas.png" alt="ikas" class="w-8 h-8 rounded-lg">
                     </div>
              
                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/samsung.png" alt="samsung" class="w-8 h-8 rounded-lg">
                     </div>
                     

                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/ibm.png" alt="ibm" class="w-8 h-8 rounded-lg">
                     </div>

                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/amazon.png" alt="amazon" class="w-8 h-8 rounded-lg">
                     </div>
              
                     <div class="logo_item w-8 h-8 rounded-lg">
                         <img src="/landingassets/img/brands/apple.png" alt="apple" class="w-8 h-8 rounded-lg">
                     </div>
                

                  <div class="logo_item w-8 h-8 rounded-lg">
                    <img src="/landingassets/img/brands/meta.png" alt="meta" class="w-8 h-8 rounded-lg">
                </div>

                <div class="logo_item w-8 h-8 rounded-lg">
                    <img src="/landingassets/img/brands/shopify.png" alt="shopify" class="w-8 h-8 rounded-lg">
                </div>
         
               </div>
              </div>
          </div>
      </section>


    <div class="h-32"></div>

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
      <section class="saas_accordion_area sec_padding bg-opacity-50" data-bg-color="#121217">
          <img class="tab_shap opacity-50" src="/landingassets/img/home-four/tab_bg.png" alt=" ">
          <div class="container">
              <div class="section_title white text-center">
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">{{__('faq_title')}}</h2>
                  <p class="wow fadeInUp" data-wow-delay="0.3s">{{__('faq_description')}}</p>
              </div>
              <div class="row">
                  <div class="col-lg-6">
                      <img class="accordion_img wow fadeInLeft" data-wow-delay="0.3s"
                          src="/landingassets/img/home-four/app_two.png" alt="">
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
      <div class="h-32"></div>
      <section class="testimonial_area_four black sec_padding" data-bg-color="#17171C">
          <div class="container">
              <div class="section_title white text-center fs-1">
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">Serbest Çalışanlar</h2>
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">Proje Yönetimlerinde</h2>
                  <h2 class="wow fadeInUp" data-wow-delay="0.2s">Craft Order'ı Tercih Ediyor</h2>
                  <p class="wow fadeInUp" data-wow-delay="0.3s">CraftOrder, küçük işletmeler ve freelancerlar için en iyi CRM çözümüdür.<br> 
                  Müşteri takibinizi ve projelerinizi düzenli bir şekilde yönetmenize yardımcı olur.</p>
              </div>
              <div class="testimonial_slider_one wow fadeInUp" data-wow-delay="0.4s">
                  <!-- Kullanıcı 1 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder sayesinde müşteri ilişkilerimi daha iyi yönetiyorum. Her projeyi kolayca takip edebiliyorum ve ödeme sistemleriyle entegre olması müthiş!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                    
                          <div class="text">
                              <h5>Ali Yılmaz</h5>
                              <h6>Dijital Pazarlama Danışmanı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 2 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"Freelancer olarak çalışırken proje takibini hep zor bulurdum, ancak CraftOrder ile işlerimi çok daha düzenli ve kolay bir şekilde yürütüyorum!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                     
                          <div class="text">
                              <h5>Emine Kaya</h5>
                              <h6>Grafik Tasarımcı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 3 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder, iş akışımı düzenlememi sağladı. Müşteri takibi yapmak ve projeleri organize etmek artık çok daha kolay ve verimli!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                       
                          <div class="text">
                              <h5>Ahmet Demir</h5>
                              <h6>Web Geliştirici</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 4 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder kullanarak her müşteri için proje oluşturuyor ve ilerlemeyi hızlıca takip edebiliyorum. İşlerimi gerçekten organize etmek çok daha kolay oldu!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                     
                          <div class="text">
                              <h5>Seda Öztürk</h5>
                              <h6>Sosyal Medya Yöneticisi</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 5 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"Müşteri ilişkilerimi ve projelerimi bir arada yönetmek benim için zor oluyordu, fakat CraftOrder ile tüm süreci çok rahat bir şekilde takip ediyorum."</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
               
                          <div class="text">
                              <h5>Mehmet Aydın</h5>
                              <h6>Fotoğrafçı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 6 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder, freelancer olarak benim için hayatı kolaylaştıran bir araç oldu. Proje yönetimi ve müşteri takibini hızla yapabiliyorum. Artık her şey daha düzenli."</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                   
                          <div class="text">
                              <h5>Melis Arslan</h5>
                              <h6>İçerik Yazarı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 7 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder sayesinde müşteri projelerimi daha düzenli takip ediyorum ve zamanımı daha verimli kullanıyorum. Gerçekten freelancerlar için çok faydalı bir platform!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                 
                          <div class="text">
                              <h5>Burak Kılıç</h5>
                              <h6>Uygulama Geliştiricisi</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 8 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder sayesinde her müşteri için projelerimi sistematik bir şekilde yönetebiliyorum. Verimliliğim arttı ve işlerimi zamanında teslim ediyorum."</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                          <div class="text">
                              <h5>Zeynep Yıldız</h5>
                              <h6>E-ticaret Danışmanı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 9 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder sayesinde işlerimi çok daha düzenli hale getirdim. Her proje için detaylı notlar alabiliyorum ve süreçlerimi takip etmek çok kolay!"</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                      
                          <div class="text">
                              <h5>Kaan Yüksel</h5>
                              <h6>Pazarlama Uzmanı</h6>
                          </div>
                      </div>
                  </div>
      
                  <!-- Kullanıcı 10 -->
                  <div class="item">
                      <div class="ratting">
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                          <i class="fa fa-star" aria-hidden="true"></i>
                      </div>
                      <p>"CraftOrder ile video prodüksiyon süreçlerimi mükemmel bir şekilde yönetiyorum. Müşteri takibi ve projeler çok daha verimli bir şekilde ilerliyor."</p>
                      <div class="quote_icon d-flex align-items-center justify-content-between">
                          <div class="icon">
                              <img src="assets/img/home-two/quote.png" alt="">
                          </div>
                      </div>
                      <div class="client_info">
                        
                          <div class="text">
                              <h5>Hande Çelik</h5>
                              <h6>Video Yapımcı</h6>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </section>
      
        
        <section class="saas_price_area saas_price_area_two sec_border sec_padding" data-bg-color="#1E1E1E">
            <img class="price_shap" src="/landingassets/img/home-four/price_bg.png" alt="">
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
                         @php 

                         $objUser = Auth::check() ?Auth::user():"";
                         
                         $monthly = App\Models\Plan::where('id', 3)->first();
                         $annually = App\Models\Plan::where('id', 4)->first();
                                                                 @endphp
                                                                    <div class="col-lg-4 col-md-6">
                                                                      <div class="saas_price_item">
                                                                          <div class="price_header">
                                                                              <h2>{{__('Demo')}}</h2>
                                      
                                                                        
                                                                        
                                                                          </div>
                                                                          <div class="price_middle">
                                                                              <h2>0 ₺<small>/1 Hafta</small></h2>
                                                                            
                                                 
                                                                              <a href="{{route('register')}}"class="price_btn">
                                                                                   {{ __('Register') }}
                                                                               </a>
                                                                          </div>
                                                                          <ul class="list-unstyled">
                                                                       
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">1 {{__('team_members')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">5 {{ __('project_management')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">5 {{__('Contract_management')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">5 {{__('customer_management')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">5 {{__('unlimited_task_management')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                                                              <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                                       
                                                                              
                                                                       
                                                                          </ul>
                                                                      </div>
                                                                  </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                    <div class="price_header text-center">
                                        <h2>{{__('Solopreneur & Freelancer Starter')}}</h2>

                                  
                                      
                                    </div>
                                    <div class="price_middle">
                                        <h2>1000 ₺<small>/{{__('Per month')}}</small></h2>
                                        <p>{{__('Monthly Biling')}}</p>
           
                                        <a href="{{ Auth::check() ?  route('payment', ['monthly', \Illuminate\Support\Facades\Crypt::encrypt($monthly->id)])  : route('login') }}" class="price_btn">
                                             {{ __('select_this_plan') }}
                                         </a>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">10 {{__('team_members')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">10 {{ __('project_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">10 {{__('Contract_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">10 {{__('customer_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('unlimited_task_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                        
                                 
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="saas_price_item">
                                 
                                    <div class="price_header text-center">
                                        <div class="badge m-5">Tercih Edilen</div>
                                        <h2>{{__('Solopreneur & Freelancer Premium')}} </h2>
                                    </div>
                                  
                                    <div class="price_middle">
                                        <h2>9000 ₺<small>/{{__('Per Year')}}</small></h2>
                                        <p>{{__('Annual Billing')}} <span class=" text-white-50 opacity-10 p-10">{{__('3 ay hediyeli')}}</span></p>
                                        
                                        <a href="{{ Auth::check() ?  route('payment', ['annually', \Illuminate\Support\Facades\Crypt::encrypt(value: $annually->id)])  : route('login') }}" class="price_btn">
                                             {{ __('select_this_plan') }}
                                         </a>                                    </div>
                                    <ul class="list-unstyled">
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('team_members')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{ __('project_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('Contract_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{ __('unlimited')}} {{__('customer_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('unlimited_task_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('unlimited_note_management')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('shared_project')}}</li>
                                        <li><img src="/landingassets/img/home-four/check.png" alt="">{{__('special_community_access')}}<a href="#" class="text-white">?</a></li>
                                        
                                 
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </section>
    
      
      
        <section class="promo_area_dark two sec_padding flex flex-col-reverse" data-bg-color="#121217">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="promo_content col-xl-7 text-center">
                        <h2 class="title-animation">{{__('ready_to_get_started')}}</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.5s">{{__('try_craftorder_free')}}</p>
                        <a href="{{ route('register') }}" class="saas_btn">
                            <div class="btn_text"><span>{{__('project_management.create_free_account')}}</span><span>{{__('project_management.create_free_account')}}</span></div>
                        </a>
                        <a href="{{ route('home.becomepartner') }}" class="saas_btn">
                         <div class="btn_text"><span>Partner ol</span><span>Partner ol</span></div>
                     </a>
                    </div>
                </div>
            </div>
        </section>
  
    </div>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KTMKXSWZ"
     height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
     <!-- End Google Tag Manager (noscript) -->
    <!-- Optional JavaScript; choose one of the two! -->
    <script src="/landingassets/js/jquery-3.6.0.min.js"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->

    <script src="/landingassets/vendors/bootstrap/js/popper.min.js"></script>
    <script src="/landingassets/vendors/bootstrap/js/bootstrap.min.js"></script>
    <script src="/landingassets/vendors/slick/slick.min.js"></script>
    <script src="/landingassets/vendors/parallax/jquery.parallax-scroll.js"></script>
    <script src="/landingassets/js/gsap.min.js"></script>
    <script src="/landingassets/js/SplitText.js"></script>
    <script src="/landingassets/js/ScrollTrigger.min.js"></script>
    <script src="/landingassets/js/SmoothScroll.js"></script>
    <script src="/landingassets/vendors/wow/wow.min.js"></script>
    <script src="/landingassets/js/custom.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0RVE9FXT1V"></script>

<script>

window.dataLayer = window.dataLayer || [];

function gtag(){dataLayer.push(arguments);}

gtag('js', new Date());

  

gtag('config', 'G-0RVE9FXT1V');

</script>

<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function(){
        $('.slick-slider').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: true,
            dots: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                    }
                }
            ]
        });
    });
</script>


<!--Start of Tawk.to Script-->
<script type="text/javascript">
     var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
     (function(){
     var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
     s1.async=true;
     s1.src='https://embed.tawk.to/67d12eb7aa6e73190c123622/1im4hsvlm';
     s1.charset='UTF-8';
     s1.setAttribute('crossorigin','*');
     s0.parentNode.insertBefore(s1,s0);
     })();
     </script>
     <!--End of Tawk.to Script-->
</body>

</html>