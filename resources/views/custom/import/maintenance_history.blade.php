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
           // dd($visible['terrian_type_id']['item']);
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
                                {!! Form::lbSelect("road", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.route_branch"),[
                                    'ng-model' => $scope_form.'.road',
                                    'ng-options' => 'item.id as item.name for item in road',
                                    'change-select' => 'segmentChange',
                                    'ng-class' => '{error: errors.road[0]}'
                                ]) !!}
                                <span class="help-block" style="color: #b94a48;" ng-show="errors.road[0]" ng-bind="errors.road[0].toString()"></span>
                            </div>
                            <div class="col-lg-4">
                                {!! Form::lbText('route_branch', '', trans('back_end.branch_no'), '', '', [
                                    'ng-model' =>  $scope_form.'.route_branch',
                                    'ng-class' => '{error: errors.route_branch[0]}',
                                    'readonly'
                                ]) !!}
                            </div>
                            <div class="col-lg-12">
                                {!! Form::lbSelect("segment_id", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.segment"),[
                                    'ng-model' => $scope_form.'.segment_id',
                                    'ng-options' => 'item.id as item.segment_info for item in segment',
                                    'ng-class' => '{error: errors.segment_id[0]}'
                                ]) !!}
                                <span class="help-block" style="color: #b94a48;" ng-show="errors.segment_id[0]" ng-bind="errors.segment_id[0].toString()"></span>
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.general_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#information" aria-expanded="true"
                                   aria-controls="information" class="trigger collapsed">
                                    {!! trans('back_end.General.Information') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="information" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        @include('custom.import.form_datepicker_import', [
                                        'title' => trans('back_end.date_collection'),
                                        'name' => 'survey_time',
                                        'hint' => trans('back_ end.date_collection_hint'),
                                        'value' => '',
                                        'model_name' => $scope_form.'.survey_time',
                                    ])
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbText('completion_date', '', trans('back_end.completion_date'), '', '', [
                                            'ng-model' =>  $scope_form.'.completion_date',
                                            'ng-class' => '{error: errors.completion_date[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.completion_date[0]"
                                              ng-bind="errors.completion_date[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbText('repair_duration', '', trans('back_end.repair_duration'), '', '', [
                                            'ng-model' =>  $scope_form.'.repair_duration',
                                            'ng-class' => '{error: errors.repair_duration[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.repair_duration[0]"
                                              ng-bind="errors.repair_duration[0].toString()"></span>
                                    </div>
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
                                    <div class="col-lg-3">
                                        {!! Form::lbText('from_lat', '', trans('back_end.latitude'), '', '', [
                                            'ng-model' =>  $scope_form.'.from_lat',
                                            'ng-class' => '{error: errors.from_lat[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.from_lat[0]"
                                              ng-bind="errors.from_lat[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-3">
                                        {!! Form::lbText('from_lng', '', trans('back_end.longitude'), '', '', [
                                            'ng-model' =>  $scope_form.'.from_lng',
                                            'ng-class' => '{error: errors.from_lng[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.from_lng[0]"
                                              ng-bind="errors.from_lng[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-3">
                                        {!! Form::lbText('to_lat', '', trans('back_end.latitude'), '', '', [
                                            'ng-model' =>  $scope_form.'.to_lat',
                                            'ng-class' => '{error: errors.to_lat[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.to_lat[0]"
                                              ng-bind="errors.to_lat[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-3">
                                        {!! Form::lbText('to_lng', '', trans('back_end.longitude'), '', '', [
                                            'ng-model' =>  $scope_form.'.to_lng',
                                            'ng-class' => '{error: errors.to_lng[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.to_lng[0]"
                                              ng-bind="errors.to_lng[0].toString()"></span>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect('province_from', '', App\Models\tblCity::allToOption(), trans('back_end.province_from'), [
                                            'ng-model' => $scope_form.'.province_from',
                                            'change-select' => 'districtFrom',
                                            'ng-class' => '{error: errors.province_from[0]}'
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.province_from[0]" ng-bind="errors.province_from[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect("district_from", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.district_from"),[
                                            'ng-model' => $scope_form.'.district_from',
                                            'ng-options' => 'item.id as item.name for item in district_from',
                                            'change-select' => 'wardFrom',
                                            'ng-class' => '{error: errors.district_from[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.district_from[0]" ng-bind="errors.district_from[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect("ward_from", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.ward_from"),[
                                        'ng-model' => $scope_form.'.ward_from',
                                        'ng-options' => 'list.id as list.name for list in ward_from',
                                        'ng-class' => '{error: errors.province_from[0]}'
                                    ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.ward_from[0]" ng-bind="errors.ward_from[0].toString()"></span>
                                    </div>
                                    {{--to--}}
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect('province_to', '', App\Models\tblCity::allToOption(), trans('back_end.province_to    '), [
                                            'ng-model' => $scope_form.'.province_to',
                                            'change-select' => 'districtTo',
                                            'ng-class' => '{error: errors.province_to[0]}'
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.province_to[0]" ng-bind="errors.province_to[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect("district_to", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.district_to"),[
                                            'ng-model' => $scope_form.'.district_to',
                                            'ng-options' => 'item.id as item.name for item in district_to',
                                            'change-select' => 'wardTo',
                                            'ng-class' => '{error: errors.district_to[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.district_to[0]" ng-bind="errors.district_to[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect("ward_to", '', [['name'=> trans('back_end.please_choose'), 'value' => null]], trans("back_end.ward_to"),[
                                        'ng-model' => $scope_form.'.ward_to',
                                        'ng-options' => 'list.id as list.name for list in ward_to',
                                        'ng-class' => '{error: errors.ward_to[0]}'
                                    ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.ward_to[0]" ng-bind="errors.ward_to[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-6 pull-right">
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
                                        {!! Form::lbText('lane_pos_number', '', trans('back_end.lane_no'), '', '', [
                                            'ng-model' =>  $scope_form.'.lane_pos_number',
                                            'ng-class' => '{error: errors.lane_pos_number[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.lane_pos_number[0]"
                                              ng-bind="errors.lane_pos_number[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-3">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne"  ng-class="{'red-panel': errors.mh_position_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#mh_position" aria-expanded="true"
                                   aria-controls="mh_position" class="trigger collapsed">
                                    {!! trans('back_end.mh_position') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="mh_position" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.import.custom_ratio',[
                                        'name' => 'direction_running',
                                        'value' => '',
                                        'items' => [
                                                    ['name' => trans('back_end.left'), 'value' => '0'],
                                                    ['name' => trans('back_end.right'), 'value' => '1']
                                                ],
                                        'title' => trans('back_end.Maintenance History Position'),
                                        'model' => 'direction_running',
                                        'ng_class' => '{error: errors.direction_running[0]}'
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        {!! Form::lbText('distance', '', trans('back_end.distance_to_center'), '', '', [
                                            'ng-model' =>  $scope_form.'.distance',
                                            'ng-class' => '{error: errors.distance[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.distance[0]"
                                              ng-bind="errors.distance[0].toString()"></span>
                                    </div>
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
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.repair_method'),
                                            'items' => \App\Models\mstRepairMethod::allToOptionTwo(),
                                            'name' => 'repair_method',
                                            'hint' => trans('back_end.repair_method'),
                                            'value' => '',
                                            'model_name' => $scope_form.'.repair_method',
                                            'change_select' => 'loadClassification',
                                        ])
                                    </div>

                                    <div class="col-lg-4">
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.repair_classification'),
                                            'items' => \App\Models\tblRClassification::allToOption(),
                                            'name' => 'r_classification_id',
                                            'hint' => trans('back_end.repair_classification_hint'),
                                            'value' => '',
                                            'model_name' => $scope_form.'.r_classification_id',
                                            'disable' => true,
                                        ])
                                    </div>
                                    <div class="col-lg-4">
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.repair_structtype'),
                                            'items' => \App\Models\tblRStructtype::allToOption(),
                                            'name' => 'r_struct_type_id',
                                            'hint' => trans('back_end.repair_structtype_hint'),
                                            'value' => @$data->r_structType_id,
                                            'model_name' => 'formAddMH.r_struct_type_id',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.material_layer_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#material_layer" aria-expanded="true"
                                   aria-controls="material_layer" class="trigger collapsed">
                                    {!! trans('back_end.material_layer') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="material_layer" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.form_select', [
                                        'title' => trans('back_end.pavement_type'),
                                            'items' => $visible['pavement_type_id']['item'],
                                            'name' => "pavement_type_id",
                                            'value' => '',
                                            'model_name' => $scope_form.'.pavement_type_id',
                                            'change_select' => 'SurfaceMHChange',

                                        ])
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.pavement_type_id[0]"
                                              ng-bind="errors.pavement_type_id[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_select', [
                                            'title' => trans('back_end.surface'),
                                            'items' => \App\Models\mstSurface::allToOption(),
                                            'name' => 'surface_id',
                                            'hint' => trans('back_end.surface_hint'),
                                            'value' => @$data->r_category_id,
                                            'model_name' => $scope_form.'.surface_id',
                                            'disable' => true,
                                        ])
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        {!! Form::lbSelect("r_category_id", '', [], trans("back_end.repair_category"),[
                                                'ng-options' => 'item.id as item.name for item in categoryMH',
                                                'ng-model' => $scope_form.'.r_category_id',
                                                'ng-class' => "{error: errors['r_category_id'][0]}"
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors['r_category_id'][0]" ng-bind="errors['r_category_id'][0].toString()"></span>
                                    </div>
                                    <div class="col-lg-6">
                                        {!! Form::lbText('pavement_thickness', '', trans('back_end.pavement_thickness'), '', '', [
                                            'ng-model' =>  $scope_form.'.total_pavement_thickness',
                                            'ng-class' => '{error: errors.total_pavement_thickness[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.total_pavement_thickness[0]" ng-bind="errors.total_pavement_thickness[0].toString()"></span>
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

