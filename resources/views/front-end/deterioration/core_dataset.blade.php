@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection

@section('deterioration_regions_evaluated')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('deterioration.home')}}</li>
        <li>{{trans('deterioration.deterioration')}}</li>
        <li>{{trans('deterioration.core_dataset')}}</li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-cube',
    'text1' => trans('deterioration.deterioration'),
    'text2' => trans('deterioration.core_dataset')
])

<section id="widget-grid">				
	<div class="row">			
		<article class="col-lg-12">		
			@box_open(trans("deterioration.core_dataset"))	
			<div>	
				<div class="widget-body no-padding">
                    @include("layouts.elements.table", [			
                        'url' => '/user/deterioration/get_core_dataset',
                        'columns' => [
                        	['data' => 'year_of_dataset', 'title' => trans('deterioration.year')],
                            ['data' => 'name_' . ((App::getLocale() == 'en') ? 'en' : 'vn'), 'title' => trans('deterioration.region')],
	                      	['data' => 'updated_at', 'title' => trans('deterioration.updated_at')],
                        ]			
                    ])
				</div>	
			</div>		
			@box_close		
		</article>			
	</div>				
</section>					
@endsection
