@extends('front-end.layouts.app')

@section('side_menu_das')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.das')}}
        </li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-lg fa-fw fa-bar-chart-o"></i>
                {{ trans('menu.das') }}
                <span>>
                    {!! trans("back_end.das_list") !!}
            </span>
            </h1>
        </div>
    </div>

    <section id="widget-grid" ng-app="DASApp" >
        <div id="tabs">
            <ul>
                <li>
                    <a href="#Summary">{!! trans('back_end.das_sumary') !!}</a>
                </li>
                <li>
                    <a href="#SummaryPC">{!! trans('back_end.das_sumary_pc') !!}</a>
                </li>
                <li>
                    <a href="#Transition">{!! trans('back_end.das_transition') !!}</a>
                </li>
                <li>
                    <a href="#Time-series">{!! trans('back_end.das_time_series') !!}</a>
                </li>
                <li>
                    <a href="#SummaryMR">{!! trans('back_end.das_summary_MR') !!}</a>
                </li>
                <li>
                    <a href="#Summary-passed-time">{!! trans('back_end.das_summary-passed-time') !!}</a>
                </li>
            </ul>
            <div id="Summary"       >
               @include('front-end.DAS.elements.summary_r_network')
            </div>
            <div id="SummaryPC">
                @include('front-end.DAS.elements.summary_PC')
            </div>
            <div id="Transition">
                @include('front-end.DAS.elements.transition_PC')
            </div>
            <div id="Time-series">
                @include('front-end.DAS.elements.time_series_PC')
            </div>
            <div id="SummaryMR">
                @include('front-end.DAS.elements.summary_MR')
            </div>
            <div id="Summary-passed-time">
                @include('front-end.DAS.elements.summary_passed_time')
            </div>
        </div>
    </section>

@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
<script>
    var app = angular.module('DASApp', []);
    app.directive('changeOption', function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.on('change', function(event) {
                    var value = element.val().replace("number:", "");
                    var load = attrs.changeOption + '(' + value + ')';
                    scope.$apply(function() {
                            $parse(load)(scope);
                    });
                });
            }
        };
    });
</script>

<script>
    $('#tabs')
            .tabs()
            .addClass('ui-tabs-vertical ui-helper-clearfix');

</script>
@endpush

@push('css')
<style>
    .ui-tabs.ui-tabs-vertical {
        padding: 0;
        width: 100%;
    }
    .ui-tabs.ui-tabs-vertical .ui-widget-header {
        border: none;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav {
        float: left;
        width: 18%;
        background: #CCC;
        border-radius: 4px 0 0 4px;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav li {
        clear: left;
        width: 100%;
        height: 60px;
        margin: 0.2em 0;
        border: 1px solid gray;
        border-width: 1px 0 1px 1px;
        border-radius: 4px 0 0 4px;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav li a {
        display: block;
        width: 100%;
        height: 60px;
        font-size: 12px;
        white-space: normal;
        padding: 0.8em 0.5em;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav li a:hover {
        cursor: pointer;
        font-weight: bold;
        color: #212121;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active {
        margin-bottom: 0.2em;
        padding-bottom: 0;
        color: #212121;
        border-right: 1px solid white !important;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-nav li:last-child {
        margin-bottom: 10px;
    }
    .ui-tabs.ui-tabs-vertical .ui-tabs-panel {
        float: left;
        width: 82%;
        border-left: 1px solid gray;
        border-radius: 0;
        position: relative;
        left: -1px;
    }
    .ui-tabs .ui-tabs-nav li{
        border-right: 1px solid gray !important;
    }
    .ui-tabs .ui-tabs-nav li.ui-tabs-active a {
        border-right: 1px solid white !important;
    }
    .ui-tabs .ui-tabs-panel {
        border: none !important;
    }
</style>
@endpush
