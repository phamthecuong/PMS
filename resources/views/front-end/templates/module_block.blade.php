<!--
	Author: Pham The Cuong 04032017
	Parameters:
	$image
	$title
	$about
	$link
-->

<div class="col-sm-6 col-md-6 col-lg-6">
	<!-- product -->
	<div class="product-content product-wrap clearfix">
		<div class="row">
			<div class="col-md-8 col-sm-12 col-xs-12">
				<div class="product-image">
					<img src="{{$image}}" class="img-responsive" style="height: 238px; width: auto; display: inline-block">
					<!-- <span class="tag2 hot"> HOT </span> -->
				</div>
			</div>
			<div class="col-md-4 col-sm-12 col-xs-12">
				<div class="product-deatil">
					<h5 class="name"><a href="#" onclick="return false;"> {{$title}} </a></h5>
					<div class="row padding-10">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<a href="#" onclick="return false;" class="btn btn-success" rel="popover-hover" data-placement="bottom"
							data-original-title="{{trans('menu.about')}}"
							data-content="{{$about}}"> {{trans('menu.about')}} </a>
						</div>
					</div>
					<div class="row padding-10">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<a href="#" onclick="return false;" class="btn btn-success">{{trans('menu.user_manual')}}</a>
						</div>
					</div>
				</div>
				<div class="product-info smart-form">
					<div class="col-md-12 col-sm-12 col-xs-12">
						@if(isset($permission))
							@if(Auth::user()->hasPermission($permission))
								<a href="{{$link}}" class="btn btn-success">{{trans('menu.start')}}</a>
							{{-- @else
								<a href="#" onclick="return false;" class="btn btn-success" rel="popover" data-placement="top"
								data-original-title="{{trans('menu.start')}}"
								data-content="{{trans('menu.start_permission')}}"> {{trans('menu.start')}} </a> --}}
							@endif
						@else
							<a href="{{$link}}" class="btn btn-success">{{trans('menu.start')}}</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end product -->
</div>