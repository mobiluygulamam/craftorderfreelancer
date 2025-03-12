@php
    use App\Models\Utility;
    $setting = Utility::getAdminPaymentSetting();
@endphp
<script async src="https://www.googletagmanager.com/gtag/js?id=G-0RVE9FXT1V"></script>

<script>

window.dataLayer = window.dataLayer || [];

function gtag(){dataLayer.push(arguments);}

gtag('js', new Date());

  

gtag('config', 'G-0RVE9FXT1V');
<!--Start of Tawk.to Script-->

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
</script>
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <span class="text-muted" style="text-align: left;">
                &copy; {{ date('Y') }}
                {{ $setting['footer_text'] ? $setting['footer_text'] : config('app.name', 'Taskly') }}
            </span>
        </div>
    </div>
</footer>
