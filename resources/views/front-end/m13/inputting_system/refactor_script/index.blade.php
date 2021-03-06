
@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_inputting')
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
            {{trans('menu.inputting_system')}}
        </li>
        <li>
            {{trans('menu.inputting')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-calendar',
        'text1' => trans('menu.inputting_system'),
        'text2' => trans('menu.inputting')
    ])

    <!-- section -->
    <section id="widget-grid" ng-app="ISApp" ng-controller="AddNewController">
        @include('front-end.m13.inputting_system.custom.context_menu')
        <div class="row" id="app">
            <manager></manager>

            <div class="ui-widget-overlay ui-front" style="display:none;"></div>
            <!-- @include('front-end.m13.inputting_system.modal.MH.modal_add_mh', [
                'id' => 'model-add-mh',
                'modal_title' => trans('menu.maintenance_history'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ])

            @include('front-end.m13.inputting_system.modal.TV.modal_add_tv', [
                'id' => 'model-add-tv',
                'modal_title' => trans('menu.traffic_volume'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ])

            @include('front-end.m13.inputting_system.modal.RI.modal_add_ri', [
                'id' => 'model-add-ri',
                'modal_title' => trans('menu.road_inventory'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ])

            @include('front-end.m13.inputting_system.modal.RI.modal_ri_survey', [
                'id' => 'model-ri-survey',
                'modal_title' => trans('menu.road_inventory_survey'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ])

            @include('front-end.m13.inputting_system.modal.MH.modal_mh_survey', [
                'id' => 'model-mh-survey',
                'modal_title' => trans('menu.maintenance_history_survey'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ])

            @include('front-end.m13.inputting_system.modal.TV.modal_tv_survey', [
                'id' => 'model-tv-survey',
                'modal_title' => trans('menu.traffic_volume_survey'),
                'button_complete' => trans('inputting.submit'),
                'button_cancel' => trans('back_end.cancel')
            ]) -->

            <article class="col-lg-3 col-md-4">
                @box_open(trans('back_end.add_new'))
                <div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Form::lbSelect('select_input', '', $select_data, ' ')!!}
                                <div style="visibility: hidden;">
                                    {!! Form::lbText('text_test', '', '', '')!!}
                                </div>  
                            </div>
                        </div>
                        <div class="widget-footer">
                            {{-- <a href="#" id="update-data" class="btn btn-primary" onclick="showModalRI(1)">{!! trans('Show') !!}</a> --}}
                            {{-- <a href="#" id="update-data" class="btn btn-primary" ng-click="showRIS(757)">{!! trans('Show RI') !!}</a> <!-- ng-click="showMH(1653)" -->
                            <a href="#" id="update-data" class="btn btn-primary" ng-click="showMHS(1653)">{!! trans('Show MH') !!}</a> <!-- ng-click="showMH(1653)" -->
                            <a href="#" id="update-data" class="btn btn-primary" ng-click="showTV(16)">{!! trans('Show TV') !!}</a> --}} <!-- ng-click="showMH(1653)" -->
                            <a href="#" id="add-new" class="btn btn-primary">{!! trans('back_end.add_new') !!}</a>
                        </div>
                    </div>
                </div>
                @box_close()
            </article>

            <streching></streching>

            <history></history>

        </div>
    </section>
@endsection

@push('script')
   
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js"></script>
    <script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vuex/3.0.1/vuex.js"></script>


    <script type="text/javascript">
        var mixin = {
            methods: {
                // convertPixelToMeter: function() {
                //     var value = (width_zoom_zone - 100) * (m - this.convertPixelToMeter(limit_left_px)) / 
                //             (this.convertPixelToMeter(limit_right_px) - this.convertPixelToMeter(limit_left_px));
                //     return +Math.round(value) + 70 ;
                // },

                // convertPixelToMeter(px) {
                //     var m = (px*(this.boundaryRight - this.boundaryLeft) / this.widthCanvas) 
                //             + this.boundaryLeft;
                //     return Math.round(m);
                // },
            }
        }
    </script>

    @include('front-end.m13.inputting_system.refactor_script.scope_manager')

    <script>
        $( function() {
            $('.modal-content').resizable({
                minHeight: 300,
                minWidth: 300
            });
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
        });

        // init instance store and vue
        var store = new Vuex.Store({
            state: {
                colors: { 
                    RMD: 'rgba(0, 0, 255, 0.8)', 
                    MH: 'rgba(255, 0, 0, 0.8)' 
                },
                segmentId: '',
                widthCanvas: 0,
                limitPx: {
                    left: 0,
                    right: 100
                },
                boundary: {
                    left: 0,
                    right: 100
                }
            },
            getters: {
                getSegmentId: function(state) {
                    return state.segmentId;
                },

                getWidthCanvas: function(state) {
                    return state.widthCanvas;
                },

                getColor: function(state) {
                    return state.colors;
                },

                getLimitPx: function(state) {
                    return state.limitPx;
                },

                getBoundary: function(state) {
                    return state.boundary;
                }
            },
            mutations: {
                setSegmentId: function(state, payload) {
                    state.segmentId = payload.segmentId;
                },
                setWidthCanvas: function(state, payload) {
                    state.widthCanvas = payload.widthCanvas;
                },
                setLimitPx: function(state, payload) {
                    state.limitPx.left  = payload.left; 
                    state.limitPx.right = payload.right; 
                },
                setBoundary: function(state, payload) {
                    state.boundary.left  = payload.left;
                    state.boundary.right = payload.right;
                }
            },
        });

        var app = new Vue({
            el: '#app',
            store
        });
               
    </script>
   
@endpush

@push('css')
<style type="text/css">

    /*jquery accordion*/
    .ui-accordion-header-icon.ui-icon-circle-minus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_a90329_256x240.png') !important;
    }

    .ui-accordion-header-icon.ui-icon-circle-plus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_739e73_256x240.png') !important;
    }
    .modal-content {
        display: none;
        width: 80%;
        height: auto;
        position: fixed !important;
        margin: auto !important;
        top: 46px ;
        left: 10%;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
        -webkit-animation-duration: 0.4s;
        animation-duration: 0.4s
    }

    .ui-dialog-buttonpane.ui-widget-content.ui-helper-clearfix {
        margin: 0px !important;
    }

    .ui-dialog .ui-dialog-content {
        height: 410px !important;
    }

    .ui-accordion-content {
        overflow: visible !important;
    }
   
    .error {
        background-color: #f2dede;
    }

    .error-color {
        color: #f2dede !important;
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
    .note {
        height: 19px;
        width: 19px;
        margin-top: 3px !important;
    }
    .note span {
        padding-left: 28px;
        font-size: 14px;
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
    .note_RMD , .note_MH, .note_TV {
        position: absolute;
        font-size: 50px;
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
    .show_data {
        width:20px;
        height: 20px;
    }
    #detail {
        padding: 2px;
        border:1px solid white;
        border-radius: 2px;
        color: white;
    }
    #RMD_hide, #MH_hide, #TV_hide {
        position: absolute;
        top: 2px;

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
   .checkbox {
        margin-right: 160px !important;
   } 
   
</style>

@endpush