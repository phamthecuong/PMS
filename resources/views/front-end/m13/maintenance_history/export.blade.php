@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_maintenance_history')
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
            {{trans('menu.maintenance_history')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-calendar',
        'text1' => trans('menu.maintenance_history'),
        'text2' => trans('menu.export')
    ])

    <section id="widget-grid">
        {!! Form::open(["url" => "/admin/maintenance_history/export", "method" => "post"]) !!}
        <div class="row">
            <article class="col-lg-7">
                @box_open(trans('back_end.mh_info'))
                <div>
                    <div class="widget-body">
                        <header>
                            <legend>{!! trans('back_end.scope_manage') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-6">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.Road Management Bureau'),
                                    'items' => \App\Models\tblOrganization::getListRmb(),
                                    'name' => 'rmb',
                                    'hint' => trans('back_end.rmb_hint'),
                                    'value' => ''
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
                                @include('custom.form_select', [
                                    'title' => trans('back_end.segment'),
                                    'items' => [],
                                    'name' => 'segment',
                                    'hint' => trans('back_end.segment_hint'),
                                    'value' => ''
                                ])
                            </div>
                        </div>
                        <header>
                            <legend>{!! trans('back_end.chainage_n_position') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-6">
                                <header>
                                    <h4><b>{!! trans('back_end.from') !!}</b></h4>
                                    <hr/>
                                </header>
                            </div>
                            <div class="col-lg-6">
                                <header>
                                    <h4><b>{!! trans('back_end.to') !!}</b></h4>
                                    <hr/>
                                </header>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'km_from',
                                    'title' => trans('back_end.km'),
                                    'value' => @$data->km_from,
                                    'hint' => trans('back_end.km_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'm_from',
                                    'title' => trans('back_end.m'),
                                    'value' => @$data->m_from,
                                    'hint' => trans('back_end.m_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'km_to',
                                    'title' => trans('back_end.km'),
                                    'value' => @$data->km_to,
                                    'hint' => trans('back_end.km_to_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'm_to',
                                    'title' => trans('back_end.m'),
                                    'value' => @$data->m_to,
                                    'hint' => trans('back_end.m_to_hint'),
                                ])
                            </div>
                        </div>
                        <div class="widget-footer">
                            <button class="btn btn-default">
                                {{ trans('back_end.export_btn_title') }}
                            </button>
                        </div>
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>
@endsection

@push('script')
<script type="text/javascript">
    var 
        rmb_select = $('[name="rmb"]'),
        sb_select = $('[name="sb"]'),
        route_select = $('[name="route"]'),
        segment_select = $('[name="segment"]'),
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

    function loadSB() {
        var rmb_id = +rmb_select.val();
        var url = '/ajax/rmb/' + rmb_id + '/sb';
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            if (response.length > 1) {
                var data = [{
                    value: '-1',
                    title: '{{ trans("back_end.all_sb") }}'
                }];
            } else {
                var data = [];
            }
            
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['organization_name']
                });
            }
            reloadOptions(sb_select, data);
            // if (old_sb !== null) {
                sb_select.trigger("change");
            //     old_sb = null;
            // }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadRoute() {
        var sb_id = +sb_select.val();
        var rmb_id = +rmb_select.val();
        var url = '/ajax/sb/' + sb_id + '/route?rmb_id=' + rmb_id;
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: '-1',
                title: '{{ trans("back_end.all_route") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['route_name']
                });
            }
            reloadOptions(route_select, data);
            // if (old_route !== null) {
                route_select.trigger("change");
            //     old_route = null;
            // }
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
                title: '{{ trans("back_end.all_segment") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['segment_info']
                });
            }
            reloadOptions(segment_select, data);
            // if (old_segment !== null) {
            segment_select.trigger("change");
            //     old_segment = null;
            // }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    $('document').ready(function(){
        setOnChangeEvent();
        loadSB();
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