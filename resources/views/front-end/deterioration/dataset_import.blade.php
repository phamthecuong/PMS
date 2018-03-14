@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection

@section('deterioration_new_process')
active
@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
	    <li>{{trans('deterioration.home')}}</li>
	    <li>{{trans('deterioration.deterioration')}}</li>
	    <li>{{trans('deterioration.start_new_process')}}</li>
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
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-pencil-square-o',
	'text1' => trans('deterioration.deterioration'),
	'text2' => trans('deterioration.input')
])

<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<!-- NEW WIDGET START -->
		<div class="col-sm-6 col-md-6 col-lg-6">
			@box_open(trans('deterioration.init'))
			<div>	
				<div class="widget-body">
					{!! Form::open(array('method' => 'POST', 'id' => 'form')) !!}
						{!! Form::lbSelect2('region', null, $region, trans('deterioration.region'), ['id' => 'region']) !!} 
						{!! Form::lbSelect2('year', null, $year, trans('deterioration.year'), ['id' => 'year']) !!}
						<div class="widget-footer">
							<button type="button" class="btn bg-color-blueLight txt-color-white" onclick="data_set(this);">
								{{trans('deterioration.module_dataset_import')}}
							</button>
						</div>
					{!! Form::close() !!}
				</div>
				<!-- end widget div -->
			</div>
			@box_close
			<!-- end widget -->
			<!-- Widget ID (each widget will need unique ID)-->
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
		function data_set(btn) {
			$(btn).addClass('disabled');
			
			showLoading();
			var year = $("select[name=year]" ).val();
			var region = $("select[name=region]" ).val();

			if (!year) {
				$.SmartMessageBox({
					title : "{{trans('budget.error')}}",
					content : "{{trans('budget.please_choose_year')}}",
					buttons : "[{{trans('budget.ok')}}]"
				});
				hideLoading();
				return false;
			}

			$.post("{{route('deterioration.get.data.init')}}", {
				"list_region": region,
				"year": year
			}, function(data) {
				if (data.code == 200) {
					location.href = 'dataset_import/' + data.deterioration_id ;
				} else {
					hideLoading();
					$(btn).removeClass('disabled');
				}
			}, "json" );
		}
		
	</script>
@endpush
