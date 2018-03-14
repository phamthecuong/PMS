@extends('front-end.layouts.app')

@section('side_menu_pmos')
    active
@endsection

@section('side_menu_pmos')
    active
@endsection

@php
    $select_data = [
        [
            'name' => trans('menu.road_inventory'),
            'value' => 0
        ],
        [
            'name' => trans('menu.maintenance_history'),
            'value' => 1
        ],
        [
            'name' => trans('menu.traffic_volume'),
            'value' => 2
        ]
    ];
@endphp

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.side_menu_pmos')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-laptop',
        'text1' => trans('menu.home'),
        'text2' => trans('menu.side_menu_pmos')
    ])

    <!-- section -->
    <section id="widget-grid" ng-app="ISApp" ng-controller="AddNewController">
        <div class="row">
            <article class="col-lg-12 col-md-12" id="demo">
                @box_open(trans('back_end.scope_manage'))
                    <div>
                        <div class="widget-body"  style="padding-bottom: 0px !important;">
                            <div class="col-xs-3" style="margin-top: -10px;">
                                {!! Form::open(["url" => "", "method" => "post"]) !!}
                                <div class="row" style="font-size: 11px;">
                                    <div class="col-xs-10">
                                        {!! 
                                            Form::lbSelect('organization', null, \App\Models\tblOrganization::getListRmb(), trans('inputting.rmb'), ['id' => 'rmb_id', 'class' => 'input-xs']) 
                                        !!}
                                    </div>
                                    <div class="col-xs-10">
                                        {!!
                                            Form::lbSelect('sub_organization', null, [['name' =>'All', 'value' => '-1']], trans('inputting.sb'), ['id' => 'sb_id', 'class' => 'input-xs'])
                                        !!}
                                    </div>
                                    <div class="col-xs-10">
                                        {!!
                                            Form::lbSelect('route_branch', null, [], trans('inputting.route_branch'), ['id' => 'road_name', 'class' => 'input-xs'])
                                        !!} 
                                    </div>
                                    <div class="col-xs-10" style="margin: 0px 0px 10px 0px">
                                        {!! 
                                            Form::lbSelect('segment', 'null', [], trans('inputting.segment'), ['id' => 'segment', 'class' => 'input-xs']) 
                                        !!} 
                                    </div>
                                </div>
                                {!! Form::close() !!} 
                            </div>
                            <div class="col-xs-9" style=" min-height: 0;padding-left: 0; padding-right: 13px; ">
                                <div  id="content_zoom" style="position: relative!important;">
                                    <div class="info">
                                        <div id="km_from" class="col-xs-4" style="text-align: left;">{{trans('inputting.km_from')}}</div>
                                        <div id="km_to" class="col-xs-offset-4 col-xs-4" style="text-align:right; padding-right:12px;">{{trans('inputting.km_to')}}</div>
                                    </div>
                                    <canvas style="height: 150px; background:white; border-right: 1px dashed blue; border-left: 1px dashed blue;" id="streching_area"></canvas>
                                    <div id="zoom"></div>
                                </div>
                            </div>
                            <div>
                                <h7>{{trans('inputting.cross_sectional_profile:')}}<span class="from_zoom" style="padding-left: 300px;"></span> </h7>
                            </div>
                        </div>
                    </div>
                @box_close()
            </article>
            <div class="scroll-fix" style="display: none; height: 280px;"></div>

            <article class="col-xs-12" id="col-full">
                @box_open(trans('back_end.zoom_zone'))
                <div>
                    <div style="padding-top: 0px;" id="scroll-id">
                        <div class="smart-form col-xs-3" id="smart-chart" style="padding: 0px;">
                            <div class="row">
                                <section class="col-xs-9" style="overflow: hidden;border: 1px solid; margin: 0px 0px 15px 25px;padding: 5px 0px 5px 10px;"">
                                    <label>
                                        <input type="checkbox" id="RMD_hide"  checked="checked">
                                        <span>{{trans('inputting.RMD')}}</span>
                                    </label>
                                    <ul class="sub-ri">
                                        <li>
                                            <div class="note"  style="background: blue; color: blue">
                                                <span>{{trans('back_end.AC')}}</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="note"  style="background: #67338D; color: #67338D">
                                                <span>{{trans('back_end.BST')}}</span>
                                            </div>
                                        </li>
                                        <li>   
                                            <div class="note"  style="background: #E605D7; color: #E605D7">
                                                <span>{{trans('back_end.CC')}}</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="note"  style="background: #7D7D7D; color: #7D7D7D">
                                                <span>{{trans('back_end.other')}}</span>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="mh">
                                        <div>
                                            <label>
                                                <input type="checkbox" id="MH_set" checked="checked">
                                                <i></i><span>{{trans('inputting.MH')}}</span>
                                            </label>
                                        </div>
                                        <ul class="mh-line">
                                            <li>
                                                <label>
                                                    <input type="checkbox" id="MH_classification" checked="checked">
                                                    <i></i><span>{{trans('back_end.Repair Classification')}}</span>
                                                </label>
                                                <ul class="sub-class">
                                                    <li>
                                                        <div class="note"  style="background: #06E72B; color: #06E72B">
                                                            <span>{{trans('back_end.Periodic Maintenance - Big')}}</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="note"  style="background: #50C878; color: #50C878">
                                                            <span>{{trans('back_end.Emergency Repair')}}</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="note"  style="background: #FFD700; color: #FFD700">
                                                            <span>{{trans('back_end.Routine Maintenance')}}</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="note"  style="background: #0B3861; color: #0B3861">
                                                            <span>{{trans('back_end.Periodic Maintenance - Medium')}}</span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <label>
                                                    <input type="checkbox" id="MH_pavement">
                                                    <i></i><span>{{trans('back_end.Repair Surface')}}</span>
                                                </label>
                                                <ul class="sub-class">
                                                    <li>
                                                        <div class="note"  style="background: red; color: red">
                                                            <span>{{trans('back_end.AC')}}</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="note"  style="background: #FF9933; color: #FF9933">
                                                            <span>{{trans('back_end.BST')}}</span>
                                                        </div>
                                                    </li>
                                                    <li> 
                                                        <div class="note"  style="background: #390B0B; color: #390B0B">
                                                            <span>{{trans('back_end.CC')}}</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="note"  style="background: #C3B091; color: #C3B091">
                                                            <span>{{trans('back_end.other')}}</span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <label>
                                        <input type="checkbox" id="TV_hide" checked="checked">
                                        <i></i><span>{{trans('inputting.TV')}}</span>
                                    </label>
                                    
                                </section>
                            </div>
                        </div>
                        <div class="col-xs-9">
                            <div style="position: relative!important;min-height: 0;padding-left: 0; padding-right: 0; margin-left: -13px;">
                                <div class="popup_info" style="position: absolute!important; background: rgba(0,0,0,.9); border-radius: 4px; width: auto; height: auto; border:1px solid black; display: none; z-index: 99;">
                                    <div style="z-index: 99" id="detail"></div>
                                </div>
                                <div class="popup_info_fix" style="position: absolute!important; background: rgba(0,0,0,.9); border-radius: 4px; border-bottom-right-radius: 0px; width: auto; height: auto; border:1px solid black; display: none; z-index: 99;">
                                    <div style="z-index: 99" id="detail_fix"></div>
                                </div>
                                <canvas id="zoom_zone" ></canvas>

                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <div>
                    <!-- Table -->
                        <table id="datatable_fixed_column" class="table table-bordered dataTable no-footer" width="100%" role="grid" style="width: 100%; position: relative;">
                            <thead>
                                <tr role="row">
                                    <th class="sorting" tabindex="0" aria-controls="datatable_fixed_column" rowspan="1" colspan="1" width="1%"></th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable_fixed_column" rowspan="1" colspan="1" width="1%"></th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable_fixed_column" rowspan="1" colspan="1" width="20%">
                                        Chairage and Position
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable_fixed_column" rowspan="1" colspan="1" width="auto"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr role="row" class="even">
                                    <td class="expand" rowspan="5">
                                        <span class="rotate">{{trans('back_end.payment_condition')}}</span>
                                    </td>
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group">
                                                <label>
                                                    <input type="checkbox" id="crack_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="title-position">{{trans('back_end.crack')}}</div>
                                        <div class="row crack legend">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right">%</span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="good">0-10</li>
                                                    <li class="fair">10-20</li>
                                                    <li class="bad">20-40</li>
                                                    <li class="poor">40 or more</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="expand" style="padding: 8px 0px 0px 10px;">
                                        <div class="title-position"></div>
                                        <div class="line-sub crack"  style="position: relative!important;  width: auto; height: auto;">
                                            <div class="popup_info_crack" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_crack" style="z-index: 100"></div>
                                            </div>
                                            <div class="popup_info_crack_fix" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_crack_fix" style="z-index: 100"></div>
                                            </div>
                                            <canvas id="PcInfo"></canvas> 
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group" >
                                                <label>
                                                    <input type="checkbox" id="rutting_average_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="title-position">{{trans('back_end.rutting_depth_ave')}}</div>
                                        <div class="row rutting_average legend">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right">mm</span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="good">0-20</li>
                                                    <li class="fair">20-30</li>
                                                    <li class="bad">30-50</li>
                                                    <li class="poor">50 or more</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="expand" style="padding: 8px 0px 0px 10px;">
                                        <div class="title-position"></div>
                                        <div class="line-sub rutting_average" style="position: relative!important;width: auto; height: auto;">
                                            <div class="popup_info_ave" style="">
                                                <div style="text-align: center; width: auto;" id="detail_ave" style="z-index: 100"></div>
                                            </div>
                                            <div class="popup_info_ave_fix" style="">
                                                <div style="text-align: center; width: auto;" id="detail_ave_fix" style="z-index: 100"></div>
                                            </div>
                                            <canvas id="RutAve_Info"></canvas> 
                                            
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group" >
                                                <label>
                                                    <input type="checkbox" id="rutting_max_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="title-position">{{trans('back_end.rutting_depth_max')}}</div>
                                        <div class="row rutting_max legend">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right">mm</span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="good">0-20</li>
                                                    <li class="fair">20-30</li>
                                                    <li class="bad">30-50</li>
                                                    <li class="poor">50 or more</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="expand" style="padding: 8px 0px 0px 10px;">
                                        <div class="title-position"></div>
                                        <div class="line-sub rutting_max" style="position: relative!important; width: auto; height: auto;">
                                            <div class="popup_info_max" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_max" style="z-index: 100"></div>
                                            </div>
                                            <div class="popup_info_max_fix" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_max_fix" style="z-index: 100"></div>
                                            </div>
                                            <canvas id="RutMax_Info"></canvas> 
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group" >
                                                <label>
                                                    <input type="checkbox" id="iri_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="title-position">{{trans('back_end.IRI')}}</div>
                                        <div class="row iri legend">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right">mm/m</span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="good">0-4</li>
                                                    <li class="fair">4-6</li>
                                                    <li class="bad">6-10</li>
                                                    <li class="poor">10 or more</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="expand" style="padding: 8px 0px 0px 10px;">
                                        <div class="title-position"></div>
                                        <div class="line-sub iri" style="position: relative!important; width: auto; height: auto;">
                                            <div class="popup_info_iri" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_iri" style="z-index: 100"></div>
                                            </div>
                                            <div class="popup_info_iri_fix" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_iri_fix" style="z-index: 100"></div>
                                            </div>
                                            <canvas id="IRI_Info"></canvas>
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group" >
                                                <label>
                                                    <input type="checkbox" id="mci_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="title-position">{{trans('back_end.MCI')}}</div>
                                        <div class="row mci legend">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right"></span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="good">5 or more</li>
                                                    <li class="fair">4-5</li>
                                                    <li class="bad">3-4</li>
                                                    <li class="poor">3 or less</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="expand" style="padding: 8px 0px 0px 10px;">
                                        <div class="title-position"></div>
                                        <div class="line-sub mci" style="position: relative!important;  width: auto; height: auto;">
                                            <div class="popup_info_mci" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_mci" style="z-index: 100"></div>
                                            </div>
                                            <div class="popup_info_mci_fix" style="">
                                                    <div style="text-align: center; width: auto;" id="detail_mci_fix" style="z-index: 100"></div>
                                            </div>
                                            <canvas id="MCI_Info"></canvas>
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand" rowspan="1">
                                        <span class="rotate-tv">{{trans('inputting.TV')}}</span>
                                    </td>
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group">
                                                <label>
                                                    <input type="checkbox" id="tv_hide" checked="checked">
                                                </label>
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div class="row tv legend" style="margin: -7px -10px -8px -10px; border:none;"> 
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"><span class="pull-right"></span></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li style="width: 50%; background: #FFCD56; text-align: center; color: white">{{trans('back_end.left')}}</li>
                                                    <li style="width: 50%; background: #22B14C; text-align: center; color: white;">{{trans('back_end.right')}}</li>
                                                </ul>
                                            </div>
                                        </div> 
                                    </td>
                                    <td class="expand" style="position: relative!important;">
                                        <div class="tv">
                                            <canvas id="leftChart" height="140"></canvas>
                                            <canvas id="rightChart" height="140"></canvas>
                                            <p class="note_TV">{{trans('inputting.no_data_TV')}}</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr role="row" class="even">
                                    <td class="expand" rowspan="1">
                                        <span class="rotate-mh">{{trans('inputting.MH')}}</span>
                                    </td>
                                    <td class="expand">
                                        <section>
                                            <div class="inline-group">
                                                
                                            </div>
                                        </section>
                                    </td>
                                    <td class="expand">
                                        <div style="font-size: 10px; font-weight: bold; text-align: center; height: 30px; padding-left: 2px">
                                            <div class="row">
                                                <div class="col-xs-5">
                                                    <label class="checkbox">
                                                        <input type="checkbox" id="MH_RS" checked="checked">
                                                        <i></i><span style="cursor: pointer;">{{trans('back_end.Repair Surface')}}</span>
                                                    </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <label class="checkbox">
                                                        <input type="checkbox" id="MH_RC">
                                                        <i></i><span style="cursor: pointer;">{{trans('back_end.Repair Classification')}}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mh legend" style="position: relative!important;">
                                            <label class="control-label col-sm-12" for="title">{{trans('back_end.legend')}}</label>
                                            <label class="control-label col-sm-3" for="title"></label>
                                            <div class="col-sm-9">
                                                <ul class="rank">
                                                    <li class="mh_1" style="background: #FF0000;">AC</li>
                                                    <li class="mh_2" style="background: #FF9933;">BST</li>
                                                    <li class="mh_3" style="background: #390B0B;">CC</li>
                                                    <li class="mh_4" style="background: #C3B091;">Other</li>
                                                </ul>

                                            </div>
                                            <div id="checkbox" class="smart-form" style="position: relative!important; left: -15px; top: 5px; margin: 13px 0px 0px 0px;"></div>
                                        </div>
                                    </td>
                                    <td class="expand" style="padding: 0px 0px 0px 10px;">
                                        <div style="margin-left: -18px; position: relative!important;">
                                            <div class="popup_info_mh" style="position: absolute; background: rgba(0,0,0,.9); border-radius: 4px; width: auto; height: auto; border:1px solid black; display: none; z-index: 100;">
                                                    <div style="text-align: center; width: auto;" id="detail_mh" style="z-index: 100"></div>
                                            </div>
                                            <canvas style="" id="MH_history"></canvas>
                                            <p class="note_MH">{{trans('inputting.no_data_MH')}}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>
