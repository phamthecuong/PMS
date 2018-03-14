<!-- The Modal -->
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons ui-draggable modal-content" tabindex="-1" role="dialog" aria-describedby="dialog_simple" aria-labelledby="ui-id-50" id="{{ $id }}">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span id="ui-id-50" class="ui-dialog-title">
                    <div class="widget-header"><h4><i class="fa fa-pencil-square-o"></i> {{  $modal_title }}</h4>
                    </div>
                </span>
        <button class="ui-dialog-titlebar-close" ng-click="cancelMH()"></button>
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
                                                {{-- <div class="col-lg-4">
                                                    @include('custom.form_datepicker', [
                                                        'title' => trans('back_end.date_collection'),
                                                        'name' => 'survey_time',
                                                        'hint' => trans('back_end.date_collection_hint'),
                                                        'value' => @$data->survey_time,
                                                        'model_name' => 'formAddMH.survey_time'
                                                    ])
                                                </div> --}}
                                                <div class="col-lg-6">
                                                    @include('custom.form_datepicker', [
                                                        'title' => trans('back_end.completion_date'),
                                                        'name' => 'completion_date',
                                                        'hint' => trans('back_end.completion_date_hint'),
                                                        'value' => @$data->completion_date,
                                                        'model_name' => 'formAddMH.completion_date'
                                                    ])
                                                </div>
                                                <div class="col-lg-6">
                                                    @include('custom.form_number', [
                                                        'name' => 'repair_duration',
                                                        'title' => trans('back_end.repair_duration'),
                                                        'value' => @$data->repair_duration,
                                                        'hint' => trans('back_end.repair_duration_hint'),
                                                        'model_name' => 'formAddMH.repair_duration'
                                                    ])
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
                                                        'model_name' => 'formAddMH.km_from',
                                                        'change' => 'updateActualLengthMH()',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'm_from',
                                                        'title' => trans('back_end.m'),
                                                        'value' => @$data->m_from,
                                                        'hint' => trans('back_end.m_from_hint'),
                                                        'model_name' => 'formAddMH.m_from',
                                                        'change' => 'updateActualLengthMH()',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'km_to',
                                                        'title' => trans('back_end.km'),
                                                        'value' => @$data->km_to,
                                                        'hint' => trans('back_end.km_to_hint'),
                                                        'model_name' => 'formAddMH.km_to',
                                                        'change' => 'updateActualLengthMH()',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'm_to',
                                                        'title' => trans('back_end.m'),
                                                        'value' => @$data->m_to,
                                                        'hint' => trans('back_end.m_to_hint'),
                                                        'model_name' => 'formAddMH.m_to',
                                                        'change' => 'updateActualLengthMH()',
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
                                                        'model_name' => 'formAddMH.from_lat',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'from_lng',
                                                        'title' => trans('back_end.longitude'),
                                                        'value' => @$data->from_lng,
                                                        'hint' => trans('back_end.longitude_from_hint'),
                                                        'model_name' => 'formAddMH.from_lng',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'to_lat',
                                                        'title' => trans('back_end.latitude'),
                                                        'value' => @$data->to_lat,
                                                        'hint' => trans('back_end.latitude_to_hint'),
                                                        'model_name' => 'formAddMH.to_lat',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_text', [
                                                        'name' => 'to_lng',
                                                        'title' => trans('back_end.longitude'),
                                                        'value' => @$data->to_lng,
                                                        'hint' => trans('back_end.longitude_to_hint'),
                                                        'model_name' => 'formAddMH.to_lng',
                                                    ])
                                                </div>
                                            </div>
                                            <!-- Administration -->
                                            @include('front-end.m13.inputting_system.custom.administration', [
                                                'model' => 'formAddMH',
                                                'name' => 'MH'
                                            ])
                                            <!-- End -->
                                            <div class="row">
                                                <div class="col-lg-6 pull-right">
                                                    {!! Form::lbText('length_as_per_chainage', '', trans('back_end.Length as per Chainage'), '', '', ['readonly', 'ng-value' => 'Math.round(formAddMH.km_to *1000 + formAddMH.m_to - formAddMH.km_from*1000 - formAddMH.m_from + 0)'])!!}
                                                </div>
                                            </div>
                                        </div>     
                                    </div>
                                </div>

                                <!-- Information of repair section -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.repair_section_error}">{!! trans('back_end.information_of_repair_section') !!}</h1>
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
                                                        'model_name' => 'formAddMH.direction',
                                                        'change' => 'changeDirectionMH()'
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'lane_pos_number',
                                                        'title' => trans('back_end.lane_no'),
                                                        'value' => @$data->lane_pos_number,
                                                        'hint' => trans('back_end.lane_no_hint'),
                                                        'model_name' => 'formAddMH.lane_pos_number',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'actual_length',
                                                        'title' => trans('back_end.actual_length'),
                                                        'value' => @$data->actual_length,
                                                        'hint' => trans('back_end.actual_length_hint'),
                                                        'model_name' => 'formAddMH.actual_length',
                                                    ])
                                                </div>
                                                <div class="col-lg-3">
                                                    @include('custom.form_number', [
                                                        'name' => 'total_width_repair_lane',
                                                        'title' => trans('back_end.repair_width'),
                                                        'value' => @$data->total_width_repair_lane,
                                                        'hint' => trans('back_end.repair_width_hint'),
                                                        'model_name' => 'formAddMH.total_width_repair_lane',
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance History Position -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.position_error}">{!! trans('back_end.mh_position') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <!--{!!
                                                        Form::lbRatio('direction_running', @$data->direction_running, [
                                                            ['name' => trans('back_end.left'), 'value' => '0'],
                                                            ['name' => trans('back_end.right'), 'value' => '1']
                                                        ], trans('back_end.Maintenance History Position'), ['ng-model' => 'formAddMH.repair_width'])
                                                    !!}-->
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.direction_running'),
                                                        'items' => [
                                                            ['name' => trans('back_end.left'), 'value' => '0'],
                                                            ['name' => trans('back_end.right'), 'value' => '1']
                                                        ],
                                                        'name' => 'direction_running',
                                                        'hint' => trans('back_end.direction_running_hint'),
                                                        'value' => @$data->r_category_id,
                                                        'model_name' => 'formAddMH.direction_running',
                                                    ])
                                                </div>
                                                <div class="col-lg-6">
                                                    @include('custom.form_number', [
                                                        'name' => 'distance',
                                                        'title' => trans('back_end.distance_to_center'),
                                                        'value' => @$data->distance ,
                                                        'hint' => trans('back_end.distance_to_center_hint'),
                                                        'model_name' => 'formAddMH.distance',
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Repair method info -->
                                <div>
                                    <h1 ng-class="{'red-panel': errors.repair_method_error}">{!! trans('back_end.repair_method_info') !!}</h1>
                                    <div class="row">
                                        <div class="widget-body">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.repair_method'),
                                                        'items' => \App\Models\mstRepairMethod::allToOptionTwo(),
                                                        'name' => 'repair_method_id',
                                                        'hint' => trans('back_end.repair_method'),
                                                        'value' => @$data->repair_method_id,
                                                        'model_name' => 'formAddMH.repair_method_id',
                                                        'change_select' => 'loadClassification',
                                                    ])
                                                </div>
                                                <div class="col-lg-4">
                                                    @include('custom.form_select', [
                                                        'title' => trans('back_end.repair_classification'),
                                                        'items' => \App\Models\tblRClassification::allToOption(),
                                                        'name' => 'r_classification_id',
                                                        'hint' => trans('back_end.repair_classification_hint'),
                                                        'value' => @$data->r_classification_id,
                                                        'model_name' => 'formAddMH.r_classification_id',
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
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('custom.textarea', [
                                                        'title' => trans('back_end.remark'),
                                                        'hint' => trans('back_end.remark_hint'),
                                                        'value' => @$data->remark,
                                                        'name' => 'remark',
                                                        'attribute' => ['rows' => 5],
                                                        'model_name' => 'formAddMH.remark',
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
                                                        'model_name' => 'formAddMH.surface_id',
                                                        'disable' => true,
                                                    ])
                                                </div>
                                                <div class="col-lg-6">
                                                    {!! Form::lbSelect("r_category_id", '', [], trans("back_end.repair_category"),[
                                                            'ng-options' => 'item.id as item.name for item in categoryMH',
                                                            'ng-model' => 'formAddMH.r_category_id',
                                                            'ng-class' => "{error: errors['r_category_id'][0]}"
                                                    ]) !!}
                                                    <span class="help-block" style="color: #b94a48;" ng-show="errors['r_category_id'][0]" ng-bind="errors['r_category_id'][0].toString()"></span>
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
                                                            <tr
                                                                @if($key == 0)
                                                                    {!!'style="background: rgba(141,180,226,0.5)"'!!} 
                                                                @elseif($key == 1 || $key == 2)
                                                                    {!!'style="background: rgba(197,217,241,0.5)"'!!}
                                                                @elseif($key == 3 || $key == 4)
                                                                    {!!'style="background: rgba(235,241,222,0.5)"'!!}
                                                                @elseif($key == 5 || $key == 6)
                                                                    {!!'style="background: rgba(242,220,219,0.5)"'!!}
                                                                @elseif($key == 7 || $key == 8)
                                                                    {!!'style="background: rgba(253,233,217,0.5)"'!!}
                                                                @elseif($key == 9 || $key == 10)
                                                                    {!!'style="background: rgba(221,217,196,0.5)"'!!}
                                                                @else
                                                                    {!!'style="background: rgba(204,192,218,0.5)"'!!}
                                                                @endif
                                                            >
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
                                                                    @if ($layer->id == 6)
                                                                        @include('custom.form_select', [
                                                                            'items' => $material_types,
                                                                            'name' => "data[$layer->id][material_type]",
                                                                            'value' => '',
                                                                            'model_name' => "formAddMH.data[".$layer->id."]['material_type']",
                                                                            'change_select' => 'loadSurfaceMH',
                                                                        ])
                                                                    @else
                                                                        @include('custom.form_select', [
                                                                            'items' => $material_types,
                                                                            'name' => "data[$layer->id][material_type]",
                                                                            'value' => '',
                                                                            'model_name' => "formAddMH.data[".$layer->id."]['material_type']",
                                                                        ])
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @include('custom.form_number', [
                                                                        'name' => "data[$layer->id][thickness]",
                                                                        'value' => '',
                                                                        'model_name' => "formAddMH.data[".$layer->id."]['thickness']",
                                                                    ])
                                                                </td>
                                                                <td colspan="4">
                                                                    {!! 
                                                                        Form::lbText("data[$layer->id][desc]", null, null,'','', ['ng-model' => "formAddMH.data[".$layer->id."]['desc']"])
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
                <button type="button" class="btn btn-danger" ng-show="formAddMH.id" click-btn-del="deleteMH()"><i class="fa fa-trash-o"></i>&nbsp;{{ trans('back_end.delete') }}</button>

                <!-- Edit -->
                <button type="button" class="btn btn-primary" ng-show="formAddMH.id" click-btn="editMH()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ trans('inputting.edit') }}</button>

                <!-- Add new -->
                <button class="btn btn-primary" ng-show="!formAddMH.id" click-btn="addMH()"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>

                <!-- Cancel -->
                <button type="button" class="btn btn-default" id="close" ng-click="cancelMH()"><i class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
            </div>
        </div>
    </form>
</div>
<!-- End the Modal -->
@push('script')
<script type="text/javascript">
    function showMH() {
        $('#{{ $id }}').show();
        $('.ui-widget-overlay').show();
    }

    function hideMH() {
        $('#{{ $id }}').hide();
        $('.ui-widget-overlay').hide();
    }
</script>
@endpush