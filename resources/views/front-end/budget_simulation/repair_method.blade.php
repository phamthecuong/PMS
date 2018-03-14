@extends('front-end.layouts.app')

@section('budget_simulation')
active
@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
		<li>
			{{trans('menu.home')}}
		</li>
		<li>
			{{trans('menu.budget_simulation')}}
		</li>
		<li>
			{{trans('menu.start_process_budget')}}
		</li>
	</ol>
@endsection

@push('css')
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.css') }}">
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/front-end/js/datepicker/datepicker3.css') }}">
	<style>
		#repair_matrix{
			display: inline;
			width: 55%;
			font-weight: 400;
		    color: #999;
		    font-size: 60%;
		    padding: 0px 12px;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.default_repair_method')
])

<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			@box_open(trans('budget.repair_matrix'))
				<div>
					<div class="widget-body">
						<ul class="list-unstyled">
							<li>
								<h1><i class="fa fa-bank"></i>&nbsp;&nbsp;<span>{{trans('budget.region')}}: </span><small>{{$text_region}}</small></h1>
							</li>
							<li>
								<h1><i class="fa fa-road"></i>&nbsp;&nbsp;<span>{{trans('budget.road')}}: </span><small>{{$text_road}}</small></h1>
							</li>
							<li>
								<h1><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('budget.year')}}: </span><small>{{$text_year}}</small></h1>
							</li>
							<li>
								<h1><i class="fa fa-codepen"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('budget.default_repair_matrix')}}:
									{!! Form::select('repair_matrix', $repair_matrix, '', array('id' => 'repair_matrix', 'class' => 'form-control')) !!}
								</h1>
								
							</li>
						</ul>
						
						<div class="widget-footer">
							<a href="{{ $back }}" class="btn bg-color-blueLight txt-color-white">
								{{ trans('budget.back') }}
							</a>
							<button class="btn bg-color-blueLight txt-color-white" onclick="updateProsession();">
								{{ trans('budget.next') }}
							</button>
						</div>
					</div>
				</div>
			@box_close
		</div>
		<!-- NEW WIDGET START -->
		<!-- <div class="col-sm-12 col-md-12 col-lg-12">
			<div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-0" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
				<header>
					<span class="widget-icon"> <i class="fa fa-edit"></i> </span>
					<h2>{{trans('budget.budget_repair_method')}} </h2>

				</header>
				<div>
					<div class="jarviswidget-editbox"></div>
					
					<div class="widget-body">
						{!! Form::select('duallistbox_demo2', array(), '', array('id' => 'initializeDuallistbox', 'multiple' => 'multiple')) !!}
						<div class="widget-footer padding-10">
							<a href="{{ $back }}" class="btn bg-color-blueLight txt-color-white pull-left">{{ trans('budget.back') }}</a>
							<button class="btn bg-color-blueLight txt-color-white" onclick="updateProsession();">{{ trans('budget.next') }}</button>
						</div>
					</div>
				</div>
			</div>
		</div> -->
	</div>
</section>

@endsection

@push('script')
<script src="{{ asset('/sa/js/plugin/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>		
<script>
	$(document).ready(function() {
		// getListRepairMethod();
		
		// var initializeDuallistbox = $('#initializeDuallistbox').bootstrapDualListbox({
          	// nonSelectedListLabel: "{{trans('budget.budget_repair_method')}}",
          	// selectedListLabel: "{{trans('budget.budget_repair_method_for_budget_simulation')}}",
          	// preserveSelectionOnMove: 'moved',
          	// moveOnSelect: false,
          	// showFilterInputs: false,
          	// infoTextEmpty: "{{trans('budget.empty_list')}}",
          	// infoText: "{{trans('budget.showing_all')}} {0}"
        // });
	});
	
	function getListRepairMethod() {
		var repair_matrix_id = $("#repair_matrix option:selected").val();
		
		$.ajax({
			url : "{{route('user.budget.get.repair.method.default')}}",
			type : 'GET',
			data : {
				repair_matrix_id: repair_matrix_id,
			},
			success : function(data) {
				// data = data.sort(compare);
				var optionsAsString = "";
				$.each(data, function(i, item) {
					if (item.selected == true) {
						optionsAsString += '<option value="' + item.value + '" selected>' + item.name + '</option>';	
					} else {
						optionsAsString += '<option value="' + item.value + '">' + item.name + '</option>';
					}
				});
				$('#initializeDuallistbox').html(optionsAsString);
				$('#initializeDuallistbox').bootstrapDualListbox('refresh', true);
			}
		}); 
	}
	
	function updateProsession() {
		showLoading();
		
		var session_id = "{{$session}}";
		var repair_matrix_id = $("#repair_matrix option:selected").val();
		if (repair_matrix_id == -1) {
			$.SmartMessageBox({
				title : "{{trans('budget.error')}}",
				content : "{{trans('budget.please_choose_repair_matrix')}}",
				buttons : "[{{trans('budget.ok')}}]"
			});
			hideLoading();
			return false;
		}
		
		$.post("{{route('ajax.budget.update.default.repair.matrix', array('session_id' => $session))}}",
		{
			"session": session_id,
			// "repair_method": repair_method,
			"repair_matrix": repair_matrix_id,
			"_method": "PUT",
			_token : '{!! csrf_token() !!}'
		},
		function(data) {
			if (data.code == 200) {
				location.href = '/user/budget_simulation/repair_matrix/' + session_id;
			} else {
				alert(data);
			}
		}, "json");
	}
	
</script>
@endpush