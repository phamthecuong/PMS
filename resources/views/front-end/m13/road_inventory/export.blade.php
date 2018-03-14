@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_road_inventory')
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
        'icon' => 'fa-road',
        'text1' => trans('menu.road_inventory'),
        'text2' => trans('back_end.export')
    ])

    <section id="widget-grid">
        {!! Form::open(["url" => "/admin/road_inventories/export", "method" => "post"]) !!}
        <div class="row">
            <article class="col-lg-7">
                @box_open(trans('back_end.rmd_info'))
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
                                    'value' => '',
                                    'hint' => trans('back_end.km_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'm_from',
                                    'title' => trans('back_end.m'),
                                    'value' => '',
                                    'hint' => trans('back_end.m_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'km_to',
                                    'title' => trans('back_end.km'),
                                    'value' => '',
                                    'hint' => trans('back_end.km_to_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'm_to',
                                    'title' => trans('back_end.m'),
                                    'value' => '',
                                    'hint' => trans('back_end.m_to_hint'),
                                ])
                            </div>
                        </div>
                        
                        <header>
                            <legend>{!! trans('back_end.Information of Motorized Lane') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-3">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.direction'),
                                    'items' => [
                                        ['name' => trans('back_end.all'), 'value' => ''],
                                        ['name' => trans('back_end.left'), 'value' => 1],
                                        ['name' => trans('back_end.right'), 'value' => 2],
                                        ['name' => trans('back_end.single'), 'value' => 3]
                                    ],
                                    'name' => 'direction',
                                    'hint' => trans('back_end.direction_hint'),
                                    'value' => ''
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'lane_no',
                                    'title' => trans('back_end.lane_no'),
                                    'value' => '',
                                    'hint' => trans('back_end.lane_no_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.year_up_to'),
                                    'items' => \App\Models\tblSectiondataRMD::getYearHasData(),
                                    'name' => 'year_up_to',
                                    'hint' => trans('back_end.year_up_to_hint'),
                                    'value' => ''
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
        {!! Form::close() !!}
    </section>
@endsection

@push('script')
    <script type="text/javascript">
        var rmb_select = $('[name="rmb"]'),
            sb_select = $('[name="sb"]'),
            route_select = $('[name="route"]'),
            segment_select = $('[name="segment"]'),
            terrain_select = $('[name="terrain_type"]'),
            road_class_select = $('[name="road_class"]'),
            km_from_input = $('[name="km_from"]'),
            m_from_input = $('[name="m_from"]'),
            km_to_input = $('[name="km_to"]'),
            m_to_input = $('[name="m_to"]');
            // ,
            // old_sb = '{{(null !== old("sb")) ? old("sb") : @$sb_id}}',
            // old_route = '{{(null !== old("route")) ? old("route") : @$branch_id}}',
            // old_segment = '{{(null !== old("segment")) ? old("segment") : @$segment_id}}'

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
                var data = [];
                if (response.length > 1) {
                    data.push({
                            value: '-1',
                            title: '{{ trans("back_end.all_sb") }}'
                        })
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
                    value: '-1',
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
