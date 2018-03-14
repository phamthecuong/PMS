@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection

@section('deterioration_show_history')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('deterioration.home')}}</li>
        <li>{{trans('deterioration.deterioration')}}</li>
        <li>{{trans('deterioration.history')}}</li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-cube',
    'text1' => trans('deterioration.deterioration'),
    'text2' => trans('deterioration.history')
])

<section id="widget-grid">				
	<div class="row">			
		<article class="col-lg-12">		
			@box_open(trans("deterioration.history"))	
			<div>	
				<div class="widget-body no-padding">
                    @include("layouts.elements.table", [			
                        'url' => '/user/deterioration/history/data',
                        'columns' => [
                            ['data' => 'created_at', 'title' => trans('deterioration.created_at')],
	                      	['data' => 'organizations.name_' . (App::isLocale('en') ? 'en' : 'vn'), 'title' => trans('deterioration.region')],
	                      	['data' => 'year_of_dataset', 'title' => trans('deterioration.year')],
	                      	['data' => 'progress', 'title' => trans('deterioration.progress'), 'hasFilter' => false],
	                      	['data' => 'action', 'hasFilter' => false, 'title' => trans('deterioration.action')]
                        ]			
                    ])
				</div>	
			</div>		
			@box_close		
		</article>			
	</div>				
</section>					
@endsection