@endsection

@push('css')
<style type="text/css">
   .fixed {
        position:fixed !important;
        top:0 !important;
        z-index: 9999 !important;
    }

</style>
<link rel="stylesheet" href="http://static.jstree.com/3.0.0-beta3/assets/dist/themes/default/style.min.css">
@endpush

@push('script')
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <!-- toadstr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="http://static.jstree.com/3.0.0-beta3/assets/dist/jstree.min.js"></script>    <!-- <script src="/front-end/js/jquery.scrollTo.min.js"></script> -->

    <!--load ajax form scope manager-->
    @include('front-end.pmos.script.scope_manager')
    <!-- end -->

    @include('front-end.pmos.script.streching')
  
    <script>
        
        $( function() {
            var icons = {
                header: "ui-icon-circle-plus",
                activeHeader: "ui-icon-circle-minus"
            };
            $(".accordion > div" ).accordion({
                header: "h1",
                heightStyle: "content",
                icons: icons,
                collapsible: true
            });
            // $(".accordion > div").accordion({ header: "h1", collapsible: true });
        });
        $(document).ready(function () {
            $('#crack_hide').click(function (e) {
                $('.crack').toggle();
            });
            $('#rutting_average_hide').click(function (e) {
                $('.rutting_average').toggle();
            });
            $('#rutting_max_hide').click(function (e) {
                $('.rutting_max').toggle();
            });
            $('#iri_hide').click(function (e) {
                $('.iri').toggle();
            });
            $('#mci_hide').click(function (e) {
                $('.mci').toggle();
            });
            $('#tv_hide').click(function (e) {
                $('.tv').toggle();
            });

            $(".plusIcon").on("click",function(){
              var obj = $(this);
              if( obj.hasClass("glyphicon-plus") ){
                obj.hide();
                obj.next().show();            
                obj.parent().parent().next().show();
              }else{
                 obj.hide();
                 obj.prev().show();
                 obj.parent().parent().next().hide();
              }
            });
        });
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();   
            $("input[name=zoom_input]").keyup(function () {
                var b = 1;
                var a = $(this).val();
                switch (a) {
                    case '100':
                        b = 1;
                        break;
                    case '150':
                        b = 1.5;
                        break;
                    case '200':
                        b = 2;
                        break;
                    default:
                }
                $('#zoom_zone').css({"zoom": b});
            });
            // if ($(document).scrollTop() > 234) {
            //     $('#demo').css({"overflow": "hidden","position": "fixed", "top": 0, "z-index": 2, "width": '100%'});
            //     $('.scroll-fix').show();
            // }

            $(window).bind('scroll', function () {
                if ($(window).scrollTop() > 234) {
                    $('#demo').css({"overflow": "hidden","position": "fixed", "top": 0, "z-index": 2, "width": '100%'});
                    if($('#demo').height() > 100)
                    {
                        $('.scroll-fix').show();
                    }
                    else {
                        $('.scroll-fix').hide();
                    }

                } else {
                    $('#demo').css({"position": "relative"});
                    if($('#demo').height() > 100)
                    {
                        $('.scroll-fix').show();
                    }
                    else {
                        $('.scroll-fix').hide();
                    }
                    
                }
            });
            // console.log($('#col-full').offset().top);
        });

        var isapp = angular.module('ISApp', []);
        isapp.controller('AddNewController', function($scope, $http, $q) {
            
        });
    </script>

