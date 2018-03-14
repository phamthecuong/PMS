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
    <li>{{trans("wp.start_process")}}</li>
</ol>
@endsection

@section('content')
    
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.working_planning'),
        'text2' => trans('wp.dataset_import')
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
                        </ul>
                    </div>
                     <!-- <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/repair_condition/{{$session_id}}/error" style="margin-bottom: 5px;" target="_blank">
                        {{trans('wp.list_records_error')}}</a> -->
                </div> 
            @box_close
        </div>
            <article class="col-lg-12">
                @box_open(trans("wp.section_list"))
                <div>               
                    <div class="widget-body">
                        <div id="tabs">
                            <ul>
                                <li>
                                    <a href="#tabs-a">{{trans('wp.valid_section')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-b">{{trans('wp.invalid_section')}}</a>
                                </li>
                            </ul>
                            <div id="tabs-a">
                                <div class="table-responsive">
                                @include("custom.table_extra_data", [ 
                                    'table_id' => 'valid_sections',    
                                    'url' => "/ajax/work/dataTable/create/$session_id",          
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
                            </div>
                            <div id="tabs-b">
                                <div class="row">
                                    <div class="col-lg-4 col-md-12">
                                        <table>
                                            <tr>
                                                <td class="padding-10"><div class="wp-error wp-error-1"></div></td>
                                                <td>{{trans('wp.road_class_invalid')}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <table>
                                            <tr>
                                                <td class="padding-10"><div class="wp-error wp-error-2"></div></td>
                                                <td>{{trans('wp.cracking_invalid')}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <table>
                                            <tr>
                                                <td class="padding-10"><div class="wp-error wp-error-3"></div></td>
                                                <td>{{trans('wp.rutting_invalid')}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <table>
                                            <tr>
                                                <td class="padding-10"><div class="wp-error wp-error-4"></div></td>
                                                <td>{{trans('wp.iri_invalid')}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <table>
                                            <tr>
                                                <td class="padding-10"><div class="wp-error wp-error-5"></div></td>
                                                <td>{{trans('wp.pavement_type_invalid')}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                @include("custom.table_extra_data", [ 
                                    'table_id' => 'invalid_sections',    
                                    'url' => "/ajax/work/dataTable/create/$session_id/1",          
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
                                        ['data' => 'error', 'title' => trans('wp.error')],  
                                    ]
                                ])
                                </div>
                            </div>
                        </div>
                        
                        <div class="widget-footer">
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/default_repair_matrix/{{$session_id}}">
                                {{trans('wp.next_base_year')}}
                            </a>
                        </div>      
                    </div>          
                </div>
                @box_close            
            </article>                  
        </div>                      
    </section>                          
@endsection

@push('script')
    <script type="text/javascript">
        $('document').ready(function() {
            $('#tabs').tabs();
        });
    </script>
@endpush
