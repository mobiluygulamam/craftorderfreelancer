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

    <title>{{__('freelance_crm')}}</title>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
     new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
     j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
     'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
     })(window,document,'script','dataLayer','GTM-KTMKXSWZ');</script>
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
        <section class=" p-12" data-bg-color="#121217">
            <img class="banner_shap" src="/landingassets/img/home-four/banner_shap.png" alt="">
            <div class="container text-center text-red-300">
                <div class="saas_banner_content_two">
                    <h2 class="title-animation">
                         <span data-parallax='{"x": -180}'>CraftOrder</span>
                         <span class="title-animation" data-parallax='{"x": 120}'>Gizlilik Politikası</span>
                     </h2>
                     
                
                </div>
            </div>

            
            <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">Giriş</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   CraftOrder ("biz", "bizim", "şirket") olarak, kullanıcıların gizliliğine büyük önem veriyoruz. 
                   Bu Gizlilik Politikası, CraftOrder platformunu (www.craftorder.com.tr) kullanan freelancerlar ve diğer kullanıcıların kişisel bilgilerini nasıl topladığımızı, kullandığımızı ve koruduğumuzu açıklamaktadır.
               </p>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Lütfen bu gizlilik politikasını dikkatlice okuyun. CraftOrder'ı kullanarak, bu politikada belirtilen şartlara uymayı kabul etmiş olursunuz.
               </p>
           </section>
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">1. Toplanan Bilgiler</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">CraftOrder, kullanıcılarının iş süreçlerini yönetmelerine yardımcı olmak amacıyla bazı kişisel verileri toplar. Toplanan bilgiler şunlardır:</p>
               <ul class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   <li><strong>Kişisel Bilgiler:</strong> Ad, soyad, e-posta adresi, telefon numarası, ödeme bilgileri, proje detayları.</li>
                   <li><strong>Teknik Bilgiler:</strong> IP adresleri, tarayıcı türü, işletim sistemi, oturum açma bilgileri, kullanıcı hareketleri, çerezler ve analiz verileri.</li>
                   <li><strong>Hizmet Kullanımı Bilgileri:</strong> Kullanıcıların uygulamayı nasıl kullandıkları (örneğin, hangi özelliklerin kullanıldığı, ne sıklıkla giriş yapıldığı vb.).</li>
               </ul>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">2. Bilgilerin Kullanımı</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Topladığımız kişisel bilgileri şu amaçlarla kullanıyoruz:
               </p>
               <ul class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   <li>Kullanıcılara daha iyi hizmet sunmak, taleplerini yerine getirmek.</li>
                   <li>Kullanıcı hesaplarını yönetmek ve destek sağlamak.</li>
                   <li>Hizmetlerin kalitesini artırmak için analizler yapmak.</li>
                   <li>Kullanıcılarla iletişime geçmek, önemli bildirimler ve pazarlama mesajları göndermek.</li>
                   <li>Yasal yükümlülükleri yerine getirmek ve platform güvenliğini sağlamak.</li>
               </ul>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">3. Çerezler ve İzleme Teknolojileri</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   CraftOrder, web sitemizde çerezler ve benzeri izleme teknolojilerini kullanmaktadır. Çerezler, kullanıcı deneyimini iyileştirmek, site trafiğini analiz etmek ve kullanıcı tercihlerini hatırlamak için kullanılır. 
                   Kullanıcılar, tarayıcı ayarlarından çerezleri devre dışı bırakabilirler, ancak bu durum bazı özelliklerin düzgün çalışmamasına neden olabilir.
               </p>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">4. Verilerin Paylaşımı ve İfşası</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   CraftOrder, kişisel bilgilerinizi üçüncü şahıslarla paylaşmaz, satmaz veya kiralamaz. Ancak, aşağıdaki durumlarda verilerinizi paylaşabiliriz:
               </p>
               <ul class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   <li><strong>Yasal Gereklilikler:</strong> Yasal merciler tarafından talep edilmesi durumunda, kanunlara uyum sağlamak için verilerinizi paylaşabiliriz.</li>
                   <li><strong>Hizmet Sağlayıcılar:</strong> Platformun işleyişi için gerekli olan dış hizmet sağlayıcılarla (örneğin, ödeme işlemcileri, bulut hizmetleri) verilerinizi paylaşabiliriz. Bu hizmet sağlayıcılar, yalnızca hizmetin yerine getirilmesi için gerekli olan bilgileri alır ve bu bilgileri yalnızca belirtilen amaçlar için kullanabilir.</li>
               </ul>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">5. Verilerin Güvenliği</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   CraftOrder, kullanıcı verilerini korumak için endüstri standardı güvenlik önlemleri uygular. Verilerinizi yetkisiz erişime, kullanıma, değiştirilmesine veya ifşasına karşı korumak için çeşitli şifreleme yöntemleri ve güvenlik protokolleri kullanıyoruz. 
                   Ancak, internet üzerinden yapılan veri iletiminde yüzde 100 güvenlik garantisi verilemez.
               </p>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">6. Kullanıcı Hakları</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Kullanıcılar, kendi kişisel bilgileri üzerinde çeşitli haklara sahiptir. Bu haklar şunları içerir:
               </p>
               <ul class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   <li><strong>Erişim Hakkı:</strong> Kişisel verilerinize erişim talep etme hakkınız vardır.</li>
                   <li><strong>Düzeltme Hakkı:</strong> Yanlış veya eksik kişisel verilerinizi düzeltme hakkınız vardır.</li>
                   <li><strong>Silme Hakkı:</strong> Belirli durumlarda, kişisel verilerinizin silinmesini talep edebilirsiniz.</li>
                   <li><strong>Veri Taşınabilirliği:</strong> Kişisel verilerinizi başka bir hizmet sağlayıcıya aktarılabilir biçimde talep etme hakkınız vardır.</li>
               </ul>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Kişisel verilerinizle ilgili herhangi bir talepte bulunmak için bizimle iletişime geçebilirsiniz.
               </p>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">7. Politika Değişiklikleri</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Bu Gizlilik Politikası zaman zaman güncellenebilir. Yapılan değişiklikler, politikada belirtildiği şekilde duyurulacaktır. Değişikliklerin yürürlüğe girmesinin ardından CraftOrder'ı kullanmaya devam etmeniz, bu değişiklikleri kabul ettiğiniz anlamına gelir.
               </p>
           </section>
       
           <section>
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">8. İletişim</h2>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Bu Gizlilik Politikası ile ilgili herhangi bir sorunuz veya endişeniz varsa, bizimle şu şekilde iletişime geçebilirsiniz:
               </p>
               <ul class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   <li>E-posta: [email@example.com]</li>
                   <li>Telefon: [Telefon numarası]</li>
                   <li>Adres: [Şirket adresi]</li>
               </ul>
           </section>
       
       </section>
       
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

</body>

</html>