@endpush


@push('css')
<style type="text/css">
    .ui-resizable-w{
        left: 0;
    }
    .smart-form section {
        margin-bottom: 0px; 
   
    }
    .smart-form label span{
        cursor: pointer;
    }
    /*#col-full{
        position: relative;
    }
    #setLane{
        position: absolute;
        right: 60px;
        top: 1px;
        width: 10px;
        height: 33px;
        background: red;
    }*/
    /*jquery accordion*/
    .ui-accordion-header-icon.ui-icon-circle-minus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_a90329_256x240.png') !important;
    }
    .ui-accordion-header-icon.ui-icon-circle-plus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_739e73_256x240.png') !important;
    }
    table, th, td {
        border: 1px solid black;
    }
    #scroll-id {
        z-index: 0;
    }
    .popup_info, .popup_info_fix, .popup_info_mh{
        position: relative;
    }
    .popup_info:before,.popup_info_mh:before{
        position: relative;
        border: solid;
        border-color: #111 transparent;
        border-color: rgba(0,0,0,.8) transparent;
        border-width: .4em .4em 0 .4em;
        bottom: -0.5em;
        content: "";
        display: block;
        left: 2em;
        position: absolute;
        z-index: 99;
    }
    .popup_info_fix:before{
        position: relative;
        border: solid;
        border-color: #111 transparent;
        border-color: rgba(0,0,0,.8) transparent;
        border-width: .4em .4em 0 .4em;
        bottom: -0.5em;
        content: "";
        display: block;
        right: 2em;
        position: absolute;
        z-index: 99;
    }
    .popup_info_crack,
    .popup_info_ave, 
    .popup_info_max, 
    .popup_info_iri, 
    .popup_info_mci{
        position: relative;
        position: absolute; background: rgba(0,0,0,.9); border-radius: 4px; width: auto; height: auto; border:1px solid black; display: none; z-index: 100;
    }
    .popup_info_crack_fix,
    .popup_info_ave_fix, 
    .popup_info_max_fix, 
    .popup_info_iri_fix, 
    .popup_info_mci_fix{
        position: relative;
        position: absolute; background: rgba(0,0,0,.9); border-radius: 4px; width: auto; height: auto; border:1px solid black; display: none; z-index: 100;
    }
    .popup_info_crack:before, 
    .popup_info_ave:before, 
    .popup_info_max:before, 
    .popup_info_iri:before, 
    .popup_info_mci:before{
        border: solid;
        border-color: #111 transparent;
        border-color: rgba(0,0,0,.9) transparent;
        border-width: .4em .4em 0 .4em;
        bottom: -0.5em;
        content: "";
        display: block;
        left: 2em;
        position: absolute;
        z-index: 99;
    }
    .popup_info_crack_fix:before, 
    .popup_info_ave_fix:before, 
    .popup_info_max_fix:before, 
    .popup_info_iri_fix:before, 
    .popup_info_mci_fix:before{
        border: solid;
        border-color: #111 transparent;
        border-color: rgba(0,0,0,1) transparent;
        border-width: .4em .4em 0 .4em;
        bottom: -0.5em;
        content: "";
        display: block;
        right: 0.1em;
        position: absolute;
        z-index: 99;
    }
    .smart{
        height: 170px;
        border: 1px dotted #ccc;
        margin-bottom: 10px;
        padding: 0px 0px 0px 20px;
        width: 100%;
    }
    #zoom {
        width: 100px;
        height: 150px !important;
        position: absolute;
        top: 18px;
        background: white;
        opacity: 0.8;
        border-radius: 5px;
        background: rgba(169,169,169, 0.5);
        height: 150px;
        cursor: move;
    }
    #zoom_zone {    
        background-image: url("/front-end/img/road.png");
    }
    #km_from {
        padding-left: 0px;
    }
    #km_to {
        padding-right: 0px;
    }
    #datatable_fixed_column{
        position: relative;
        overflow: auto;
    }
    .smart-form .checkbox input+i:after {
        content: '\f00c';
        top: 0px;
        left: 1px;
        width: 10px;
        height: 10px;
        font: 400 12px/13px FontAwesome;
        text-align: center;
    }
    .smart-form .checkbox i, .smart-form .radio i {
        position: absolute;
        top: 5px;
        left: 0;
        display: block;
        width: 14px;
        height: 12px;
        outline: 0;
        border-width: 1px;
        border-style: solid;
        background: #FFF;
    }
    .note {
        height: 10px;
        width: 10px;
        margin-top: 3px !important;
    }
    .note span {
        margin: -1px 0px 0px 0px;
        padding-left: 17px;
        font-size: 9px;
        float: left;
        width: 170px;
    }
    .td_note {
        padding-right: 14px;
        padding-bottom: 10px;
        width: 236px;

    }
    .td_note :hover {
        cursor: pointer;
    }
    .note img {
        width: 20px;
        height: 20px;
    }
    #checkbox input{
        width: 20px;
        height: 40px;
    }
    .smart-form .checkbox, .smart-form .radio {
        margin-bottom: 0px;
        padding-left: 25px;
        line-height: 22px;
        color: #404040;
        cursor: pointer;
        font-size: 13px;
    }
    .note_RMD , .note_MH, .note_TV {
        position: absolute;
        font-size: 30px;
        text-align: center;
        top: 50%;
        left: 50%;
        color: #7A8B8B;
        display: none;
        transform: translate(-50%,-50%);
        width: 1000px;
        text-align: center;
    }
    .note_RMD span {
        width: 176px !important;
    }
    .note_TV {
        padding-left: 0px !important;
    }
    /*.note_MH {
        left: 350px !important;
    }*/
    .show_data {
        width:20px;
        height: 20px;
    }
    .form-group {
     margin-bottom: 0;
    }
    #detail ,#detail_mh{
        padding: 2px;
        border-radius: 2px;
        color: white;
        text-align: center;
    }
    #detail_fix {
        padding: 2px;
        border-radius: 2px;
        border-bottom-right-radius: 0px;
        color: white;
        text-align: center;
    }
    #RMD_hide {
        right: 228px;
    }
    #MH_hide {
        right: 228px;
    }
    #TV_hide {
        right: 264px;
    }
    .inline-group{
        position: relative;
        overflow: hidden;
    }
    .inline-group li{
        padding: 0px 50px 0px 0px;
        list-style: none;
        float: left;
    }
    .inline-group label{
        margin: 0;
    }
    .mh-line{
        overflow: hidden;
        margin: 0px 0px 0px 15px;
    }
    .mh-line li{
        overflow: hidden;
        float: none;
        position: relative;
    }
    .sub-class{
        list-style: none;
        margin: 0px 0px 0px 0px;
    }
    .sub-class li{
        padding: 0px 30px 0px 0px;
        font-size: 10px;
        float: left;
        width: 35%;
        
    }
    .sub-ri{
        list-style: none;
        margin: 0px 0px 0px 0px;
        overflow: hidden;
    }
    .sub-ri li{
        padding: 0px 30px 0px 0px;
        font-size: 10px;
        float: left;
    }
    .rotate {
        height: 145px;
        -webkit-writing-mode:vertical-rl; 
        -ms-writing-mode:tb-rl; 
        writing-mode:vertical-rl; 
        transform: rotate(-180deg);
        overflow: hidden;
    }
    .rotate-tv {
        height: 88px;
        -webkit-writing-mode:vertical-rl; 
        -ms-writing-mode:tb-rl; 
        writing-mode:vertical-rl; 
        transform: rotate(-180deg);
        overflow: hidden;
    }
    .rotate-mh {
        height: 146px;
        -webkit-writing-mode:vertical-rl; 
        -ms-writing-mode:tb-rl; 
        writing-mode:vertical-rl; 
        transform: rotate(-180deg);
        overflow: hidden;
    }
    .title-position{
        font-weight: bold;
        text-align: center;
        height: 20px;
    }
    .legend {
        margin: 4px -10px -8px -10px;
        border-top: 1px dotted #cccccc;
        background: #EFF0F3;
    }
    .line{
        border-top: 1px dotted #cccccc;
        border-bottom: 1px dotted #cccccc;
        padding: 0px 0px 4px 0px;
        margin: 4px -10px 0px -10px;
        text-align: center;
        position: relative;
    }
    .line-sub{
        border-top: 1px dotted #cccccc;
        margin: 4px 0px -4px -10px;
    }
    input[type="checkbox"] {
        cursor: pointer;
        margin: 0;
    }
    .rank{
        list-style: none;
        overflow: hidden;
        position: relative;
        margin: 0px -13px 0px -50px;
        font-size: 10px;
    }
    .mh_1{
        text-align: center;
        color: white;
        width: 23.3%;
    }
    .mh_2{
        text-align: center;
        color: white;
        width: 23.3%;
    }
    .mh_3{
        text-align: center;
        color: white;
        width: 23.3%;
    }
    .mh_4{
        text-align: center;
        color: white;
        width: 30%;
    }
    .rank li{
        float: left;
        padding: 3px 5px 3px 5px;
    }
    .rank li.good{
        background: #3F48CC;
        text-align: center;
        color: white;
        width: 23.3%;
    }
    .rank li.fair{
        background: #22B14C;
        text-align: center;
        color: white;
         width: 23.3%;
    }
    .rank li.bad{
        background: #FF7F27;
        text-align: center;
        color: white;
         width: 23.3%;
    }
    .rank li.poor{
        background: #ED1C24;
        text-align: center;
        color: white;
         width: 30%;
    }
   .expand{
        padding: 8px 0px 0px 10px;
   }
</style>

@endpush