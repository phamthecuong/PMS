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
        'text2' => (isset($breadcrumb_txt)) ? $breadcrumb_txt : trans('back_end.add_new')
    ])

    <section id="widget-grid">
        @if (\Session::has('success'))
        <div class="row">
            <article class="col-sm-12">
                <div class="alert alert-success fade in">
                    <button class="close" data-dismiss="alert">
                        ×
                    </button>
                    <i class="fa-fw fa fa-check"></i>
                    <strong>{{ trans('back_end.success') }}!</strong> {{\Session::get('success')}}
                </div>
            </article>
        </div>
        @endif
        {!! Form::open(["url" => "/admin/maintenance_history", "method" => "post"]) !!}
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
                            <legend>{!! trans('back_end.General.Information') !!}</legend>
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
                                @include('custom.form_datepicker', [
                                    'title' => trans('back_end.completion_date'),
                                    'name' => 'completion_date',
                                    'hint' => trans('back_end.completion_date_hint'),
                                    'value' => @$data->completion_date
                                ])
                            </div>
                            <div class="col-lg-4">
                                @include('custom.form_number', [
                                    'name' => 'repair_duration',
                                    'title' => trans('back_end.repair_duration'),
                                    'value' => @$data->repair_duration,
                                    'hint' => trans('back_end.repair_duration_hint'),
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
                        <div class="row">
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'latitude_from',
                                    'title' => trans('back_end.latitude'),
                                    'value' => @$data->from_lat,
                                    'hint' => trans('back_end.latitude_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'longitude_from',
                                    'title' => trans('back_end.longitude'),
                                    'value' => @$data->from_lng,
                                    'hint' => trans('back_end.longitude_from_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'latitude_to',
                                    'title' => trans('back_end.latitude'),
                                    'value' => @$data->to_lat,
                                    'hint' => trans('back_end.latitude_to_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'longitude_to',
                                    'title' => trans('back_end.longitude'),
                                    'value' => @$data->to_lng,
                                    'hint' => trans('back_end.longitude_to_hint'),
                                ])
                            </div>
                            <div class="col-lg-6 pull-right">
                                {!! Form::lbText('length_as_per_chainage', '', trans('back_end.Length as per Chainage'), '', '', ['readonly'])!!}
                            </div>
                        </div>

                        <header>
                            <legend>{!! trans('back_end.information_of_repair_section') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-3">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.direction'),
                                    'items' => [
                                        ['name' => trans('back_end.left'), 'value' => 1],
                                        ['name' => trans('back_end.right'), 'value' => 2],
                                        ['name' => trans('back_end.single'), 'value' => 3]
                                    ],
                                    'name' => 'direction',
                                    'hint' => trans('back_end.direction_hint'),
                                    'value' => @$data->direction
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'lane_no',
                                    'title' => trans('back_end.lane_no'),
                                    'value' => @$data->lane_pos_number,
                                    'hint' => trans('back_end.lane_no_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'actual_length',
                                    'title' => trans('back_end.actual_length'),
                                    'value' => @$data->actual_length,
                                    'hint' => trans('back_end.actual_length_hint'),
                                ])
                            </div>
                            <div class="col-lg-3">
                                @include('custom.form_number', [
                                    'name' => 'repair_width',
                                    'title' => trans('back_end.repair_width'),
                                    'value' => @$data->total_width_repair_lane,
                                    'hint' => trans('back_end.repair_width_hint'),
                                ])
                            </div>
                        </div>
                        <header>
                            <legend>{!! trans('back_end.mh_position') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-6">
                                {!!
                                    Form::lbRatio('direction_running', @$data->direction_running, [
                                        ['name' => trans('back_end.left'), 'value' => '0'],
                                        ['name' => trans('back_end.right'), 'value' => '1']
                                    ], trans('back_end.Maintenance History Position') )
                                !!}
                            </div>
                            <div class="col-lg-6">
                                @include('custom.form_number', [
                                    'name' => 'distance_to_center',
                                    'title' => trans('back_end.distance_to_center'),
                                    'value' => @$data->distance ,
                                    'hint' => trans('back_end.distance_to_center_hint'),
                                ])
                            </div>
                        </div>

                        <header>
                            <legend>{!! trans('back_end.repair_method_info') !!}</legend>
                        </header>
                        <div class="row">
                            <div class="col-lg-4">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.repair_category'),
                                    'items' => \App\Models\tblRCategory::allToOption(),
                                    'name' => 'repair_category',
                                    'hint' => trans('back_end.repair_category_hint'),
                                    'value' => @$data->r_category_id
                                ])
                            </div>
                            <div class="col-lg-4">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.repair_structtype'),
                                    'items' => \App\Models\tblRStructtype::allToOption(),
                                    'name' => 'repair_structtype',
                                    'hint' => trans('back_end.repair_structtype_hint'),
                                    'value' => @$data->r_structType_id
                                ])
                            </div>
                            <div class="col-lg-4">
                                @include('custom.form_select', [
                                    'title' => trans('back_end.repair_classification'),
                                    'items' => \App\Models\tblRClassification::allToOption(),
                                    'name' => 'repair_classification',
                                    'hint' => trans('back_end.repair_classification_hint'),
                                    'value' => @$data->r_classification_id
                                ])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                @include('custom.textarea', [
                                    'title' => trans('back_end.remark'),
                                    'hint' => trans('back_end.remark_hint'),
                                    'value' => @$data->remark,
                                    'name' => 'remark',
                                    'attribute' => ['rows' => 5]
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                @box_close()
            </article>
            <article class="col-lg-5">
                @box_open(trans('back_end.material_layer'))
                <div>
                    <div class="widget-body no-padding">
                        <!-- <div class="row">
                            <div class="col-lg-4">
                                {!! Form::lbText('pavement_type', '', trans('back_end.pavement_type'), '', trans('back_end.pavement_type_hint'), ['readonly'])!!}
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('binder_course_thickness', '', trans('back_end.binder_course_thickness'), '', trans('back_end.binder_course_thickness_hint'), ['readonly'])!!}
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('wearing_course_thickness', '', trans('back_end.wearing_course_thickness'), '', trans('back_end.wearing_course_thickness_hint'), ['readonly'])!!}
                            </div>
                        </div> -->
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered table-striped table-hover">
                                <thead style="text-align: center;">
                                    <tr>
                                        <td><strong>{{ trans('back_end.layer') }}</strong></td>
                                        <td><strong>{{ trans('back_end.material_type') }}</strong></td>
                                        <td><strong>{{ trans('back_end.thickness_(cm)') }}</strong></td>
                                        <td colspan="4"><strong>{{ trans('back_end.description') }}</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $layers = \App\Models\mstPavementLayer::with('pavementTypes')
                                            ->whereNotNull('parent_id')
                                            ->get();
                                    ?>
                                    @foreach($layers as $key => $layer)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                {{$key+1}}. {{$layer->layer_name}}
                                            </td>
                                            <td>
                                                <?php 
                                                    $material_types = [
                                                        [
                                                            'name' => '',
                                                            'value' => ''
                                                        ]
                                                    ];
                                                    foreach ($layer->pavementTypes as $p)
                                                    {
                                                        $material_types[] = [
                                                            'name' => $p->name,
                                                            'value' => $p->id
                                                        ];
                                                    }
                                                ?>
                                                @include('custom.form_select', [
                                                    'items' => $material_types,
                                                    'name' => "data[$layer->id][material_type]",
                                                    'value' => ''
                                                ])
                                            </td>
                                            <td>
                                                @include('custom.form_number', [
                                                    'name' => "data[$layer->id][thickness]",
                                                    'value' => ''
                                                ])
                                            </td>
                                            <td colspan="4">
                                                {!! 
                                                    Form::lbText("data[$layer->id][desc]", null, null)
                                                !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @box_close()
            </article>
        </div>
        @if(isset($breadcrumb_txt))
        @else
        <div class="well" style="text-align: right">
            {!! Form::lbSubmit(trans('back_end.submit')) !!}
        </div>
        @endif
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
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function getDesignSpeed() {
        var terrain_type_id = +terrain_select.val();
        var road_class_id = +road_class_select.val();
        var url = '/ajax/design_speed';
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                terrain_type_id: terrain_type_id,
                road_class_id: road_class_id
            }
        })
        .done(function(response) {
            var speed = response.speed;
            if (!speed) {
                speed = 'N/A';
            }

            $('[name="design_speed"]').val(speed + ' km/h');
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
        $('[name="length_as_per_chainage"').val(length);
    }

    $('document').ready(function(){
        setOnChangeEvent();
        loadSB();
        getDesignSpeed();
                @if (isset($edit_flg) && $edit_flg == 0)
        var disabled = ['rmb', 'sb', 'route', 'segment', 'date_collection', 'km_from', 'm_from', 'km_to', 'm_to'];
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
        terrain_select.change(getDesignSpeed);
        road_class_select.change(getDesignSpeed);
        km_from_input.change(calculateLength);
        m_from_input.change(calculateLength);
        km_to_input.change(calculateLength);
        m_to_input.change(calculateLength);
    }
</script>
@endpush