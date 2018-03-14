@extends('front-end.layouts.app')

@section('budget_simulation')
active
@endsection

@section('budget_simulation_start_new_process')
active
@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
	    <li>{{trans('budget.home')}}</li>
	    <li>{{trans('budget.budget_simulation')}}</li>
	    <li>{{trans('menu.start_process_budget')}}</li>
	</ol>
@endsection

@push('css')
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.css') }}">
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/front-end/js/datepicker/datepicker3.css') }}">
	<style>
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
		    background-color: #fff;
	        border: none;
	        font-size: 15px;
		}
		
		#year, #region {
			font-size: 15px;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.init')
])

<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<!-- NEW WIDGET START -->
		<article class="col-sm-12 col-md-12 col-lg-6">
			@box_open(trans('budget.init_form'))
				<div>
					<div class="widget-body">
						{!! Form::open(array('method' => 'POST', 'id' => 'form')) !!}
							{!! 
								Form::lbSelect2(
									'year', 
									'-1', 
									$year, 
									trans('budget.deterioration'), 
									[
										'id' => 'year',
										'onchange' => 'getListRegion();'
									]
								) 
							!!}
							{!! 
								Form::lbSelect(
									'region', 
									1, 
									[], 
									trans('budget.region'), 
									[
										'id' => 'region',
										'multiple' => 'multiple'
									]
								) 
							!!}
							{!! 
								Form::lbSelect(
									'road', 
									1, 
									[], 
									trans('budget.route'), 
									[
										'id' => 'road',
										'multiple' => 'multiple'
									]
								) 
							!!}
							<div class="widget-footer">
								<button id="module_dataset_import" type="button" class="btn bg-color-blueLight txt-color-white" onclick="data_set();">
									{{trans('budget.module_dataset_import')}}
								</button>
				            </div>
						{!! Form::close() !!}
					</div>
					<!-- end widget content -->
				</div>
				<!-- end widget div -->
			@box_close
			<!-- end widget -->
		</div>
		<!-- WIDGET END -->
	</div>	
</section>
@endsection

@push('script')
	<script src="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.js') }}"></script>
	<script src="{{ asset('/front-end/js/datepicker/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('/sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>

	<script>
		var list_region = [];
		var list_road = [];

		$(document).ready(function() {
			$('#road').multiselect({
				columns: 4,
				search: true,
				selectAll: true,
				texts:{
					selectAll: "{{trans('budget.select_all')}}",
					placeholder: "{{trans('budget.selected_options_road')}}",
					search: "{{trans('budget.search')}}",
					noneSelected: "{{trans('budget.none_selected')}}",
					selectedOptions: " {{trans('budget.selected_options')}}",
				},
				onOptionClick : function(element, option) {
					if (jQuery.inArray(option.value, list_road) !== -1) {
						delete list_road[option.value];
					} else {
						list_road[option.value] = option.value;	
					}
				},
			});
			
			$('#region').multiselect({
				columns: 4,
				search: true,
				selectAll: true,
				texts:{
					selectAll: "{{trans('budget.select_all')}}",
					placeholder: "{{trans('budget.selected_options')}}",
					search: "{{trans('budget.search')}}",
					noneSelected: "{{trans('budget.none_selected')}}",
					selectedOptions: " {{trans('budget.selected_options')}}",
				},
				onOptionClick : function(element, option) {
					if (jQuery.inArray(option.value, list_region) !== -1) {
						delete list_region[option.value];
					} else {
						list_region[option.value] = option.value;	
					}
					
					array_region = unique(list_region);
					list_road = [];
					getListRoad(array_region);
				},
			});

			getListRegion();
		});
		
		function getListRoad(list_region) {
			if (list_region.length > 0) {
				$.get("{{route('ajax.budget.get.road')}}",
				{
					"list_region": list_region
				},
				
	   			function(data) {
					var optionsAsString = "";
					for(var i = 0; i < data.length; i++) {
						var item = data[i];
					    optionsAsString += "<option value='" + item.id + "'>" + item.name + "</option>";
					}
					$('#road').html(optionsAsString);
					$('#road').multiselect('reload');
				}
				,"json" );
			}
		}
		
		function unique(array) {
		    return $.grep(array, function(el, index) {
		        return index === $.inArray(el, array);
		    });
		}
		
		function getListRegion() {
			list_region = [];
			list_road = [];
			$('#road').html('');
			$('#road').multiselect('reload');
			
			$.get("{{route('ajax.budget.get.region')}}",
			{
				"year": $('select[name=year] option:selected').text(),
			},
			function(data) {
				var optionsAsString = "";
				for(var i = 0; i < data.length; i++) {
					var item = data[i];
				    optionsAsString += "<option value='" + item.value + "'>" + item.name + "</option>";
				}
				
				$('#region').html(optionsAsString);
				$('#region').multiselect('reload');
			}
			,"json" );
		}
		
		function data_set() {
			var test = $('li.selected').find('input[type=checkbox]');
			
			var year = $('select[name=year] option:selected').text();
			var road = unique(list_road);
			var region = unique(list_region);
			var deterioration = $('select[id=deterioration] option:selected').val();
			
			if ($('select[name=year] option:selected').val() == -1) {
				$.SmartMessageBox({
					title : "{{trans('budget.error')}}",
					content : "{{trans('budget.please_choose_year')}}",
					buttons : "[{{trans('budget.ok')}}]"
				});
				
				return false;
			}
			
			if (region.length < 1) {
				$.SmartMessageBox({
					title : "{{trans('budget.error')}}",
					content : "{{trans('budget.please_choose_region')}}",
					buttons : "[{{trans('budget.ok')}}]"
				});
				
				return false;
			}
			
			if (road.length < 1) {
				$.SmartMessageBox({
					title : "{{trans('budget.error')}}",
					content : "{{trans('budget.please_choose_road')}}",
					buttons : "[{{trans('budget.ok')}}]"
				});
				
				return false;
			}
			
			$('#module_dataset_import').addClass('disabled');
			showLoading('{{trans('budget.please_wait_importing_module_dataset')}}');
			$.post("{{route('ajax.budget.create.init')}}",
			{
				"deterioration": deterioration,
				"list_road": road,
				"list_region": region,
				"year": year,
				_token : '{!! csrf_token() !!}'
			},
			function(data) {
				if (data.code == 200) {
					location.href = '/user/budget_simulation/dataset_import/' + data.session_id;
				} else {
					$('#module_dataset_import').removeClass('disabled');
				}
			}
			,"json" );
		}
		
	</script>
@endpush