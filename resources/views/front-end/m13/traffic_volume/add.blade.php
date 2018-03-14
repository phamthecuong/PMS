@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_traffic_volume')
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
            {{trans('menu.traffic_volume')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-exchange',
        'text1' => trans('menu.traffic_volume'),
        'text2' => (isset($breadcrumb_txt)) ? $breadcrumb_txt : trans('back_end.add_new')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                @box_open(trans('back_end.scope_manage'))
                <div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.Road Management Bureau'),
                                    'items' => \App\Models\tblOrganization::getListRmb(),
                                    'name' => 'rmb',
                                    'hint' => trans('back_end.rmb_hint'),
                                    'value' => @$rmb_id
                                ])
                            </div>
                            <div class="col-lg-6">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.sub_bureau'),
                                    'items' => [],
                                    'name' => 'sb',
                                    'hint' => trans('back_end.sb_hint'),
                                    'value' => ''
                                ])
                            </div>
                            <div class="col-lg-8">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.route_branch'),
                                    'items' => [],
                                    'name' => 'route',
                                    'hint' => trans('back_end.route_hint'),
                                    'value' => ''
                                ])
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('branch_no', '', trans('back_end.branch_no'), '', trans('back_end.branch_no_hint'), ['readonly']) !!}
                            </div>
                            <div class="col-lg-12">
                                @if (isset($edit_flg) && $edit_flg == 0)
                                    {!! Form::lbText('segment', $segment_id, trans('back_end.segment'), '', trans('back_end.segment_hint'), ['readonly'])!!}
                                @else
                                    @include('custom.form_select', [
                                        'title' => trans('back_end.segment'),
                                        'items' => [],
                                        'name' => 'segment',
                                        'hint' => trans('back_end.segment_hint'),
                                        'value' => ''
                                    ])
                                @endif
                            </div>
                        </div>
                        <header>
                            <legend>{!! trans('back_end.Information Traffic') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-4">
                                @include('custom.form_datepicker', [
                                    'title' => trans('back_end.date_collection'),
                                    'name' => 'date_collection',
                                    'hint' => trans('back_end.date_collection_hint'),
                                    'value' => @$data->survey_time
                                ])
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('traffic_name_en', @$data->name_en, trans('back_end.Traffic name_en'), '', trans('back_end.traffic_nameEn_hint'))!!}
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('traffic_name_vn', @$data->name_vn, trans('back_end.Traffic name_vn'), '', trans('back_end.traffic_nameVn_hint'))!!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::lbText("km_station", @$data->km_station, trans('back_end.km_station'), "", trans('back_end.km_station_hint')) !!}
                                {!! Form::lbText("m_station", @$data->m_station, trans('back_end.m_station'), "", trans('back_end.m_station_hint')) !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::lbText("lat_station", @$data->lat_station, trans('back_end.general.latitude'), "", trans('back_end.latitude_hint')) !!}
                                {!! Form::lbText("lng_station", @$data->lng_station, trans('back_end.general.longitude'), "", trans('back_end.longitude_hint')) !!}
                            </div>
                        </div>
                    </div>
                    @include('custom.textarea', [
                                   'title' => trans('back_end.remark'),
                                   'hint' => trans('back_end.remark_hint'),
                                   'value' => @$data->remark,
                                   'name' => 'remark',
                                   'attribute' => ['rows' => 5]
                               ])
                </div>
                @box_close()
            </article>
            <article class="col-lg-6">
                @box_open(trans('back_end.Input Data of Maintenance History'))
                <div>
                    <div class="widget-body">

                        <header>
                            <legend>{!! trans('back_end.Other Information') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('back_end.up') !!}</b></h4>
                                    <hr/>
                                </header>
                                {!! Form::lbNumber('car_jeep_up', @$up_info[1], trans('back_end.Car, Jeep'), '0', '99999') !!}
                                {!! Form::lbNumber('light_truck_up', @$up_info[2], trans('back_end.Light Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('medium_truck_up', @$up_info[3], trans('back_end.Medium Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('heavy_truck_up', @$up_info[4], trans('back_end.Heavy Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('heavy_truck3_up', @$up_info[5], trans('back_end.Heavy Truck3'), '0', '99999') !!}
                                {!! Form::lbNumber('small_bus_up', @$up_info[6], trans('back_end.Small Bus'), '0', '99999') !!}
                                {!! Form::lbNumber('large_bus_up', @$up_info[7], trans('back_end.Large Bus'), '0', '99999') !!}
                                {!! Form::lbNumber('tractor_up', @$up_info[8], trans('back_end.Tractor'), '0', '99999') !!}
                                {!! Form::lbNumber('motobike_including_3_wheeler_up', @$up_info[9], trans('back_end.Motobike including 3 wheeler'), '0', '99999') !!}
                                {!! Form::lbNumber('bicycle_pedicab_up', @$up_info[10], trans('back_end.Bicycle/Pedicab'), '0', '99999') !!}
                                {!! Form::lbText('traffic_volume_up', @$data->total_traffic_volume_up, trans('back_end.Total Traffic Volume'), '', '', ['readonly']) !!}
                                {!! Form::lbText('heavy_traffic_volume_up', @$data->heavy_traffic_up, trans('back_end.Total heavy traffic volume'), '', '', ['readonly']) !!}
                            </div>
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('back_end.down') !!}</b></h4>
                                    <hr/>
                                </header>
                                {!! Form::lbNumber('car_jeep_down', @$down_info[1], trans('back_end.Car, Jeep'), '0', '99999') !!}
                                {!! Form::lbNumber('light_truck_down', @$down_info[2], trans('back_end.Light Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('medium_truck_down', @$down_info[3], trans('back_end.Medium Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('heavy_truck_down', @$down_info[4], trans('back_end.Heavy Truck'), '0', '99999') !!}
                                {!! Form::lbNumber('heavy_truck3_down', @$down_info[5], trans('back_end.Heavy Truck3'), '0', '99999') !!}
                                {!! Form::lbNumber('small_bus_down', @$down_info[6], trans('back_end.Small Bus'), '0', '99999') !!}
                                {!! Form::lbNumber('large_bus_down', @$down_info[7], trans('back_end.Large Bus'), '0', '99999') !!}
                                {!! Form::lbNumber('tractor_down', @$down_info[8], trans('back_end.Tractor'), '0', '99999') !!}
                                {!! Form::lbNumber('motobike_including_3_wheeler_down', @$down_info[9], trans('back_end.Motobike including 3 wheeler'), '0', '99999') !!}
                                {!! Form::lbNumber('bicycle_pedicab_down', @$down_info[10], trans('back_end.Bicycle/Pedicab'), '0', '99999') !!}
                                {!! Form::lbText('traffic_volume_down', @$data->total_traffic_volume_down, trans('back_end.Total Traffic Volume'), '', '', ['readonly']) !!}
                                {!! Form::lbText('heavy_traffic_volume_tt', @$data->heavy_traffic_down, trans('back_end.Total heavy traffic volume'), '', '', ['readonly']) !!}
                            </div>
                            <div class="col-lg-4">
                                <header>
                                    <h4><b>{!! trans('back_end.total') !!}</b></h4>
                                    <hr/>
                                </header>
                                {!! Form::lbText('car_jeep_tt', (@$up_info[1] + @$down_info[1]), trans('back_end.Car, Jeep'), '', '', ['readonly']) !!}
                                {!! Form::lbText('light_truck_tt', (@$up_info[2] + @$down_info[2]), trans('back_end.Light Truck'), '', '', ['readonly']) !!}
                                {!! Form::lbText('medium_truck_tt', (@$up_info[3] + @$down_info[3]), trans('back_end.Medium Truck'), '', '', ['readonly']) !!}
                                {!! Form::lbText('heavy_truck_tt', (@$up_info[4] + @$down_info[4]), trans('back_end.Heavy Truck'), '', '', ['readonly']) !!}
                                {!! Form::lbText('heavy_truck3_tt', (@$up_info[5] + @$down_info[5]), trans('back_end.Heavy Truck3'), '', '', ['readonly']) !!}
                                {!! Form::lbText('small_bus_tt', (@$up_info[6] + @$down_info[6]), trans('back_end.Small Bus'), '', '', ['readonly']) !!}
                                {!! Form::lbText('large_bus_tt', (@$up_info[7] + @$down_info[7]), trans('back_end.Large Bus'), '', '', ['readonly']) !!}
                                {!! Form::lbText('tractor_tt', (@$up_info[8] + @$down_info[8]), trans('back_end.Tractor'), '', '', ['readonly']) !!}
                                {!! Form::lbText('motobike_including_3_wheeler_tt', (@$up_info[9] + @$down_info[9]), trans('back_end.Motobike including 3 wheeler'), '', '', ['readonly']) !!}
                                {!! Form::lbText('bicycle_pedicab_tt', (@$up_info[10] + @$down_info[10]), trans('back_end.Bicycle/Pedicab'), '', '', ['readonly']) !!}
                                {!! Form::lbText('traffic_volume_tt', (@$data->total_traffic_volume_up + @$data->total_traffic_volume_down), trans('back_end.Total Traffic Volume'), '', '', ['readonly']) !!}
                                {!! Form::lbText('heavy_traffic_volume_tt', (@$data->heavy_traffic_up + @$data->heavy_traffic_down), trans('back_end.Total heavy traffic volume'), '', '', ['readonly']) !!}
                            </div>
                            <div class="col-lg-6 pull-right">
                                {!! Form::lbText('total', (@$data->total_traffic_volume_up + @$data->total_traffic_volume_down + @$data->heavy_traffic_up + @$data->heavy_traffic_down), trans('back_end.total'), '', '', ['readonly']) !!}
                            </div>

                        </div>
                    </div>

                </div>
                @box_close()
            </article>
        </div>
        @if(isset($breadcrumb_txt))
        @else
            <!-- <div class="well">
                {!! Form::lbSubmit(trans('back_end.genaral.accept')) !!}
            </div> -->
        @endif
    </section>
@endsection
@push('script')
<script type="text/javascript">
    var rmb_select = $('[name="rmb"]'),
            sb_select = $('[name="sb"]'),
            route_select = $('[name="route"]'),
            segment_select = $('[name="segment"]'),
            // terrain_select = $('[name="terrain_type"]'),
            // road_class_select = $('[name="road_class"]'),
            km_from_input = $('[name="km_from"]'),
            m_from_input = $('[name="m_from"]'),
            km_to_input = $('[name="km_to"]'),
            m_to_input = $('[name="m_to"]'),
            old_sb = '{{(null !== old("sb")) ? old("sb") : @$sb_id}}',
            old_route = '{{(null !== old("route")) ? old("route") : @$branch_id}}',
            old_segment = '{{(null !== old("segment")) ? old("segment") : @$segment_id}}';

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

    function loadSB() {
        var rmb_id = +rmb_select.val();
        var url = '/ajax/rmb/' + rmb_id + '/sb';
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: '',
                title: '{{ trans("back_end.select_a_sb") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['organization_name']
                });
            }
            reloadOptions(sb_select, data);
            if (old_sb !== null) {
                sb_select.val(old_sb).trigger("change");
                old_sb = null;
            } else {
                sb_select.trigger("change");
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadRoute() {
        var sb_id = +sb_select.val();
        var url = '/ajax/sb/' + sb_id + '/route';
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: '',
                title: '{{ trans("back_end.select_a_route") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['route_name']
                });
            }
            reloadOptions(route_select, data);
            if (old_route !== null) {
                route_select.val(old_route).trigger("change");
                old_route = null;
            } else {
                route_select.trigger("change");
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function getRouteDetail() {
        var route_id = +route_select.val();
        var url = '/ajax/route/' + route_id;
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            $('[name="branch_no"]').val(response.branch_number);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadSegment() {
        var route_id = +route_select.val();
        var sb_id = +sb_select.val();
        var url = '/ajax/route/' + route_id + '/segment?sb_id=' + sb_id;
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: '',
                title: '{{ trans("back_end.select_a_segment") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['segment_info']
                });
            }
            reloadOptions(segment_select, data);
            if (old_segment !== null) {
                segment_select.val(old_segment).trigger("change");
                old_segment = null;
            } else {
                segment_select.trigger("change");
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    $('document').ready(function(){
        setOnChangeEvent();
        loadSB();
        // getDesignSpeed();
        @if (isset($edit_flg) && $edit_flg == 0)
            var disabled = ['rmb', 'sb', 'route', 'segment', 'date_collection', 'km_station', 'm_station'];
            for (var i in disabled) {
                $('[name="' + disabled[i] + '"]').attr('disabled', 'disabled');
            }
        @endif
    });

    function setOnChangeEvent() {
        sb_select.change(loadRoute);
        rmb_select.change(loadSB);
        route_select.change(function(){
            getRouteDetail();
            loadSegment();
        });
    }
</script>
@endpush
