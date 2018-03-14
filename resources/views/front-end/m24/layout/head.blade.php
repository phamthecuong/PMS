	<!-- #HEADER -->
<header id="header" style="max-width: 100%;">
	<div id="logo-group">
		<!-- PLACE YOUR LOGO HERE -->
		<span id="logo"> <b style="color: white;">PMS</b> </span>
	</div>
	<div class="pull-right">

		<!-- logout button -->
		@if (\Auth::check())
		<div id="logout" class="btn-header transparent pull-right" >
			<span> <a href="/user/logout"  title="{{trans('map.SignOut')}}" data-action="userLogout" data-logout-msg="{{trans('map.message_logout')}}"><i class="fa fa-sign-out"></i><span style="color:white;font-size:10px;margin-left: 2px;">{{trans('map.logout')}} </span></a></span>
		</div>
		@endif
		<!-- end logout button -->

		<!-- search mobile button (this is hidden till mobile view port) -->
		<div id="search-mobile" class="btn-header transparent pull-right">
			<span>
				<a href="javascript:void(0)" >
					<span style="color:white;font-size:10px;margin-left: 2px;">{{trans('map.search')}}</span>
				</a>
			</span>
		</div>
		<!-- end search mobile button -->
		<!-- #SEARCH -->
		<!-- end input: search field -->
		<!-- fullscreen button -->
		<div id="fullscreen" class="btn-header transparent pull-right">
			<span>
				<a href="javascript:void(0);" data-action="launchFullscreen" title="{{trans('map.FullScreen')}}">
					<i class="fa fa-arrows-alt"></i>
					<span style="color:white;font-size:10px;margin-left: 2px;">{{trans('map.FullScreen')}}</span>
				</a>
			</span>
		</div>

	</div>

	<!--go back home-->
	<div id="fullscreen" class="btn-header transparent pull-right">
		<span>
		<a href="/user/home" ><i class="fa fa-home" aria-hidden="true"></i><span  style="color:white;font-size:10px;margin-left: 2px;" >{{trans('map.back_home')}}</span></a></span>
	</div>
	<!-- seach-map -->
	@if (Auth::check())
	<div class="btn-header pull-right search_panel" style="padding-top: 9px;">
		<a href="#" id="modal_link" class="btn bg-color-purple txt-color-white">
			<i class="fa fa-search" aria-hidden="true"></i> {{trans('map.search')}}
		</a>
		<div id="dialog-message" style="display: none;">
			{!! Form::open(["url" => "", "method" => "post"]) !!}
			<div class="row">
				<div class="control-label col-xs-3">{{trans('map.rmb')}}</div>
				<div class="col-xs-9">
					{!!
						Form::lbSelect('organization', -1, \App\Models\tblOrganization::getListRmb(1), null, ['id' => 'rmb_id'])
					!!}
				</div>
				<div class="control-label col-xs-3">{{trans('map.sb')}}</div>
				<div class="col-xs-9">

					{!!
						 Form::lbSelect('sub_organization', -1, \App\Models\tblOrganization::getListSB(1), null, ['id' => 'sb_id'])
					!!}
				</div>
				<div class="control-label col-xs-3">{{trans('map.road')}}</div>
				<div class="col-xs-9">
					{!!
						Form::lbSelect('branch', 'null', [], null,['id' => 'road_name'])
					!!}
				</div>
				<div class="control-label col-xs-3">{{trans('map.year')}}</div>
				<div class="col-xs-9">
					{!!
						Form::lbSelect('year', 'null', [], null, ['id' => 'year_name'])
					!!}
				</div>
				<div class="control-label col-xs-3 kilopost-title search-title">{{trans('map.Kilopost')}}</div>
				<div class="col-xs-9 ">
					<div class="row">
						<div class="col-xs-6">
							{!!
								 Form::lbNumber('kilopost_from', null, trans("map.From"), '0')
							!!}
						</div>
						<div class="col-xs-6">
							{!!
								Form::lbNumber('kilopost_to', null, trans("map.to"), '0')
							!!}
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
	@endif
<!-- 			login
-->		<a href="#" id="smart-mod-eg5" class="btn btn-danger pull-right" style="margin-top: 10px;">
		@if(Auth::check())
			{{Auth::user()->name}}
		@else
			{{trans('map.login')}}
		@endif
	</a>
	<!-- end voice command -->
	<!-- multiple lang dropdown : find all flags in the flags page -->
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
<!-- end pulled right: nav area -->
</header>
<style>
	@media only screen and (max-width: 980px) {
		.search_panel {
			display: none !important;
		}
	}
</style>
