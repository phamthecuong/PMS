@extends('admin.layouts.app')
@section('contentheader_title')
    {{trans('back_end.manager_condition_rank')}}
@endsection

@section('contentheader_link')
    <li><a href="/condition_rank"><i class="fa fa-dashboard"></i> {{ trans('back_end.manager_condition_rank') }}</a></li>
@endsection

@section('main-content')
<div class="theme-showcase">
     <!-- box -->
    <div class="box">
   
        <div class="box-header">
            <h3 class="box-title">{{trans('back_end.manager_condition_rank')}}</h3>
        </div>
        <!-- body -->
        <div class="box-body">
            <div class="form-group">
                <!-- table -->
                <div  id="container" >
                
                    <table class="table table-bordered">
               
                        <thead>
                            <tr role='row'>
                                <th rowspan="2" colspan="1" style="width: 10% ;text-align: center" >{{trans('back_end.rank')}}</th>
                                <th rowspan="1" colspan="3" style="text-align: center">{{trans('back_end.range')}}</th>
                            </tr>
                            <tr role='row'>
                                <!-- <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th> -->
                                <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.from')}}</th>
                                <th rowspan="1" colspan="1" style="width: 10%;text-align: center" >{{trans('back_end.type')}}</th>
                                <th rowspan="1" colspan="1" style="text-align: center" >{{trans('back_end.to')}}</th>

                            </tr>
                        </thead>
                        <tbody>
                        @for ($i = 0; $i < $rank; $i++)
                        <input type="hidden" name="total" value="{{$rank}}">
                            <tr >
                                <td > {{$i+1}}
                                </td>
                                <td >
                                    <!-- <input id="a{{$i}}"  type="text" name="from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"> -->
                                     <input id="a{{$i}}"  type="text" name="from{{$i}}" style="text-align: center ; width: 80%;" class="form-control validate_form" placeholder="" value="" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace (/(\.\d\d)\d+|([\d.]*)[^\d.]/, '$1$2')">

                                </td>
                                <td style="width: 10%;text-align: center">
                                    <  {{$type}} <=
                                </td>
                                <td>
                                    <!-- <input id="b{{$i}}" name="to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="" onchange="check({{$i}}, {{$rank}}) " >  -->
                                    <input id="b{{$i}}" name="to{{$i}}"  type="text" style="text-align: center ; width: 80%;" class="form-control text_number" placeholder="" value="" onchange="check({{$i}}, {{$rank}}) " onkeypress="return validate(event , {{$i}} ,{{$rank}})"> 
                                </td>
                            </tr>
                            <!-- end line  -->
                        @endfor
                        </tbody>
                    </table>
                    <div class="col-md-2 col-md-offset-3">
                        <button type="submit"  class="btn btn-block btn-primary"  id="save" >{{trans('back_end.save')}}</button>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- end body -->
    </div>
    <!-- end box -->
</div>


@endsection

@section('js_extend')
<script language="Javascript">

$("#b"+y).keypress(function(e){
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
           return false;
        }
    });
    
    $("#a"+x).keypress(function(e){
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
           return false;
        }
    });
function check(y) {
    var val = $("#b"+y).val();
    var x = y +1;
   $("#a"+x).val(val);
}

</script>
@endsection
