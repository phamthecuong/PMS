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
			<li>{{ trans('menu.budget_show_history') }}</li>
		@else
	    	<li>{{ trans('menu.start_process_budget') }}</li>
	    @endif
	</ol>
@endsection

@push('css')
	<style type="text/css">
		.ui-jqgrid .ui-jqgrid-titlebar {
		    display: none !important;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.dataset_import')
])

<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12">
			@box_open(trans('budget.process_info'))
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
						</ul>
					</div>
				</div>
			@box_close
		</article>
		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			@box_open(trans('budget.data_table'))
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body no-padding">
						<h4 class="padding-10">{{ trans('budget.show_first_100_rows') }}</h4>
						<div>
							<table id="jqgrid"></table>
							<div id="pjqgrid"></div>
						</div>
				
						<div class="widget-footer">
							<a href="{{$next}}" class="btn bg-color-blueLight txt-color-white">{{ trans('budget.next_matrix') }}</a>
						</div>
					</div>
					<!-- end widget content -->
				</div>
				<!-- end widget div -->
			@box_close
		</article>
		<!-- WIDGET END -->
	</div>
	<!-- end row -->
</section>
@endsection

@push('script')
	<script type="text/javascript">
		$(document).ready(function() {
			pageSetUp();
			jQuery("#jqgrid").jqGrid({
				data : {!! $json_data !!},
				datatype : "local",
				height : 'auto',
				colNames : {!! $col_name !!},
				colModel : {!! $col_model !!},
				rowNum : 10,
				rowList : [10, 20, 30],
				pager : '#pjqgrid',
				sortname : 'id',
				toolbarfilter : true,
				viewrecords : true,
				sortorder : "asc",
				editurl : "",
				caption : "{{ trans('budget.data_table') }}",
				multiselect : false,
				autowidth : true,
				width: "",
				// shrinkToFit:false,
				cmTemplate: { autoResizable: true },
				autoResizing: { compact: true },
				autoresizeOnLoad: true
		
			});
			// jQuery("#jqgrid").jqGrid('navGrid', "#pjqgrid", {
			// 	edit : false,
			// 	add : false,
			// 	del : false
			// });
			// jQuery("#jqgrid").jqGrid('inlineNav', "#pjqgrid");
			$("body").mLoading("hide");
		});

		$(window).on('resize.jqGrid', function() {
			$("#jqgrid").jqGrid('setGridWidth', $("#content").width());
		})

	</script>
@endpush
