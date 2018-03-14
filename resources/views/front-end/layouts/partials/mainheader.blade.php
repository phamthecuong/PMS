<header id="header">
	<div id="logo-group">

		<!-- PLACE YOUR LOGO HERE -->
		<span id="logo" style="width: 380px;"><b>PMS - {{trans('menu.pavement_management_system')}}</b></span>
		<!-- END LOGO PLACEHOLDER -->

		<!-- Note: The activity badge color changes when clicked and resets the number to 0
		Suggestion: You may want to set a flag when this happens to tick off all checked messages / notifications -->
		<!-- <span id="activity" class="activity-dropdown" onclick="change_data_notification()"> 
			<i class="fa fa-bell-o"></i> 
			<b class="badge"><?php echo App\Models\tblNotification::where('status_notification', 0)->count() ; ?> </b> 
		</span>
 -->
		<!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
		<div class="ajax-dropdown">

			<!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
			<div class="btn-group btn-group-justified" data-toggle="buttons">
				<label class="btn btn-default active" onclick="complete();">
					<input type="radio" name="activitys">
					{{trans('menu.complete')}} </label>
				<label class="btn btn-default" onclick="running();">
					<input type="radio" name="activitys">
					{{trans('menu.running')}} </label>
			</div>

			<!-- notification content -->
			<div class="ajax-notifications custom-scroll" id="notification">
                <?php $notification = App\Models\tblNotification::where('status_process', 1)->orderBy('updated_at', 'desc')->get();?>
                @if (count($notification) == 0)
                    <ul class="notification-body">
                        <li>
                            <span class="padding-10 unread">
                                <!-- <em class="badge padding-5 no-border-radius bg-color-blueLight pull-left margin-right-5">
                                    <i class="fa fa-user fa-fw fa-2x"></i>
                                </em> -->
                                <span>
                                     {{trans('deterioration.no_notification')}}
                                </span>
                            </span>
                        </li>
                    </ul>
                @else
                    <ul class="notification-body">
                        @foreach ($notification as $n)
                             <li>
                                <span class="padding-10 unread">
                                    <!-- <em class="badge padding-5 no-border-radius bg-color-blueLight pull-left margin-right-5">
                                        <i class="fa fa-user fa-fw fa-2x"></i>
                                    </em> -->
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
			</div>
			<!-- end notification content -->

			<!-- footer: refresh area -->
			    <span>
				<button type="button" onclick="refresh()" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
					<i class="fa fa-refresh"></i>
				</button> </span>
			<!-- end footer -->

		</div>
		<!-- END AJAX-DROPDOWN -->
	</div>

	<!-- #PROJECTS: projects dropdown -->
	<div class="project-context hidden-xs">

		<!-- <span class="label">Projects:</span>
		<span class="project-selector dropdown-toggle" data-toggle="dropdown">Recent projects <i class="fa fa-angle-down"></i></span> -->

		<!-- Suggestion: populate this list with fetch and push technique -->
		<!-- <ul class="dropdown-menu"> -->
			<!-- <li>
				<a href="javascript:void(0);">Online e-merchant management system - attaching integration with the iOS</a>
			</li>
			<li>
				<a href="javascript:void(0);">Notes on pipeline upgradee</a>
			</li>
			<li>
				<a href="javascript:void(0);">Assesment Report for merchant account</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);"><i class="fa fa-power-off"></i> Clear</a>
			</li> -->
		<!-- </ul> -->
		<!-- end dropdown-menu-->

	</div>
	<!-- end projects dropdown -->

	<!-- #TOGGLE LAYOUT BUTTONS -->
	<!-- pulled right: nav area -->
	<div class="pull-right">

		<!-- collapse menu button -->
		<div id="hide-menu" class="btn-header pull-right">
			<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
		</div>
		<!-- end collapse menu -->

		<!-- #MOBILE -->
		<!-- Top menu profile link : this shows only when top menu is active -->
		<ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
			<li class="">
				<!-- <a href="#" class="dropdown-toggle no-margin userdropdown btn-header pull-right" data-toggle="dropdown">  -->
					<!-- <img src="{{ asset('img/favicon.ico') }}" alt="John Doe" class="online" />  -->
					<!-- <i class="fa fa-angle-double-down"></i> -->
				<!-- </a> -->
				<div class="btn-header pull-right" data-toggle="dropdown" style="margin-top: -5px">
					<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-user"></i></a> </span>
				</div>
				<ul class="dropdown-menu pull-right">
					<!-- <li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="profile.html" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
					</li> -->
					<li class="divider"></li>
					<li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> {{trans('menu.full_screen')}}</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="/user/change_password" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-lock fa-lg"></i> {{trans('menu.change_password')}}</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="{{ route('user.logout') }}" onclick="return userLogout()" class="padding-10 padding-top-5 padding-bottom-5"><i class="fa fa-sign-out fa-lg"></i> <strong>{{trans('menu.logout')}}</strong></a>
					</li>
				</ul>
			</li>
		</ul>
		
		<!-- logout button -->
		<div id="logout" class="btn-header transparent pull-right">
			<span> <a href="{{ route('user.logout') }}" title="{{ trans('header.signout') }}" data-action="userLogout" data-logout-msg="{{ trans('header.signout_text') }}"><i class="fa fa-sign-out"></i></a> </span>
		</div>
		<!-- end logout button -->

		<!-- search mobile button (this is hidden till mobile view port) -->
		<!-- <div id="search-mobile" class="btn-header transparent pull-right">
			<span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
		</div> -->
		<!-- end search mobile button -->

		<!-- #SEARCH -->
		<!-- input: search field -->
		<!-- <form action="search.html" class="header-search pull-right">
			<input id="search-fld" type="text" name="param" placeholder="Find reports and more">
			<button type="submit">
				<i class="fa fa-search"></i>
			</button>
			<a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"><i class="fa fa-times"></i></a>
		</form> -->
		<!-- end input: search field -->

		<!-- fullscreen button -->
		<div id="fullscreen" class="btn-header transparent pull-right">
			<span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
		</div>
		<!-- end fullscreen button -->

		<!-- multiple lang dropdown : find all flags in the flags page -->
		<span href="#" id="smart-mod-eg5" class="btn btn-info pull-right" style="margin-top: 10px;">
		<?php $name = (App::getLocale() == 'en') ? 'name_en' : 'name_vn'; ?>
		@if(Auth::check())
			{{Auth::user()->name}} - {{@Auth::user()->organizations->$name}}
		@endif
		</span>
		<ul class="header-dropdown-list hidden-xs">
			<li>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					@if (\App::getLocale() == "en")
					<img src="{{ asset('sa/img/blank.gif') }}" class="flag flag-us" alt="United States"> <span> English (US) </span> <i class="fa fa-angle-down"></i>
					@else
					<img src="{{ asset('sa/img/blank.gif') }}" class="flag flag-vn" alt="Vietnamese"> <span> Tiếng Việt (VI) </span> <i class="fa fa-angle-down"></i>
					@endif
				</a>
				<ul class="dropdown-menu pull-right">
					<li 
					@if (\App::getLocale() == "en")
					class="active"
					@endif
					>
						<a href="{{ route('language.switcher', array('lang' => 'en')) }}"><img src="{{ asset('sa/img/blank.gif') }}" class="flag flag-us" alt="United States"> English (US)</a>
					</li>
					<li
					@if (\App::getLocale() == "vi")
					class="active"
					@endif
					>
						<a href="{{ route('language.switcher', array('lang' => 'vi')) }}"><img src="{{ asset('sa/img/blank.gif') }}" class="flag flag-vn" alt="Vietnamese"> Tiếng Việt (VI)</a>
					</li>
				</ul>
			</li>
		</ul>
		<!-- end multiple lang -->

	</div>
	<!-- end pulled right: nav area -->

</header>
<script>
function userLogout() {
    $.SmartMessageBox({
        title : "<?php echo e(trans('general.Logout')); ?> <span class='txt-color-orangeDark'><strong><?php echo \Auth::user()->name?></strong></span>?",
        content : "<?php echo e(trans('header.signout_text')); ?>",
        buttons : "[<?php echo e(trans('menu.no')); ?>][<?php echo e(trans('menu.yes')); ?>]"
    }, function(ButtonPressed) {
        if (ButtonPressed === "<?php echo e(trans('menu.yes')); ?>") {
            window.location = '/user/logout';
            return true;
        } else {
            return false;
        }
    });
    return false;
}
</script>

