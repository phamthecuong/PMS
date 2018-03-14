@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_show_history')
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
            {{trans('menu.wp_history')}}
        </li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.work_planning'),
	'text2' => trans('menu.wp_history')
])

<section id="widget-grid">				
	<div class="row">			
		<div class="col-lg-12">
		    @if (Session::has('flash_message'))
		        <div class="alert alert-{!! Session::get('flash_level') !!}">
		            {!! Session::get('flash_message') !!}
		        </div>
		    @endif      
		</div>
		<article class="col-lg-12">		
			@box_open(trans("wp.history_panel_title"))	
			<div>	
				<div class="widget-body no-padding">
                    @include("layouts.elements.table", [			
                        'url' => '/ajax/work/history',
                        'columns' => [
                            ['data' => 'created_at', 'title' => trans('wp.created_at')],
	                      	['data' => 'organization_name', 'title' => trans('wp.region')],
	                      	['data' => 'year', 'title' => trans('wp.year')],
	                      	['data' => 'candidate', 'title' => trans('wp.candidate'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'proposal', 'title' => trans('wp.proposal'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'final', 'title' => trans('wp.final'), 'hasFilter' => false, 'sortable' => false],
	                      	['data' => 'action', 'hasFilter' => false, 'title' => trans('wp.action'), 'sortable' => false]
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
		tr td:nth-child(4), tr td:nth-child(5), tr td:nth-child(6) {
			text-align: center;
		}
	</style>
@endpush
