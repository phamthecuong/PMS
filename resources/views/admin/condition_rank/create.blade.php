@extends('admin.layouts.app')
@section('contentheader_title')
    {{trans('back_end.manager_condition_rank')}}
@endsection

@section('contentheader_link')
    <li><a href="/condition_rank"><i class="fa fa-dashboard"></i> {{ trans('back_end.manager_condition_rank') }}</a></li>
@endsection

@section('main-content')
{!!Form::open(array('method' => 'POST', 'onsubmit' =>'return checkFrom()' ,'id' => 'save' , 'route' => array('condition_rank.store')))!!}
<div class="theme-showcase">
     <!-- box -->
    <div class="box">
    
        <div class="box-header">
            <h3 class="box-title">{{trans('back_end.manager_condition_rank')}}</h3>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-md-6">
                    <!-- tesst -->
                    <div class="box box-info">
                        <!-- form start -->
                        <form class="form-horizontal">
                            <div class="box-body">
                                <div class="form-group">
                                    <label>{{trans('back_end.Disabled_type')}}</label>
                                    <select id="select" class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="disabled" style="width: 90%">
                                      <!-- <option selected="selected">Alabama</option> -->
                                        <option value="1">Crack Ratio</option>
                                          <!-- <option disabled="disabled">California (disabled)</option> -->
                                        <option value="2">Rutting Depth</option>
                                        <option value="3">IRI</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('back_end.matrix')}}</label>
                                    <input id="maxtrix" type="text" class="form-control" placeholder=""  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" style="width: 90%">
                                </div>
                                <div class="col-md-4 col-md-offset-3">
                                    <button type="button" class="btn btn-block btn-primary" onclick="managerConditionRank();" >{{trans('back_end.generate')}}</button>
                                </div>
                          </div>
                          <!-- /.box-body -->
                        
                          <!-- /.box-footer -->
                        </form>
                        
                    </div>
                      <!-- tét  -->
                </div>
            </div>
        <!-- end box -->
            <div class="row">
                <div class="col-md-6 ">
                    <div class="box box-success">
                        <div class="box-body">
                            <div id="table">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('js_extend')
<script >

$(document).ready(function() {
  // $('#table').on('keydown', 'input.text_number', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

  // $('#table').on('change',  'input.text_number', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

 });

$(function () {
    $("#select").select2();
    var c = ($('#select').val());
    
    $(".select2").select2();
    var c = ($('#select').val());
});


function validate(evt , i ,rank) {
    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
        }

        return true;
    
}

function check(y,rank) {
	// console.log(rank);
	var n = rank -1 ;
	$("#b"+n).prop('disabled', true);
	var x = 0;
	if (x < rank){
		var x = y +1;
	}
    var last = y - 1;
    var val = $("#b"+y).val();
    var val_last = $("#b"+last).val();
    
    if ((val_last != '') && (val < val_last))  {
        alert("{{trans('back_end.ko_hop_le')}}")
    } 
    
	$("#a"+x).val(val);
//     disable form 
    $("#a"+x).prop('readOnly', true);
}

function managerConditionRank() {
    var c = $('#select').val();
    var number = $('input#maxtrix').val();
    
    if (number < 5) {
        alert("{{trans('back_end.Matrix_Lon_hon_5')}}");
    } else {
        $("#table").load("/condition_rank_table?type="+c+"&rank="+number+" #container");
    }   
    $('.text_number').css('color' , 'red');
}

function checkFrom() {
	var dd =0;
	$('.validate_form').each(function(){
		
		var value_form = $.trim($(this).val());
		
		if (value_form.length == 0){
			 dd =0;
		} else {
			 dd = 1;
		}
	});
	if (dd == 1){
		// console.log(555567567555);
		// console.log(dd);
		return true;
	} else {
		alert("{{trans('back_end.filt_it')}}");
		return false;
	}
	var a=$.trim($("#a0").val());
	var b=$.trim($("#b0").val());
	if (b < a){
		alert("{{trans('back_end.ko_hop_le')}}");
		return false;
	} 
	return false;
}

</script>
@endsection