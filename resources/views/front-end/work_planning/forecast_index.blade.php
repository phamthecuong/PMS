@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_start_new_process')
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
            {{trans('menu.start_process')}}
        </li>
    </ol>
@endsection

@section('content')
    
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.working_planning'),
        'text2' => trans('wp.forecast_index')
    ])

    <section id="widget-grid">                          
        <div class="row">  
        <div class="col-sm-12 col-md-12 col-lg-12">
            @box_open(trans("wp.info"))
                <div>
                    <div class="widget-body no-padding">
                        <ul class="list-unstyled padding-10">
                            <li>
                                <h1>
                                    <i class="fa fa-bank"></i>&nbsp;&nbsp;<span>{{trans('wp.Target_Region')}}: </span>
                                    <small>
                                        {{$region}}
                                    </small>
                                </h1>
                            </li>
                            <li>
                                <h1>
                                    <i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('wp.target_PMS_year')}} : </span>
                                    <small>{{$year}}</small>
                                </h1>
                            </li>
                            <li>
                                <h1>
                                    <i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('wp.base_planning_year')}} : </span>
                                    <small>{{$base_planning_year}}</small>
                                </h1>
                            </li>
                        </ul>
                        <div class="widget-footer text-right">
                            <a href="/admin/repair_methods" target="_blank">
                                {{ trans('wp.check_full_repair_methods_list') }} <i class="fa fa-arrow-right"></i> 
                            </a>
                        </div>
                    </div>
                    
                     <!-- <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/repair_condition/{{$session_id}}/error" style="margin-bottom: 5px;" target="_blank">
                        {{trans('wp.list_records_error')}}</a> -->
                </div> 
            @box_close
        </div>
            <article class="col-lg-12">
                @box_open(trans("wp.section_list"))
                <div>               
                    <div class="widget-body no-padding">
                        <div class="table-responsive"> 
                            @include("custom.table_extra_data", [ 
                                'table_id' => 'valid_sections',    
                                'url' => "/ajax/work/dataTable/create/$session_id/2",          
                                'columns' => [          
                                    [
                                        'data' => 'route_name', 
                                        'title' => trans('wp.RouteName'),
                                        'name' => 'route_name',
                                        'items' => App\Models\tblBranch::allOptionToAjax(false, true),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'branch_number', 
                                        'title' => trans('wp.branch_number'),
                                        'name' => 'branch_number',
                                        'items' => App\Models\tblBranch::branchNumberOptionToAjax(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'road_class', 
                                        'title' => trans('wp.RoadClass'),
                                        'name' => 'road_class',
                                        'items' => App\Models\mstRoadClass::allOptionToAjax(false, 1, true),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'construction_year', 
                                        'title' => trans('wp.ConstractionYear'),
                                        'name' => 'construction_year',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'km_from', 
                                        'title' => trans('wp.from_km'),
                                        'name' => 'km_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'm_from', 
                                        'title' => trans('wp.from_m'),
                                        'name' => 'm_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'km_to', 
                                        'title' => trans('wp.to_km'),
                                        'name' => 'km_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'm_to', 
                                        'title' => trans('wp.to_m'),
                                        'name' => 'm_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    ['data' => 'section_length', 'title' => trans('wp.Lenght, m')],
                                    ['data' => 'number_of_lanes', 'title' => trans('wp.NumberOf Lanes')],
                                    [
                                        'data' => 'direction', 
                                        'title' => trans('wp.UpOr Down'),
                                        'name' => 'direction',
                                        'items' => \App\Classes\Helper::getListDirection(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'pavement_type', 
                                        'title' => trans('wp.PavementType'),
                                        'name' => 'pavement_type',
                                        'items' => \App\Models\mstSurface::getData(true),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    ['data' => 'lane_width', 'title' => trans('wp.Width,m')],
                                ]           
                            ])
                        </div>
                        <div class="widget-footer">
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/base_planning/{{$session_id}}">
                                {{trans('wp.back_base_year')}}
                            </a>
                            <a class="btn bg-color-blueLight txt-color-white" onclick="showLoading()" href="/user/work_planning/repair_matrix/{{$session_id}}">
                                {{trans('wp.next_matrix')}}
                            </a>
                        </div>
                    </div>          
                </div>
                @box_close            
            </article>                  
        </div>                      
    </section>                          
@endsection
