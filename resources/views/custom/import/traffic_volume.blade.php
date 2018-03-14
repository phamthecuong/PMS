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
                <div class="collapse-group"     >
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
                        <div class="panel-heading" role="tab" id="headingOne" ng-class="{'red-panel': errors.information_traffic_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#information" aria-expanded="true"
                                   aria-controls="information" class="trigger collapsed">
                                    {!! trans('back_end.Information Traffic') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="information" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.import.form_ympicker_import', [
                                          'title' => trans('back_end.date_collection'),
                                          'name' => 'survey_time',
                                          'hint' => trans('back_ end.date_collection_hint'),
                                          'value' => '',
                                          'model_name' => $scope_form.'.survey_time',
                                      ])
                                    </div>
                                    <div class="col-lg-6">
                                        {!! Form::lbText('name', '', trans('back_end.name'), '', '', [
                                            'ng-model' =>  $scope_form.'.name',
                                            'ng-class' => '{error: errors.name[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.name[0]"
                                              ng-bind="errors.name[0].toString()"></span>
                                    </div>
                                </div>
                                <div class="row">
                                   
                                    <div class="col-lg-6">
                                        {!! Form::lbText('km_station', '', trans('back_end.km_station'), '', '', [
                                            'ng-model' =>  $scope_form.'.km_station',
                                            'ng-class' => '{error: errors.km_station[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.km_station[0]"
                                              ng-bind="errors.km_station[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.lat_station'),
                                        'value' => '',
                                        'name' => 'lat_station',
                                        'model_name' => $scope_form.'.lat_station'
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.m_station'),
                                        'value' => '',
                                        'name' => 'm_station',
                                        'model_name' => $scope_form.'.m_station'
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.lng_station'),
                                        'value' => '',
                                        'name' => 'lng_station',
                                        'model_name' => $scope_form.'.lng_station'
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        {!! Form::lbSelect('province_to', '', App\Models\tblCity::allToOption(), trans('back_end.province_to'), [
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
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        {!! Form::lbTextarea('remark', '', trans('back_end.remark'), '', [
                                            'ng-model' =>  $scope_form.'.remark',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Chaninage -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne"  ng-class="{'red-panel': errors.input_data_of_MH_error}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" href="#InputData" aria-expanded="true"
                                   aria-controls="InputData" class="trigger collapsed">
                                    {!! trans('back_end.TV_inputdata') !!}
                                </a>
                            </h4>
                        </div>
                        <div id="InputData" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <header>
                                            <h4><b>{!! trans('back_end.up') !!}</b></h4>
                                            <hr/>
                                        </header>
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Car, Jeep'),
                                        'value' => '',
                                        'name' => 'up1',
                                        'model_name' => $scope_form.'.up1'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Light Truck'),
                                        'value' => '',
                                        'name' => 'up2',
                                        'model_name' => $scope_form.'.up2'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Medium Truck'),
                                        'value' => '',
                                        'name' => 'up3',
                                        'model_name' => $scope_form.'.up3'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Heavy Truck'),
                                        'value' => '',
                                        'name' => 'up4',
                                        'model_name' => $scope_form.'.up4'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Heavy Truck3'),
                                        'value' => '',
                                        'name' => 'up5',
                                        'model_name' => $scope_form.'.up5'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Small Bus'),
                                        'value' => '',
                                        'name' => 'up6',
                                        'model_name' => $scope_form.'.up6'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Large Bus'),
                                        'value' => '',
                                        'name' => 'up7',
                                        'model_name' => $scope_form.'.up7'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Tractor'),
                                        'value' => '',
                                        'name' => 'up8',
                                        'model_name' => $scope_form.'.up8'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Motobike including 3 wheeler'),
                                        'value' => '',
                                        'name' => 'up9',
                                        'model_name' => $scope_form.'.up9'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Bicycle/Pedicab'),
                                        'value' => '',
                                        'name' => 'up10',
                                        'model_name' => $scope_form.'.up10'
                                        ])

                                        {!! Form::lbText('total_traffic_volume_up', '', trans('back_end.TotalTrafficVolumeUp'), '', '', [
                                            'ng-class' => '{error: errors.total_traffic_volume_up[0]}',
                                            'ng-value' => 'forme.up1 + forme.up6 + forme.up8 + forme.up9 + forme.up10 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total_traffic_volume_up[0]"
                                              ng-bind="errors.total_traffic_volume_up[0].toString()"></span>

                                        {!! Form::lbText('heavy_traffic_up', '', trans('back_end.total_heavy_traffic_up'), '', '', [
                                            'ng-class' => '{error: errors.heavy_traffic_up[0]}',
                                            'ng-value' => 'forme.up2 + forme.up3 + forme.up4 + forme.up5 + forme.up7 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.heavy_traffic_up[0]"
                                              ng-bind="errors.heavy_traffic_up[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        <header>
                                            <h4><b>{!! trans('back_end.down') !!}</b></h4>
                                            <hr/>
                                        </header>
                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Car, Jeep'),
                                        'value' => '',
                                        'name' => 'down1',
                                        'model_name' => $scope_form.'.down1'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Light Truck'),
                                        'value' => '',
                                        'name' => 'down2',
                                        'model_name' => $scope_form.'.down2'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Medium Truck'),
                                        'value' => '',
                                        'name' => 'down3',
                                        'model_name' => $scope_form.'.down3'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Heavy Truck'),
                                        'value' => '',
                                        'name' => 'down4',
                                        'model_name' => $scope_form.'.down4'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Heavy Truck3'),
                                        'value' => '',
                                        'name' => 'down5',
                                        'model_name' => $scope_form.'.down5'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Small Bus'),
                                        'value' => '',
                                        'name' => 'down6',
                                        'model_name' => $scope_form.'.down6'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Large Bus'),
                                        'value' => '',
                                        'name' => 'down7',
                                        'model_name' => $scope_form.'.down7'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Tractor'),
                                        'value' => '',
                                        'name' => 'down8',
                                        'model_name' => $scope_form.'.down8'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Motobike including 3 wheeler'),
                                        'value' => '',
                                        'name' => 'down9',
                                        'model_name' => $scope_form.'.down9'
                                        ])

                                        @include('custom.form_number',[
                                        'title' => trans('back_end.Bicycle/Pedicab'),
                                        'value' => '',
                                        'name' => 'down10',
                                        'model_name' => $scope_form.'.down10'
                                        ])

                                        {!! Form::lbText('total_traffic_volume_down', '', trans('back_end.total_traffic_volume_down'), '', '', [
                                            'ng-class' => '{error: errors.total_traffic_volume_down[0]}',
                                            'ng-value' => 'forme.down1 + forme.down6 + forme.down8 + forme.down9 + forme.down10 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total_traffic_volume_down[0]"
                                              ng-bind="errors.total_traffic_volume_down[0].toString()"></span>

                                        {!! Form::lbText('heavy_traffic_down', '', trans('back_end.total_heavy_traffic_down'), '', '', [
                                            'ng-class' => '{error: errors.heavy_traffic_down[0]}',
                                            'ng-value' => 'forme.down2 + forme.down3 + forme.down4 + forme.down5 + forme.down7 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.heavy_traffic_down[0]"
                                              ng-bind="errors.heavy_traffic_down[0].toString()"></span>
                                    </div>
                                    <div class="col-lg-4">
                                        <header>
                                            <h4><b>{!! trans('back_end.total') !!}</b></h4>
                                            <hr/>
                                        </header>
                                        {!! Form::lbText('total1', '', trans('back_end.Car, Jeep'), '', '', [
                                            'ng-class' => '{error: errors.total1[0]}',
                                            'ng-value' => 'forme.up1 + forme.down1 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total1[0]"
                                              ng-bind="errors.total1[0].toString()"></span>

                                        {!! Form::lbText('total2', '', trans('back_end.Light Truck'), '', '', [
                                            'ng-class' => '{error: errors.total2[0]}',
                                            'ng-value' => 'forme.up2 + forme.down2 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total2[0]"
                                              ng-bind="errors.total2[0].toString()"></span>

                                        {!! Form::lbText('total3', '', trans('back_end.Medium Truck'), '', '', [
                                            'ng-class' => '{error: errors.total3[0]}',
                                            'ng-value' => 'forme.up3 + forme.down3 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total3[0]"
                                              ng-bind="errors.total3[0].toString()"></span>

                                        {!! Form::lbText('total4', '', trans('back_end.Heavy Truck'), '', '', [
                                            'ng-class' => '{error: errors.total4[0]}',
                                            'ng-value' => 'forme.up4 + forme.down4 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total4[0]"
                                              ng-bind="errors.total4[0].toString()"></span>

                                        {!! Form::lbText('total5', '', trans('back_end.Heavy Truck3'), '', '', [
                                            'ng-class' => '{error: errors.total5[0]}',
                                            'ng-value' => 'forme.up5 + forme.down5 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total5[0]"
                                              ng-bind="errors.total5[0].toString()"></span>

                                        {!! Form::lbText('total6', '', trans('back_end.Small Bus'), '', '', [
                                            'ng-class' => '{error: errors.total6[0]}',
                                            'ng-value' => 'forme.up6 + forme.down6 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total6[0]"
                                              ng-bind="errors.total6[0].toString()"></span>

                                        {!! Form::lbText('total7', '', trans('back_end.Large Bus'), '', '', [
                                            'ng-class' => '{error: errors.total7[0]}',
                                            'ng-value' => 'forme.up7 + forme.down7 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total7[0]"
                                              ng-bind="errors.total7[0].toString()"></span>

                                        {!! Form::lbText('total8', '', trans('back_end.Tractor'), '', '', [
                                            'ng-class' => '{error: errors.total8[0]}',
                                            'ng-value' => 'forme.up8 + forme.down8 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total8[0]"
                                              ng-bind="errors.total8[0].toString()"></span>

                                        {!! Form::lbText('total9', '', trans('back_end.Motobike including 3 wheeler'), '', '', [
                                            'ng-class' => '{error: errors.total9[0]}',
                                            'ng-value' =>'forme.up9 + forme.down9 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total9[0]"
                                              ng-bind="errors.total9[0].toString()"></span>

                                        {!! Form::lbText('total10', '', trans('back_end.Bicycle/Pedicab'), '', '', [
                                            'ng-class' => '{error: errors.total10[0]}',
                                            'ng-value' => 'forme.up10 + forme.down10 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.total10[0]"
                                              ng-bind="errors.total10[0].toString()"></span>

                                        {!! Form::lbText('traffic_volume_total', '', trans('back_end.Total Traffic Volume'), '', '', [
                                            'ng-class' => '{error: errors.traffic_volume_total[0]}',
                                            'ng-value' => 'forme.up1 + forme.up6 + forme.up8 + forme.up9 + forme.up10 +
                                                           forme.down1 + forme.down6 + forme.down8 + forme.down9 + forme.down10 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.traffic_volume_total[0]"
                                              ng-bind="errors.traffic_volume_total[0].toString()"></span>

                                        {!! Form::lbText('heavy_traffic_total', '', trans('back_end.Total heavy traffic volume'), '', '', [
                                            'ng-class' => '{error: errors.heavy_traffic_total[0]}',
                                            'ng-value' => 'forme.up2 + forme.up3 + forme.up4 + forme.up5 + forme.up7 +
                                                           forme.down2 + forme.down3 + forme.down4 + forme.down5 + forme.down7 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.heavy_traffic_total[0]"
                                              ng-bind="errors.heavy_traffic_total[0].toString()"></span>
                                    </div>
                                    {{--end total traffic--}}
                                    <div class="col-lg-6 pull-right">
                                        {!! Form::lbText('grand_total', '', trans('back_end.grand_total'), '', '', [
                                            'ng-class' => '{error: errors.grand_total[0]}',
                                            'ng-value' => 'forme.up1 + forme.up6 + forme.up8 + forme.up9 + forme.up10
                                             + forme.up2 + forme.up3 + forme.up4 + forme.up5 + forme.up7
                                             + forme.down1 + forme.down6 + forme.down8 + forme.down9 + forme.down10
                                             + forme.down2 + forme.down3 + forme.down4 + forme.down5 + forme.down7 | number : 2',
                                            'readonly'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;"
                                              ng-show="errors.grand_total[0]"
                                              ng-bind="errors.grand_total[0].toString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
                <div class="ui-dialog-buttonset">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>
                    <button type="button" class="btn btn-default" id="close"><i
                                class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- End the Modal -->
@push('script')
<script type="text/javascript">
    $(".open-button").on("click", function() {
        $(this).closest('.collapse-group').find('.collapse').collapse('show');
    });

    $(".close-button").on("click", function() {
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

