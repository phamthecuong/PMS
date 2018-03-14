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
    <li>{{trans("wp.start_new_process")}}</li>
</ol>
@endsection

@section('content')
    <?php 
        if ($list == 0)
        {
            $text2 = trans('wp.5_year_plan');
        }
        else if ($list == 1)
        {
            $text2 = trans('wp.proposal_list');
        }
        else if ($list == 2)
        {
            $text2 = trans('wp.planned_list');
        }
        
    ?>
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.working_planning'),
        'text2' => $text2,
    ])
    @if ($status == 1 && $list == 2)
        <div class="alert alert-warning">
            <i class="fa fa-check"></i> {{ trans('wp.plan_done') }}
        </div>
    @endif
    <section id="widget-grid">                          
        <div class="row">  
            <div class="col-sm-12 col-md-12 col-lg-6">
                @box_open(trans("wp.info"))
                    <div>
                        <div class="widget-body">
                            @include('front-end.work_planning.process_info', [
                                'text_region' => $text_region,
                                'text_year' => $text_year,
                                'base_planning_year' => $base_planning_year
                            ])
                            <div class="widget-footer text-right">
                                <a href="/admin/repair_methods" target="_blank">
                                    {{ trans('wp.check_full_repair_methods_list') }} <i class="fa fa-arrow-right"></i> 
                                </a>
                            </div>
                        </div>
                    </div>
                @box_close
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6">
                @box_open(trans("wp.cost_by_year"))
                    <div>
                        <div class="widget-body">
                            <div>
                                {{trans('wp.year_1_cost')}}: 
                                <small class="text-muted"><i> <span id="year1total">0</span> <i></i></i></small>
                                 {{trans('wp.1000_vnd')}}
                            </div>
                            <div>
                                {{trans('wp.year_2_cost')}}: 
                                <small class="text-muted"><i> <span id="year2total">0</span><i></i></i></small>
                                 {{trans('wp.1000_vnd')}}
                            </div>
                            <div>
                                {{trans('wp.year_3_cost')}}: 
                                <small class="text-muted"><i> <span id="year3total">0</span><i></i></i></small>
                                 {{trans('wp.1000_vnd')}}
                            </div>
                            <div>
                                {{trans('wp.year_4_cost')}}: 
                                <small class="text-muted"><i> <span id="year4total">0</span><i></i></i></small>
                                 {{trans('wp.1000_vnd')}}
                            </div>
                            <div>
                                {{trans('wp.year_5_cost')}}: 
                                <small class="text-muted"><i> <span id="year5total">0</span><i></i></i></small>
                                 {{trans('wp.1000_vnd')}}
                            </div>
                        </div>
                    </div>
                @box_close
            </div>

            <article class="col-sm-12 col-md-12 col-lg-12">                 
                @box_open(trans("wp.sections_list"))             
                <div>
                    <div class="widget-body">
                        <div id="tabs">
                            <ul>
                                <li>
                                    <a href="#tabs-a">{{trans('wp.invalid_section')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-j">{{ trans('wp.planned_section') }}</a>
                                </li>
                                <li>
                                    <a href="#tabs-b">{{trans('wp.target_section')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-c">{{trans('wp.repair_work_long_list')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-d">{{trans('wp.list_for_year_1')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-e">{{trans('wp.list_for_year_2')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-f">{{trans('wp.list_for_year_3')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-g">{{trans('wp.list_for_year_4')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-h">{{trans('wp.list_for_year_5')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-i">{{ trans('wp.remaining_section') }}</a>
                                </li>
                            </ul>
                            <div id="tabs-a">
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
                            <div id="tabs-b">
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
                            <div id="tabs-c">
                                <div class="table-responsive"> 
                                    @include("custom.table_extra_data", [ 
                                        'table_id' => 'repair_work_long_list',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/3",          
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
                            <div id="tabs-d">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/1/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/1/". $list,
                                        'table_id' => 'year_1',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/4/". $list,
                                        'status' => $status,
                                        'columns' => [      
                                            [],    
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
                                            ['data' => 'selected_repair_method', 'title' => trans('wp.repair_method')],
                                            ['data' => 'selected_quantity_unit', 'title' => trans('wp.quantity')],
                                            ['data' => 'selected_unit_quantity', 'title' => trans('wp.unit_of_quantity')],
                                            ['data' => 'selected_repair_classification', 'title' => trans('wp.repair_classification')],
                                            ['data' => 'amount', 'title' => trans('wp.repair_cost')],      

                                        ],
       
                                        'extra_dropdown' => [
                                            [
                                                'year' => 2,
                                                'title' => trans('wp.to_year_2')
                                            ],
                                            [
                                                'year' => 3,
                                                'title' => trans('wp.to_year_3')
                                            ],
                                            [
                                                'year' => 4,
                                                'title' => trans('wp.to_year_4')
                                            ],
                                            [
                                                'year' => 5,
                                                'title' => trans('wp.to_year_5')
                                            ],
                                            [
                                                'year' => null,
                                                'title' => trans('wp.remaining_section')
                                            ]
                                        ]
                                        
                                    ])
                                </div>
                            </div>
                            <div id="tabs-e">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/2/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/2/". $list,
                                        'table_id' => 'year_2',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/5/". $list,
                                        'status' => $status,          
                                        'columns' => [          
                                            [],  
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
                                            ['data' => 'selected_repair_method', 'title' => trans('wp.repair_method')],
                                            ['data' => 'selected_quantity_unit', 'title' => trans('wp.quantity')],
                                            ['data' => 'selected_unit_quantity', 'title' => trans('wp.unit_of_quantity')],
                                            ['data' => 'selected_repair_classification', 'title' => trans('wp.repair_classification')],
                                            ['data' => 'amount', 'title' => trans('wp.repair_cost')],
                                        ],
                                        'extra_dropdown' => [
                                            [
                                                'year' => 1,
                                                'title' => trans('wp.to_year_1')
                                            ],
                                            [
                                                'year' => 3,
                                                'title' => trans('wp.to_year_3')
                                            ],
                                            [
                                                'year' => 4,
                                                'title' => trans('wp.to_year_4')
                                            ],
                                            [
                                                'year' => 5,
                                                'title' => trans('wp.to_year_5')
                                            ],
                                            [
                                                'year' => null,
                                                'title' => trans('wp.remaining_section')
                                            ]
                                        ]
                                    ])
                                </div>
                            </div>
                            <div id="tabs-f">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/3/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/3/". $list,
                                        'table_id' => 'year_3',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/6/". $list,   
                                        'status' => $status,       
                                        'columns' => [          
                                            [],  
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
                                            ['data' => 'selected_repair_method', 'title' => trans('wp.repair_method')],
                                            ['data' => 'selected_quantity_unit', 'title' => trans('wp.quantity')],
                                            ['data' => 'selected_unit_quantity', 'title' => trans('wp.unit_of_quantity')],
                                            ['data' => 'selected_repair_classification', 'title' => trans('wp.repair_classification')],
                                            ['data' => 'amount', 'title' => trans('wp.repair_cost')],
                                        ],
                                        'extra_dropdown' => [
                                            [
                                                'year' => 1,
                                                'title' => trans('wp.to_year_1')
                                            ],
                                            [
                                                'year' => 2,
                                                'title' => trans('wp.to_year_2')
                                            ],
                                            [
                                                'year' => 4,
                                                'title' => trans('wp.to_year_4')
                                            ],
                                            [
                                                'year' => 5,
                                                'title' => trans('wp.to_year_5')
                                            ],
                                            [
                                                'year' => null,
                                                'title' => trans('wp.remaining_section')
                                            ]
                                        ]
                                    ])
                                </div>
                            </div>
                            <div id="tabs-g">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/4/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/4/". $list,
                                        'table_id' => 'year_4',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/7/". $list, 
                                        'status' => $status,         
                                        'columns' => [
                                            [],     
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
                                            ['data' => 'selected_repair_method', 'title' => trans('wp.repair_method')],
                                            ['data' => 'selected_quantity_unit', 'title' => trans('wp.quantity')],
                                            ['data' => 'selected_unit_quantity', 'title' => trans('wp.unit_of_quantity')],
                                            ['data' => 'selected_repair_classification', 'title' => trans('wp.repair_classification')],
                                            ['data' => 'amount', 'title' => trans('wp.repair_cost')],
                                        ],
                                        'extra_dropdown' => [
                                            [
                                                'year' => 1,
                                                'title' => trans('wp.to_year_1')
                                            ],
                                            [
                                                'year' => 2,
                                                'title' => trans('wp.to_year_2')
                                            ],
                                            [
                                                'year' => 3,
                                                'title' => trans('wp.to_year_3')
                                            ],
                                            [
                                                'year' => 5,
                                                'title' => trans('wp.to_year_5')
                                            ],
                                            [
                                                'year' => null,
                                                'title' => trans('wp.remaining_section')
                                            ]
                                        ]
                                    ])
                                </div>
                            </div>
                            <div id="tabs-h">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/5/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/5/". $list,
                                        'table_id' => 'year_5',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/8/". $list,
                                        'status' => $status,          
                                        'columns' => [  
                                            [],        
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
                                            ['data' => 'selected_repair_method', 'title' => trans('wp.repair_method')],
                                            ['data' => 'selected_quantity_unit', 'title' => trans('wp.quantity')],
                                            ['data' => 'selected_unit_quantity', 'title' => trans('wp.unit_of_quantity')],
                                            ['data' => 'selected_repair_classification', 'title' => trans('wp.repair_classification')],
                                            ['data' => 'amount', 'title' => trans('wp.repair_cost')],
                                        ],
                                        'extra_dropdown' => [
                                            [
                                                'year' => 1,
                                                'title' => trans('wp.to_year_1')
                                            ],
                                            [
                                                'year' => 2,
                                                'title' => trans('wp.to_year_2')
                                            ],
                                            [
                                                'year' => 3,
                                                'title' => trans('wp.to_year_3')
                                            ],
                                            [
                                                'year' => 4,
                                                'title' => trans('wp.to_year_4')
                                            ],
                                            [
                                                'year' => null,
                                                'title' => trans('wp.remaining_section')
                                            ]
                                        ]
                                    ])
                                </div>
                            </div>
                            <div id="tabs-i">
                                <div class="table-responsive"> 
                                    @include("custom.table_checkbox", [ 
                                        'export_url' => "/user/work_planning/export_file/$session_id/6/". $list,
                                        'move_url' => "/user/work_planning/move_section/$session_id/6/". $list,
                                        'table_id' => 'remaining_section',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/9/". $list,  
                                        'status' => $status,        
                                        'columns' => [  
                                            [],        
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
                                        ],
                                        'extra_dropdown' => [
                                            [
                                                'year' => 1,
                                                'title' => trans('wp.to_year_1')
                                            ],
                                            [
                                                'year' => 2,
                                                'title' => trans('wp.to_year_2')
                                            ],
                                            [
                                                'year' => 3,
                                                'title' => trans('wp.to_year_3')
                                            ],
                                            [
                                                'year' => 4,
                                                'title' => trans('wp.to_year_4')
                                            ],
                                            [
                                                'year' => 5,
                                                'title' => trans('wp.to_year_5')
                                            ],
                                        ]
                                    ])
                                </div>
                            </div>
                            <div id="tabs-j">
                                <div class="table-responsive"> 
                                    @include("custom.table_extra_data", [ 
                                        'table_id' => 'planned_section',    
                                        'url' => "/ajax/work/dataTable/create/$session_id/11",          
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
                        </div>
                        <div class="widget-footer">
                            @if ($list == 0)
                                <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/formulate_annual_year/{{$session_id}}">
                                    {{trans('wp.back_budget')}}
                                </a>
                            @elseif ($list == 1)
                                <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/result/{{$session_id}}">
                                    {{trans('wp.back_result')}}
                                </a>
                            @elseif ($list == 2)
                                <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/proposal/{{$session_id}}">
                                    {{trans('wp.back_proposal')}}
                                </a>
                            @endif

                            <button class="btn bg-color-blueLight txt-color-white" onclick="generate()">{{ trans('wp.generate') }}</button>

                            @if (!isset($excel_flg) || $excel_flg < 16)
                                <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/export/{{ $session_id }}/{{ $list }}" disabled>{{ trans('wp.export') }}</a>
                            @else
                                <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/export/{{ $session_id }}/{{ $list }}">{{ trans('wp.export') }}</a>
                            @endif

                            @if ($list == 0)
                                <button class="btn bg-color-blueLight txt-color-white" onclick="getProposal();">{{ trans('wp.next_proposal') }}</button>
                            @elseif ($list == 1)
                                <button class="btn bg-color-blueLight txt-color-white" onclick="getPlanned();">{{ trans('wp.next_planned') }}</button>
                            @elseif ($list == 2 && $status == 0)   
                                <button class="btn bg-color-blueLight txt-color-white" onclick="savePlan();">{{ trans('wp.save_plan') }}</button>
                            @endif
                            
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
        function generate() {
            showLoading();
            var url = '{{route('ajax.wp.generate.excel')}}';
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    session_id: '{{$session_id}}',
                    list: '{{ $list }}'
                }
            })
            .done(function(response) {
                if (response.code == 200) {
                    location.href =  '/user/work_planning/generate/' + "{{$session_id}}" + "/" + "{{ $list }}";
                } else {
                    alert(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }

        function getProposal() {
            showLoading();
            var url = '{{route('ajax.wp.proposal')}}';
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    session_id: '{{$session_id}}',
                }
            })
            .done(function(response) {
                if (response.code == 200) {
                    location.href =  '/user/work_planning/proposal/' + "{{$session_id}}" + "/";
                } else {
                    alert(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }

        function getPlanned() {
            showLoading();
            var url = '{{route('ajax.wp.planned')}}';
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    session_id: '{{$session_id}}',
                }
            })
            .done(function(response) {
                if (response.code == 200) {
                    location.href =  '/user/work_planning/planned/' + "{{$session_id}}" + "/";
                } else {
                    alert(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }

        function savePlan() {
            showLoading();
            var url = '{{route('ajax.wp.save.plan')}}';
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    session_id: '{{$session_id}}',
                }
            })
            .done(function(response) {
                if (response.code == 200) {
                    alertSmart('{{ trans('wp.plan_has_been_saved') }}');
                    hideLoading();
                } else {
                    alert(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }
        $('document').ready(function() {
            $('#tabs').tabs();
            getTotalCost();
        });

        function getTotalCost() {
            
            $.get("{{route('ajax.wp.total.cost', array(
                'session_id' => $session_id,
                'list' => $list
            ))}}", {
                _token : '{!! csrf_token() !!}'
            }, function(data) {
                $('#year1total').html(data[1]);
                $('#year2total').html(data[2]);
                $('#year3total').html(data[3]);
                $('#year4total').html(data[4]);
                $('#year5total').html(data[5]);
            }, "json");
        }

        function alertSmart(content) {
            $.SmartMessageBox({
                title : "{{trans('wp.success')}}",
                content : content,
                buttons : "[{{trans('wp.ok')}}]"
            },function(ButtonPressed) {
                if (ButtonPressed === "{{trans('wp.ok')}}") {
                    location.reload();    
                }
            });
        }
    </script>
@endpush
