@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('slide_menu_condition_rank')
    active
@endsection



@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.side_menu_condition_rank')}}
        </li>
    </ol>
@endsection

@section('content')
{!!Form::open(array('method' => 'POST', 'onsubmit' =>'return checkFrom()' ,'id' => 'save' , 'route' => array('rank.update')))!!}
<div class="theme-showcase">
     <!-- box -->
    <div class="box">
   
        <div class="box-header">
            <h3 class="box-title">{{trans('back_end.manager_condition_rank')}}</h3>
        </div>
        <!-- body -->
        <section id="widget-grid">
            <div class="row">
                <article class="col-lg-12 col-md-12" id="demo">
                    @box_open(trans('back_end.condition_rank'))
                    <!-- table -->
                    <div  id="container" >
                    
                        <div class="row">
                            <div class="col-xs-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr role='row'>
                                            <th rowspan="1" colspan="3" style="text-align: center">{{trans('back_end.crack')}}</th>
                                        </tr>
                                        <tr role='row'>
                                            <!-- <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th> -->
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.from')}}</th>
                                            <th rowspan="1" colspan="1" style="width: 10%;text-align: center" >{{trans('back_end.type')}}</th>
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.to')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @for ($i = 0; $i < count($crack); $i++)
                                    <input type="hidden" name="total_crack" value="{{count($crack)}}">
                                    <input type="hidden" name="id_crack_{{$i}}" value="{{ $crack[$i]['id'] }}">
                                        <tr >
                                            <td >
                                                <input id="crack_a{{$i}}"  type="text" name="crack_from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="{{intval($crack[$i]['from'])}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace (/(\.\d\d)\d+|([\d.]*)[^\d.]/, '$1$2')" @if($i!=0){{'disabled'}}@endif >
                                                <input id="crack_a_{{$i}}" type="hidden" name="crack_from{{$i}}" value="{{$crack[$i]['from']}}">

                                            </td>
                                            <td style="width: 10%;text-align: center">
                                                <  {{"C"}} <=
                                            </td>
                                            <td>
                                                <input id="crack_b{{$i}}" name="crack_to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="@if($crack[$i]['to'] == null){{$crack[$i]['to']}}@else{{intval($crack[$i]['to'])}}@endif" onchange="check({{$i}}, {{count($crack)}}) " onkeypress="return validate(event , {{$i}} ,{{count($crack)}})" @if($crack[$i]['to'] == null) {{'disabled'}} @endif> 
                                            </td>
                                        </tr>
                                        <!-- end line  -->
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr role='row'>
                                            <th rowspan="1" colspan="3" style="text-align: center">{{trans('back_end.rutting_depth')}}</th>
                                        </tr>
                                        <tr role='row'>
                                            <!-- <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th> -->
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.from')}}</th>
                                            <th rowspan="1" colspan="1" style="width: 10%;text-align: center" >{{trans('back_end.type')}}</th>
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.to')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @for ($i = 0; $i < count($rut); $i++)
                                    <input type="hidden" name="total_rut" value="{{count($rut)}}">
                                    <input type="hidden" name="id_rut_{{$i}}" value="{{ $rut[$i]['id'] }}">
                                        <tr >
                                            <td >
                                                 <input id="rut_a{{$i}}"  type="text" name="rut_from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="{{intval($rut[$i]['from'])}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace (/(\.\d\d)\d+|([\d.]*)[^\d.]/, '$1$2')" @if($i!=0){{'disabled'}}@endif >
                                                 <input id="rut_a_{{$i}}" type="hidden" name="rut_from{{$i}}" value="{{intval($rut[$i]['from'])}}">

                                            </td>
                                            <td style="width: 10%;text-align: center">
                                                <  {{"R"}} <=
                                            </td>
                                            <td>
                                                <input id="rut_b{{$i}}" name="rut_to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="@if($rut[$i]['to'] == null) {{$rut[$i]['to']}}@else{{intval($rut[$i]['to'])}}@endif" onchange="check_rut({{$i}}, {{count($rut)}}) " onkeypress="return validate(event , {{$i}} ,{{count($rut)}})" @if($rut[$i]['to'] == null) {{'disabled'}} @endif> 
                                            </td>
                                        </tr>
                                        <!-- end line  -->
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr role='row'>
                                            <th rowspan="1" colspan="3" style="text-align: center">{{trans('back_end.IRI')}}</th>
                                        </tr>
                                        <tr role='row'>
                                            <!-- <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th> -->
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.from')}}</th>
                                            <th rowspan="1" colspan="1" style="width: 10%;text-align: center" >{{trans('back_end.type')}}</th>
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.to')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @for ($i = 0; $i < count($iri); $i++)
                                    <input type="hidden" name="total_iri" value="{{count($iri)}}">
                                    <input type="hidden" name="id_iri_{{$i}}" value="{{ $iri[$i]['id'] }}">
                                        <tr >
                                            <td >
                                                 <input id="iri_a{{$i}}"  type="text" name="iri_from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="{{intval($iri[$i]['from'])}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace (/(\.\d\d)\d+|([\d.]*)[^\d.]/, '$1$2')" @if($i!=0){{'disabled'}}@endif >
                                                 <input id="iri_a_{{$i}}" type="hidden" name="iri_from{{$i}}" value="{{intval($iri[$i]['from'])}}">

                                            </td>
                                            <td style="width: 10%;text-align: center">
                                                <  {{"I"}} <=
                                            </td>
                                            <td>
                                                <input id="iri_b{{$i}}" name="iri_to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="@if($iri[$i]['to'] == null) {{$iri[$i]['to']}} @else {{intval($iri[$i]['to'])}}@endif" onchange="check_iri({{$i}}, {{count($iri)}} ) " onkeypress="return validate(event , {{$i}} ,{{count($iri)}})" @if($iri[$i]['to'] == null) {{'disabled'}} @endif> 
                                            </td>
                                        </tr>
                                        <!-- end line  -->
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr role='row'>
                                            <th rowspan="1" colspan="3" style="text-align: center">{{trans('back_end.MCI')}}</th>
                                        </tr>
                                        <tr role='row'>
                                            <!-- <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th> -->
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.from')}}</th>
                                            <th rowspan="1" colspan="1" style="width: 10%;text-align: center" >{{trans('back_end.type')}}</th>
                                            <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.to')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @for ($i = 0; $i < count($mci); $i++)
                                    <input type="hidden" name="total_mci" value="{{count($mci)}}">
                                    <input type="hidden" name="id_mci_{{$i}}" value="{{ $mci[$i]['id'] }}">
                                        <tr >
                                            <td >
                                                 <input id="mci_a{{$i}}"  type="text" name="mci_from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="{{intval($mci[$i]['from'])}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace (/(\.\d\d)\d+|([\d.]*)[^\d.]/, '$1$2')" @if($i!=0){{'disabled'}}@endif >
                                                 <input id="mci_a_{{$i}}" type="hidden" name="mci_from{{$i}}" value="{{intval($mci[$i]['from'])}}">

                                            </td>
                                            <td style="width: 10%;text-align: center">
                                                <  {{"M"}} <=
                                            </td>
                                            <td>
                                                <input id="mci_b{{$i}}" name="mci_to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="@if($mci[$i]['to'] == null) {{$mci[$i]['to']}} @else{{intval($mci[$i]['to'])}}@endif" onchange="check_mci({{$i}}, {{count($mci)}}) " onkeypress="return validate(event , {{$i}} ,{{count($mci)}})" @if($mci[$i]['to'] == null) {{'disabled'}} @endif> 
                                            </td>
                                        </tr>
                                        <!-- end line  -->
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-1 pull-right" >
                            <button type="submit" class="btn btn-block btn-primary" style="margin: -5px 0px 0px 0px;" id="save" >{{trans('back_end.save')}}</button>
                        </div>
                    </div>
                @box_close()
                </article>
            </div>  
            
        </section>
        <!-- end body -->
    </div>
    <!-- end box -->
</div>


@endsection

@push('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
<script language="Javascript">

$("#crack_b"+y,"#rut_b"+y,"#iri_b"+y,"#mci_b"+y).keypress(function(e){
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
       return false;
    }
});
    
$("#crack_a"+x,"#rut_a"+x,"#iri_a"+x,"#mci_a"+x).keypress(function(e){
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
       return false;
    }
});
    function validate(evt , i ,rank) {
    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
        }

        return true;
    
}
function check(y) {
    var val = $("#crack_b"+y).val();
    var x = y +1;
   $("#crack_a"+x).val(val);
}

function check(y,rank,) {
    // console.log(rank);
    var n = rank -1 ;
    $("#crack_b"+n).prop('disabled', true);
    var x = 0;
    if (x < rank){
        var x = y +1;
    }
    var last = y - 1;
    var val = $("#crack_b"+y).val();
    var val_last = $("#crack_b"+last).val();
    
    if ((val_last != '') && (val < val_last))  {
        alert("{{trans('back_end.ko_hop_le')}}")
    } 
    
    $("#crack_a"+x).val(val);
    $("#crack_a_"+x).val(val);
//     disable form 
    $("#crack_a"+x).prop('readOnly', true);
}
function check_rut(y,rank) {
    // console.log(rank);
    var n = rank -1 ;
    $("#rut_b"+n).prop('disabled', true);
    var x = 0;
    if (x < rank){
        var x = y +1;
    }
    var last = y - 1;
    var val = $("#rut_b"+y).val();
    var val_last = $("#rut_b"+last).val();
    
    if ((val_last != '') && (val < val_last))  {
        alert("{{trans('back_end.ko_hop_le')}}")
    } 
    
    $("#rut_a"+x).val(val);
    $("#rut_a_"+x).val(val);
//     disable form 
    $("#rut_a"+x).prop('readOnly', true);
}
function check_iri(y,rank) {
    // console.log(rank);
    var n = rank -1 ;
    $("#iri_b"+n).prop('disabled', true);
    var x = 0;
    if (x < rank){
        var x = y +1;
    }
    var last = y - 1;
    var val = $("#iri_b"+y).val();
    var val_last = $("#iri_b"+last).val();
    
    if ((val_last != '') && (val < val_last))  {
        alert("{{trans('back_end.ko_hop_le')}}")
    } 
    
    $("#iri_a"+x).val(val);
    $("#iri_a_"+x).val(val);
//     disable form 
    $("#iri_a"+x).prop('readOnly', true);
}
function check_mci(y,rank) {
    // console.log(rank);
    var n = rank -1 ;
    $("#mci_b"+n).prop('disabled', true);
    var x = 0;
    if (x < rank){
        var x = y +1;
    }
    var last = y - 1;
    var val = $("#mci_b"+y).val();
    var val_last = $("#mci_b"+last).val();
    
    if ((val_last != '') && (val < val_last))  {
        alert("{{trans('back_end.ko_hop_le')}}")
    } 
    
    $("#mci_a"+x).val(val);
    $("#mci_a_"+x).val(val);
//     disable form 
    $("#mci_a"+x).prop('readOnly', true);
}
</script>
@endpush
