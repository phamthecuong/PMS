<!-- The Modal -->
<div id="{{ $id }}" class="my-modal" style="z-index: 100">
    <div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons ui-draggable modal-content"
         tabindex="-1" role="dialog" aria-describedby="dialog_simple" aria-labelledby="ui-id-50"
         style="height: auto; width: 900px; top: 479px; left: 475px;">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span id="ui-id-50" class="ui-dialog-title">
                    <div class="widget-header"><h4><i class="fa fa-pencil-square-o"></i> {{  $modal_title }}</h4>
                    </div>
                </span>
            <button class="ui-dialog-titlebar-close"></button>
        </div>
        @php
            $lang = App::isLocale('en') ? 'en' : 'vi';
        @endphp
        <form ng-submit="{{ $event_submit }}">
            <div id="dialog_simple" class="ui-dialog-content ui-widget-content"
                 style="width: auto; min-height: 0px; max-height: none; height: auto;">
                <div class="collapse-group">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree" ng-class="{'red-panel': errors.scope_manage_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#scopeManage" aria-expanded="true"
                                   aria-controls="scopeManage" class="trigger">
                                    {!! trans('back_end.scope_manage') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="scopeManage" class="panel-collapse collapse in" role="tabpanel"
                             aria-labelledby="headingThree">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        {!! Form::lbSelect('rmb', '', $visible['rmb']['item'], trans('back_end.Road Management Bureau'), [
                                            'ng-model' => $scope_form.'.rmb',
                                            'change-select' => 'sbChange',
                                            'ng-class' => '{error: errors.rmb[0]}'
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.rmb[0]" ng-bind="errors.rmb[0].toString()"></span>
                                    </div>

                                    <div class="col-lg-6">
                                        {!! Form::lbSelect("sb", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.sub_bureau"),[
                                            'ng-model' => $scope_form.'.sb',
                                            'ng-options' => 'item.id as item.organization_name for item in sb',
                                            'change-select' => 'roadChange',
                                            'ng-class' => '{error: errors.sb[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.sb[0]" ng-bind="errors.sb[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-8">
                                        {!! Form::lbSelect("route_name", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.route_branch"),[
                                            'ng-model' => $scope_form.'.route_name',
                                            'ng-options' => 'item.id as item.name for item in road',
                                            'change-select' => 'segmentChange',
                                            'ng-class' => '{error: errors.route_name[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.route_name[0]" ng-bind="errors.route_name[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbText('branch_number', '', trans('back_end.branch_no'), '', '', [
                                            'ng-model' =>  $scope_form.'.branch_number',
                                            'ng-class' => '{error: errors.branch_number[0]}',
                                            'readonly'
                                        ]) !!}
                                    </div>
                                    {{-- <div class="col-lg-12">
                                        {!! Form::lbSelect("segment_id", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.segment"),[
                                            'ng-model' => $scope_form.'.segment_id',
                                            'ng-options' => 'item.id as item.segment_info for item in segment',
                                            'ng-class' => '{error: errors.segment_id[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.segment_id[0]" ng-bind="errors.segment_id[0].toString()"></span>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Chaninage -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.chainage_n_position_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#chainage" aria-expanded="true"
                                   aria-controls="chainage" class="trigger collapsed">
                                    {!! trans('back_end.chainage_n_position') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="chainage" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
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
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.km'),
                                        'value' => '',
                                        'name' => 'km_from',
                                        'model_name' => $scope_form.'.km_from'
                                        ])
                                    </div>
                                    <div class="col-lg-3">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.m'),
                                        'value' => '',
                                        'name' => 'm_from',
                                        'model_name' => $scope_form.'.m_from'
                                        ])
                                    </div>
                                    <div class="col-lg-3">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.km'),
                                        'value' => '',
                                        'name' => 'km_to',
                                        'model_name' => $scope_form.'.km_to'
                                        ])
                                    </div>
                                    <div class="col-lg-3">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.m'),
                                        'value' => '',
                                        'name' => 'm_to',
                                        'model_name' => $scope_form.'.m_to'
                                        ])
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        {!! Form::lbText('length', '', trans('back_end.Length as per Chainage'), '', '', [
                                            'ng-class' => '{error: errors.length[0]}',
                                            'ng-value' => '(forme.km_to * 1000 + forme.m_to) - (forme.km_from * 1000 + forme.m_from)',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.length[0]"
                                              ng-bind="errors.length[0].toString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.information_of_repair_section_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#repair_section" aria-expanded="true"
                                   aria-controls="repair_section" class="trigger collapsed">
                                    {!! trans('back_end.information_of_repair_section') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="repair_section" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
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
                                     'value' => '',
                                     'model_name' => 'forme.direction'
                                 ])
                                    </div>
                                    <div class="col-lg-3">
                                        {!! Form::lbText('lane_pos_no', '', trans('back_end.lane_no'), '', '', [
                                            'ng-model' =>  $scope_form.'.lane_pos_no',
                                            'ng-class' => '{error: errors.lane_pos_no[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.lane_pos_no[0]"
                                              ng-bind="errors.lane_pos_no[0].toString()"></span>
                                    </div>
                                    {{-- <div class="col-lg-3">
                                        {!! Form::lbText('actual_length', '', trans('back_end.actual_length'), '', '', [
                                            'ng-model' =>  $scope_form.'.actual_length',
                                            'ng-class' => '{error: errors.actual_length[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.actual_length[0]"
                                              ng-bind="errors.actual_length[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-3">
                                        {!! Form::lbText('total_width_repair_lane', '', trans('back_end.repair_width'), '', '', [
                                            'ng-model' =>  $scope_form.'.total_width_repair_lane',
                                            'ng-class' => '{error: errors.total_width_repair_lane[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total_width_repair_lane[0]"
                                              ng-bind="errors.total_width_repair_lane[0].toString()"></span>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.repair_method_info_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#repair_method_info" aria-expanded="true"
                                   aria-controls="repair_method_info" class="trigger collapsed">
                                    {!! trans('back_end.repair_method_info') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="repair_method_info" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        {!! Form::lbText('planned_year', '', trans('wp.planned_year'), '', '', [
                                            'ng-model' =>  $scope_form.'.planned_year',
                                            'ng-class' => '{error: errors.planned_year[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.planned_year[0]"
                                              ng-bind="errors.planned_year[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.repair_method'),
                                            'items' => \App\Models\mstRepairMethod::allToOptionTwo(),
                                            'name' => 'repair_method',
                                            'hint' => trans('back_end.repair_method'),
                                            'value' => '',
                                            'model_name' => $scope_form.'.repair_method',
                                            'attribute' => [
                                                'ng-change' => 'loadRepairInfo(' .$scope_form.'.rmb, ' . $scope_form.'.repair_method)'
                                            ]
                                        ])

                                    </div>

                                    <div class="col-lg-4">
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.repair_classification'),
                                            'items' => \App\Models\tblRClassification::allToOption(),
                                            'name' => 'repair_classification',
                                            'hint' => trans('back_end.repair_classification_hint'),
                                            'value' => '',
                                            'model_name' => $scope_form.'.repair_classification',
                                            'disable' => true,
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        {!! Form::lbText('unit_cost', '', trans('wp.unit_cost'), '', '', [
                                            'ng-class' => '{error: errors.unit_cost[0]}',
                                            'ng-model' =>  $scope_form.'.unit_cost',
                                            'readonly'
                                        ]) !!}
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbText('repair_quantity', '', trans('wp.repair_quantity'), '', '', [
                                            'ng-model' =>  $scope_form.'.repair_quantity',
                                            'ng-class' => '{error: errors.repair_quantity[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.repair_quantity[0]"
                                              ng-bind="errors.repair_quantity[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbText('repair_amount', '', trans('wp.repair_cost'), '', '', [
                                            'ng-model' =>  $scope_form.'.repair_amount',
                                            'ng-class' => '{error: errors.repair_amount[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.repair_amount[0]"
                                              ng-bind="errors.repair_amount[0].toString()"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        {!! Form::lbText('remarks', '', trans('wp.remarks'), '', '', [
                                            'ng-model' =>  $scope_form.'.remarks',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
            <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
                <div class="ui-dialog-buttonset">
                    <button type="submit" class="btn btn-primary"><i
                                class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>
                    <button type="button" class="btn btn-default" id="close"><i
                                class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- End the Modal -->
@push('script')
<script>
    $(function () {
        $("#accordion").accordion({
            collapsible: true
        });
    });
</script>
<script type="text/javascript">
    $(".open-button").on("click", function () {
        $(this).closest('.collapse-group').find('.collapse').collapse('show');
    });

    $(".close-button").on("click", function () {
        $(this).closest('.collapse-group').find('.collapse').collapse('hide');
    });
    $(document).ready(function () {
        $('.ui-dialog-titlebar-close, #close').click(function () {
            $('#{{ $id }}').hide();
        });
    });

</script>
@endpush
@push('css')
<style type="text/css">
    a:hover, a:visited, a:link, a:active {
        text-decoration: none;
    }

    .menu-on-top aside#left-panel {
        z-index: 1;
    }

    .controls {
        margin-bottom: 10px;
    }

    .collapse-group {

        margin-bottom: 10px;
    }

    .panel-title .trigger:before {
        content: '\e082';
        font-family: 'Glyphicons Halflings';
        vertical-align: text-bottom;
    }

    .panel-title .trigger.collapsed:before {
        content: '\e081';
    }

    .error {
        background-color: #f2dede;
    }

    .my-modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 0px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        /*overflow: auto;  Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Fallback color */
        background-color: rgba(0, 0, 0, 0); /* Black w/ opacity */
    }

    .modal-content {
        position: relative !important;
        margin: auto !important;
        top: 70px !important;
        left: 0 !important;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
        -webkit-animation-duration: 0.4s;
        animation-duration: 0.4s
    }

    .ui-dialog .ui-dialog-content {
        height: 400px !important;
    }

    .ui-draggable .ui-dialog-titlebar {
        cursor: auto;
    }
</style>
@endpush

