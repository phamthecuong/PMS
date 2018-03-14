@extends('front-end.layouts.app')

@section('side_menu_pms_dataset')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('menu.home')}}</li>
        <li>{{trans('menu.pms_dataset_title')}}</li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-database',
    'text1' => trans('menu.pms_dataset_title'),
    'text2' => trans('menu.list_pms_process')
])

<section id="widget-grid">				
	<div class="row">			
		<article class="col-lg-6 col-md-12 col-xs-12">
			@box_open(trans("pms_dataset.pms_data_panel"))	
			<div>	
				<div class="widget-body no-padding">
                    @include("layouts.elements.table", [			
                        'url' => '/ajax/pms_dataset',
                        'columns' => [			
                            ['data' => 'year', 'title' => trans('pms_dataset.year'), 'hasFilter' => false],
	                      	['data' => 'progress', 'title' => trans('pms_dataset.progress'), 'hasFilter' => false],
	                      	['data' => 'created_at', 'title' => trans('pms_dataset.created_at'), 'hasFilter' => false],
                            ['data' => 'action', 'title' => trans('pms_dataset.action'), 'hasFilter' => false, 'sortable' => false]
                        ],
                        'reloadTime' => 3000
                    ])
                    <div class="widget-footer">
                        @if ($rec > 0)
                            <a class="btn btn-danger" href="#" disabled>
                                {!!trans('pms_dataset.process_is_running')!!}
                            </a>
                        @else
                    	<a class="btn btn-danger" href="/user/pms_dataset/create">
	                		{!!trans('pms_dataset.new_process')!!}
	                	</a>
                        @endif
                    </div>
				</div>	
			</div>		
			@box_close		
		</article>			
        @if(!empty($year))
        <article class="col-lg-6 col-md-12 col-xs-12">
            @box_open(trans("pms_dataset.summary_result"))  
            <div>   
                <div class="widget-body">
                    <legend><b>{!! trans('pms_dataset.summary_result_in_year') !!} {!! $year !!}</b></legend>
                    <div class="table-responsive">
                        <table id="MyDataTable" class="table table-bordered">
                            <tr>
                                <td rowspan="2" class="title_data">{!! trans('pms_dataset.pc_data') !!}</td>
                                <td>{!! trans('pms_dataset.total_length') !!}</td>
                                <td>{{ $pc_total_length }}</td>
                            </tr>
                            <tr>
                                <td>{!! trans('pms_dataset.total_100m_pc_section') !!}</td>
                                <td>{{ $pc_total }}</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="title_data">{!! trans('pms_dataset.ri_data') !!}</td>
                                <td>{!! trans('pms_dataset.total_length') !!}</td>
                                <td>{{ $ri_total_length }}</td>
                            </tr>
                            <tr>
                                <td>{!! trans('pms_dataset.total_100m_ri_section') !!}</td>
                                <td>{{ $ri_total }}</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="title_data">{!! trans('pms_dataset.mh_data') !!}</td>
                                <td>{!! trans('pms_dataset.total_length') !!}</td>
                                <td>{{ $mh_total_length }}</td>
                            </tr>
                            <tr>
                                <td>{!! trans('pms_dataset.total_100m_mh_section') !!}</td>
                                <td>{{ $mh_total }}</td>
                            </tr>
                        </table>
                    </div>
                </div>  
            </div>      
            @box_close      
        </article>
        @endif
	</div>				
</section>					
@endsection

@push("css")
<style type="text/css">
    .title_data {
        vertical-align: middle !important;
        font-weight: bold;
    }
    .pace {
        display: none !important;
    }
</style>
@endpush