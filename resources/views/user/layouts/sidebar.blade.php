<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="{{ asset('index.html') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="24">
                    </span>
                </a>
                <a href="{{ asset('index.html') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ asset('#sidebarDashboards') }}" data-bs-toggle="collapse">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarDashboards">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ asset('index.html') }}" class="tp-link">CRM</a>
                            </li>
                            <li>
                                <a href="{{ asset('analytics.html') }}" class="tp-link">Analytics</a>
                            </li>
                            <li>
                                <a href="{{ asset('ecommerce.html') }}" class="tp-link">eCommerce</a>
                            </li>
                        </ul>
                    </div>
                </li>


             
                <li class="menu-title mt-2">Apps</li>
                
                <li>
                    <a href="#" class="tp-link">
                        <i data-feather="columns"></i>
                        <span> User </span>
                    </a>
                </li>

               
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>