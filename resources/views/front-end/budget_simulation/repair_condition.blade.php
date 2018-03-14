@extends('front-end.layouts.app')

@section('budget_simulation')
active
@endsection

@if ($history_flg)
	@section('budget_simulation_show_history')
	active
	@endsection
@else
	@section('budget_simulation_start_new_process')
	active
	@endsection
@endif

@section('breadcrumb')
	<ol class="breadcrumb">
		<li>
			{{trans('menu.home')}}
		</li>
		<li>
			{{trans('menu.budget_simulation')}}
		</li>
		@if ($history_flg)
		<li>
			{{trans('menu.budget_show_history')}}
		</li>
		@else
		<li>
			{{trans('menu.start_process_budget')}}
		</li>
		@endif
	</ol>
@endsection

@push('css')
	<style>
		.padding-10 {
		    padding: 13px!important;
		}
		
		.form-horizontal fieldset+fieldset legend {
		    border-top: 1px solid rgba(0,0,0,.1) !important;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.repair_condition')
])
<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-6">
			@box_open(trans('budget.budget_repair_info'))
				<div>
					<div class="widget-body">
						@include('front-end.budget_simulation.process_info', [
                    		'text_region' => $text_region,
                    		'text_year' => $text_year,
                    		'text_route' => $text_road
                    	])
					</div>
				</div>
			@box_close
		</div>
	
		<div class="col-sm-12 col-md-12 col-lg-6" id="element">
			@box_open(trans('budget.repair_condition_parameter'))
				<div>
					<div class="widget-body" id="cus">
						{{ Form::open(array('class' => 'form-horizontal', 'id' => 'form')) }}
							<fieldset>
								
								<legend>{{trans('budget.simulation')}}</legend>
								<div class="form-group">
									<label class="col-md-4 control-label">
										{{trans('budget.simulation_term')}} ({{trans('budget.year_simulation')}})
									</label>
									<div class="col-md-8">
										@if ($history_flg && isset($budget->simulation_term))
											{!! 
												Form::select(
													'simulation_term', 
													$simulation_term, 
													'30', 
													array(
														'class' => 'form-control',
														'disabled' => 'disabled'
													)
												) 
											!!}
										@else
											{!! 
												Form::select(
													'simulation_term', 
													$simulation_term, 
													'30', 
													array(
														'class' => 'form-control input-sm select2'
													)
												) 
											!!}
										@endif
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-4 control-label">
										{{trans('budget.simulation_time')}} ({{trans('budget.times')}})
									</label>
									<div class="col-md-8">
										@if ($history_flg && isset($budget->simulation_time))
											{!! 
												Form::select(
													'simulation_time', 
													$simulation_time, 
													'100', 
													array(
														'class' => 'form-control', 
														'disabled' => 'disabled'
													)
												) 
											!!}
										@else
											{!! 
												Form::select(
													'simulation_time', 
													$simulation_time, 
													'100', 
													array(
														'class' => 'form-control select2',
													)
												) 
											!!}
										@endif
									</div>
								</div>
							</fieldset>
							
							<!-- <fieldset>
								<legend>{{trans('budget.scenario_1')}}</legend>
								
								<div class="form-group">
									<div class="col-md-1"></div>
									<div class="col-md-10" style="text-align: left;">
										<strong class="text-danger"><i>{{trans('budget.no_parameter_needed_1')}}</i></strong>										
									</div>
								</div>
							</fieldset>
							
							<fieldset>
								<legend>{{trans('budget.scenario_2')}}</legend>
								
								<div class="form-group">
									<label class="col-md-4 control-label">{{trans('budget.budget_constraint')}}</label>
									<div class="col-md-8">
										<div class="row">
											<div class="col-sm-12">
												<div class="input-group">
													<input class="form-control" placeholder="{{trans('budget.budget_constraint')}}" type="text" name="budget_constraint">
													<span class="input-group-addon">{{trans('budget.billion_VND')}}</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
							
							<fieldset>
								<legend>{{trans('budget.scenario_3')}}</legend>
								
								<div class="form-group">
									<div class="col-md-1"></div>
									<div class="col-md-10" style="text-align: left;">
										<strong class="text-danger"><i>{{trans('budget.no_parameter_needed_3')}}</i></strong>
									</div>
								</div>
							</fieldset>
							
							<fieldset>
								<legend>{{trans('budget.scenario_4')}}</legend>
								
								<div class="form-group">
									<label class="col-md-4 control-label">{{trans('budget.target_risk')}}</label>
									<div class="col-md-8">
										<div class="row">
											<div class="col-sm-12">
												<div class="input-group">
													<input class="form-control" placeholder="{{trans('budget.target_risk_level')}}" type="text"name="target_risk_level">
													<span class="input-group-addon"> %</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</fieldset> -->
						{{ Form::close() }}
						<div class="widget-footer">
							<a href="{{ $back }}" class="btn bg-color-blueLight txt-color-white">{{ trans('budget.back_matrix') }}</a>
							@if ($history_flg && isset($budget->simulation_term) && isset($budget->simulation_time))
								<a class="btn bg-color-blueLight txt-color-white" href="/user/budget_simulation/scenario_tab/{{$session}}">{{ trans('budget.next_result') }}</a>
							@else
								<button class="btn bg-color-blueLight txt-color-white" onclick="updateRepaircondition();">{{ trans('budget.next_result') }}</button>
							@endif
						</div>
					</div>
				</div>
				<!-- end widget div -->
			@box_close
		</div>
		<!-- WIDGET END -->
	</div>
