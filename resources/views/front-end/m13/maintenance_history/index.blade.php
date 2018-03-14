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
        'text2' => trans('back_end.mh_section_list')
    ])

    <section id="widget-grid">
        @if (\Session::has('error'))
            <div class="row">
                <article class="col-lg-12">
                    <div class="alert alert-danger fade in">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        <strong>{{ trans('admin.Error') }}!</strong> {{ \Session::get('error') }}
                    </div>
                </article>
            </div>
        @endif
        <div class="well">
            
            <a href="{{url('maintenance_history/import_data')}}" class="btn btn-success">
                {{trans('back_end.import')}}
            </a>
            <a class="btn btn-primary" href="{!! url('/admin/maintenance_history/export') !!}">
                {!! trans('back_end.export') !!}
            </a>
        </div>
        <div class="row">
            <article class="col-lg-12">
                @if (\Session::has('count_new') && \Session::has('count_err'))
                    <div class="row">
                        <article class="col-lg-12">
                            <div class="alert alert-success fade in">
                                <button class="close" data-dismiss="alert">
                                    ×
                                </button>
                                {{trans('validation.import_success').' '.\Session::get('count_new') .' '.trans('validation.count_new')}} <br/>

                                {{trans('validation.import_success').' '.\Session::get('update_h') .' '.trans('validation.update_history')}} <br/>
                                {{trans('validation.has_err').' '.\Session::get('count_err').' '.trans('validation.count_err') }}
                                @if(\Session::get('count_err')!=0)
                                <a href="{!! url('/maintenance_history/'.\Session::get('file_name').'/export_invalid') !!}"> {!! trans('back_end.download_here') !!}</a>
                                @endif
                            </div>
                        </article>
                    </div>
                @endif
                @box_open(trans("back_end.mh_list_panel_title"))
                <div>
                    <div class="widget-body no-padding">
                        <div class="table-responsive">
                            <?php $lang = App::isLocale('en') ? 'en' : 'vn'; ?>
                            @include("custom.table_extra_data", [
                                'table_id' => 'mh_list',
                                'url' => "/ajax/maintenance_history",
                                'columns' => [
                                    [
                                        'data' => 'segment.tbl_branch.name_' . $lang,
                                        'title' => trans('back_end.route_name'),
                                        'name' => 'route_name',
                                        'items' => App\Models\tblBranch::allOptionToAjax(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'segment.segname_' . $lang,
                                        'title' => trans('back_end.segment_name'),
                                        'name' => 'segment_id',
                                        'items' => App\Models\tblSegment::allOptionToAjax(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
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
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'segment.tbl_organization.name_' . $lang,
                                        'title' => trans('back_end.sb_manager'),
                                        'name' => 'sb',
                                        'items' => App\Models\tblOrganization::getListSB(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'km_from',
                                        'title' => trans('back_end.km_from'),
                                        'name' => 'km_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'm_from',
                                        'title' => trans('back_end.m_from'),
                                        'name' => 'm_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'km_to',
                                        'title' => trans('back_end.km_to'),
                                        'name' => 'km_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'm_to',
                                        'title' => trans('back_end.m_to'),
                                        'name' => 'm_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'direction',
                                        'title' => trans('back_end.direction'),
                                        'name' => 'direction',
                                        'items' => \App\Classes\Helper::getListDirection(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'lane_pos_number',
                                        'title' => trans('back_end.lane_pos_number'),
                                        'name' => 'lane_pos_number',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                    ],
                                    [
                                        'data' => 'survey_time',
                                        'title' => trans('back_end.survey_time'),
                                        'name' => 'survey_time',
                                        'filterType' => "super_input",
                                        'hasFilter' => true,
                                        'hint' => false,
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
        var branch_data = JSON.parse('{!! json_encode($branch_data) !!}');
        var sb_data = JSON.parse('{!! json_encode($sb_data) !!}');
        console.log(tree_data);
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

