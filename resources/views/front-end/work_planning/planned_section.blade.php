@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_planned_section')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.work_planning')}}
        </li>
        <li>
            {{trans('menu.planned_section')}}
        </li>
    </ol>
@endsection

@section('content')
    
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.working_planning'),
        'text2' => trans('wp.planned_section')
    ])

    <section id="widget-grid">                          
        <div class="row">  
            <div class="col-lg-12">
                @if (Session::has('flash_message'))
                    <div class="alert alert-{!! Session::get('flash_level') !!}">
                        {!! Session::get('flash_message') !!}
                    </div>
                @endif      
            </div>
            <article class="col-lg-12">
                @box_open(trans("wp.section_list"))
                <div>               
                    <div class="widget-body no-padding">
                        <div class="table-responsive">
                            <?php $lang = \App::isLocale('en') ? 'en' : 'vn'; ?>
                            @include("custom.table_planned_section", [ 
                                'url' => "/ajax/work/planned_section",          
                                'columns' => [
                                    [],
                                    [
                                        'data' => 'branch_name_'.$lang,
                                        'title' => trans('wp.RouteName'),
                                        'name' => 'branch_name_'.$lang,
                                        'items' => App\Models\tblBranch::allOptionToAjax(false, true),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,

                                    ],
                                    [
                                        'data' => 'branch_number',
                                        'title' => trans('wp.branch_number'),
                                        'name' => 'branch_number',
                                        'items' => App\Models\tblBranch::branchNumberOptionToAjax(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'rmb_name_'.$lang,
                                        'title' => trans('wp.rmb'),
                                        'name' => 'rmb_name_'.$lang,
                                        'items' => App\Models\tblOrganization::getListRmb(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true,

                                    ],
                                    [
                                        'data' => 'sb_name_'.$lang,
                                        'title' => trans('wp.sb'),
                                        'name' => 'sb_name_'.$lang,
                                        'items' => App\Models\tblOrganization::getListSB(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'km_from', 
                                        'title' => trans('wp.from_km'),
                                        'name' => 'km_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'm_from', 
                                        'title' => trans('wp.from_m'),
                                        'name' => 'm_from',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'km_to', 
                                        'title' => trans('wp.to_km'),
                                        'name' => 'km_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'm_to', 
                                        'title' => trans('wp.to_m'),
                                        'name' => 'm_to',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'section_length', 
                                        'title' => trans('wp.Lenght, m')
                                    ],
                                    [
                                        'data' => 'direction', 
                                        'title' => trans('wp.UpOr Down'),
                                        'name' => 'direction',
                                        'items' => \App\Classes\Helper::getListDirection(),
                                        'filterType'=> 'dropdown',
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'lane_pos_no', 
                                        'title' => trans('wp.lane_pos_no'),
                                    ],
                                    [
                                        'data' => 'planned_year', 
                                        'title' => trans('wp.planned_year'),
                                        'name' => 'planned_year',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'repair_method_'.$lang,
                                        'title' => trans('wp.repair_method')
                                    ],
                                    [
                                        'data' => 'repair_classification_'.$lang,
                                        'title' => trans('wp.repair_classification')
                                    ],
                                    [
                                        'data' => 'unit_cost',
                                        'title' => trans('wp.unit_cost'),
                                        'name' => 'unit_cost',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'repair_quantity',
                                        'title' => trans('wp.repair_work_quantity'),
                                        'name' => 'repair_quantity',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ],
                                    [
                                        'data' => 'repair_cost',
                                        'title' => trans('wp.repair_cost'),
                                        'name' => 'repair_cost',
                                        'filterType' => "super_input",
                                        'hasFilter' => true
                                    ]
                                ]           
                            ])
                        </div>
                        <div class="widget-footer">
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/work_planning/planned_section/import_data">
                                {{trans('wp.import')}}
                            </a>
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
                text: '{{trans('back_end.all')}}',
                id: ''
            }];
            if (!new_rmb || new_rmb == '') {
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