<!-- The Modal -->
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons ui-draggable modal-content" tabindex="-1" role="dialog" aria-describedby="dialog_simple" aria-labelledby="ui-id-50" id="{{ $id }}">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span id="ui-id-50" class="ui-dialog-title">
                    <div class="widget-header"><h4><i class="fa fa-pencil-square-o"></i> {{  $modal_title }}</h4>
                    </div>
                </span>
        <button class="ui-dialog-titlebar-close" ng-click="cancelTV()"></button>
    </div>
    <form>
        <div id="dialog_simple" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
           <!-- Content -->
           <section id="widget-grid">
                <div class="row">
                    <article class="col-lg-12">
                        <div class="widget-body">
                            <div class="accordion">

                                <!-- Information Traffic -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.information_error}">{!! trans('back_end.Information Traffic') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    @include('custom.form_ympicker', [
                                                        'title' => trans('back_end.date_collection'),
                                                        'name' => 'survey_time',
                                                        'hint' => trans('back_end.date_collection_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.survey_time'
                                                    ])
                                                </div>
                                                <div class="col-lg-4">
                                                    @include('custom.form_text', [
                                                        'title' => trans('back_end.Traffic name_en'),
                                                        'name' => 'name_en',
                                                        'hint' => trans('back_end.traffic_nameEn_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.name_en'
                                                    ])
                                                </div>
                                                <div class="col-lg-4">
                                                    @include('custom.form_text', [
                                                        'title' => trans('back_end.Traffic name_vn'),
                                                        'name' => 'name_vn',
                                                        'hint' => trans('back_end.traffic_nameVn_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.name_vn'
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    @include('custom.form_number', [
                                                        'title' => trans('back_end.km_station'),
                                                        'name' => 'km_station',
                                                        'hint' => trans('back_end.km_station_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.km_station'
                                                    ])

                                                    @include('custom.form_number', [
                                                        'title' => trans('back_end.m_station'),
                                                        'name' => 'm_station',
                                                        'hint' => trans('back_end.m_station_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.m_station'
                                                    ])
                                                </div>
                                                <div class="col-lg-6">
                                                    @include('custom.form_text', [
                                                        'title' => trans('back_end.general.latitude'),
                                                        'name' => 'lat_station',
                                                        'hint' => trans('back_end.latitude_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.lat_station'
                                                    ])
                                                    @include('custom.form_text', [
                                                        'title' => trans('back_end.general.longitude'),
                                                        'name' => 'lng_station',
                                                        'hint' => trans('back_end.longitude_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddTV.lng_station'
                                                    ])
                                                </div>
                                            </div>
                                            <!-- Administration -->
                                            @include('front-end.m13.inputting_system.custom.administration_tv', [
                                                'model' => 'formAddTV',
                                                'name' => 'TV'
                                            ])
                                            <!-- End -->
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('custom.textarea', [
                                                       'title' => trans('back_end.remark'),
                                                       'hint' => trans('back_end.remark_hint'),
                                                       'value' => @$data->remark,
                                                       'name' => 'remark',
                                                       'attribute' => ['rows' => 5],
                                                       'model_name' => 'formAddTV.remark'
                                                   ])
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Data of Maintenance History -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.input_data_error}">{!! trans('back_end.data_traffic_volumne_title') !!}</h1>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <header>
                                                <h4><b>{!! trans('back_end.up') !!}</b></h4>
                                                <hr/>
                                            </header>
                                            @include('custom.form_number', [
                                                'name' => 'car_jeep_up',
                                                'title' => trans('back_end.Car, Jeep'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.car_jeep_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'light_truck_up',
                                                'title' => trans('back_end.Light Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.light_truck_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'medium_truck_up',
                                                'title' => trans('back_end.Medium Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.medium_truck_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'heavy_truck_up',
                                                'title' => trans('back_end.Heavy Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.heavy_truck_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'heavy_truck3_up',
                                                'title' => trans('back_end.Heavy Truck3'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.heavy_truck3_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'small_bus_up',
                                                'title' => trans('back_end.Small Bus'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.small_bus_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'large_bus_up',
                                                'title' => trans('back_end.Large Bus'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.large_bus_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'tractor_up',
                                                'title' => trans('back_end.Tractor'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.tractor_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'motobike_including_3_wheeler_up',
                                                'title' => trans('back_end.Motobike including 3 wheeler'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.motobike_including_3_wheeler_up',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'bicycle_pedicab_up',
                                                'title' => trans('back_end.Bicycle/Pedicab'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.bicycle_pedicab_up',
                                            ])
                                            {!! Form::lbText('total_traffic_volume_up', @$data->total_traffic_volume_up, trans('back_end.Total Traffic Volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.car_jeep_up + formAddTV.small_bus_up + formAddTV.tractor_up + formAddTV.motobike_including_3_wheeler_up + formAddTV.bicycle_pedicab_up + 0))/1000']) !!}
                                            {!! Form::lbText('heavy_traffic_up', @$data->heavy_traffic_up, trans('back_end.Total heavy traffic volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.light_truck_up + formAddTV.medium_truck_up + formAddTV.heavy_truck_up + formAddTV.heavy_truck3_up + formAddTV.large_bus_up + 0))/1000']) !!}
                                        </div>
                                        <div class="col-lg-4">
                                            <header>
                                                <h4><b>{!! trans('back_end.down') !!}</b></h4>
                                                <hr/>
                                            </header>
                                            @include('custom.form_number', [
                                                'name' => 'car_jeep_down',
                                                'title' => trans('back_end.Car, Jeep'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.car_jeep_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'light_truck_down',
                                                'title' => trans('back_end.Light Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.light_truck_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'medium_truck_down',
                                                'title' => trans('back_end.Medium Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.medium_truck_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'heavy_truck_down',
                                                'title' => trans('back_end.Heavy Truck'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.heavy_truck_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'heavy_truck3_down',
                                                'title' => trans('back_end.Heavy Truck3'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.heavy_truck3_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'small_bus_down',
                                                'title' => trans('back_end.Small Bus'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.small_bus_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'large_bus_down',
                                                'title' => trans('back_end.Large Bus'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.large_bus_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'tractor_down',
                                                'title' => trans('back_end.Tractor'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.tractor_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'motobike_including_3_wheeler_down',
                                                'title' => trans('back_end.Motobike including 3 wheeler'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.motobike_including_3_wheeler_down',
                                            ])
                                            @include('custom.form_number', [
                                                'name' => 'bicycle_pedicab_down',
                                                'title' => trans('back_end.Bicycle/Pedicab'),
                                                'value' => @$data->km_from,
                                                'model_name' => 'formAddTV.bicycle_pedicab_down',
                                            ])
                                            {!! Form::lbText('total_traffic_volume_down', @$data->total_traffic_volume_down, trans('back_end.Total Traffic Volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.car_jeep_down + formAddTV.small_bus_down + formAddTV.tractor_down + formAddTV.motobike_including_3_wheeler_down + formAddTV.bicycle_pedicab_down + 0))/1000']) !!}
                                            {!! Form::lbText('heavy_traffic_down', @$data->heavy_traffic_down, trans('back_end.Total heavy traffic volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.light_truck_down + formAddTV.medium_truck_down + formAddTV.heavy_truck_down + formAddTV.heavy_truck3_down + formAddTV.large_bus_down + 0))/1000']) !!}
                                        </div>
                                        <div class="col-lg-4">
                                            <header>
                                                <h4><b>{!! trans('back_end.total') !!}</b></h4>
                                                <hr/>
                                            </header>
                                            {!! Form::lbText('car_jeep_tt', (@$up_info[1] + @$down_info[1]), trans('back_end.Car, Jeep'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.car_jeep_up + formAddTV.car_jeep_down + 0))/1000']) !!}
                                            {!! Form::lbText('light_truck_tt', (@$up_info[2] + @$down_info[2]), trans('back_end.Light Truck'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.light_truck_up + formAddTV.light_truck_down + 0))/1000']) !!}
                                            {!! Form::lbText('medium_truck_tt', (@$up_info[3] + @$down_info[3]), trans('back_end.Medium Truck'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.medium_truck_up + formAddTV.medium_truck_down + 0))/1000']) !!}
                                            {!! Form::lbText('heavy_truck_tt', (@$up_info[4] + @$down_info[4]), trans('back_end.Heavy Truck'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.heavy_truck_up + formAddTV.heavy_truck_down + 0))/1000']) !!}
                                            {!! Form::lbText('heavy_truck3_tt', (@$up_info[5] + @$down_info[5]), trans('back_end.Heavy Truck3'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.heavy_truck3_up + formAddTV.heavy_truck3_down + 0))/1000']) !!}
                                            {!! Form::lbText('small_bus_tt', (@$up_info[6] + @$down_info[6]), trans('back_end.Small Bus'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.small_bus_up + formAddTV.small_bus_down + 0))/1000']) !!}
                                            {!! Form::lbText('large_bus_tt', (@$up_info[7] + @$down_info[7]), trans('back_end.Large Bus'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.large_bus_up + formAddTV.large_bus_down + 0))/1000']) !!}
                                            {!! Form::lbText('tractor_tt', (@$up_info[8] + @$down_info[8]), trans('back_end.Tractor'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.tractor_up + formAddTV.tractor_down + 0))/1000']) !!}
                                            {!! Form::lbText('motobike_including_3_wheeler_tt', (@$up_info[9] + @$down_info[9]), trans('back_end.Motobike including 3 wheeler'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.motobike_including_3_wheeler_up + formAddTV.motobike_including_3_wheeler_down + 0))/1000']) !!}
                                            {!! Form::lbText('bicycle_pedicab_tt', (@$up_info[10] + @$down_info[10]), trans('back_end.Bicycle/Pedicab'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.bicycle_pedicab_up + formAddTV.bicycle_pedicab_down + 0))/1000']) !!}
                                            {!! Form::lbText('traffic_volume_tt', (@$data->total_traffic_volume_up + @$data->total_traffic_volume_down), trans('back_end.Total Traffic Volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.car_jeep_up + formAddTV.small_bus_up + formAddTV.tractor_up + formAddTV.motobike_including_3_wheeler_up + formAddTV.bicycle_pedicab_up + formAddTV.car_jeep_down + formAddTV.small_bus_down + formAddTV.tractor_down + formAddTV.motobike_including_3_wheeler_down + formAddTV.bicycle_pedicab_down + 0))/1000']) !!}
                                            {!! Form::lbText('heavy_traffic_volume_tt', (@$data->heavy_traffic_up + @$data->heavy_traffic_down), trans('back_end.Total heavy traffic volume'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.light_truck_up + formAddTV.medium_truck_up + formAddTV.heavy_truck_up + formAddTV.heavy_truck3_up + formAddTV.large_bus_up + formAddTV.light_truck_down + formAddTV.medium_truck_down + formAddTV.heavy_truck_down + formAddTV.heavy_truck3_down + formAddTV.large_bus_down + 0))/1000']) !!}
                                        </div>
                                        <div class="col-lg-6 pull-right">
                                            {!! Form::lbText('total', '', trans('back_end.total'), '', '', ['readonly', 'ng-value' => 'Math.round(1000*(formAddTV.car_jeep_up + formAddTV.light_truck_up + formAddTV.medium_truck_up + formAddTV.heavy_truck_up + formAddTV.heavy_truck3_up + formAddTV.small_bus_up + formAddTV.large_bus_up + formAddTV.tractor_up + formAddTV.motobike_including_3_wheeler_up + formAddTV.bicycle_pedicab_up + formAddTV.car_jeep_down + formAddTV.light_truck_down + formAddTV.medium_truck_down + formAddTV.heavy_truck_down + formAddTV.heavy_truck3_down + formAddTV.small_bus_down + formAddTV.large_bus_down + formAddTV.tractor_down + formAddTV.motobike_including_3_wheeler_down + formAddTV.bicycle_pedicab_down + 0))/1000']) !!}
                                        </div>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                    </article> 
                </div>
            </section>
        </div>
        <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
            <div class="ui-dialog-buttonset">
                <!--Delete -->
                <button type="button" class="btn btn-danger" ng-show="formAddTV.id" click-btn-del="deleteTV()"><i class="fa fa-trash-o"></i>&nbsp;{{ trans('back_end.delete') }}</button>

                <!-- Edit -->
                <button type="button" class="btn btn-primary" ng-show="formAddTV.id" click-btn="editTV()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ trans('inputting.edit') }}</button>

                <!-- Add new -->
                <button class="btn btn-primary" ng-show="!formAddTV.id" click-btn="addTV()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>

                <!-- Cancel -->
                <button type="button" class="btn btn-default" id="close" ng-click="cancelTV()"><i class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
            </div>
        </div>
    </form>
</div>
<!-- End the Modal -->
@push('script')
<script type="text/javascript">
    function showTV() {
        $('#{{ $id }}').show();
        $('.ui-widget-overlay').show();
    }

    function hideTV() {
        $('#{{ $id }}').hide();
        $('.ui-widget-overlay').hide();
    }
</script>
@endpush