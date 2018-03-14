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

@push('css')
<style>
    .error_color_0 {
        background: #ffffff;
        height: 60px;
    }
    .error_color_1 {
        background: blue;
         height: 60px;
    }
     .error_color_2 {
        background: yellow;
         height: 60px;
    }
     .error_color_3 {
        background: red;
        height: 60px;
    }
</style> 
@endpush

@section('content')
    
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.work_planning'),
        'text2' => trans('wp.repair_condition')
    ])

    <section id="widget-grid">                          
        <div class="row">  
            <div class="col-sm-12 col-md-12 col-lg-12">
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

<!--             <div class="col-sm-12 col-md-12 col-lg-6">
                @box_open(trans("wp.price_method"))
                    <div>
                        <div class="widget-body no-padding">
                             <table class='table table-bordered'>
                                <thead >
                                    <tr>
                                        <th>{{trans('wp.method_name')}}</th>
                                        <th>{{trans('wp.price')}} (vnd)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($methods as $row)
                                    <tr>
                                        <td> {{$row['method_name']}}</td>
                                        <td> {{$row['price']}}</td>
                                    </tr>
                                @endforeach  
                                </tbody>
                             </table>
                        </div>
                    </div>
                @box_close
            </div>  -->

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
                                    <a href="#tabs-d">{{trans('wp.planned_section')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-b">{{trans('wp.target_section')}}</a>
                                </li>
                                <li>
                                    <a href="#tabs-c">{{trans('wp.repair_work_long_list')}}</a>
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
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/repair_matrix/{{$session_id}}">
                                {{trans('wp.back_matrix')}}
                            </a>
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/formulate_annual_year/{{$session_id}}">
                                {{trans('wp.formulate_annual_year')}}
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
            $('#tabs').tabs({
                active: 2
            });
        });
    </script>
@endpush