</section>

@endsection

@push('script')
	<script src="{{ asset('/sa/js/plugin/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
	<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
	<script>
		// var flag_change = true;
		
		// function checkChangeDefault() {
		// 	flag_change = false;
		// }
		
		function updateRepaircondition() {
			// var fv = $('#form').data('formValidation'),
	        // $container = $('#form');

			// fv.validateContainer($container);
			// var isValid = fv.isValidContainer($container);
			
			// if (isValid !== false && isValid !== null) {
				// var budget_constraint = $('input[name=budget_constraint]').val();
				// var target_risk_level = $('input[name=target_risk_level]').val();
				var simulation_term = $('select[name=simulation_term] option:selected').val();
				var simulation_time = $('select[name=simulation_time] option:selected').val();
				// var flag = true;
				
				// if (flag_change && simulation_term == 30 && simulation_time == 100) {
				// 	$.SmartMessageBox({
				// 		title : "{{trans('budget.alret')}}",
				// 		content : "{{trans('budget.are_you_sure_to_keep_the_default_value')}}",
				// 		buttons : "[{{trans('menu.no')}}][{{trans('menu.yes')}}]"
				// 	}, function(ButtonPressed) {
				// 		if (ButtonPressed === "{{trans('menu.yes')}}") {
				// 			$.post("{{route('ajax.budget.create.repair.condition')}}",
				// 			{
				// 				"budget_constraint": budget_constraint,
				// 				"target_risk_level": target_risk_level,
				// 				"simulation_term": simulation_term,
				// 				"simulation_time": simulation_time,
				// 				"session_id": "{{$session}}",
				// 				_token : '{!! csrf_token() !!}'
				// 			},
				// 			function(data) {
				// 				if (data.code == 200) {
				// 					location.href = '/user/budget_simulation/scenario_tab/' + data.session_id;
				// 					// alert('Scenario. Coming soon!');
				// 				}
				// 			}
				// 			,"json" );
				// 		}
				// 	});
				// } else {
					showLoading();
					$.post("{{route('ajax.budget.create.repair.condition')}}",
					{
						// "budget_constraint": budget_constraint,
						// "target_risk_level": target_risk_level,
						"simulation_term": simulation_term,
						"simulation_time": simulation_time,
						"session_id": "{{$session}}",
						_token : '{!! csrf_token() !!}'
					}, function(data) {
						if (data.code == 200) {
							location.href = '/user/budget_simulation/scenario_tab/' + data.session_id;
						} else {
							alert(data);
							hideLoading();
						}
					}, "json");
				// }
			// }
		}
	</script>
@endpush