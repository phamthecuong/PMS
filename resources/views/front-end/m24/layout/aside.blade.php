<aside id="left-panel" style="background: white;"  class="smart-style-2">

			<!-- User info -->
			<div class="login-info" style="background: " >
				<span> <!-- User image size is adjusted inside CSS, it should stay as is --> 
					<a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
						<span style="overflow: visible;">
							<!-- <img src="/sa/img/avatars/sunny.png" alt="John Doe" class="online" />   -->
							<?php $name = (App::getLocale() == 'en') ? 'name_en' : 'name_vn'; ?>
							@if (Auth::check())
								{{Auth::user()->name}} 
								<span>- {{@Auth::user()->organizations->$name}}</span>
							@else
								
							@endif
						</span>
					</a> 
				</span>
			</div>
			<nav id="left-list">
				<ul  style="height: 350px;">
					<li class="top-menu-invisible boundary1 nav-demo-btn" >
						<a href="#"><img  src="/front-end/image/ranhgioiquocgia.png"><span class="menu-item-parent">{{trans('map.national_boundary')}}</span></a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/ranhgioitinh.png"><span class="menu-item-parent">{{trans('map.district_boundary')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/ranhgioihuyen.png"><span class="menu-item-parent">{{trans('map.Ward_commune_boundary')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/ranhgioixa.png"><span class="menu-item-parent">{{trans('map.route')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/quoclo.png"><span class="menu-item-parent">{{trans('map.Provincial_Highway')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/tinhlo.png"><span class="menu-item-parent">{{trans('map.Rail')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/duongsat.png"><span class="menu-item-parent">{{trans('map.railRoad')}}</span> </a>
					</li>
					<li class="boundary1 nav-demo-btn">
						<a href="#"><img  src="/front-end/image/songho.png"><span class="menu-item-parent">{{trans('map.Rivers_and_lakes')}}</span> </a>
					</li>
				</ul>

				<p class="nav-demo-btn" style=" font-weight: bold; margin-left: 10px; position:absolute;top: 292px;left: 14px;">
					{{trans('map.road_condition')}}
				</p>
				<div class="boundary left-option" style="padding-left: 7px;position: absolute;top: 318px;">		
					
					<div class="boundary2" id="img">
						<table class="nav-demo-btn">
							<tr><td><img src="/front-end/image/good.png"></td></tr>
							<tr><td><img src="/front-end/image/fair.png"></td></tr>
							<tr><td><img src="/front-end/image/poor.png"></td></tr>
							<tr><td><img src="/front-end/image/bad.png"></td></tr>
						</table>
					</div>
					<div class="boundary3" id="number1" style="display: none">
						<table class="nav-demo-btn" style="width: 158px;">
							<tr>
								<td>0  - 10 (%) ( {{trans('map.good')}} )</td>
							</tr>
							<tr>
								<td>10 - 20 (%) ( {{trans('map.Land')}} )</td>
							</tr>
							<tr>
								<td>20 - 40 (%) ( {{trans('map.Bad')}} )</td>
							</tr>
							<tr>
								<td>40 -    (%) ( {{trans('map.Extremely_bad')}})</td>
							</tr>
						</table>
					</div>
					<div style="clear: both;"></div>
					<div class="boundary3" id="number2" style="display: none">
						<table class="nav-demo-btn" style="width: 158px;">
							<tr>
								<td>0 -     (mm) ( {{trans('map.good')}} )</td>
							</tr>
							<tr>
								<td>20 - 30 (mm) ( {{trans('map.Land')}} )</td>
							</tr>
							<tr>
								<td>30 - 50 (mm) ( {{trans('map.Bad')}} ) </td>
							</tr>
							<tr>
								<td>50 -    (mm) ( {{trans('map.Extremely_bad')}})</td>
							</tr>
						</table>
					</div>
					<div class="boundary3" id="number3" style="display: none">
						<table class="nav-demo-btn" style="width: 158px;">
							<tr>
								<td>0 - 4  (mm/m) ( {{trans('map.good')}} )</td>
							</tr>
							<tr>
								<td>4 - 6  (mm/m) ( {{trans('map.Land')}} )</td>
							</tr>
							<tr>
								<td>6 - 10 (mm/m) ( {{trans('map.Bad')}} )</td>
							</tr>
							<tr>
								<td>10 -   (mm/m) ( {{trans('map.Extremely_bad')}})</td>
							</tr>
						</table>
					</div>
					<div class="boundary3" id="number4">
						<table class="nav-demo-btn" style="width: 158px;">
							<tr>
								<td>5 -   ( {{trans('map.good')}} )</td>
							</tr>
							<tr>
								<td>5 - 4 ( {{trans('map.Land')}}  )</td>
							</tr>
							<tr>
								<td>4 - 3 ( {{trans('map.Bad')}} )</td>
							</tr>
							<tr>
								<td>3 - 0 ( {{trans('map.Extremely_bad')}})</td>
							</tr>
						</table>
					</div>
				</div>	
				<div style="clear: both;"></div>
			</nav>
			
			<span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i> </span>

		</aside>