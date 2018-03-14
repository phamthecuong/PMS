<aside id="left-panel">

	<!-- User info -->
	<div class="login-info">
		<span> <!-- User image size is adjusted inside CSS, it should stay as it --> 
			
			<a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
				<span>
					{{ @Auth::user()->name }}
				</span>
				<i class="fa fa-angle-down"></i>
			</a> 
			
		</span>
	</div>
	<nav>
		<ul>
			<li class="@yield('home')">
				<a href="{{route('user.home')}}" title="{{ trans('menu.home') }}"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">{{ trans('menu.home') }}</span></a>
			</li>
			
			@include("libressltd.lbsidemenu.sidemenu")
		</ul>
	</nav>
	<span class="minifyme" data-action="minifyMenu"> 
		<i class="fa fa-arrow-circle-left hit"></i> 
	</span>

</aside>