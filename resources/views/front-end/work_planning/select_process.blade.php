@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_start_new_process')
active
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li>{{trans("wp.home")}}</li>
    <li>{{trans("wp.working_planning")}}</li>
    <li>{{trans("wp.start_new_process")}}</li>
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
    'icon' => 'fa-th',
    'text1' => trans('wp.working_planning'),
    'text2' => trans('wp.init')
])

<section id="widget-grid">					
	<div class="row">				
		<article class="col-lg-6">			
			@box_open(trans("wp.init_title"))		
			<div>		
				<div class="widget-body">	
					{!! Form::open(array('method' => 'POST', 'files' => true, 'id' => 'form')) !!}
					{!! 
						Form::lbSelect2(
							'year', 
							'-1', 
							$year, 
							trans('wp.target_PMS_dataset'), 
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

					<div class="widget-footer">	
						<button type="button" class="btn bg-color-blueLight txt-color-white" onclick="data_set(this);">
							{{trans('wp.module_dataset_import')}}
						</button>
					</div>	
					{!! Form::close() !!}
				</div>		
			</div>			
			@box_close			
		</article>				
	</div>					
</section>	
@endsection					

@push('script')
<script src="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.js') }}"></script>
<script src="{{ asset('/front-end/js/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>

<script>
	var list_region = [];

	$(document).ready(function() {
		
		$('#region').multiselect({
			columns: 4,
			search: true,
			selectAll: true,
			texts:{
				selectAll: "{{trans('wp.select_all')}}",
				placeholder: "{{trans('wp.selected_options')}}",
				search: "{{trans('wp.search')}}",
				noneSelected: "{{trans('wp.none_selected')}}",
				selectedOptions: " {{trans('wp.selected_options')}}",
			},
			onOptionClick : function(element, option) {
				if (jQuery.inArray(option.value, list_region) !== -1) {
					delete list_region[option.value];
				} else {
					list_region[option.value] = option.value;	
				}
				
			},
		});
	});
	
	
	function unique(array) {
	    return $.grep(array, function(el, index) {
	        return index === $.inArray(el, array);
	    });
	}
	
	function getListRegion() {
		list_region = [];

		$.get("{{route('ajax.work.get.region')}}",
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
		var year = $('select[name=year] option:selected').text();
		var region = unique(list_region);

		if ($('select[name=year] option:selected').val() == -1) {
			$.SmartMessageBox({
				title : "{{trans('wp.error')}}",
				content : "{{trans('wp.please_choose_year')}}",
				buttons : "[{{trans('wp.ok')}}]"
			});
			
			return false;
		}
		
		if (region.length < 1) {
			$.SmartMessageBox({
				title : "{{trans('wp.error')}}",
				content : "{{trans('wp.please_choose_region')}}",
				buttons : "[{{trans('wp.ok')}}]"
			});
			
			return false;
		}
		
		$('#module_dataset_import').addClass('disabled');
		showLoading('{{trans('wp.please_wait_for_20_to_40_seconds_to_import_data')}}');
		$.post("{{route('ajax.work.create.init')}}",
		{
			"list_region": region,
			"year": year,
			_token : '{!! csrf_token() !!}'
		},
		function(data) {
			if (data.code == 200) {
				location.href = '/user/work_planning/dataset_import/'+data.session_id;
			} else {
				$('#module_dataset_import').removeClass('disabled');
			}
			
		}, "json");
	}
</script>
@endpush
