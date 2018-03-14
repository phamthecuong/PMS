@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('payment_condition')
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
            {{trans('menu.payment_condition')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa fa-map-marker',
        'text1' => trans('menu.payment_condition'),
        'text2' => trans('menu.export')
    ])

     <section id="widget-grid">
        {!! Form::open(["url" => "pavement_conditions/post_export", "method" => "post"]) !!}
        <div class="row">
            <article class="col-lg-7">
                @box_open("")
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
                                @include('custom.form_select', [
                                        'title' => trans('back_end.year'),
                                        'items' => [],
                                        'name' => 'year',
                                        'hint' => trans('back_end.year'),
                                        'value' => ''
                                ])
                               <!--  {!!
                                    Form::lbSelect('year', 'null', [], trans('back_end.year'), ['id' => 'year_name'])
                                !!} -->
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
            year_select = $('[name="year"]'),
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
                sb_select.trigger("change");
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
                route_select.trigger("change");                
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
        }

        function loadYear() {
            var route_id = +route_select.val();
            var sb_id = +sb_select.val();
            var rmb_id = +rmb_select.val();
            var url = '/get_year_value?rmb_id='+rmb_id+'&sb_id='+sb_id+'&branch_id='+route_id;
            $.ajax({
                url: url,
                method: 'GET'
            })
            .done(function(response) {
                console.log('year' + response);
                var data = [];
                for (var i in response) {
                    if (response[i]['text'] == '****' || i==0 || i==1) {
                        continue;
                    }
                    data.push({
                        value: response[i]['value'],
                        title: response[i]['text']
                    });
                }
                reloadOptions(year_select, data);
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
                loadYear(); 
            });
        }
    </script>
@endpush



