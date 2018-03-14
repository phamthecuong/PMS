<!-- The Modal -->
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons modal-content" tabindex="-1" role="dialog" aria-describedby="dialog_simple" aria-labelledby="ui-id-50" id="{{ $id }}">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span id="ui-id-50" class="ui-dialog-title">
                    <div class="widget-header"><h4><i class="fa fa-pencil-square-o"></i> {{  $modal_title }}</h4>
                    </div>
                </span>
        <button class="ui-dialog-titlebar-close" ng-click="cancelRIS()"></button>
    </div>
    <form>
        <div id="dialog_simple" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
           <!-- Content -->
           <section id="widget-grid">
                <div class="row">
                    <article class="col-lg-12">
                        <div class="widget-body">
                            <div class="accordion">
                                <!-- General Information -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.general_error}">{!! trans('back_end.General.Information') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    @include('custom.form_datepicker', [
                                                        'title' => trans('back_end.date_collection'),
                                                        'name' => 'survey_time',
                                                        'hint' => trans('back_end.date_collection_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddRIS.survey_time',
                                                        'disabled' => 'formAddRIS.id',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.terrain_type'),
                                                        'items' => \App\Models\tblTerrainType::allToOption(),
                                                        'name' => 'terrian_type_id',
                                                        'hint' => trans('back_end.terrain_type_hint'),
                                                        'value' => @$data->terrain_type_id,
                                                        'model_name' => 'formAddRIS.terrian_type_id',
                                                        'change' => 'loadDesignSpeedRIS()'
                                                    ])
                                                </div>

                                                <div class="col-lg-2">
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.road_class'),
                                                        'items' => \App\Models\mstRoadClass::allOptionToAjax(),
                                                        'name' => 'road_class_id',
                                                        'hint' => trans('back_end.road_class_hint'),
                                                        'value' => @$data->road_class_id,
                                                        'model_name' => 'formAddRIS.road_class_id',
                                                        'change' => 'loadDesignSpeedRIS()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    {!! Form::lbText('design_speed_ri', 'N/A (km/h)', trans('back_end.design_speed'), '', trans('back_end.design_speed_hint'), [
                                                    'readonly',
                                                    'ng-model' => 'formAddRIS.design_speed',
                                                    ])!!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chainage and Position -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.chainage_error}">{!! trans('back_end.chainage_n_position') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
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
                                                        'model_name' => 'formAddRIS.km_from',
                                                        
                                                        'change' => 'updateActualLengthRIS()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'm_from',
                                                        'title' => trans('back_end.m'),
                                                        'value' => @$data->m_from,
                                                        'hint' => trans('back_end.m_from_hint'),
                                                        'model_name' => 'formAddRIS.m_from',
                                                       
                                                        'change' => 'updateActualLengthRIS()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'km_to',
                                                        'title' => trans('back_end.km'),
                                                        'value' => @$data->km_to,
                                                        'hint' => trans('back_end.km_to_hint'),
                                                        'model_name' => 'formAddRIS.km_to',
                                                      
                                                        'change' => 'updateActualLengthRIS()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'm_to',
                                                        'title' => trans('back_end.m'),
                                                        'value' => @$data->m_to,
                                                        'hint' => trans('back_end.m_to_hint'),
                                                        'model_name' => 'formAddRIS.m_to',
                                                       
                                                        'change' => 'updateActualLengthRIS()'
                                                    ])
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'from_lat',
                                                        'title' => trans('back_end.latitude'),
                                                        'value' => @$data->from_lat,
                                                        'hint' => trans('back_end.latitude_from_hint'),
                                                        'model_name' => 'formAddRIS.from_lat',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'from_lng',
                                                        'title' => trans('back_end.longitude'),
                                                        'value' => @$data->from_lng,
                                                        'hint' => trans('back_end.longitude_from_hint'),
                                                        'model_name' => 'formAddRIS.from_lng',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'to_lat',
                                                        'title' => trans('back_end.latitude'),
                                                        'value' => @$data->to_lat,
                                                        'hint' => trans('back_end.latitude_to_hint'),
                                                        'model_name' => 'formAddRIS.to_lat',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'to_lng',
                                                        'title' => trans('back_end.longitude'),
                                                        'value' => @$data->to_lng,
                                                        'hint' => trans('back_end.longitude_to_hint'),
                                                        'model_name' => 'formAddRIS.to_lng',
                                                    ])
                                                </div>
                                            </div>
                                            <!-- Administration -->
                                            @include('front-end.m13.inputting_system.custom.administration', [
                                                'model' => 'formAddRIS',
                                                'name' => 'RIS'
                                            ])
                                            <!-- End -->
                                            <div class="row">
                                                <div class="col-lg-6 pull-right">
                                                    {!! Form::lbText('length_as_per_chainage', '', trans('back_end.Length as per Chainage'), '', '', ['readonly', 'ng-value' => 'Math.round(formAddRIS.km_to *1000 + formAddRIS.m_to - formAddRIS.km_from*1000 - formAddRIS.m_from + 0)'])!!}
                                                </div>
                                            </div>
                                        </div>     
                                    </div>
                                </div>

                                <!-- back_end.Information of Motorized Lane -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.motorized_lane_error}">{!! trans('back_end.Information of Motorized Lane') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
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
                                                        'value' => @$data->direction,
                                                        'model_name' => 'formAddRIS.direction',
                                                        'change' => 'changeDirectionRIS()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'lane_pos_number',
                                                        'title' => trans('back_end.lane_no'),
                                                        'value' => @$data->lane_pos_number,
                                                        'hint' => trans('back_end.lane_no_hint'),
                                                        'model_name' => 'formAddRIS.lane_pos_number',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'no_lane',
                                                        'title' => trans('back_end.no_of_lane'),
                                                        'value' => @$data->no_lane,
                                                        'hint' => trans('back_end.no_of_lane_hint'),
                                                        'model_name' => 'formAddRIS.no_lane',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'lane_width',
                                                        'title' => trans('back_end.lane_width'),
                                                        'value' => @$data->lane_width,
                                                        'hint' => trans('back_end.lane_width_hint'),
                                                        'model_name' => 'formAddRIS.lane_width',
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Information -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.other_information_error}">{!! trans('back_end.Other Information') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    @include('custom.form_ympicker', [
                                                        'title' => trans('back_end.construct_year'),
                                                        'name' => 'construct_year',
                                                        'hint' => trans('back_end.construct_year_hint'),
                                                        'value' => isset($data->construct_year) ? (substr($data->construct_year, 0, 4) . '/' . substr($data->construct_year, 4, 2)) : '',
                                                        'model_name' => 'formAddRIS.construct_year',
                                                    ])
                                                </div>
                                                <div class="col-lg-6">
                                                    @include('custom.form_ympicker', [
                                                        'title' => trans('back_end.service_start_year'),
                                                        'name' => 'service_start_year',
                                                        'hint' => trans('back_end.service_start_year_hint'),
                                                        'value' => isset($data->service_start_year) ? (substr($data->service_start_year, 0, 4) . '/' . substr($data->service_start_year, 4, 2)) : '',
                                                        'model_name' => 'formAddRIS.service_start_year',
                                                    ])
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    @include('custom.form_number', [
                                                        'name' => 'temperature',
                                                        'title' => trans('back_end.Temperature'),
                                                        'value' => @$data->temperature,
                                                        'hint' => trans('back_end.temperature_hint'),
                                                        'model_name' => 'formAddRIS.temperature',
                                                    ])
                                                </div>
                                                <div class="col-lg-4">
                                                    @include('custom.form_number', [
                                                        'name' => 'annual_precipitation',
                                                        'title' => trans('back_end.annual_precipitation'),
                                                        'value' => @$data->annual_precipitation,
                                                        'hint' => trans('back_end.annual_precipitation_hint'),
                                                        'model_name' => 'formAddRIS.annual_precipitation',
                                                    ])
                                                </div>
                                                <div class="col-lg-4">
                                                    @include('custom.form_number', [
                                                        'name' => 'actual_length',
                                                        'title' => trans('back_end.actual_length'),
                                                        'value' => @$data->actual_length,
                                                        'hint' => trans('back_end.actual_length_hint'),
                                                        'model_name' => 'formAddRIS.actual_length',
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
                                                        'attribute' => ['rows' => 5],
                                                        'model_name' => 'formAddRIS.remark',
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Material Detai -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.material_layer_error}">{!! trans('back_end.material_layer') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.surface'),
                                                        'items' => \App\Models\mstSurface::allToOption(),
                                                        'name' => 'surface_id',
                                                        'hint' => trans('back_end.surface_hint'),
                                                        'value' => @$data->r_category_id,
                                                        'model_name' => 'formAddRIS.surface_id',
                                                        'disable' => true,
                                                    ])
                                                </div>
                                            </div>
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
                                                                        $name_material_type = "data[{$layer->id}]['material_type']";
                                                                        $name_thickness = "data[{$layer->id}]['thickness']";
                                                                        $name_desc = "data[{$layer->id}]['desc']";
                                                                    ?>
                                                                    @include('custom.form_select', [
                                                                        'items' => $material_types,
                                                                        'name' => "data[$layer->id][material_type]",
                                                                        'value' => '',
                                                                        'model_name' => "formAddRIS.$name_material_type",
                                                                        'change_select' => 'loadSurfaceRIS',
                                                                    ])
                                                                </td>
                                                                <td>
                                                                    @include('custom.form_number', [
                                                                        'name' => "data[$layer->id][thickness]",
                                                                        'value' => '',
                                                                        'model_name' => "formAddRIS.$name_thickness",
                                                                    ])
                                                                </td>
                                                                <td colspan="4">
                                                                    {!! 
                                                                        Form::lbText("data[$layer->id][desc]", null, null,'','', ['ng-model' => "formAddRIS.$name_desc"])
                                                                    !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
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
                <button type="button" class="btn btn-danger" ng-show="formAddRIS.id" click-btn-del="deleteRIS()"><i class="fa fa-trash-o"></i>&nbsp;{{ trans('back_end.delete') }}</button>

                <!-- Edit -->
                <button type="button" class="btn btn-primary" ng-show="formAddRIS.id" click-btn="editRIS()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ trans('back_end.edit') }}</button>

                <!-- Add new -->
                <button type="submit" class="btn btn-primary" ng-show="!formAddRIS.id" click-btn="addRIS()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>

                <!-- Cancel -->
                <button type="button" class="btn btn-default" id="close" ng-click="cancelRIS()"><i class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
            </div>
        </div>
    </form>
</div>
<!-- End the Modal -->
@push('script')
<script type="text/javascript">
    function showRIS() {
        $('#{{ $id }}').show();
        $('.ui-widget-overlay').show();
    }

    function hideRIS() {
        $('#{{ $id }}').hide();
        $('.ui-widget-overlay').hide();
    }
</script>
@endpush