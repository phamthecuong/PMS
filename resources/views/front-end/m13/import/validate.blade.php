@extends('front-end.layouts.app')
@php
    $import_active;
        if ($prefix_url == 'road_inventories')
        {
            $import_active = 'road_inventory';
            $custom_column_datatable_err = ['id','section_id','road','segment_id','rmb', 'sb', 'km_from', 'm_from', 'km_to', 'm_to', 'direction', 'lane_pos_number', 'status'];
        }
        else if($prefix_url == 'maintenance_history')
        {
            $import_active = 'maintenance_history';
            $custom_column_datatable_err = ['id','section_id','road','segment_id','rmb', 'sb', 'km_from','m_from','km_to','m_to', 'direction', 'lane_pos_number', 'status'];
        }
        else
        {
            $import_active = 'traffic_volume';
            $custom_column_datatable_err = ['id','section_id','road','segment_id','rmb', 'sb', 'name', 'km_station', 'm_station', 'survey_time', 'status'];
        }
    $arr_datatable = [];
    foreach ($custom_column_datatable as $ccd)
    {
    $title = trans("import.".str_replace('_id', '', $ccd));
        $arr_datatable[] = [
            'data' => $ccd,
            'title' => $title
        ];
    }
    $arr_datatable_err = [];
    foreach ($custom_column_datatable_err as $columns)
    {
        $title = trans("import.".str_replace('_id', '', $columns));
        $arr_datatable_err[] = [
            'data' => $columns,
            'title' => $title
        ];
    }
 $lang = App::isLocale('en') ? 'en' : 'vi';
@endphp

@section('side_menu_'.$import_active)
    active
@endsection

@section('inputting_system')
    active
@endsection

