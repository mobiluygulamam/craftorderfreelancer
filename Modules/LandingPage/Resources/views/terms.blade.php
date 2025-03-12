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
        <section class="saas_banner_area_four  h-full text-white p-12" data-bg-color="#121217">
            <img class="banner_shap" src="/landingassets/img/home-four/banner_shap.png" alt="">
            <div class="container text-center text-red-300">
                <div class="saas_banner_content_two">
                    <h2 class="title-animation">
                         <span data-parallax='{"x": -180}'>CraftOrder</span>
                         <span class="title-animation" data-parallax='{"x": 120}'>Kullanım Koşulları</span>
                     </h2>
                     
                
                </div>
            </div>

            
            <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">1. Genel Şartlar</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, freelancerlar ve solopreneurlar için geliştirilmiş bir CRM (Müşteri İlişkileri Yönetimi) uygulamasıdır. Bu uygulama, kullanıcıların iş süreçlerini yönetmelerine yardımcı olmak amacıyla geliştirilmiştir. CraftOrder’ın tüm hizmetleri, aşağıdaki koşullar altında sunulmaktadır.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">2. Hesap Oluşturma ve Kullanıcı Sorumlulukları</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder'ı kullanmak için kullanıcıların geçerli bir e-posta adresi ve doğru bilgileri sağlamaları gerekmektedir. Kullanıcı, sağladığı bilgilerin doğruluğundan ve güncelliğinden sorumludur.
               </p>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Kullanıcılar, CraftOrder'a erişim sağlayabilmek için sadece kendilerine ait olan hesap bilgilerini kullanmalı ve başkalarının hesap bilgileriyle giriş yapmamalıdır. Hesabınızla ilgili herhangi bir güvenlik ihlali veya yetkisiz kullanım durumunda hemen bize bildirimde bulunmalısınız.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">3. Hizmetlerin Kullanımı</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, kullanıcıların iş süreçlerini yönetebileceği çeşitli araçlar sunmaktadır. Bu araçların kullanımı, sadece kişisel ve ticari amaçlarla olmalıdır.
               </p>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Kullanıcılar, CraftOrder'ı kullanırken tüm yasalara ve düzenlemelere uymak zorundadır. Aksi takdirde hesapları askıya alınabilir veya kapatılabilir. CraftOrder, kullanıcıların oluşturduğu içeriği izlemek ve denetlemek hakkını saklı tutar. Ancak, içeriklerin tüm sorumluluğu kullanıcılara aittir.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">4. Fikri Mülkiyet</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, tüm yazılım, tasarım, metinler, grafikler, logolar ve diğer içeriklerin fikri mülkiyet haklarına sahiptir. Kullanıcılar, CraftOrder'da bulunan içeriği izinsiz olarak kopyalayamaz, değiştiremez veya başka bir şekilde kullanamazlar.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">5. Gizlilik</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, kullanıcıların gizliliğine saygı göstermekte ve kişisel bilgilerini korumak için gerekli önlemleri almaktadır. Kullanıcıların kişisel bilgileri, yalnızca gizlilik politikamızda belirtilen şekilde kullanılacaktır. Kullanıcılar, kişisel bilgilerinin nasıl toplandığı, kullanıldığı ve saklandığı hakkında gizlilik politikamızı incelemelidir.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">6. Ücretler ve Ödemeler</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, ücretsiz ve ücretli Paketler sunmaktadır. Ücretli Paketler, belirtilen fiyatlarla sunulacaktır. Kullanıcılar, seçtikleri planı satın alırken, belirtilen ödeme yöntemini kullanmak zorundadır.
               </p>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   Ücretli Paketlerın otomatik yenileme özelliği olabilir. Kullanıcılar, yenileme işlemlerini iptal etmek için belirli bir süre önce bildirimde bulunmalıdır.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">7. Hesap İptali ve Kullanımın Sonlandırılması</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   Kullanıcılar, istedikleri zaman CraftOrder hesabını iptal edebilirler. Hesap iptali işlemi, kullanıcıların tüm verilerinin silinmesine neden olabilir. CraftOrder, herhangi bir kullanıcıyı, kullanıcı koşullarını ihlal ettiği takdirde, bildirimde bulunmaksızın hesaplarını askıya alma veya sonlandırma hakkını saklı tutar.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">8. Sorumluluk Reddi</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, hizmetlerinin kesintisiz, hatasız ve güvenli olacağını garanti etmez. Kullanıcılar, hizmeti kullandıkları sürece oluşabilecek her türlü veri kaybı veya zarar riskini kabul ederler.
               </p>
               <p class="wow fadeInUp text-white" data-wow-delay="0.3s">
                   CraftOrder, üçüncü taraf hizmet sağlayıcılarının faaliyetlerinden veya hizmetlerinden sorumlu değildir. Bu hizmet sağlayıcılar, yalnızca kullanıcıların daha verimli kullanabilmesi için entegre edilmiştir.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">9. Değişiklikler ve Güncellemeler</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   CraftOrder, kullanım koşullarını zaman zaman değiştirme hakkını saklı tutar. Değişiklikler, bu sayfada yayımlandığında geçerli olacaktır. Kullanıcılar, bu sayfadaki güncellemeleri düzenli olarak takip etmeli ve uygulama üzerinde yapılan değişiklikleri kabul etmelidir.
               </p>
           </section>
           
           <section class="text-left">
               <h2 class="wow fadeInUp text-white" data-wow-delay="0.3s">10. Uygulanan Hukuk ve Yargı Yetkisi</h2>
               <p class="wow fadeInUp text-white-100" data-wow-delay="0.3s">
                   İşbu kullanım koşulları Türkiye Cumhuriyeti yasalarına tabidir. Kullanıcılar, herhangi bir anlaşmazlık durumunda İstanbul Mahkemeleri ve İcra Daireleri’nin yetkili olduğunu kabul ederler.
               </p>
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