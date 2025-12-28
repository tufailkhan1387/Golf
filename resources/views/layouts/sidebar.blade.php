<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left active">
       <h2 class="text-danger" style="font-weight: 800">Auto Golf</h2>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('public/assets/img/timbor-logo.png') }}" alt="">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-white">
            <img src="{{ asset('public/assets/img/timbor-logo.png') }}" alt="">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-small">
            <img src="{{ asset('public/assets/img/timbor-logo.png') }}" alt="">
        </a>
        <a id="toggle_btn" href="javascript:void(0);">
            <i data-feather="chevrons-left" class="feather-16"></i>
        </a>

    </div>
    <!-- /Logo -->

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <!-- Header Menu -->
    <ul class="nav user-menu">

        <li class="nav-item dropdown has-arrow main-drop">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-letter">
                        <img src="{{ asset('public/assets/img/profiles/avator1.jpg') }}" alt=""
                            class="img-fluid">
                    </span>
                    <span class="user-detail">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">Super Admin</span>
                    </span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profilename">
                    <div class="profileset">
                        <span class="user-img"><img src="{{ asset('public/assets/img/profiles/avator1.jpg') }}"
                                alt="">
                            <span class="status online"></span></span>
                        <div class="profilesets">
                            <h6>{{ Auth::user()->name }}</h6>
                            <h5>Super Admin</h5>
                        </div>
                    </div>
                    <hr class="m-0">
                    <a class="dropdown-item" > <i class="me-2" data-feather="user"></i>
                        My
                        Profile</a>

                    <hr class="m-0">
                    <a class="dropdown-item logout pb-0" ><img
                            src="{{ asset('assets/img/icons/log-out.svg') }}" class="me-2"
                            alt="img">Logout</a>
                </div>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" >My Profile</a>
            <a >Logout</a>
        </div>
    </div>
    <!-- /Mobile Menu -->
</div>
<!-- /Header -->

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu-open">
                  
                        <ul>
                            <li class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}"><a
                                    href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i><span>Dashboard</span></a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}"><a
                                    href="{{ route('admin.users') }}"><i data-feather="users"></i><span>Users</span></a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.reports' ? 'active' : '' }}"><a
                                    href="{{ route('admin.reports') }}"><i data-feather="bar-chart-2"></i><span>Reports</span></a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.plans' ? 'active' : '' }}"><a
                                    href="{{ route('admin.plans') }}"><i data-feather="package"></i><span>Subscription Plans</span></a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.email-notifications' ? 'active' : '' }}"><a
                                    href="{{ route('admin.email-notifications') }}"><i data-feather="mail"></i><span>Email Notifications</span></a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.push-notifications' ? 'active' : '' }}"><a
                                    href="{{ route('admin.push-notifications') }}"><i data-feather="bell"></i><span>Push Notifications</span></a>
                            </li>
                           

                        </ul>
                  
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->

<!-- Sidebar -->
<div class="sidebar collapsed-sidebar" id="collapsed-sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu-2" class="sidebar-menu sidebar-menu-three">

            <div class="tab-content tab-content-four pt-2">
                <ul class="tab-pane" id="product" aria-labelledby="messages-tab">
                    <li class="submenu-open">
                      
                            <ul>
                                <li class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}"><a
                                        href="{{ route('admin.dashboard') }}"><i
                                            data-feather="grid"></i><span>Dashboard</span></a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}"><a
                                        href="{{ route('admin.users') }}"><i
                                            data-feather="users"></i><span>Users</span></a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'admin.reports' ? 'active' : '' }}"><a
                                        href="{{ route('admin.reports') }}"><i
                                            data-feather="bar-chart-2"></i><span>Reports</span></a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'admin.plans' ? 'active' : '' }}"><a
                                        href="{{ route('admin.plans') }}"><i
                                            data-feather="package"></i><span>Subscription Plans</span></a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'admin.email-notifications' ? 'active' : '' }}"><a
                                        href="{{ route('admin.email-notifications') }}"><i
                                            data-feather="mail"></i><span>Email Notifications</span></a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'admin.push-notifications' ? 'active' : '' }}"><a
                                        href="{{ route('admin.push-notifications') }}"><i
                                            data-feather="bell"></i><span>Push Notifications</span></a>
                                </li>
                             
                      
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- /Sidebar -->

<!-- Sidebar -->
<div class="sidebar horizontal-sidebar">
    <div id="sidebar-menu-3" class="sidebar-menu">
        <ul class="nav">
            <li class="submenu-open">
               
                    <ul>
                        <li class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}"><a
                                href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i><span>Dashboard</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}"><a
                                href="{{ route('admin.users') }}"><i data-feather="users"></i><span>Users</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.reports' ? 'active' : '' }}"><a
                                href="{{ route('admin.reports') }}"><i data-feather="bar-chart-2"></i><span>Reports</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.plans' ? 'active' : '' }}"><a
                                href="{{ route('admin.plans') }}"><i data-feather="package"></i><span>Subscription Plans</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.email-notifications' ? 'active' : '' }}"><a
                                href="{{ route('admin.email-notifications') }}"><i data-feather="mail"></i><span>Email Notifications</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.push-notifications' ? 'active' : '' }}"><a
                                href="{{ route('admin.push-notifications') }}"><i data-feather="bell"></i><span>Push Notifications</span></a>
                        </li>

                     

                    </ul>
              
            </li>
        </ul>
    </div>
</div>
<!-- /Sidebar -->
