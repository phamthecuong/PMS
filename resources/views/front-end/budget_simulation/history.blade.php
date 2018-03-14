@extends('front-end.layouts.app')

@section('budget_simulation')
active
@endsection

@section('budget_simulation_show_history')
active
@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
	    <li>{{trans('budget.home')}}</li>
	    <li>{{trans('budget.budget_simulation')}}</li>
	    <li>{{trans('menu.list_history')}}</li>
	</ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.show_history_process')
])

<section id="widget-grid">				
	<div class="row">			
		<article class="col-lg-12">		
			@box_open(trans("budget.history_panel_title"))	
			<div>	
				<div class="widget-body no-padding">
                    @include("layouts.elements.table", [			
                        'url' => '/ajax/budget/history',
                        'columns' => [
                            ['data' => 'created_at', 'title' => trans('budget.created_at')],
	                      	['data' => 'organization_name', 'title' => trans('budget.region')],
	                      	['data' => 'route_name', 'title' => trans('budget.route')],
	                      	['data' => 'year', 'title' => trans('budget.year')],
	                      	['data' => 'progress0', 'title' => trans('budget.progress_scenario_0'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'progress1', 'title' => trans('budget.progress_scenario_1'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'progress2', 'title' => trans('budget.progress_scenario_2'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'progress3', 'title' => trans('budget.progress_scenario_3'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'action', 'hasFilter' => false, 'title' => trans('budget.action'), 'sortable' => false]
                        ]			
                    ])
				</div>	
			</div>		
			@box_close		
		</article>			
	</div>				
</section>					
@endsection

@push('css')
	<style type="text/css">
		.dataTable [data-progressbar-value] {
		    min-width: 150px;
		}
	</style>
@endpush
