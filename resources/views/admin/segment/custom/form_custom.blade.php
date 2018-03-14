<div class="row">
    <div class="col-lg-6">
        {!! Form::lbSelect2("province_from_".$name,'', App\Models\tblCity::allToOption(), trans('back_end.province'),[
            'ng-model' => 'province_from_'.$name,
            'ng-init' => "province_from_".$name."=''",
            'change-select' => 'loadDistrictFrom'.$name
        ]) !!} 

    </div>
    <div class="col-lg-6">
        {!! Form::lbSelect2("district_from_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans("back_end.district"),[
            'ng-options' => 'item.id as item.name for item in districtFrom'.$name,
            'ng-model' => 'district_from_'.$name,
            'change-select' => 'loadWardFrom'.$name
        ]) !!}
       
    </div>
    <div class="col-lg-6">
        {!! Form::lbSelect2("province_to_".$name,'', App\Models\tblCity::allToOption(), trans('back_end.province'),[
            'ng-model' => 'province_to_'.$name,
            'ng-init' => "province_to".$name."=''",
            'change-select' => 'loadDistrictTo'.$name
        ]) !!} 
    </div>
    <div class="col-lg-6">
        {!! Form::lbSelect2("district_to_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans("back_end.district"),[
            'ng-options' => 'item.id as item.name for item in districtTo'.$name,
            'ng-model' => 'district_to_'.$name,
            'change-select' => 'loadWardTo'.$name
        ]) !!}
    </div>
</div>
<div class="row">
    <div class=" col-lg-6">
        {!! Form::lbSelect2("ward_from_id_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans('back_end.ward'),[
            'ng-options' => 'item.id as item.name for item in wardFrom'.$name,
            'ng-model' => $model.'.ward_from_id'
        ]) !!}
        {{-- {!! Form::lbSelect2('ward_from_id', '', \App\Models\tblWard::allToOption(), trans('back_end.ward_from'), ['ng-model' => 'formAddRI.ward_from_id'])!!} --}}
    </div>
    <div class="col-lg-6">
        {!! Form::lbSelect2("ward_to_id_".$name, '', [['name'=> trans('back_end.please_choose'), 'value' => '']], trans('back_end.ward'),[
            'ng-options' => 'item.id as item.name for item in wardTo'.$name,
            'ng-model' => $model.'.ward_to_id'
        ]) !!}
    </div>
</div>