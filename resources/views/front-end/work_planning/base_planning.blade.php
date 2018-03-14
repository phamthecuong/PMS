@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_start_new_process')
active
@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
		<li>
			{{trans('menu.home')}}
		</li>
		<li>
			{{trans('menu.work_planning')}}
		</li>
		<li>
			{{trans('menu.start_process')}}
		</li>
	</ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-inbox',
    'text1' => trans('wp.working_planning'),
    'text2' => trans('wp.base_planning_step')
])

<section id="widget-grid">						
	<div class="row">					
		<article class="col-lg-6">				
			@box_open(trans("wp.pavement_condition_forecasting"))			
			<div>			
				<div class="widget-body">		
					<ul class="list-unstyled">
						<li>
							<h1><i class="fa fa-bank"></i>&nbsp;&nbsp;<span>{{trans('wp.region')}} : </span><small>
								{{$text_region}}
							</small></h1>
						</li>
						<li>
							<h1><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('wp.target_PMS_year')}} : </span><small>{{$text_year}}</small></h1>
						</li>
						<li>
							<h1>
								<i class="fa fa-codepen"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('wp.base_planning_year')}}:
							</h1>
							<?php 
								$items = [];
								for ($i = $text_year; $i <= 2100; $i++) 
								{ 
									$items[] = [
										'name' => $i,
										'value' => $i
									];
								}
							?>
							@if ($status == 0)
							{!! 
								Form::lbSelect2('base_planning_year', @$base_planning_year, $items) 
							!!}
							@else
							{!! 
								Form::lbSelect2('base_planning_year', @$base_planning_year, $items, null, ['disabled']) 
							!!}
							@endif
						</li>
					</ul>

					<div class="widget-footer">	
						<a href="{{ $back }}" class="btn bg-color-blueLight txt-color-white pull-left">{{ trans('wp.back') }}</a>
						<button class="btn bg-color-blueLight txt-color-white" onclick="updateProsession();">{{ trans('wp.estimate_pavement_condition') }}</button>
					</div>	
				</div>		
			</div>			
			@box_close			
		</article>				
	</div>					
</section>

@endsection

@push('script')
	<script>

		function updateProsession() {
			showLoading('{{trans('wp.forecasting_pavement_condition_index_for_5_year')}}');
			var session_id = "{{$session}}";
			var base_planning_year = $("[name='base_planning_year']").val();

			if (base_planning_year == -1) {
				$.SmartMessageBox({
					title : "{{trans('wp.error')}}",
					content : "{{trans('wp.please_choose_base_planning_year')}}",
					buttons : "[{{trans('wp.ok')}}]"
				});
				hideLoading();
				return false;
			}
			
			$.post("{{route('ajax.work_planning.update.base_planning_year', array('session_id' => $session))}}", {
				"base_planning_year": base_planning_year,
				"_method": "PUT",
				_token : '{!! csrf_token() !!}'
			}, function(data) {
				if (data.code == 200) {
					location.href = '/user/work_planning/forecast_index/' + session_id;
				} else {
					alert(data);
				}
			}, "json");
		}
		
	</script>
@endpush

@push('css')
<style type="text/css">
	.select2-container--disabled {
		cursor: not-allowed;
	}
</style>
@endpush