<div class="row">
    <div class="col-lg-4">
        {!! Form::lbSelect2("province_".$name,'', App\Models\tblCity::allToOption(), trans('back_end.province'),[
            'ng-model' => 'province_'.$name,
            'ng-init' => "province_".$name."=''",
            'change-select' => 'loadDistrict'.$name
        ]) !!}
    </div>
    <div class="col-lg-4">

        {!! Form::lbSelect2("district_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans("back_end.district"),[
            'ng-options' => 'item.id as item.name for item in district'.$name,
            'ng-model' => 'district_'.$name,
            'change-select' => 'loadWard'.$name
        ]) !!}
    </div>
    <div class="col-lg-4">
        {!! Form::lbSelect2("ward_id_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans('back_end.ward'),[
            'ng-options' => 'item.id as item.name for item in ward'.$name,
            'ng-model' => $model.'.ward_id'
        ]) !!}
    </div>
</div>