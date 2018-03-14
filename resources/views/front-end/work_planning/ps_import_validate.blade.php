@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_planned_section')
active
@endsection

@section('work_planning_planned_section_import')
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
            {{trans('menu.planned_section')}}
        </li>
        <li>
            {{trans('menu.planned_section_import')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-file-text',
        'text1' => trans('import.title'),
        'text2' => trans('import.small_title_list')
    ])

    <section ng-app="TBApp" ng-controller="ImportController" ng-init="reload()" id="widget-grid" class="">
        <div class="row">
            <article class="col-lg-12">
                <div class="jarviswidget" id="wid-id-0"
                     data-widget-togglebutton="false" data-widget-editbutton="false"
                     data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget">
                
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-file-image-o txt-color-darken"></i> </span>
                        <h2 class="hidden-xs hidden-sm">{{ trans('back_end.import')}} </h2>

                        <ul class="nav nav-tabs pull-right in" id="myTab">
                    
                            <li class="active">
                                <a data-toggle="tab" href="#s1" aria-expanded="true"><i class="fa fa-plus" aria-hidden="true" style="color: green"></i> <span class="hidden-mobile hidden-tablet">{{ trans('back_end.new_section')}} (@{{success}})</span></a>
                            </li>

                            <li class="">
                                <a data-toggle="tab" href="#s2" aria-expanded="false"><i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i> <span class="hidden-mobile hidden-tablet">{{ trans('back_end.invalid')}} (@{{err}})</span></a>
                            </li>

                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
                    <!-- widget div-->
                    <div role="content">
                        <!-- widget edit box -->
                        <div class="widget-body">
                            <!-- content -->
                            <div id="myTabContent" class="tab-content">
                                <div class="tab-pane fade active in" id="s1">
                                    <div class="widget-body no-padding">
                                        <div class="table-responsive">
                                             @include("custom.table_planned_section_import", [ 
                                                'table_id' => 'success',    
                                                'url' => "/user/work_planning/planned_section/import/". $file_name . "/success",          
                                                'columns' => [          
                                                    [
                                                        'data' => 'id',
                                                        'title' => trans('wp.id')
                                                    ],
                                                    [
                                                        'data' => 'route_name',
                                                        'title' => trans('wp.RouteName'),
                                                    ],
                                                    [
                                                        'data' => 'branch_number',
                                                        'title' => trans('wp.branch_number'),
                                                    ],
                                                    [
                                                        'data' => 'rmb',
                                                        'title' => trans('wp.rmb'),
                                                    ],
                                                    [
                                                        'data' => 'sb',
                                                        'title' => trans('wp.sb'),
                                                    ],
                                                    [
                                                        'data' => 'km_from', 
                                                        'title' => trans('wp.from_km'),                                             
                                                    ],
                                                    [
                                                        'data' => 'm_from', 
                                                        'title' => trans('wp.from_m'), 
                                                    ],
                                                    [
                                                        'data' => 'km_to', 
                                                        'title' => trans('wp.to_km'),                                                      
                                                    ],
                                                    [
                                                        'data' => 'm_to', 
                                                        'title' => trans('wp.to_m'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'length', 
                                                        'title' => trans('wp.Lenght, m')
                                                    ],
                                                    [
                                                        'data' => 'direction', 
                                                        'title' => trans('wp.UpOr Down'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'lane_pos_no', 
                                                        'title' => trans('wp.lane_pos_no'),
                                                    ],
                                                    [
                                                        'data' => 'planned_year', 
                                                        'title' => trans('wp.planned_year'),                                                     
                                                    ],
                                                    [
                                                        'data' => 'repair_method',
                                                        'title' => trans('wp.repair_method')
                                                    ],
                                                    [
                                                        'data' => 'repair_classification',
                                                        'title' => trans('wp.repair_classification')
                                                    ],
                                                    [
                                                        'data' => 'unit_cost',
                                                        'title' => trans('wp.unit_cost'),                                                     
                                                    ],
                                                    [
                                                        'data' => 'repair_quantity',
                                                        'title' => trans('wp.repair_work_quantity'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'repair_amount',
                                                        'title' => trans('wp.repair_cost'),      
                                                    ],
                                                    [
                                                        'data' => 'remarks',
                                                        'title' => trans('wp.remarks'),      
                                                    ]
                                                ]           
                                            ])
                                        </div>
                                    </div>
                                </div>
                                <!-- new tab: API interface -->
                                <div class="tab-pane fade" id="s2">
                                    <div class="widget-body no-padding">
                                        {{-- <div class="widget-body-toolbar">
                                            <a href="#" class="btn btn-default edit" ng-click="$event.preventDefault(); checkData()">{!! trans("back_end.edit") !!}</a>
                                        </div> --}}
                                        
                                        <div class="table-responsive">
                                             @include("custom.table_planned_section_import", [ 
                                                'table_id' => 'error',    
                                                'url' => "/user/work_planning/planned_section/import/". $file_name . "/error",          
                                                'columns' => [          
                                                    [
                                                        'data' => 'id',
                                                        'title' => trans('wp.id')
                                                    ],
                                                    [
                                                        'data' => 'route_name',
                                                        'title' => trans('wp.RouteName'),
                                                    ],
                                                    [
                                                        'data' => 'branch_number',
                                                        'title' => trans('wp.branch_number'),
                                                    ],
                                                    [
                                                        'data' => 'rmb',
                                                        'title' => trans('wp.rmb'),
                                                    ],
                                                    [
                                                        'data' => 'sb',
                                                        'title' => trans('wp.sb'),
                                                    ],
                                                    [
                                                        'data' => 'km_from', 
                                                        'title' => trans('wp.from_km'),                                             
                                                    ],
                                                    [
                                                        'data' => 'm_from', 
                                                        'title' => trans('wp.from_m'), 
                                                    ],
                                                    [
                                                        'data' => 'km_to', 
                                                        'title' => trans('wp.to_km'),                                                      
                                                    ],
                                                    [
                                                        'data' => 'm_to', 
                                                        'title' => trans('wp.to_m'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'length', 
                                                        'title' => trans('wp.Lenght, m')
                                                    ],
                                                    [
                                                        'data' => 'direction', 
                                                        'title' => trans('wp.UpOr Down'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'lane_pos_no', 
                                                        'title' => trans('wp.lane_pos_no'),
                                                    ],
                                                    [
                                                        'data' => 'planned_year', 
                                                        'title' => trans('wp.planned_year'),                                                     
                                                    ],
                                                    [
                                                        'data' => 'repair_method',
                                                        'title' => trans('wp.repair_method')
                                                    ],
                                                    [
                                                        'data' => 'repair_classification',
                                                        'title' => trans('wp.repair_classification')
                                                    ],
                                                    [
                                                        'data' => 'unit_cost',
                                                        'title' => trans('wp.unit_cost'),                                                     
                                                    ],
                                                    [
                                                        'data' => 'repair_quantity',
                                                        'title' => trans('wp.repair_work_quantity'),                                                       
                                                    ],
                                                    [
                                                        'data' => 'repair_amount',
                                                        'title' => trans('wp.repair_cost'),      
                                                    ],
                                                    [
                                                        'data' => 'remarks',
                                                        'title' => trans('wp.remarks'),      
                                                    ]
                                                ]           
                                            ])
                                        </div>
                                    </div>
                                </div>
            
                            </div>
                        <div class="widget-footer">
                            <!-- Button trigger modal -->
                            {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import">
                              <i class="fa fa-file-text fa-fw "></i> {{ trans('back_end.import') }}
                            </button> --}}
                            <form ng-if="success != 0" method="POST" action="/user/work_planning/planned_section/{{ $file_name }}/import" style="display: inline;">
                                {{ csrf_field() }}
                                {!! Form::lbSubmit(trans('back_end.import')) !!}
                            </form>
                        </div>
                            <!-- end content -->
                        </div>

                    </div>
                    <!-- end widget div -->
                </div>
            </article>
        </div>
        <!--Model for edit-->
        @include('custom.import.planned_section', [
            'element_id' => "element_id",
            'id' => 'model-edit',
            'element_show' => '.edit',
            'modal_title' => trans("master_table.edit"),
            'event_submit' => 'reValidate()',
            'scope_form' => 'forme',
            'button_complete' => trans('master_table.submit'),
            'button_cancel' => trans('master_table.cancel'),
            'visible' => $config
        ])
        <!-- Modal -->
       {{--  <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-file-text fa-fw "></i> {{ trans('back_end.import')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
               
                <div class="font-import alert-import">
                    <i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i> <span style="color: red">@{{err}}</span> {{ trans('back_end.record_err')}} <span ng-if="err != 0" >(<a href="{!! url('/'.$prefix_url.'/'.$file_name.'/export_invalid') !!}">{!! trans("back_end.exportInvalid") !!}</a>)</span>
                </div>
               
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('back_end.close') }}</button>
                <form ng-if="new != 0 || update !=0" method="POST" action="/user/work_planning/planned_section/import" style="display: inline;">
                    {{ csrf_field() }}
                    {!! Form::lbSubmit(trans('back_end.import')) !!}
                </form>
              </div>
            </div>
          </div>
        </div> --}}
    </section>
@endsection
@push('css')

    <style>
        .alert-import{
            padding: 0px 0px 10px 0px;
        }
        .font-import{
            font-size: 14px;
        }
        table.dataTable tbody tr td:nth-child(1),table.dataTable thead th:nth-child(1) { display:none; }
        table.dataTable.select tbody tr,
        table.dataTable thead th:first-child {
            cursor: pointer;
        }
        
        .selected{
            background-color: #B0BED9 !important;
        }
        tbody tr{
            cursor: pointer;
        }
        
        .form-control.text_number {
            width: 60% !important;
        }
        
        .form-group {
            width: 100% !important;
        }
        
        .boxbody {
            padding: 5px !important;
        }
        .form-horizontal .control-label {
            text-align: left !important;
        }
        
    </style>

@endpush
@push('script')
    <!-- toadstr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script type="text/javascript">
        var tbapp = angular.module('TBApp', []);
        tbapp.directive('changeSelect', function ($parse) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    element.on('change', function(event) {
                        var value = element.val().replace("number:", "");
                        var load = attrs.changeSelect + '(' + value + ')';
                        scope.$apply(function() {
                            $parse(load)(scope);
                        });
                    });
                }
            };
        });
        // tbapp.controller('ImportController', function($scope, $http, $timeout, $q) {});
        tbapp.controller('ImportController', function($scope, $http, $timeout, $q) {
            

            $scope.checkData = function() {
                if($scope.ids){
                 $http({
                        method: "GET",
                        url: '/user/work_planning/planned_section/{{ $file_name }}/import/' + $scope.ids,
                        params: {}
                 }).then(function (response) {
                         $scope.forme = response.data;
                         var d = $scope.loadClassification($scope.forme.repair_method);
                         // if (screen_url == 'road_inventories') {
                         //     $scope.loadSurfaceRI();
                         //     $scope.loadDesignSpeed();
                         // }
                         // else if (screen_url == 'maintenance_history') {
                         //     $scope.loadSurfaceMH();
                         // }
                         // if (screen_url == 'road_inventories') {
                         //     $scope.errors = convertErrorroad_inventories(response.data.err);
                         // }
                         // else if (screen_url == 'maintenance_history') {
                         //     $scope.errors = convertErrormaintenance_history(response.data.err);
                         // }
                         // else {
                         //     $scope.errors = convertErrortraffic_volume(response.data.err);
                         // }
                         $scope.errors = response.data.err;
                         $scope.is_import = false;
                         var a = $scope.loadSb();
                         var b = $scope.loadRoad();
                         var c = $scope.loadBrach();
                         // var d = $scope.loadSegment();
                         // var e = $scope.loadDistrictFrom();
                         // var g = $scope.loadDistrictTo();
                         // var k = $scope.loadWardFrom();
                         // var l = $scope.loadWardTo();
                         $q.all([a, b, c]).then(function () {
                             $('#model-edit').show();
                             $timeout(function () {
                                $('.my-modal:visible').find('.red-panel').scrollintoview();
                             });
                         });
                    });
                }else {
                    alert('{{trans('back_end.item_choose')}}')
                }
            }
            $scope.loadCategory = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/surface')}}/'+ id +'/repair_category'
                    }).then(function (response) {
                        $scope.categoryMH = response.data;
                        if (response.data) {
                            $scope.forme.r_category_id = response.data[0].id;
                            $scope.Datasurface = response.data;

                        }
                    }, function (xhr) {});
            }
            $scope.loadClassification = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/repair_method')}}/'+ id +'/classification'
                }).then(function (response) {
                    $scope.forme.repair_classification = response.data;
                    console.log($scope.forme.repair_classification);
                }, function (xhr) {});
            }

            $scope.loadCost = function(rmb_id, repair_method_id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/rmb')}}/'+ rmb_id +'/repair_method/' + repair_method_id + '/cost'
                }).then(function (response) {
                    $scope.forme.unit_cost = response.data;
                }, function (xhr) {});
            }
            // SB
            $scope.sbChange = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/rmb/')}}/' + id + '/sb',
                }).then(function (response) {
                    $scope.sb = response.data;
                    $scope.road =  [];
                    $scope.segment =  [];
                }, function (xhr) {

                });
            }
            $scope.loadSb = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/rmb/')}}/' + $scope.forme.rmb + '/sb',
                }).then(function (response) {
                    $scope.sb = response.data;
                }, function (xhr) {

                });
            }
            // Road
            $scope.roadChange = function(id) {
                
                return $http({
                    method: 'GET',
                    url: '{{asset('/ajax/sb/')}}/' + id + '/route',
                }).then(function (response) {
                    $scope.road = response.data;
                    $scope.segment =  [];
                }, function (xhr) {
                
                });
                
            }
            $scope.loadRoad = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('/ajax/sb/')}}/' + $scope.forme.sb + '/route',
                }).then(function (response) {
                    $scope.road = response.data;
                }, function (xhr) {

                });

            }
            $scope.loadBrach = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('/ajax/route/')}}/' + $scope.forme.road,
                }).then(function (response) {
                    $scope.forme.route_branch = response.data.branch_number;
                }, function (xhr) {

                });
            }
          
            $scope.reload = function(){
                $scope.filename = '<?php echo json_encode($file_name); ?>';
                    return $http({
                        method: 'GET',
                        url: '/user/work_planning/planned_section/{{$file_name}}/check',
                    }).then(function (response) {
                        $scope.data = response.data
                        $scope.success = response.data.success;
                        $scope.err = response.data.err;
                    }, function (xhr) {

                    });
            }

            $scope.loadRepairInfo = function(rmb_id, repair_method_id) {
                var r_classification = $scope.loadClassification(repair_method_id);
                var cost = $scope.loadCost(rmb_id, repair_method_id);
            }
            $scope.reValidate = function() {
                var data = $scope.forme;
                return $http({
                    method : "PUT",
                    url: '/user/work_planning/planned_section/{{$file_name}}/import/' + data.id,
                    params: data
                }).then(function (response) {
                    $('#model-edit').hide();
                    if (response.data == 2) {
                        $scope.is_import = true;
                        window.location.reload();
                        $scope.reload();
                    } else {
                        $scope.forme = {};
                        $('table.dataTable').DataTable().draw();
                        toastr.success("{{ trans('validation.success')  }}");
                        $scope.reload();

                    }
                }, function myError(xhr) {
                    toastr.error('Error');
                    $timeout(function(){
                        $('.my-modal:visible').find('.red-panel').scrollintoview();
                    });

                });
            };
            // $('#error').on('click', 'tbody tr', function(event) {
            //     $(this).addClass('selected').siblings().removeClass('selected');

            //     var val = $(this).find("td:nth-child(1)").text();
            //     $scope.ids = val;
            // });
            $('.ui-dialog-titlebar-close, #close').click(function () {
                $scope.errors = {};
            });
        });
    </script>
    <script type="text/javascript">

        // $('document').ready(function(){

        //     setOnChangeEvent();
        //     @if (isset($edit_flg) && $edit_flg == 0)
        //         var disabled = ['rmb', 'sb', 'route', 'segment', 'date_collection', 'km_from', 'm_from', 'km_to', 'm_to'];
        //         for (var i in disabled) {
        //             $('[name="' + disabled[i] + '"]').attr('disabled', 'disabled');
        //         }
        //     @endif
        // });

        // function setOnChangeEvent() {
        //     //sb_select.change(loadRoute);
        //    // rmb_select.change(loadSB);
        //     // route_select.change(function(){
        //     //     getRouteDetail();
        //     //     loadSegment();
        //     // });
        //     direction_select.change(function () {
        //         var direction_value = +direction_select.val();
        //         if (direction_value == 3)
        //         {
        //             if ($('[name="lane_pos_number"]')){
        //                 $('[name="lane_pos_number"]').val('0');
        //             }
        //             if ($('[name="no_lane"]')){
        //                 $('[name="no_lane"]').val('1');
        //             }
        //         }
        //     })
        //     // terrain_select.change(getDesignSpeed);
        //     // road_class_select.change(getDesignSpeed);
        //     km_from_input.change(calculateLength);
        //     m_from_input.change(calculateLength);
        //     km_to_input.change(calculateLength);
        //     m_to_input.change(calculateLength);
        // }
        // function convertErrorroad_inventories(errors) {
        //     var dataset = {
        //         scope_manage_error: [
        //             'rmb', 'sb', 'road', 'route_branch', 'segment_id'
        //         ],
        //         general_error: [
        //             'survey_time', 'terrian_type_id', 'road_class_id', 'design_speed'
        //         ],
        //         chainage_n_position_error: [
        //             'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat',
        //             'province_from', 'district_from', 'ward_from', 'province_to', 'district_to',
        //             'ward_to', 'length_as_per_chainage'
        //         ],
        //         information__of_ML_error: [
        //             'direction', 'lane_pos_number', 'no_lane', 'lane_width'
        //         ],
        //         other_information_error: [
        //             'construct_year', 'service_start_year', 'temperature', 'annual_precipitation', 'actual_length',
        //             'remark'
        //         ],
        //         material_layer_error: [
        //             'pavement_type_id', 'pavement_width', 'pavement_thickness'
        //         ]
        //     };

        //     for (var i in dataset) {
        //         var inputs = dataset[i];
        //         for (var j in inputs) {
        //             if (typeof errors[inputs[j]] != 'undefined') {
        //                 errors[i] = true;
        //                 break;
        //             }
        //         }
        //     }

        //     return errors;
        // }

        // function convertErrormaintenance_history(errors) {
        //     var dataset = {
        //         scope_manage_error: [
        //             'rmb', 'sb', 'road', 'route_branch', 'segment_id'
        //         ],
        //         general_error: [
        //             'survey_time', 'completion_date', 'repair_duration'
        //         ],
        //         chainage_n_position_error: [
        //             'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat',
        //             'province_from', 'district_from', 'ward_from', 'province_to', 'district_to',
        //             'ward_to', 'length'
        //         ],
        //         information_of_repair_section_error: [
        //             'direction', 'lane_pos_number', 'actual_length', 'total_width_repair_lane'
        //         ],
        //         mh_position_error: [
        //             'direction_running', 'distance'
        //         ],
        //         repair_method_info_error: [
        //             'repair_method', 'r_classification_id', 'r_struct_type_id'
        //         ],
        //         material_layer_error: [
        //             'pavement_type_id', 'r_category_id', 'total_pavement_thickness', 'binder_course', 'wearing_course'
        //         ]
        //     };

        //     for (var i in dataset) {
        //         var inputs = dataset[i];
        //         for (var j in inputs) {
        //             if (typeof errors[inputs[j]] != 'undefined') {
        //                 errors[i] = true;
        //                 break;
        //             }
        //         }
        //     }

        //     return errors;
        // }

        // function convertErrortraffic_volume(errors) {
        //     var dataset = {
        //         scope_manage_error: [
        //             'rmb', 'sb', 'road', 'route_branch', 'segment_id'
        //         ],
        //         information_traffic_error: [

        //             'survey_time', 'name', 'km_station', 'm_station', 'lat_station', 'lng_station'
        //         ],
        //         input_data_of_MH_error: [

        //             'up1', 'up2', 'up3', 'up4', 'up5', 'up6', 'up7', 'up8', 'up9', 'up10', 'total_traffic_volume_up', 'heavy_traffic_up',
        //             'down1', 'down2', 'down3', 'down4', 'down5', 'down6', 'down7', 'down8', 'down9', 'down10', 'total_traffic_volume_down', 'heavy_traffic_down',
        //             'total1', 'total2', 'total3', 'total4', 'total5', 'total6', 'total7', 'total8', 'total9', 'total10', 'traffic_volume_total', 'heavy_traffic_total', 'grand_total'
        //         ]
        //     };

        //     for (var i in dataset) {
        //         var inputs = dataset[i];
        //         for (var j in inputs) {
        //             if (typeof errors[inputs[j]] != 'undefined') {
        //                 errors[i] = true;
        //                 break;
        //             }
        //         }
        //     }

        //     return errors;
        // }
        $(".my-modal").draggable({
            handle: ".ui-dialog-titlebar",
            // containment: "window"
        });
    </script>
@endpush

