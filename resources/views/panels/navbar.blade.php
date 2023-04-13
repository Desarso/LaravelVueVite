@if ($configData['mainLayoutType'] == 'horizontal' && isset($configData['mainLayoutType']))
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item"><a class="navbar-brand" href="dashboard-tasks">
                        <div class="brand-logo"></div>
                    </a></li>
            </ul>
        </div>
    @else
        <nav
            class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
@endif
<div class="navbar-wrapper">
    <div class="navbar-container content">
        <div class="navbar-collapse" id="navbar-mobile">
            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav">
                    <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                            class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                class="ficon feather icon-menu"></i></a></li>
                </ul>
                <ul class="nav navbar-nav bookmark-icons">

                    @foreach ($shortcuts as $item)
                        <li class="nav-item d-none d-lg-block"><a class="nav-link" data-url="{{ $item->url }}"
                                href="{{ $item->url }}" data-toggle="tooltip" data-placement="top"
                                title="{{ $item->name }}"><i class="ficon {{ $item->icon }}"></i></a></li>
                    @endforeach
                    <!--
              <li class="nav-item d-none d-lg-block"><a class="nav-link" data-name="dashboard-tasks" href="dashboard-tasks" data-toggle="tooltip" data-placement="top" title="Inicio"><i class="ficon far fa-home-lg-alt"></i></a></li>
              <li class="nav-item d-none d-lg-block"><a class="nav-link" data-name="dashboard-dashboard" href="config-dashboard" data-toggle="tooltip" data-placement="top" title="Configurations"><i class="ficon feather icon-server"></i></a></li>
              <li class="nav-item d-none d-lg-block"><a class="nav-link" href="config-items" data-toggle="tooltip" data-placement="top" title="Ítems"><i class="ficon fad fa-tools" style="color: #C72A1B"></i></a></li>
              <li class="nav-item d-none d-lg-block"><a class="nav-link" href="config-spots" data-toggle="tooltip" data-placement="top" title="Spots"><i class="ficon fad fa-store-alt"></i></a></li>
              <li class="nav-item d-none d-lg-block"><a class="nav-link" href="config-users" data-toggle="tooltip" data-placement="top" title="Usuarios"><i class="ficon fad fa-users"></i></a></li>
              <li class="nav-item d-none d-lg-block"><a class="nav-link" href="config-apps" data-toggle="Apps" data-placement="top" title="Apps"><i class="ficon fad fa-shapes" style="color: #41AD9D"></i></a></li>
              -->
                </ul>
                <ul class="nav navbar-nav">
                <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i
                            class="ficon feather icon-star warning"></i></a>
                    <div class="bookmark-input search-input">
                        <div class="bookmark-input-icon"><i class="feather icon-search primary"></i></div>
                        <input class="form-control input" type="text" placeholder="Buscar en Whagons ..."
                            tabindex="0" data-search="laravel-search-list" />
                        <ul class="search-list search-list-bookmark">
                        </ul>
                    </div>
                    <!-- select.bookmark-select-->
                    <!--   option 1-Column-->
                    <!--   option 2-Column-->
                    <!--   option Static Layout-->
                </li>
                </ul>
            </div>
            <ul class="nav navbar-nav float-right">

                <!--TODO matthias comentar -->
                <li class="nav-item" style="display: flex;
                padding: 10px;">
                <button id="btnLog" type="button"
                class="btn btn-outline-light round mr-1 waves-effect waves-light">{{ __('locale.Log') }}</button>
            </li>

                <li class="dropdown dropdown-language nav-item" style="display:block;">
                    <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="{{ url('lang/en') }}" data-language="en">
                            <i class="flag-icon flag-icon-us"></i>English
                        </a>
                        <a class="dropdown-item" href="{{ url('lang/es') }}" data-language="es">
                            <i class="flag-icon flag-icon-es"></i>Español
                        </a>

                    </div>
                </li>

                <li class="nav-item"><a class="nav-link" id="darkSwitch"><i class="fa fa-moon-o fa-icon-maximize"
                            id="icon-mode"></i></a></li>

                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                            class="ficon feather icon-maximize"></i></a></li>
                <!--
                            <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i
                            class="ficon feather icon-search"></i></a>
                    <div class="search-input">
                        <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                        <input class="input" type="text" placeholder="Explore Whagons..." tabindex="-1"
                            data-search="laravel-search-list" />
                        <div class="search-input-close"><i class="feather icon-x"></i></div>
                        <ul class="search-list search-list-main"></ul>
                    </div>
                </li>
            -->
                <li id="dropdown-notification" class="dropdown dropdown-notification nav-item"><a
                        class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i
                            class="ficon feather icon-bell"></i><span id="badge-unread-notification"
                            class="badge badge-pill badge-primary badge-up" style="display: none;">0</span></a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header m-0 p-2">
                                <h3 id="title-notification" class="white">{{ __('locale.Notifications') }}</h3>
                                <!--<span class="grey darken-2">Notificaciones</span>-->
                            </div>
                        </li>
                        <li id="list-notification" class="scrollable-container media-list">
                        </li>
                    </ul>
                </li>
                <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                        href="#" data-toggle="dropdown">
                        <div class="user-nav d-sm-flex d-none"><span
                                class="user-name text-bold-600">{{ auth()->user()->firstname }}</span>
                            <span class="hidden user-status">Disponible</span>
                        </div><span><img class="round" src="{{ auth()->user()->urlpicture }}" alt="avatar"
                                height="40" width="40" /></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="profile"><i
                                class="feather icon-user"></i> {{ __('locale.Profile') }}</a>
                        <div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
													document.getElementById('logout-form').submit();"><i
                                class="feather icon-power"></i> {{ __('locale.Exit') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</nav>

{{-- Search Start Here --}}
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer">
        <a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No
                    results found.</span></div>
        </a>
    </li>
</ul>
{{-- Search Ends --}}
<!-- END: Header-->
