<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="/admin_manager" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">PMS</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">PMS</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('adminlte_lang::message.togglenav') }}</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <?php
                $vn = '';$en = ''; 
                if (App::getLocale() == 'en')
                {
                    $en = 'opacity: 0.5;pointer-events:none;';
                }
                else
                {
                    $vn = 'opacity: 0.5;pointer-events:none;';
                }
                 ?>
                <li class="dropdown messages-menu">
                    <a href="/language/vi" style="{{$vn}}" class="language_available"><img src="/img/vn.png" alt="User Image"/></a>
                </li>
                <li class="dropdown messages-menu">
                    <a href="/language/en" style="{{$en}}" class="language_available"><img src="/img/en.png" alt="User Image"/></a>
                </li>
                @if (Auth::guest())
                    <li><a href="{{ url('/register') }}">{{ trans('adminlte_lang::message.register') }}</a></li>
                    <li><a href="{{ url('/login') }}">{{ trans('adminlte_lang::message.login') }}</a></li>
                @else
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header" style="padding-top: 30px;">
                            <p>
                              {!! trans('back_end.management_system_pavement_condition') !!}
                            </p>
                          </li>
                          <!-- Menu Footer-->
                          <li class="user-footer">
                            <div class="pull-right">
                              <a href="/user/logout" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                          </li>
                        </ul>
                    </li>
                @endif

                <!-- Control Sidebar Toggle Button -->
               
            </ul>
        </div>
    </nav>
</header>
