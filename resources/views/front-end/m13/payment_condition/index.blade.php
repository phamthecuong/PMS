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
        'text2' => trans('back_end.payment_condition_list')
    ])

    <section id="widget-grid">
        <div class="well">
            <a class="btn btn-primary" href="{!! url('/pavement_condition/export') !!}">
                {!! trans('back_end.export') !!}
            </a>
        </div>
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans(""))
                <div>
                    <div class="widget-body no-padding">
                        <div class="table-responsive">
                            <?php $lang = App::isLocale('en') ? 'en' : 'vn'; ?>
                            @include("custom.table_payment_condition", [
                                'table_id' => 'payment_condition',
                                'url' => "/ajax/payment_condition",
                                'columns' => [
                                    [
                                        'data' => 'geographical_area',
                                        'title' => trans('back_end.Geographical_area'),
                                        'name' => 'geographical_area',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'rmb_name_'.$lang,
                                        'title' => trans('back_end.Jurisdiction'),
                                        'name' => 'rmb_name_'.$lang,
                                        'items' => App\Models\tblOrganization::getListRmb(1),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'sb_name_'.$lang,
                                        'title' => trans('back_end.sb_manager'),
                                        'name' => 'sb_name_'.$lang,
                                        'items' => App\Models\tblOrganization::getListSB(1),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'road_number',
                                        'title' => trans('back_end.Route_number'),
                                        'name' => 'road_number',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'branch_number',
                                        'title' => trans('back_end.branch_number'),
                                        'name' => 'branch_number',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'road_number_supplement',
                                        'title' => trans('back_end.road_number_supplement'),
                                        'name' => 'road_number_supplement',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'branch_name_'.$lang,
                                        'title' => trans('back_end.route_name'),
                                        'name' => 'branch_name_'.$lang,
                                        'hasFilter' => false
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
                                        'data' => 'section_length',
                                        'title' => trans('back_end.section_length'),
                                        'name' => 'section_length',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'analysis_area',
                                        'title' => trans('back_end.analysis_area'),
                                        'name' => 'analysis_area',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'structure',
                                        'title' => trans('back_end.structure'),
                                        'name' => 'structure',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'intersection',
                                        'title' => trans('back_end.intersection'),
                                        'name' => 'intersection',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'overlapping',
                                        'title' => trans('back_end.overlapping'),
                                        'name' => 'overlapping',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'number_of_lane_U',
                                        'title' => trans('back_end.number_of_lane_U'),
                                        'name' => 'number_of_lane_U',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'number_of_lane_D',
                                        'title' => trans('back_end.number_of_lane_D'),
                                        'name' => 'number_of_lane_D',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'direction',
                                        'title' => trans('back_end.direction'),
                                        'name' => 'direction',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'lane_position_no',
                                        'title' => trans('back_end.lane_position_no'),
                                        'name' => 'lane_position_no',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'surface_type',
                                        'title' => trans('back_end.surface_type'),
                                        'name' => 'surface_type',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'date_y',
                                        'title' => trans('back_end.date_y'),
                                        'name' => 'date_y',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                     [
                                        'data' => 'date_m',
                                        'title' => trans('back_end.date_m'),
                                        'name' => 'date_m',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'cracking_ratio_cracking',
                                        'title' => trans('back_end.cracking_ratio_cracking'),
                                        'name' => 'cracking_ratio_cracking',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'cracking_ratio_patching',
                                        'title' => trans('back_end.cracking_ratio_patching'),
                                        'name' => 'cracking_ratio_patching',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'cracking_ratio_pothole',
                                        'title' => trans('back_end.cracking_ratio_pothole'),
                                        'name' => 'cracking_ratio_pothole',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'cracking_ratio_total',
                                        'title' => trans('back_end.cracking_ratio_total'),
                                        'name' => 'cracking_ratio_total',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'rutting_depth_max',
                                        'title' => trans('back_end.rutting_depth_max'),
                                        'name' => 'rutting_depth_max',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'rutting_depth_ave',
                                        'title' => trans('back_end.rutting_depth_ave'),
                                        'name' => 'rutting_depth_ave',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'IRI',
                                        'title' => trans('back_end.IRI'),
                                        'name' => 'IRI',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'MCI',
                                        'title' => trans('back_end.MCI'),
                                        'name' => 'MCI',
                                        'hasFilter' => false
                                    ],
                                    [
                                        'data' => 'note',
                                        'title' => trans('back_end.note'),
                                        'name' => 'note',
                                        'hasFilter' => false
                                    ],
                                ]
                            ])
                        </div>
                    </div>
                </div>
                @box_close
            </article>
        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript">
        var tree_data = JSON.parse('{!! json_encode($tree_data) !!}');
        console.log(tree_data);
        var lang = "<?php echo $lang; ?>";
        $(document).ready(function() {
            $('[name="rmb_name_'+lang+'"]').on('change', function() {
                var new_rmb = $(this).val();
                var new_data = [{
                    id: -1,
                    text: '{{trans('back_end.all')}}'
                }];
                if (!new_rmb || new_rmb == -1) {
                    for (var i in tree_data) {
                        new_data = new_data.concat(tree_data[i]);
                    }
                } 
                else {
                    new_data = new_data.concat(tree_data[new_rmb]);
                }

                $('[name="sb_name_'+lang+'"]').select2('destroy').empty().select2({
                    data: new_data
                });
            });
        })
    </script>
@endpush



