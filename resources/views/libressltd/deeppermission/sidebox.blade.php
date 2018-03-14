<div class="col-md-3">
	<div class="box box-solid">
		<div class="box-header with-border">
			<h3 class="box-title">Role &amp; Permission</h3>
		</div>
		<div class-"box-body">
			<ul class="nav nav-pills nav-stacked">
				@if (Auth::user()->hasPermission("user_role.view"))
				<li class="@yield('sidebox_user_role')">
					<a href="{{ url("user_role") }}">
						User's Role
					</a>
				</li>
				@endif
				@if (Auth::user()->hasPermission("role.view"))
				<li class="@yield('sidebox_role')">
					<a href="{{ url("role") }}">
						Role
					</a>
				</li>
				@endif
				@if (Auth::user()->hasPermission("permission_group.view"))
				<li class="@yield('sidebox_permission_group')">
					<a href="{{ url("permission/group") }}">
						Group Permission
					</a>
				</li>
				@endif
				@if (Auth::user()->hasPermission("permission.view"))
				<li class="@yield('sidebox_permission')">
					<a href="{{ url("permission") }}">
						Permission
					</a>
				</li>
				@endif
				@if (Auth::user()->hasPermission("permission.setting"))
				<li class="@yield('sidebox_setting')">
					<a href="{{ url("permission/setting") }}">
						Setting
					</a>
				</li>
				@endif
			</ul>
		</div>
	</div>
</div>