<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
<style type="text/css">
    .child{
        padding-left: 15px;
    }
</style>
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->

        <!-- search form (Optional) -->
        <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="{{ trans('adminlte_lang::message.search') }}..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form> -->
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" style="padding-top: 50px;">
            <!-- <li class="header">{{ trans('adminlte_lang::message.header') }}</li> -->
            <!-- Optionally, you can add icons to the links -->
            <li class="treeview {{ Request::is('admin_manager*') || Request::is('user_manager*') ? ' active ' : null }}">
                <a href="#"><i class="fa fa-users" aria-hidden="true"></i> <span>{!!trans('back_end.account_administration')!!}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    @if(Auth::user()->hasPermission("admin_management.view"))
                    <li class="nav-item {{ Request::is('admin_manager*') ? ' active open ' : null }} child"><a href="/admin_manager"><i class='fa fa-user-secret'></i> <span>{{ trans('back_end.auth_admin') }}</span></a></li>
                    @endif
                    @if (Auth::user()->hasPermission("user_management.view"))
                    <li class="nav-item {{ Request::is('user_manager*') ? ' active open ' : null }} child"><a href="/user_manager"><i class='fa fa-user'></i> <span>{{ trans('back_end.auth_user') }}</span></a></li>
                	@endif
                </ul>
            </li>
            @if(Auth::user()->hasPermission("rmb_management.View"))
            <li class="treeview {{ Request::is('organization*')? ' active ' : null }}">
                <a href="#"><i class="fa fa-sitemap" aria-hidden="true"></i> <span>{!!trans('back_end.organization_administration')!!}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="nav-item {{ Request::is('organization*') ? ' active open ' : null }} child"><a href="/organization"><i class='fa fa-user-secret'></i> <span>{{ trans('back_end.Manage_RMB') }}</span></a></li>
                </ul>
            </li>
            @endif
            <li class="treeview-menu {{ Request::is('branch*') ? ' active ' : null }}">
                <a href="#"><i class='fa fa fa-cogs'></i> <span>{!!trans('back_end.setting_master')!!}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="nav-item {{ Request::is('branch*') ? ' active open ' : null }} child"><a href="/organization"><a href="/branch"><i class="fa fa-table"></i>{!! trans('back_end.branch') !!}</a></li>
                    <!-- <li><a href="#">{{ trans('adminlte_lang::message.linklevel2') }}</a></li> -->
                </ul>
            </li>
            @if(Auth::user()->hasPermission("segment_management.view"))
            <li class="treeview {{ Request::is('manager_segment*') ? ' active ' : null }}">
                <a href="#"><i class="fa fa-road" aria-hidden="true"></i> <span>{!!trans('back_end.road_administration')!!}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="nav-item {{ Request::is('manager_segment*') ? ' active open ' : null }} child"><a href="/admin/manager_segment"><i class='fa fa-user-secret'></i> <span>{{ trans('back_end.manager_segment') }}</span></a></li>
                </ul>
            </li>
            @endif
            @if(Auth::user()->hasPermission("condition_rank.view"))
            <li class="treeview {{ Request::is('manager_condition*') ? ' active ' : null }}">
                <a href="#"><i class="fa fa-road" aria-hidden="true"></i> <span>{!!trans('back_end.condition_rank')!!}</span> <i class="fa fa-angle-left pull-right"></i></a>

                <ul class="treeview-menu">
                    <li class="nav-item {{ Request::is('manager_condition*') ? ' active open ' : null }} child"><a href="/condition_rank"><i class='fa fa-user-secret'></i> <span>{{ trans('back_end.condition_rank') }}</span></a></li>
                </ul>
            </li>
            @endif
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
