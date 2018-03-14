@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_road_inventory')
    active
@endsection

@section('side_menu_data_review_tool_road_inventory')
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
            {{trans('menu.road_inventory')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th-large',
        'text1' => trans('menu.road_inventory'),
        'text2' => trans('menu.data_review_tool'),
    ])

    <!-- section -->
    <section id="widget-grid" >
        <div class="row">
        <form method="POST" id="export_data" action="/road_inventory/data_review_tool">
            <article class="col-lg-12" ng-app="DTApp" ng-controller="ReviewToolController">
                @box_open(trans('review_tool.setup_test_data'))
                <div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('review_tool.check_location_data') !!}</b></h4>
                                    <hr/>
                                </header>
                                {!! Form::lbCheckbox('chainage_one', '', trans('review_tool.check_overlap_chainage'), [
                                    'ng-model' => 'formDT.chainage.one',
                                    'ng-true-value' => 1,
                                    'ng-false-value' => 0
                                ]) !!} 
                                {!! Form::lbCheckbox('chainage_two', '', trans('review_tool.check_overlap_section'), [
                                    'ng-model' => 'formDT.chainage.two',
                                    'ng-true-value' => 1,
                                    'ng-false-value' => 0
                                ]) !!} 
                                {!! Form::lbCheckbox('chainage_three', '', trans('review_tool.check_actual_length'), [
                                    'ng-model' => 'formDT.chainage.three',
                                    'ng-true-value' => 1,
                                    'ng-false-value' => 0
                                ]) !!} 
                            </div>
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('review_tool.check_lane_width') !!}</b></h4>
                                    <hr/>
                                </header>
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'width_min',
                                            'title' => trans('review_tool.width_min'),
                                            'value' => '',
                                            'model_name' => 'formDT.width_min',
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'width_max',
                                            'title' => trans('review_tool.width_max'),
                                            'value' => '',
                                            'model_name' => 'formDT.width_max',
                                        ])
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('review_tool.check_temperature') !!}</b></h4>
                                    <hr/>
                                </header>
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'temperature_min',
                                            'title' => trans('review_tool.temperature_min'),
                                            'value' => '',
                                            'model_name' => 'formDT.temperature_min',
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'temperature_max',
                                            'title' => trans('review_tool.temperature_max'),
                                            'value' => '',
                                            'model_name' => 'formDT.temperature_max',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('review_tool.check_time_data') !!}</b></h4>
                                    <hr/>
                                </header>
                                {!! Form::lbCheckbox('time_data_one', '', trans('review_tool.y_m_construct_year'), [
                                    'ng-model' => 'formDT.time_data.one',
                                    'ng-true-value' => 1,
                                    'ng-false-value' => 0
                                ]) !!} 
                                {!! Form::lbCheckbox('time_data_two', '', trans('review_tool.y_m_service_start_year'), [
                                    'ng-model' => 'formDT.time_data.two',
                                    'ng-true-value' => 1,
                                    'ng-false-value' => 0
                                ]) !!}
                            </div>
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('review_tool.check_annual_precipitation') !!}</b></h4>
                                    <hr/>
                                </header>
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'precipitation_min',
                                            'title' => trans('review_tool.precipitation_min'),
                                            'value' => '',
                                            'model_name' => 'formDT.precipitation_min',
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_number', [
                                            'name' => 'precipitation_max',
                                            'title' => trans('review_tool.precipitation_max'),
                                            'value' => '',
                                            'model_name' => 'formDT.precipitation_max',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-footer">
                            <button type="button" id="add-new" class="btn btn-primary" ng-click="submitForm()">{!! trans('back_end.submit') !!}</button>
                        </div>
                    </div>
                </div>
                @box_close()
            </article>

            <article class="col-lg-12" id="error_area" style="display: none;"> 
                @box_open(trans("review_tool.list_panel_title"))
                <div>
                    <div class="widget-body no-padding table-responsive">
                        <?php $lang = App::isLocale('en') ? 'en' : 'vn'; ?>
                        @include("custom.table_extra_data", [
                            'table_id' => 'rmd',
                            'url' => "/ajax/backend/road_inventory/review",
                            'columns' => [
                                [
                                    'data' => 'segment.tbl_branch.name_' . $lang,
                                    'title' => trans('back_end.route_name'),
                                    'name' => 'route_name',
                                    'items' => App\Models\tblBranch::allOptionToAjax(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'segment.segname_' . $lang,
                                    'title' => trans('back_end.segment_name'),
                                    'name' => 'segment_id',
                                    'items' => App\Models\tblSectiondataRMD::getListSegment(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'segment.tbl_branch.branch_number',
                                    'title' => trans('back_end.branch_number'),
                                    'name' => 'branch_number',
                                    'items' => App\Models\tblBranch::branchNumberOptionToAjax(),
                                ],
                                [
                                    'data' => 'segment.tbl_organization.rmb.name_' . $lang,
                                    'title' => trans('back_end.rmb_manager'),
                                    'name' => 'rmb',
                                    'items' => App\Models\tblOrganization::getListRmb(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'segment.tbl_organization.name_' . $lang,
                                    'title' => trans('back_end.sb_manager'),
                                    'name' => 'sb',
                                    'items' => App\Models\tblOrganization::getListSB(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'km_from',
                                    'title' => trans('back_end.km_from'),
                                    'name' => 'km_from',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'm_from',
                                    'title' => trans('back_end.m_from'),
                                    'name' => 'm_from',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'km_to',
                                    'title' => trans('back_end.km_to'),
                                    'name' => 'km_to',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'm_to',
                                    'title' => trans('back_end.m_to'),
                                    'name' => 'm_to',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'segment.tbl_city_from.name',
                                    'title' => trans('back_end.province_from_list'),
                                    'name' => 'prfrom_id',
                                    'items' => App\Models\tblCity::allToOptionAjax(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'segment.tbl_city_to.name',
                                    'title' => trans('back_end.province_to_list'),
                                    'name' => 'prto_id',
                                    'items' => App\Models\tblCity::allToOptionAjax(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'lane_pos_number',
                                    'title' => trans('back_end.lane_no'),
                                    'name' => 'lane_pos_number',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'direction',
                                    'title' => trans('back_end.direction'),
                                    'name' => 'direction',
                                    'items' => \App\Classes\Helper::getListDirection(),
                                    'filterType'=> 'dropdown',
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'lane_width',
                                    'title' => trans('back_end.lane_width'),
                                    'name' => 'lane_width',
                                    'filterType' => "super_input",
                                    'hasFilter' => true
                                ],
                                [
                                    'data' => 'err_name',
                                    'title' => trans('back_end.error_record'),
                                    'name' => 'error',
                                ]
                            ]
                        ])
                        {{-- </div> --}}
                        <div class="widget-footer">
                            {{ csrf_field() }}
                            <input type="hidden" name="export_data_json">
                            <button type="submit" class="btn btn-warning header-btn" id="exportData">{{ trans('back_end.export') }}</button>
                        </div>
                    </div>
                </div>
                @box_close
            </article>
        </form>

        </div>
    </section>
@endsection
@push('css')
    <style type="text/css">
        .error {
            color: red;
        }

        #rmd tbody tr td:nth-child(16) {
            color: red;
        }
    </style>
@endpush
@push('script')
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script type="text/javascript">
        var dtapp = angular.module('DTApp', []);
        dtapp.controller('ReviewToolController', function($scope, $http, $q) {
            $scope.formDT = {
                chainage : {
                    one : 0,
                    two : 0,
                    three: 0
                },
                time_data: {
                    one : 0,
                    two : 0
                }
            };
            $scope.submitForm = function () {
                console.log($scope.formDT);
                // event.preventDefault();
                var query_string = $.param($scope.formDT);
                var ajax_source = "/ajax/backend/road_inventory/review?" + query_string;
                $('#rmd').DataTable().ajax.url(ajax_source).load();
                $('#error_area').show();
            }
        });

        
        var tree_data = JSON.parse('{!! json_encode($tree_data) !!}');
        var branch_data = JSON.parse('{!! json_encode($branch_data) !!}');
        var sb_data = JSON.parse('{!! json_encode($sb_data) !!}');
        $(document).ready(function() {
            $('[name="rmb"]').on('change', function() {
                var new_rmb = $(this).val();
                var new_data = [{
                    id: 0,
                    text: '{{trans('back_end.all')}}'
                }];
                if (!new_rmb) {
                    for (var i in tree_data) {
                        new_data = new_data.concat(tree_data[i]);
                    }
                } else {
                    new_data = new_data.concat(tree_data[new_rmb]);
                }
                $('[name="sb"]').select2('destroy').empty().select2({
                    data: new_data
                });
            });

            $('[name="route_name"]').on('change', function() {
                var new_seg = $(this).val();
                var new_data = [{
                    id: 0,
                    text: '{{trans('back_end.all')}}'
                }];
                if (!new_seg) {
                    for (var i in branch_data) {
                        new_data = new_data.concat(branch_data[i]);
                    }
                } else {
                    new_data = new_data.concat(branch_data[new_seg]);
                }
                $('[name="segment_id"]').select2('destroy').empty().select2({
                    data: new_data
                });
            });

            $('[name="sb"]').on('change', function() {
                var new_seg = $(this).val();
                var new_data = [{
                    id: 0,
                    text: '{{trans('back_end.all')}}'
                }];
                if (!new_seg) {
                    for (var i in sb_data) {
                        new_data = new_data.concat(sb_data[i]);
                    }
                } else {
                    new_data = new_data.concat(sb_data[new_seg]);
                }
                $('[name="segment_id"]').select2('destroy').empty().select2({
                    data: new_data
                });
            });
        })
    </script>
@endpush