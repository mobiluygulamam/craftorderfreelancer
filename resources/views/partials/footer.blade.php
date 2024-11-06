@php
    use App\Models\Utility;
    $setting = Utility::getAdminPaymentSetting();
@endphp
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