@section('side_menu_import_'.$import_active)
    active
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.inputting_system')}}
        </li>
        <li>
            {{trans('menu.'.$prefix_url)}}
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
                                <a data-toggle="tab" href="#s1" aria-expanded="true"><i class="fa fa-plus" aria-hidden="true" style="color: green"></i> <span class="hidden-mobile hidden-tablet">{{ trans('back_end.new_section')}} (@{{new}})</span></a>
                            </li>
                            
                            <li class="">
                                <a data-toggle="tab" href="#s2" aria-expanded="false"><i class="fa fa-pencil-square-o" aria-hidden="true" style="color: green"></i> <span class="hidden-mobile hidden-tablet">{{ trans('back_end.update')}} (@{{update}})</span></a>
                            </li>

                            <li class="">
                                <a data-toggle="tab" href="#s3" aria-expanded="false"><i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i> <span class="hidden-mobile hidden-tablet">{{ trans('back_end.invalid')}} (@{{err}})</span></a>
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
                                <!-- new tab: API interface -->
                                <div class="tab-pane fade active in" id="s1">
                                    <div class="widget-body no-padding">
                                        <div class="table-responsive">
                                            @include("custom.table_extra_data", [
                                                'table_id' => 'new',
                                                'url' => '/'.$prefix_url.'/'.$file_name.'/new',
                                                'columns' => $arr_datatable
                                            ])
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="s2">
                                    <div class="widget-body no-padding">

                                        <div class="table-responsive">
                                            @include("custom.table_extra_data", [
                                                'table_id' => 'update',
                                                'url' => '/'.$prefix_url.'/'.$file_name.'/ajax_update',
                                                'columns' => $arr_datatable
                                            ])
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="s3">
                                    <div class="widget-body no-padding">
                                        <div class="widget-body-toolbar">
                                            <a href="{!! url('/'.$prefix_url.'/'.$file_name.'/export_invalid') !!}" class="btn btn-info">{!! trans("back_end.exportInvalid") !!}</a>
                                            <a href="#" class="btn btn-default edit" ng-click="$event.preventDefault(); checkData()">{!! trans("back_end.edit") !!}</a>
                                        </div>
                                        <div class="table-responsive">
                                            @include("custom.table_extra_data", [
                                                'table_id' => 'error',
                                                'url' => '/'.$prefix_url.'/'.$file_name.'/ajax',
                                                'columns' => $arr_datatable_err
                                            ])
                                            
                                           
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        <div class="widget-footer">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import">
                              <i class="fa fa-file-text fa-fw "></i> {{ trans('back_end.import') }}
                            </button>
                        </div>
                            <!-- end content -->
                        </div>

                    </div>
                    <!-- end widget div -->
                </div>
            </article>
        </div>
        <!--Model for edit-->
        @include('custom.import.'.$prefix_url, [
            'element_id' => "element_id",
            'id' => 'model-edit',
            'element_show' => '.edit',
            'modal_title' => trans("master_table.edit"),
            'event_submit' => 'reValidate()',
            'event_check' => 'reCheck()',
            'scope_form' => 'forme',
            'construct_year' => 'construct_year',
            'service_start_year' => 'service_start_year',
            'button_complete' => trans('master_table.submit'),
            'button_cancel' => trans('master_table.cancel'),
            'visible' => $config
        ])
        <!-- Modal -->
        <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-file-text fa-fw "></i> {{ trans('back_end.import')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="font-import alert-import"><i class="fa fa-plus" aria-hidden="true" style="color: green"></i></span> {{ trans('back_end.new_section')}} <span style="color: green">@{{new}}</span> {{ trans('back_end.record')}}</div>
                <div class="font-import alert-import"><i class="fa fa-pencil-square-o" aria-hidden="true" style="color: green"></i> {{ trans('back_end.update')}} <span style="color: green">@{{update}}</span> {{ trans('back_end.record')}}</div>
                <div class="font-import alert-import">
                    <i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i> <span style="color: red">@{{err}}</span> {{ trans('back_end.record_err')}} <span ng-if="err != 0" >(<a href="{!! url('/'.$prefix_url.'/'.$file_name.'/export_invalid') !!}">{!! trans("back_end.exportInvalid") !!}</a>)</span>
                </div>
                <div class="font-import">
                    <i class="fa fa-ban" aria-hidden="true" style="color: #7d5b15"></i> {{ trans('back_end.ignore')}} <span style="color: #7d5b15;">@{{ignore}}</span> {{ trans('back_end.record')}}
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('back_end.close') }}</button>
                <form ng-if="new != 0 || update !=0" method="POST" action="/{{ $prefix_url }}/{{ $file_name }}/import" style="display: inline;">
                    {{ csrf_field() }}
                    {!! Form::lbSubmit(trans('back_end.import')) !!}
                </form>
              </div>
            </div>
          </div>
        </div>
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
        table.dataTable tbody tr td:nth-child(2),table.dataTable thead th:nth-child(2) { display:none; }
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

        tbapp.controller('ImportController', function($scope, $http, $timeout, $q) {
            

            $scope.checkData = function() {
                if($scope.ids){
                 $http({
                        method: "GET",
                        url: '/{{ $prefix_url }}/{{ $file_name }}/import/' + $scope.ids,
                        params: {}
                 }).then(function (response) {
                     console.log(response.data.err);
                         var screen_url = '{{$prefix_url}}';
                         $scope.construct_year = (response.data.construct_year_y + '/' + response.data.construct_year_m);
                         $scope.service_start_year = (response.data.service_start_year_y + '/' + response.data.service_start_year_m);
                         $scope.forme = response.data;
                         if (screen_url == 'road_inventories') {
                             $scope.loadSurfaceRI();
                             $scope.loadDesignSpeed();
                         }
                         else if (screen_url == 'maintenance_history') {
                             $scope.loadSurfaceMH();
                         }
                         if (screen_url == 'road_inventories') {
                             $scope.errors = convertErrorroad_inventories(response.data.err);
                         }
                         else if (screen_url == 'maintenance_history') {
                             $scope.errors = convertErrormaintenance_history(response.data.err);
                         }
                         else if (screen_url == 'traffic_volume')  {
                             $scope.errors = convertErrortraffic_volume(response.data.err);
                         }
                         $scope.is_import = false;
                         var a = $scope.loadSb();
                         var b = $scope.loadRoad();
                         var c = $scope.loadBrach();
                         var d = $scope.loadSegment();
                         var e = $scope.loadDistrictFrom();
                         var g = $scope.loadDistrictTo();
                         var k = $scope.loadWardFrom();
                         var l = $scope.loadWardTo();
                         $q.all([a, b, c, d, e, g, k, l]).then(function () {
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
            $scope.reCheck = function() {
                showLoading();
                var data = $scope.forme;
                $http({
                    method : "PUT",
                    url: '/{{ $prefix_url }}/{{$file_name}}/import/' + data.id,
                    params: data,
                }).then(function (response) {
                    
                    hideLoading();
                    if (response.data == 2) {
                        $scope.is_import = true; 
                        window.location.reload();
                    }
                    else 
                    {
                        $scope.forme = {};
                        console.log(response.data);
                        $('table').DataTable().draw();
                        toastr.success("{{ trans('validation.success')  }}");
                        $('#model-edit').show();
                        $('#dialog_simple').scrollTop(0);
                        if (data.id == document.getElementById("error").rows[2].cells[1].innerHTML) 
                        {
                            $scope.idNext = document.getElementById("error").rows[1].cells[1].innerHTML;
                        }
                        else 
                        {
                            $scope.idNext = document.getElementById("error").rows[2].cells[1].innerHTML;
                        }
                        $http({
                            method : "GET",
                            url: '/{{ $prefix_url }}/{{ $file_name }}/import/' + $scope.idNext,
                            params: {}
                        }).then(function (response) {
                            var screen_url = '{{$prefix_url}}';
                            if (screen_url == 'road_inventories') {
                                $scope.errors = convertErrorroad_inventories(response.data.err);
                            }
                            else if (screen_url == 'maintenance_history') {
                                $scope.errors = convertErrormaintenance_history(response.data.err);
                            }
                            else if (screen_url == 'traffic_volume')  {
                                $scope.errors = convertErrortraffic_volume(response.data.err);
                            }
                            console.log(response.data);
                            $scope.forme = response.data;
                            $scope.errors = response.data.err;
                            $scope.is_import = false;
                        });
                    }
                },function myError(xhr) {
                    hideLoading();
                    var screen_url = '{{$prefix_url}}';
                    if (screen_url == 'road_inventories') {
                        $scope.errors = convertErrorroad_inventories(xhr.data);
                    }
                    else if (screen_url == 'maintenance_history') {
                        $scope.errors = convertErrormaintenance_history(xhr.data);
                    }
                    else {
                        $scope.errors = convertErrortraffic_volume(xhr.data);
                    }
                    console.log(xhr.data);
                    $scope.errors = xhr.data;
                    toastr.error('Error');
                });
            };
            $scope.loadDesignSpeed = function (id) {
                $http({
                    method: 'GET',
                    url: '{{asset('/ajax/frontend/terrain/')}}/'+ $scope.forme.terrian_type_id +'/road_class/' +  $scope.forme.road_class_id,
                    }).then(function (response) {
                        $scope.forme.design_speed = response.data;
                }, function (xhr) {});
            }
            $scope.SurfaceRIChange = function (id) {
                $http({
                    method: 'GET',
                    url: '{{asset('/ajax/frontend/material_type/')}}/'+ id +'/surface'
                }).then(function (response) {
                    $scope.forme.surface_id = response.data.surface_id;
                }, function (xhr) {});
            }
            $scope.loadSurfaceRI = function (id) {
                $http({
                    method: 'GET',
                    url: '{{asset('/ajax/frontend/material_type/')}}/'+ $scope.forme.pavement_type_id +'/surface'
                }).then(function (response) {
                    $scope.forme.surface_id = response.data.surface_id;
                }, function (xhr) {});
            }

            $scope.SurfaceMHChange = function (id) {
                $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/material_type/')}}/'+ id +'/surface'
                }).then(function (response) {
                    $scope.forme.surface_id = response.data.surface_id;
                    $scope.categoryMH = response.data.repair_categories;
                    if (response.data.repair_categories) {
                        $scope.forme.r_category_id = response.data.repair_categories[0].id;
                    }
                }, function (xhr) {});
            }

            $scope.loadSurfaceMH = function (id) {
                $http({
                    method: 'GET',
                    url:  '{{asset('ajax/frontend/material_type/')}}/'+ $scope.forme.pavement_type_id +'/surface'
                }).then(function (response) {
                    $scope.forme.surface_id = response.data.surface_id;
                    $scope.categoryMH = response.data.repair_categories;
                    if (response.data.repair_categories) {
                        $scope.forme.r_category_id = response.data.repair_categories[0].id;
                    }
                }, function (xhr) {});
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
                    $scope.forme.r_classification_id = response.data;
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
                    
                    $scope.loadSegment();
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
                    console.log($scope.forme.route_branch);
                }, function (xhr) {

                });
            }
            // segment
            $scope.segmentChange = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('/ajax/route/')}}/' + id + '/segment?sb_id=' + $scope.forme.sb,
                }).then(function (response) {
                    $scope.segment = response.data;
                    $scope.loadBrach();
                }, function (xhr) {
                });
            }
            $scope.loadSegment = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('/ajax/route/')}}/' + $scope.forme.road + '/segment?sb_id=' + $scope.forme.sb,
                }).then(function (response) {
                    $scope.segment = response.data;
                }, function (xhr) {

                });
            }
            // District Form
            $scope.districtFrom = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/province')}}/' + id + '/district',
                }).then(function (response) {
                    $scope.district_from = response.data;
                    $scope.ward_from = [];
                }, function (xhr) {

                });
            }
            $scope.loadDistrictFrom = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/province')}}/' + $scope.forme.province_from + '/district',
                }).then(function (response) {
                    $scope.district_from = response.data;
                }, function (xhr) {

                });
            }
            // District to
            $scope.districtTo = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/province')}}/' + id + '/district',
                }).then(function (response) {
                    $scope.district_to = response.data;
                    $scope.ward_to = [];
                }, function (xhr) {

                });
            }
            $scope.loadDistrictTo = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/province')}}/' + $scope.forme.province_to + '/district',
                }).then(function (response) {
                    $scope.district_to = response.data;
                }, function (xhr) {

                });
            }
            // Ward Form
            $scope.wardFrom = function(id) {
               return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/district')}}/' + id + '/ward',
                }).then(function (response) {
                    $scope.ward_from = response.data;
                }, function (xhr) {

                });
            }
            $scope.loadWardFrom = function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/district')}}/' + $scope.forme.district_from + '/ward',
                }).then(function (response) {
                    $scope.ward_from = response.data;
                }, function (xhr) {

                });
            }
            // Ward Form
            $scope.wardTo = function(id) {
               return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/district')}}/' + id + '/ward',
                }).then(function (response) {
                    $scope.ward_to = response.data;
                }, function (xhr) {

                });
            }
            $scope.loadWardTo= function(id) {
                return $http({
                    method: 'GET',
                    url: '{{asset('ajax/frontend/district')}}/' + $scope.forme.district_to + '/ward',
                }).then(function (response) {
                    $scope.ward_to = response.data;
                }, function (xhr) {

                });
            }
            $scope.reload = function(){
                $scope.filename = '<?php echo json_encode($file_name); ?>';
                    return $http({
                        method: 'GET',
                        url: '/{{ $prefix_url }}/{{$file_name}}/check/' + $scope.filename,
                    }).then(function (response) {
                        $scope.data = response.data
                        $scope.new = response.data.new;
                        $scope.update = response.data.update;
                        $scope.ignore = response.data.ignore;
                        $scope.err = response.data.err;
                    }, function (xhr) {

                    });
            }
            $scope.reValidate = function() {
                showLoading();
                var data = $scope.forme;
                var screen_url = '{{$prefix_url}}';
                if (angular.element("textarea"))
                {
                    var remark = angular.element("textarea").val();
                    data = angular.merge(data, {remark: remark});
                }
                if (screen_url == 'road_inventories') {
                    $scope.forme.construct_year_y = angular.element("input[name=construct_year]").val().substring(0,4);
                    $scope.forme.construct_year_m = angular.element("input[name=construct_year]").val().substring(5,7);
                    $scope.forme.service_start_year_y = angular.element("input[name=service_start_year]").val().substring(0,4);
                    $scope.forme.service_start_year_m = angular.element("input[name=service_start_year]").val().substring(5,7);
                    data = $scope.forme;
                }
                if (screen_url != 'traffic_volume') {
                    $scope.forme.direction = angular.element("select[name=direction]").val();
                    $scope.forme.lane_pos_number = angular.element("input[name=lane_pos_number]").val();
                    $scope.forme.no_lane = angular.element("input[name=no_lane]").val();
                    data = $scope.forme;
                }
                if (angular.element("input[name=length]"))
                {
                    var length = angular.element("input[name=length]").val();
                    var total = angular.element("input[name=total]").val();
                    data = angular.merge($scope.forme, {length: length});
                }

                if (angular.element("input[name=total1]") && angular.element("input[name=total2]")
                    && angular.element("input[name=total3]") && angular.element("input[name=total4]")
                    && angular.element("input[name=total5]") && angular.element("input[name=total6]")
                    && angular.element("input[name=total7]") && angular.element("input[name=total8]")
                    && angular.element("input[name=total9]") && angular.element("input[name=total10]")
                    && angular.element("input[name=total_traffic_volume_up]") && angular.element("input[name=total_traffic_volume_down]")
                    && angular.element("input[name=heavy_traffic_up]") && angular.element("input[name=heavy_traffic_down]")
                    && angular.element("input[name=traffic_volume_total]") && angular.element("input[name=heavy_traffic_total]")
                    && angular.element("input[name=grand_total]"))
                {
                    var total1 = angular.element("input[name=total1]").val();
                    var total2 = angular.element("input[name=total2]").val();
                    var total3 = angular.element("input[name=total3]").val();
                    var total4 = angular.element("input[name=total4]").val();
                    var total5 = angular.element("input[name=total5]").val();
                    var total6 = angular.element("input[name=total6]").val();
                    var total7 = angular.element("input[name=total7]").val();
                    var total8 = angular.element("input[name=total8]").val();
                    var total9 = angular.element("input[name=total9]").val();
                    var total10 = angular.element("input[name=total10]").val();
                    var total_traffic_volume_up = angular.element("input[name=total_traffic_volume_up]").val();
                    var total_traffic_volume_down = angular.element("input[name=total_traffic_volume_down]").val();
                    var heavy_traffic_up = angular.element("input[name=heavy_traffic_up]").val();
                    var heavy_traffic_down = angular.element("input[name=heavy_traffic_down]").val();
                    var traffic_volume_total = angular.element("input[name=traffic_volume_total]").val();
                    var heavy_traffic_total = angular.element("input[name=heavy_traffic_total]").val();
                    var grand_total = angular.element("input[name=grand_total]").val();
                    data = angular.merge($scope.forme, {total1: total1}, {total2: total2},
                            {total3: total3}, {total4: total4},
                            {total5: total5}, {total6: total6},
                            {total7: total7}, {total8: total8},
                            {total9: total9}, {total10: total10},
                            {total_traffic_volume_up: total_traffic_volume_up}, {total_traffic_volume_down: total_traffic_volume_down},
                            {heavy_traffic_up: heavy_traffic_up}, {heavy_traffic_down: heavy_traffic_down},
                            {traffic_volume_total: traffic_volume_total}, {heavy_traffic_total: heavy_traffic_total},
                            {grand_total: grand_total});
                }
                return $http({
                    method : "PUT",
                    url: '/{{ $prefix_url }}/{{$file_name}}/import/' + data.id,
                    params: data,
                }).then(function (response) {
                    hideLoading();
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

                        $scope.ids = null;
                    }
                }, function myError(xhr) {
                    hideLoading();
                    var screen_url = '{{$prefix_url}}';
                    if (screen_url == 'road_inventories') {
                        $scope.errors = convertErrorroad_inventories(xhr.data);
                    }
                    else if (screen_url == 'maintenance_history') {
                        $scope.errors = convertErrormaintenance_history(xhr.data);
                    }
                    else {
                        $scope.errors = convertErrortraffic_volume(xhr.data);
                    }
                    toastr.error('Error');
                    $timeout(function(){
                        $('.my-modal:visible').find('.red-panel').scrollintoview();
                    });

                });
            };
          /*  $scope.submitAndNext = function () {
                $scope.reValidate();
                if ($scope.is_next == 1)
                {
                    alert(1);
                    angular.element('#error tbody tr:nth-child(1)').addClass('selected').siblings().removeClass('selected');
                    var next_id = angular.element('#error').find("tbody tr:nth-child(1) td:nth-child(2)").text();
                    $scope.ids = next_id;
                    if (next_id != '')
                    {
                        $(this).addClass('edit');
                        $('.my-modal').show();
                        $scope.checkData();
                    }
                }
                else{
                    $scope.reValidate();
                }

            }*/
            $('#error,#ignore').on('click', 'tbody tr', function(event) {
                $(this).addClass('selected').siblings().removeClass('selected');

                var val = $(this).find("td:nth-child(2)").text();
                $scope.ids = val;
                // var i = $scope.ids;
                // console.log(i);
                // // if (i) {
                // //     function loadcheck(){
                // //         $(".edit").attr('class','btn btn-success');
                // //     }
                // // }else {
                // //     alert(0);
                // // }
            });
            $(document).on('click','#show-edit', function () {
                /*var id = $(this).attr('data-edit');
                $scope.ids = id;*/
            });
            $('.ui-dialog-titlebar-close, #close').click(function () {
                $scope.errors = {};
            });
          /*  $scope.exportInvalid = function() {
                return $http({
                    method : "GET",
                    url: '',
                    params: {}
                }).then(function (response) {
                    alert('export_success');
                });
            }*/
        });
    </script>
    <script type="text/javascript">
        function ignore(id){
            $.ajax({
                url: '/{{ $prefix_url }}/{{ $file_name }}/ignore/' + id,
                method: 'GET'
            })
            .done(function(response) {
                $('#error,#new,#success,#update,#ignore').DataTable().ajax.reload();
                toastr.success("{{ trans('backend.ignore_sucess')  }}");
                angular.element(document.getElementById('widget-grid')).scope().reload();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }
        function restore(id){
            $.ajax({
                url: '/{{ $prefix_url }}/{{ $file_name }}/restore/' + id,
                method: 'GET'
            })
            .done(function(response) {
                $('#error,#new,#success,#update,#ignore').DataTable().ajax.reload();
                toastr.success("{{ trans('validation.restore_sucess')  }}");
                angular.element(document.getElementById('widget-grid')).scope().reload();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }
        var direction_select = $('[name="direction"]'),
            terrain_select = $('[name="terrian_type_id"]'),
            road_class_select = $('[name="road_class_id"]'),
            km_from_input = $('[name="km_from"]'),
            m_from_input = $('[name="m_from"]'),
            km_to_input = $('[name="km_to"]'),
            m_to_input = $('[name="m_to"]');

        function reloadOptions(selector, options) {
            selector.empty();
            var opts = [];
            $.each(options, function (ix, val) {
                var option = $('<option>').text(val.title).val(val.value);
                opts.push(option);
            });
            selector.html(opts);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

       

        function getDesignSpeed() {
            var terrain_type_id = +terrain_select.val();
            var road_class_id = +road_class_select.val();
            $.ajax({
                url: '{{asset('/ajax/frontend/terrain/')}}/'+ terrain_type_id +'/road_class/' + road_class_id,
                method: 'GET',
            })
            .done(function(response) {
                var speed = response;
                var scope = angular.element('#widget-grid').scope();
                scope.$apply(function () {
                    scope.forme.design_speed = speed;
                });
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }

        function calculateLength() {
            var km_from = +km_from_input.val(),
                m_from = +m_from_input.val(),
                km_to = +km_to_input.val(),
                m_to = +m_to_input.val(),
                length = 1000*km_to + m_to - 1000*km_from - m_from;
            $('[name="length_as_per_chainage"]').val(length);
        }

        $('document').ready(function(){

            setOnChangeEvent();
            @if (isset($edit_flg) && $edit_flg == 0)
                var disabled = ['rmb', 'sb', 'route', 'segment', 'date_collection', 'km_from', 'm_from', 'km_to', 'm_to'];
                for (var i in disabled) {
                    $('[name="' + disabled[i] + '"]').attr('disabled', 'disabled');
                }
            @endif
        });

        function setOnChangeEvent() {
            //sb_select.change(loadRoute);
           // rmb_select.change(loadSB);
            // route_select.change(function(){
            //     getRouteDetail();
            //     loadSegment();
            // });
            direction_select.change(function () {
                var direction_value = +direction_select.val();
                if (direction_value == 3)
                {
                    if ($('[name="lane_pos_number"]')){
                        $('[name="lane_pos_number"]').val('0');
                    }
                    if ($('[name="no_lane"]')){
                        $('[name="no_lane"]').val('1');
                    }
                }
            })
            terrain_select.change(getDesignSpeed);
            road_class_select.change(getDesignSpeed);
            km_from_input.change(calculateLength);
            m_from_input.change(calculateLength);
            km_to_input.change(calculateLength);
            m_to_input.change(calculateLength);
        }
        function convertErrorroad_inventories(errors) {
            var dataset = {
                scope_manage_error: [
                    'rmb', 'sb', 'road', 'segment_id'
                ],
                general_error: [
                    'survey_time', 'terrian_type_id', 'road_class_id', 'design_speed'
                ],
                chainage_n_position_error: [
                    'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat',
                    'province_from', 'district_from', 'ward_from', 'province_to', 'district_to',
                    'ward_to'
                ],
                information__of_ML_error: [
                    'direction', 'lane_pos_number', 'no_lane', 'lane_width'
                ],
                other_information_error: [
                    'construct_year', 'service_start_year', 'temperature', 'annual_precipitation', 'actual_length',
                    'remark'
                ],
                material_layer_error: [
                    'pavement_type_id', 'pavement_width', 'pavement_thickness'
                ]
            };

            for (var i in dataset) {
                var inputs = dataset[i];
                for (var j in inputs) {
                    if (typeof errors[inputs[j]] != 'undefined') {
                        errors[i] = true;
                        break;
                    }
                }
            }

            return errors;
        }

        function convertErrormaintenance_history(errors) {
            var dataset = {
                scope_manage_error: [
                    'rmb', 'sb', 'road', 'segment_id'
                ],
                general_error: [
                    'survey_time', 'completion_date', 'repair_duration'
                ],
                chainage_n_position_error: [
                    'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat',
                    'province_from', 'district_from', 'ward_from', 'province_to', 'district_to',
                    'ward_to'
                ],
                information_of_repair_section_error: [
                    'direction', 'lane_pos_number', 'actual_length', 'total_width_repair_lane'
                ],
                mh_position_error: [
                    'direction_running', 'distance'
                ],
                repair_method_info_error: [
                    'repair_method', 'r_classification_id', 'r_struct_type_id'
                ],
                material_layer_error: [
                    'pavement_type_id', 'r_category_id', 'total_pavement_thickness', 'binder_course', 'wearing_course'
                ]
            };

            for (var i in dataset) {
                var inputs = dataset[i];
                for (var j in inputs) {
                    if (typeof errors[inputs[j]] != 'undefined') {
                        errors[i] = true;
                        break;
                    }
                }
            }

            return errors;
        }

        function convertErrortraffic_volume(errors) {
            var dataset = {
                scope_manage_error: [
                    'rmb', 'sb', 'road', 'segment_id'
                ],
                information_traffic_error: [

                    'survey_time', 'name', 'km_station', 'm_station', 'lat_station', 'lng_station'
                ],
                input_data_of_MH_error: [

                    'up1', 'up2', 'up3', 'up4', 'up5', 'up6', 'up7', 'up8', 'up9', 'up10', 'total_traffic_volume_up', 'heavy_traffic_up',
                    'down1', 'down2', 'down3', 'down4', 'down5', 'down6', 'down7', 'down8', 'down9', 'down10', 'total_traffic_volume_down', 'heavy_traffic_down',
                    'total1', 'total2', 'total3', 'total4', 'total5', 'total6', 'total7', 'total8', 'total9', 'total10', 'traffic_volume_total', 'heavy_traffic_total', 'grand_total'
                ]
            };

            for (var i in dataset) {
                var inputs = dataset[i];
                for (var j in inputs) {
                    if (typeof errors[inputs[j]] != 'undefined') {
                        errors[i] = true;
                        break;
                    }
                }
            }

            return errors;
        }
        $(".my-modal").draggable({
            handle: ".ui-dialog-titlebar",
            // containment: "window"
        });
        $( document ).ready(function() {
            // complete();
            $('[data-toggle="tooltip"]').tooltip();
            
            $('.ympicker').datepicker( {
                // changeMonth: true,
                // changeYear: true,
                // showButtonPanel: true,
                startView: "year", 
                minViewMode: "months",
                format: 'yyyy/mm',
                // onClose: function(dateText, inst) { 
                //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                // }
            });

            $('.customDatepicker').datepicker( {
                // changeMonth: true,
                // changeYear: true,
                // showButtonPanel: true,
                // startView: "year", 
                // minViewMode: "months",
                format: 'yyyy-mm-dd',
                // onClose: function(dateText, inst) { 
                //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                // }
            });
        });
    </script>
@endpush

