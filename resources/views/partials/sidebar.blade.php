@php
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSetting();
        $company_logo = App\Models\Utility::get_logo();
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-4';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
        $adminSetting = App\Models\Utility::getAdminPaymentSetting();

        //    $company_logo = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $company_logo = App\Models\Utility::getcompanylogo($currentWorkspace->id);

        if ($company_logo == '' || $company_logo == null) {
            $company_logo = App\Models\Utility::get_logo();
        }
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSetting();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $settings = App\Models\Utility::getAdminPaymentSetting();

        $company_logo = App\Models\Utility::get_logo();
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $settings = App\Models\Utility::getAdminPaymentSetting();

        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp

<nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('home') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                {{-- <img src="{{ asset($logo . $company_logo . '?timestamp=' . strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')) }}" alt="logo" class="sidebar_logo_size " /> --}}
                <img src="{{ asset('../resources/'.$logo . $company_logo . '?v=' . time()) }}" alt="logo" class="sidebar_logo_size " />
                {{-- <img src="{{ url('storage/logo/logo-light.png') }}" alt="logo" class="sidebar_logo_size " /> --}}
            </a>
        </div>

        <div class="navbar-content">
            <ul class="dash-navbar">
                @if (\Auth::guard('client')->check())
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('client.home') }}"
                            class="dash-link {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }}">
                            <span class="dash-micon"><i class="ti ti-home"></i></span>
                            <span class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @else
                    <li class="dash-item dash-hasmenu ">
                        <a href="{{ route('home') }}"
                            class="dash-link  {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }}">
                            <span class="dash-micon"><i class="ti ti-home"></i></span>
                            <span class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif

                @if (isset($currentWorkspace) && $currentWorkspace)
                    @auth('web')

                     

                     @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime()) 
                        <a href="{{ route('plans.index', $currentWorkspace->slug) }}" class="dash-link "><span
                         class="dash-micon"><i data-feather="list"></i></span>
                        
                         <span
                         class="dash-mtext">{{ __('Team') }} 
                       
                        </span>
                    
                    </a> 
               
                          
                     @else   <li class="dash-item {{ Request::route()->getName() == 'users.index' ? ' active' : '' }}">
                         <a href="{{ route('users.index', $currentWorkspace->slug) }}" class="dash-link ">
                           
                              <span
                                 class="dash-micon"><i data-feather="list"></i></span><span
                                 class="dash-mtext">{{ __('Team') }}  
                                 @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime())
                                 <span class=" top-10 right-0 transform translate-x-1/2 -translate-y-1/2 badge bg-red-500 p-2 px-3 text-white text-xs font-bold rounded-full shadow-md rounded-2">
                                   Paket Yükselt
                               </span>
                   
                                 @endif</span>
                         </a>
                         
                     </li>
                        @endif

                        @if ($currentWorkspace->creater->id == Auth::user()->id)
                        
                            <li class="dash-item dash-hasmenu">
                                <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                                    class="dash-link {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }}"><span
                                        class="dash-micon"> <i class="ti ti-brand-python"></i></span><span
                                        class="dash-mtext "> {{ __('Clients') }}</span>      @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime())
                                        <span class=" top-10 right-0 transform translate-x-1/2 -translate-y-1/2 badge bg-red-500 p-2 px-3 text-white text-xs font-bold rounded-full shadow-md rounded-2">
                                          Paket Yükselt
                                      </span>

                                     
                                        @endif</a>
                                    
                            </li>
                        @endif

                        <li
                            class="dash-item {{ Request::route()->getName() == 'projects.index' || Request::segment(2) == 'projects' ? ' active' : '' }}">
                            <a href="{{ route('projects.index', $currentWorkspace->slug) }}" class="dash-link"><span
                                    class="dash-micon"> <i data-feather="briefcase"></i></span><span
                                    class="dash-mtext">{{ __('Projects') }}</span>
                                    @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime()) &nbsp;
                                 <span class=" top--10 right-0 transform translate-x-1/2 -translate-y-1/2 badge bg-red-500 p-2 px-3 text-white text-xs font-bold rounded-full shadow-md rounded-2">
                                   Paket  Yükselt
                               </span>
                             
                                 @endif
                                   </a>
                                    
                        </li>
                        @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime()) 
                        <a href="{{ route('plans.index', $currentWorkspace->slug) }}" class="dash-link "><span
                         class="dash-micon"><i data-feather="list"></i></span><span
                         class="dash-mtext">{{ __('Tasks') }}   &nbsp; <span class=" top--10 right-0 transform translate-x-1/2 -translate-y-1/2 badge bg-red-500 p-2 px-3 text-white text-xs font-bold rounded-full shadow-md rounded-2">
                              Paket Yükselt
                          </span>
                         </span></a>
                     @else   <li class="dash-item {{ Request::route()->getName() == 'tasks.index' ? ' active' : '' }}">
                         <a href="{{ route('tasks.index', $currentWorkspace->slug) }}" class="dash-link "><span
                                 class="dash-micon"><i data-feather="list"></i></span><span
                                 class="dash-mtext">{{ __('Tasks') }}
                                
                              
                              </span></a>
                           
                                 
                     </li>
                        @endif
                     

                     
                      

                   

                        {{-- @if ($currentWorkspace->creater->id == Auth::user()->id)
                            <li
                                class="dash-item {{ Request::route()->getName() == 'invoices.index' || Request::segment(2) == 'invoices' ? ' active' : '' }}">
                                <a href="{{ route('invoices.index', $currentWorkspace->slug) }}" class="dash-link"><span
                                        class="dash-micon"><i data-feather="printer"></i></span><span
                                        class="dash-mtext">{{ __('Invoices') }} </span></a>
                            </li>
                        @endif --}}

                        @if (isset($currentWorkspace) && $currentWorkspace && $currentWorkspace->creater->id == Auth::user()->id)

                        @if(App\Models\Utility::isdemopackage()|| App\Models\Utility::isFinishPackageTime()) 
                        <li class=" relative dash-item  dash-hasmenu {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? 'active' : '' }}">
                         <a href="{{ route('plans.index', $currentWorkspace->slug) }}" class="dash-link flex items-center absolute">
                             <span class="dash-micon"><i class="ti ti-device-floppy"></i></span>
                             <span class="dash-mtext left-0">{{ __('Contracts') }}</span>
                             <span class=" top--10 right-0 transform translate-x-1/2 -translate-y-1/2 badge bg-red-500 p-2 px-3 text-white text-xs font-bold rounded-full shadow-md rounded-2">
                              Paket Yükselt
                          </span>
                         </a>
                     
                     </li>
                     
                         @else
                    <li
                    class="dash-item dash-hasmenu {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? ' active' : '' }}" >
                    <a href="#" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-device-floppy"></i></span><span
                            class="dash-mtext">{{ __('Contracts') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul
                        class="dash-submenu collapse  {{ Request::route()->getName() == 'contracts.index' ? ' active' : '' }}">
                    
                        <li
                            class="dash-item {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? 'active' : '' }}">
                            <a class="dash-link"
                                href="{{ route('contracts.index', $currentWorkspace->slug) }}">{{ __('Contracts') }}</a>
                        </li>
                    
                        <li class="dash-item ">
                            <a class="dash-link"
                                href="{{ route('contract_type.index', $currentWorkspace->slug) }}">{{ __('Contract Type') }}</a>
                        </li>
                    </ul>
                    </li>
                    @endif
                  
                        @endif

                        <li class="dash-item {{ Request::route()->getName() == 'calender.index' ? ' active' : '' }}">
                            <a href="{{ route('calender.google.calendar', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="calendar"></i></span><span
                                    class="dash-mtext">{{ __('Calendar') }}</span></a>
                        </li>

                        <li class="dash-item {{ Request::route()->getName() == 'notes.index' ? ' active' : '' }}">
                            <a href="{{ route('notes.index', $currentWorkspace->slug) }}" class="dash-link "><span
                                    class="dash-micon"><i data-feather="clipboard"></i></span><span
                                    class="dash-mtext">{{ __('Notes') }} </span></a>
                        </li>

                 

                        <li
                            class="dash-item {{ Request::route()->getName() == 'project_report.index' || Request::segment(2) == 'project_report' ? ' active' : '' }}">
                            <a href="{{ route('project_report.index', $currentWorkspace->slug) }}" class="dash-link "><span
                                    class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                                    class="dash-mtext">{{ __('Project Report') }}</span></a>
                        </li>

                      

                        @elseauth
                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.projects.index' || Request::segment(3) == 'projects' ? ' active' : '' }}">
                            <a href="{{ route('client.projects.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="briefcase"></i></span><span
                                    class="dash-mtext">{{ __('Projects') }}</span></a>
                                    
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.timesheet.index' ? ' active' : '' }}">
                            <a href="{{ route('client.timesheet.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="clock"></i></span><span
                                    class="dash-mtext">{{ __('Timesheet') }}</span></a>
                        </li>

                     

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.project_report.index' || Request::segment(3) == 'project_report' ? ' active' : '' }}">
                            <a href="{{ route('client.project_report.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                                    class="dash-mtext">{{ __('Project Report') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.contracts.index' || Request::route()->getName() == 'client.contracts.show' ? 'active' : '' }}">
                            <a href="{{ route('client.contracts.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i
                                        class="ti ti-device-floppy"></i></span><span
                                    class="dash-mtext">{{ __('Contracts') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.calender.index' ? ' active' : '' }}">
                            <a href="{{ route('client.calender.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="calendar"></i></span><span
                                    class="dash-mtext">{{ __('Calendar') }}</span></a>
                        </li>

                  
                    @endauth
                @endif

                @if (Auth::user()->type == 'admin')
                    <li class="dash-item {{ Request::route()->getName() == 'admin.users.index' ? ' active' : '' }}">
                        <a href="{{ route('admin.users.index', '') }}" class="dash-link "><span class="dash-micon">
                                <i data-feather="user"></i></span><span
                                class="dash-mtext">{{ __('Company') }}</span></a>
                    </li>
                @endif
                @if (
                    Auth::user()->type == 'admin'  &&
                        Auth::user()->getGuard() != 'client')

                    <li class="dash-item {{ Request::route()->getName() == 'plans.index' ? ' active' : '' }}">
                        <a href="{{ route('plans.index') }}" class="dash-link "><span class="dash-micon"> <i
                                    class="ti ti-trophy"></i></span><span
                                class="dash-mtext">{{ __('Plans') }}</span></a>
                    </li>

                  

                    @if (Auth::user()->type == 'admin')
                        <li class="dash-item {{ request()->is('plan_request*') ? 'active' : '' }}">
                            <a href="{{ route('plan_request.index') }}" class="dash-link "><span
                                    class="dash-micon"><i class="ti ti-brand-telegram"></i></span><span
                                    class="dash-mtext">{{ __('Plan Request') }}</span></a>
                        </li>
                    @endif
                @endif

                @if (Auth::user()->type == 'admin')
              

                    <li
                        class="dash-item {{ Request::route()->getName() == 'coupons.index' || Request::segment(1) == 'coupons' ? ' active' : '' }}">
                        <a href="{{ route('coupons.index') }}" class="dash-link "><span class="dash-micon">
                                <i class="ti ti-tag"></i></span><span
                                class="dash-mtext">{{ __('Coupons') }}</span></a>
                    </li>

                    <li
                        class="dash-item {{ Request::route()->getName() == 'email_template*' || Request::segment(1) == 'email_template_lang' ? ' active' : '' }}">
                        <a class="dash-link" href="{{ route('email_template.index') }}">
                            <span class="dash-micon"><i class="ti ti-mail"></i></span><span
                                class="dash-mtext">{{ __('Email Templates') }}</span>
                        </a>
                    </li>

                    {{-- @stack('add_menu') --}}

                    <li class="dash-item {{ Request::route()->getName() == 'settings.index' ? ' active' : '' }}">
                        <a href="{{ route('settings.index') }}" class="dash-link "><span class="dash-micon"><i
                                    data-feather="settings"></i></span><span class="dash-mtext">
                                {{ __('Settings') }}</span></a>
                    </li>
                @endif

                @if (isset($currentWorkspace) &&
                        $currentWorkspace &&
                        $currentWorkspace->creater->id == Auth::user()->id &&
                        Auth::user()->getGuard() != 'client')
           

                    <li
                        class="dash-item {{ Request::route()->getName() == 'notification-templates.index' ? ' active' : '' }}">
                        <a href="{{ route('notification-templates.index', $currentWorkspace->slug) }}"
                            class="dash-link "><span class="dash-micon"><i
                                    class="ti ti-notification"></i></span><span
                                class="dash-mtext">{{ __('Notification Template') }}</span></a>
                    </li>

                    <li class="dash-item {{ Request::route()->getName() == 'workspace.settings' ? ' active' : '' }}">
                        <a href="{{ route('workspace.settings', $currentWorkspace->slug) }}"
                            class="dash-link "><span class="dash-micon"><i data-feather="settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span></a>
                    </li>
                @endif
        </div>
    </div>
</nav>
