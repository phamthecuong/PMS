@extends('front-end.layouts.app')
@section('backend')
    active
@endsection

@section('back_end_segment_manager')
    active
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li>{{trans("menu.back_end")}}</li>
    <li>{{trans("menu.manager_segment")}}</li>
</ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-ellipsis-h',
        'text1' => trans('menu.manager_segment'),
        'text2' => (!isset($segment)) ? trans('back_end.addnew_segment'): trans('back_end.edit_segment')
    ])

    @if(!isset($segment))
        {!!Form::open(array('method' => 'POST', 'id' => 'submit' , 'route' => array('manager_segment.store')))!!}
    @else
        {!!Form::open(array('method' => 'PUT', 'id' => 'submit' , 'name' => 'submit', 'route' => array('manager_segment.update', $segment->id)))!!}
    @endif
        <section id="widget-grid">
            <div class="row">
                <!-- Visualization -->
                <article class="col-lg-2 col-md-12 col-xs-12">
                    @box_open(trans('back_end.boundary'))
                    <div>
                        <div class="widget-body">
                            <div class="form-group row">
                                <label class="col-md-4 control-label">{{trans('segment.KM From')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="km_from_boundary">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">{{trans('segment.M From')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="m_from_boundary">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">{{trans('segment.KM To')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="km_to_boundary">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">{{trans('segment.M To')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="m_to_boundary">
                                </div>
                            </div>
                            <div class="widget-footer">
                                <button type="button" class="btn btn-default" onclick="changeBoundary()">{{trans('segment.Change Boundary')}}</button> 
                            </div>
                        </div>
                    </div>
                    @box_close
                </article>
                <article class="col-lg-10 col-md-12 col-xs-12">
                    @box_open(trans('back_end.visualization'))
                    <div>
                        <div class="widget-body">
                            <div style="text-align: center;">
                                <canvas id="visualization" height="400" style="border: solid 0;-moz-box-shadow: 1px 1px 5px #CCC;-webkit-box-shadow: 1px 1px 5px red;box-shadow: 1px 1px 5px #CCC;"></canvas>
                            </div>
                        </div>
                    </div>
                    @box_close
                </article>
                <!-- Visualization - end -->

                <!-- Scope Management -->
                <article class="col-lg-6">
                    @box_open(trans('back_end.scope_manage'))
                    <div>
                        <div class="widget-body">
                            <div class="row">
                                <!-- Road Management Bureau -->
                                <div class="col-lg-6">
                                    <div class="form-group"style="">
                                        <label for="{{trans('back_end.road_management_bureau')}}" class="control-label">
                                            {!!trans('back_end.road_management_bureau')!!}
                                        </label>
                                        <select class="form-control select2" {{$disable}} name="road" onchange="change_bureau()">
                                            @foreach($road as $r)
                                                <?php
                                                $cakess = $r->id;
                                                $selected = '';
                                                if ($r->id == @App\Models\tblOrganization::find(@$segment->tblOrganization->parent_id)->id) {
                                                    @$selected = 'selected';
                                                }
                                                ?>
                                                @if (App::getLocale() == 'en')
                                                    <option {{@$selected}} value="{{$r->id}}">{{$r->name_en}}</option>
                                                @else
                                                    <option {{@$selected}} value="{{$r->id}}">{{$r->name_vn}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Sub Bureau -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                            <label for="{{trans('back_end.road_management_bureau')}}" class="control-label">{!!trans('back_end.sub_bureau')!!}</label>
                                            <select id="bureau" class="form-control  select2" name="bureau" onchange="mainSegmentRedraw();">
                                                @if(!isset($segment))
                                                    @foreach($sub as $r)
                                                        <?php
                                                        $selected = '';
                                                        $value = @$segment->tblOrganization->id;
                                                        if ($r->id == $value) {$selected = 'selected';
                                                        }
                                                        ?>
                                                        @if (App::getLocale() == 'en')
                                                            <option value="{{$r->id}}">{{$r->name_en}}</option>
                                                        @else
                                                            <option value="{{$r->id}}">{{$r->name_vn}}</option>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach($road_bureau as $r)
                                                    <?php
                                                    $selected = '';
                                                    $value = @$segment->tblOrganization->id;
                                                    if ($r->id == $value) {
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                        @if (App::getLocale() == 'en')
                                                            <option value="{{$r->id}}">{{$r->name_en}}</option>
                                                        @else
                                                            <option value="{{$r->id}}">{{$r->name_vn}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                </div>
                                <!-- Route Name -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                            <label for="{{trans('back_end.road_management_bureau')}}" class="control-label">{!!trans('back_end.name_route')!!}</label>
                                            <?php $disable = '';
                                                if (isset($segment))
                                                {
                                                    $disable = 'disabled="disabled"';
                                                }
                                                ?>
                                            <select {{$disable}} id="route" class="form-control select2" name="route" onchange="change_branch()">
                                                @if(!isset($segment))
                                                    @foreach($branch as $r)
                                                        @if (App::getLocale() == 'en')
                                                            <option value="{{$r->id}}">{{$r->name_en}}</option>
                                                        @else
                                                            <option value="{{$r->id}}">{{$r->name_vn}}</option>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach($branch as $r)
                                                        <?php
                                                            $value = $segment->branch_id;
                                                            $selected = '';
                                                            if ($r->id == $value) {
                                                                $selected = 'selected';
                                                            }
                                                        ?>

                                                        @if (App::getLocale() == 'en')
                                                            <option value="{{$r->id}}" {{$selected}}>{{$r->name_en}}</option>
                                                        @else
                                                            <option value="{{$r->id}}" {{$selected}}>{{$r->name_vn}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                </div>
                                <?php
                                    $items = array();
                                    foreach ($branch as $b) 
                                    {
                                        $items[] = array('value' => $b->id, 'name' => $b->branch_number);
                						$branch_slect = $items;
                                    }
                                    if (isset($segment)) 
                                    {
                                        $value = @$segment->tblBranch->id;
                                    }
                                ?>
                                <input type="hidden" name="branch" id="branch" value="{{@$value}}" />
                                <!-- Branch -->
                                <div class="col-lg-6">
                                	<label for="{{trans('back_end.branch')}}" class="control-label">{!!trans('back_end.name_branch')!!}</label>
                                    <select disabled="disabled" id="branch_slect" class="form-control" name="branch_slect">
                                	</select>
                                </div>
                                <?php $value = @$segment->segname_en; ?>
                                <!-- Name English -->
                                <div class="col-lg-12">
                                    {!! Form::lbText('name_en', @$value , trans('back_end.name_en')) !!}
                                </div>
                                <?php $value = @$segment->segname_vn; ?>
                                <!-- Name Vietnamese -->
                                <div class="col-lg-12">
                                    {!! Form::lbText('name_vn', @$value , trans('back_end.name_vn')) !!}
                                </div>
                                <?php
                                $items = array();
                                for ($i = 2000; $i < 2100; $i++) {
                                    $items[] = array('value' => $i, 'name' => $i);
                                }
                                ?>
                                <!-- Year Valid -->
                                <?php 
                                // $year = '';
                                // if (@$segment->effect_at != null)
                                // {
                                //     $year = Carbon\Carbon::parse($segment->effect_at)->format('Y-m-d');
                                // }
                                ?>
                                <?php 
                                    // $a = ''; 
                                    // if(isset($segment)){$a = date('Y-m-d', strtotime(@$segment->effect_at));}
                                ?>
                                {{-- <div class="col-lg-12">
                                    <div class="form-group" style="">
                                        <label>{!! trans('back_end.year') !!}</label>
                                        <div class="input-group date">
                                            @if (@$segment->effect_at == "0000-00-00 00:00:00")
                                            <input type="text" value="" class="form-control pull-right" name="year" id="datepicker">
                                            @else
                                            <input type="text" value="{{$year}}" class="form-control pull-right" name="year" id="datepicker">
                                            @endif
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    @box_close
                </article>
                <!-- Scope Management - end -->

                <article class="col-lg-6">
                    @box_open(trans('back_end.Chainage_KM_Postss'))
                    <div>
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <header>
                                        {{trans('back_end.chainage_from')}}
                                    </header>
                                    <div class="form-group">
                                        <label for="M" class="control-label">Km</label>
                                        <input class="form-control" placeholder="Km" name="f_km" type="text" value="{{@$segment->km_from}}" onchange="mainSegmentRedraw();">
                                    </div>
                                    <div class="form-group">
                                        <label for="M" class="control-label">M</label>
                                        <input class="form-control" placeholder="M" name="f_m" type="text" value="{{@$segment->m_from}}" onchange="mainSegmentRedraw();">
                                    </div>
                                    <?php
                                        $lang = \App::isLocale('en') ? 'en' : 'vn';
                                        // $items = array();
                                        // foreach ($city as $b) {
                                        //     $items[] = array('value' => $b->id, 'name' => $b->{"name_$lang"});
                                        // }
                                        if (isset($segment)) {
                                            $value = @$segment->prfrom_id;
                                        }
                                    ?>
                                    <!-- Province from -->
                                    {!! Form::lbSelect2('fro_pro',  @$value, App\Models\tblCity::allToOption(), trans('back_end.province'),['onchange' => "loadD('fro_pro', 'dis_pro')"]) !!}

                                    <?php
                                        // $items = array();
                                        // foreach ($distric as $d) {
                                        //     $items[] = array('value' => $d->id, 'name' => $d->{"name_$lang"});
                                        // }
                                        if (isset($segment)) {
                                            $value = @$segment->distfrom_id;
                                        }
                                    ?>
                                    <!-- Distric from -->
                                    {!! Form::lbSelect2('dis_pro', @$value, [], trans('back_end.distric'),['onchange' => "loadW('dis_pro', 'commune_fro')"]) !!}
                                    <?php $value = @$segment->commune_from;?>
                                    <!-- Commune from -->

                                    {!! Form::lbSelect2('commune_fro', @$value, [], trans('back_end.commune')) !!}
                                </div>
                                <div class="col-md-6">
                                    <header>
                                        {{trans('back_end.chainageTo')}}
                                    </header>
                                    <div class="form-group">
                                        <label for="M" class="control-label">Km</label>
                                        <input class="form-control" placeholder="Km" name="t_km" type="text" value="{{@$segment->km_to}}" onchange="mainSegmentRedraw();">
                                    </div>
                                    <div class="form-group">
                                        <label for="M" class="control-label">M</label>
                                        <input class="form-control" placeholder="M" name="t_m" type="text" value="{{@$segment->m_to}}" onchange="mainSegmentRedraw();">
                                    </div>
                                    <?php
                                        // $items = array();
                                        // foreach ($city as $b) {
                                        //     $items[] = array('value' => $b->id, 'name' => $b->{"name_$lang"});
                                        // }
                                        if (isset($segment)) {
                                            $value = @$segment->prto_id;
                                        }
                                    ?>
                                    <!-- Province to -->
                                    {!! Form::lbSelect2('fro_to', @$value, App\Models\tblCity::allToOption(), trans('back_end.province'),['onchange' => "loadD('fro_to', 'dis_to')"]) !!}

                                     <?php
                                        // $items = array();
                                        // foreach ($distric as $d) {
                                        //     $items[] = array('value' => $d->id, 'name' => $d->{"name_$lang"});
                                        // }
                                        if (isset($segment)) {
                                            $value = @$segment->distto_id;
                                        }
                                    ?> 
                                    <!-- Distric to -->
                                    {!! Form::lbSelect2('dis_to', @$value, [], trans('back_end.distric'),['onchange' => "loadW('dis_to', 'commune_to')"]) !!}
                                    <?php $value = @$segment->commune_to; ?>
                                    <!-- Commune to -->
                                    {!! Form::lbSelect2('commune_to', @$value, [], trans('back_end.commune')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @box_close
                </article>
            </div>
        </section>

        <div class="well" style="text-align: right">        
            @if(!isset($segment))
                <button type="submit" class="btn btn-primary">
                    {{trans('back_end.add_new')}}
                </button>
            @else
                <button type="button" onclick="popup()" id="btn_edit" data-toggle="" data-target="" class="btn btn-primary">
                    {{trans('back_end.edit')}}
                </button>
            @endif
        </div>
    {!! Form::close() !!}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel" style="text-align: center"> {{trans('segment.overlap')}} </h4>
                </div>
                <div class="modal-body" id="modaltable" style="padding-bottom: 0px;">
                    <table id="overlapping_list" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                              <th>{!! trans('back_end.segment') !!}</th>
                              <th>{!! trans('back_end.overlaptype') !!}</th>
                              <th>{!! trans('back_end.description') !!}</th>
                              <th>{!! trans('back_end.action') !!}</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
                <div class="modal-body" id="section" style="padding-top: 0px;">
                </div>
                <div class="modal-footer" style="">
                    <button type="button" class="btn btn-default pull-left" style="" data-dismiss="modal">{{trans('menu.cancel')}}</button>
                    <button onclick="$(this).css('pointer-events', 'none');document.getElementById('submit').submit();" type="button" class="btn btn-default pull-left process" style="">{{trans('menu.proceed')}}</button>
              </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    var count = 1; 
	function load_route_name()
	{
		$("#branch_slect option").remove();
		var branch_slect = [];
		@foreach ($branch_slect as $b)
			branch_slect.push([{{$b['value']}}, "{{$b['name']}}"]);
		@endforeach
		var html = '';
		var route = $("#route").val();
		for (var i = 0; i < branch_slect.length; i++)
		{
			var select = '';
			if (branch_slect[i][0] == route)
			{
				html += '<option selected value = "' + branch_slect[i][0] + '">';
				html += branch_slect[i][1];
				html += '</option>';
				select = branch_slect[i][0];
				// html += '<input type="hidden" name="branch_slect" value="' + branch_slect[i][0] + '">'
			}
			else
			{
				html += '<option value = "' + branch_slect[i][0] + '">';
				html += branch_slect[i][1];
				html += '</option>';
			}
		}
        $("#branch_slect").append(html);
		$("#branch_slect").trigger('change');
	}

    $(function() {
        change_bureau();
        $('select[name="branch"]').on('change', function(){
            stage.removeAllChildren();
            init();
        });

        $('#datepicker').datepicker({
            format : 'yyyy-mm-dd',
            autoclose : true
        }); 
        
        $('#submit').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });
    });

	function change_bureau() {
		var t = document.getElementsByName("road")[0];
        //var t2 = document.getElementsByName("bureau")[0];
		var t2 = $('#bureau');
		// for (var option in t2) {
		// 	t2.remove(option);
		// }
        t2.empty();
		var s = t.options[t.selectedIndex].value;
		$.ajax({
			type : "GET",
			url : '/get_data_bureau_segment/' + s,
			data : {
				id : s
			},
		}).done(function(msg) {
			var i;
            var html = '';
            @if (App::getLocale() == 'en')
    			for ( i = 0; i < msg.length; i++) {
                    html+= '<option value="' + msg[i]['id'] + '">' + msg[i]['name_en'] + '</option>';
    				// $('select[name="bureau"]').append('<option value="' + msg[i]['id'] + '">' + msg[i]['name_en'] + '</option>')
    			}
            @else
                for ( i = 0; i < msg.length; i++) {
                    html += '<option value="' + msg[i]['id'] + '">' + msg[i]['name_vn'] + '</option>';
                    // $('select[name="bureau"]').append('<option value="' + msg[i]['id'] + '">' + msg[i]['name_vn'] + '</option>')
                }
            @endif
            t2.html(html);
            var selected_id = "{{@$segment->tblOrganization->id}}";
            if (count == 1) {
                console.log(count);
                t2.val(selected_id);
            }
            if (count > 1) {
                t2.trigger('change');    
            }
            count ++;
		});
	}

	function change_branch() {
		load_route_name();
		// var t = document.getElementsByName("route")[0];
		// var t2 = document.getElementsByName("branch")[0];
		// for (var option in t2) {
			// t2.remove(option);
		// }
		// var s = t.options[t.selectedIndex].value;
		
		// $.ajax({
			// type : "GET",
			// url : '/get_data_branch_segment/' + s,
			// data : {
				// id : s
			// },
		// }).done(function(msg) {
			// var i;
            // @if (App::getLocale() == 'en')
			// for ( i = 0; i < msg.length; i++) {
				// $('select[name="branch"]').append('<option value="' + msg[i]['id'] + '">' + msg[i]['name_en'] + '</option>')
			// }
            // @else
            // for ( i = 0; i < msg.length; i++) {
                // $('select[name="branch"]').append('<option value="' + msg[i]['id'] + '">' + msg[i]['name_vn'] + '</option>')
            // }
            // @endif
		// });
        // stage.removeAllChildren();
        // init();
	}

	
	@if(isset($segment))
    function request_f_km()
    {
        var f_km = document.getElementsByName('f_km')[0].value;
        return f_km;
    }

    function request_f_m()
    {
        var f_km = document.getElementsByName('f_m')[0].value;
        return f_km;
    }

    function request_t_km()
    {
        var f_km = document.getElementsByName('t_km')[0].value;
        return f_km;
    }

    function request_t_m()
    {
        var f_km = document.getElementsByName('t_m')[0].value;
        return f_km;
    }

	function popup()
	{
	    var f_km = document.getElementsByName('f_km')[0].value;
	    var f_m = document.getElementsByName('f_m')[0].value;
	    var t_km = document.getElementsByName('t_km')[0].value;
	    var t_m = document.getElementsByName('t_m')[0].value;
	    $.ajax({
	        url: '/check_segment_neutral',
	        method: 'GET',
	        data: {
	            branch: $("#branch").val(),
	            // "_token": "{{ csrf_token() }}",
	            f_km : f_km,
	            f_m : f_m,
	            t_km : t_km,
	            t_m : t_m,
	            id : {{@$segment->id}},
	            rb : $('#bureau').val()
	        }
	    }).done(function(msg){
	        if (msg == 'error_point_in')
	        {
	            bootbox.alert("Error");
	        }
            // else if (msg[0] == 'ok')
            // {
                // $("#modaltable table").remove();
                // console.log(msg[0][0]);
                // $('#myModal').modal();
                // var x = $('#overlapping_list tbody tr');
                // var array = [];
                // for (var i=0; i< msg.length; i++)
                // {
                    // x.remove(x.i);
                    // array.push(msg[i]['id']);
                // }
                // $("#section p").remove();
                // for(var i = 1; i< msg.length; i++)
                // {
                    // $("#section").append(msg[i][0]);
                // }
                // //document.getElementById('submit').submit();
            // }
	        else if (msg != 'error')
	        {
	            if (msg.length == 0)
	            {
	                console.log($("#submit"));
	                document.getElementById('submit').submit();
	                return true;
	            }
	            $('#myModal').modal();
	            var x = $('#overlapping_list tbody tr');
                var array = [];
	            for (var i=0; i< msg.length; i++)
	            {
	                x.remove(x.i);
                    array.push(msg[i]['id']);
	            }
                var array_segment = "'" + array + "'";
                $("#section p").remove();
	            var case_seg;
	            for (var i=0; i < msg.length; i++) {
	                if (msg[i].search("<p>") == -1)
                    {
                        console.log(msg[i].search("<p>"));
                        $('#overlapping_list tbody').append(msg[i]);
				    }
				    else if (msg[i].search("<p>") == 0)
                    {
                        $("#overlapping_list").hide();
                        $("#myModal .modal-header h4").text('{{trans("segment.infomation")}}');
                        $("#section").append(msg[i]);
                    }
				};
	        }
	        else 
	        {
                alert(msg);
	        }
	    });
	}

    // H.ANH  2016.12.04  remove unused code
    // function ViewSegment(array_segment, id)
    // {
    //     $("#hidden input").remove();
    //     $('#myModal').modal('hide');
    //     var f_km = request_f_km();
    //     var f_m = request_f_m();
    //     var t_km = request_t_km();
    //     var t_m = request_t_m();
    //     $("#hidden").append("<input type='hidden' name='f_km' value=" + f_km + ">");
    //     $("#hidden").append("<input type='hidden' name='f_m' value=" + f_m + ">");
    //     $("#hidden").append("<input type='hidden' name='t_km' value=" + t_km + ">");
    //     $("#hidden").append("<input type='hidden' name='t_m' value=" + t_m + ">");
    //     $("#hidden").append("<input type='hidden' name='array_segment' value=" + array_segment + ">");
    //     $("#hidden").append("<input type='hidden' name='id' value=" + {{$segment->id}} + ">");
    //     $.ajax({
    //         url: '/check_segment_component',
    //         method: 'GET',
    //         data: {
    //             array_segment: array_segment,
    //             id_segment : id,
    //             id : {!!$segment->id!!},
    //             f_km : f_km,
    //             f_m : f_m,
    //             t_km : t_km,
    //             t_m : t_m
    //         }
    //     }).done(function(msg){
    //         $('#myModal1').modal();
    //         $("#segment div").remove();
    //         for (var i = 0; i < msg.length; i++) {
    //             $('#segment').append(msg[i]);
    //         }
    //     });
    //     // $("#ComponentSubmit").click(function(){
    //     //     $("#SubmitComponent").append("<input type='hidden' name='f_m' value=" + f_km + ">");
    //     //     console.log(id);
    //     // });
    // }
    @endif
</script>
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
    	$('form').bind('submit', function () {
    	    $(this).find(':input').prop('disabled', false);  
	    });
    	load_route_name();
    	$('#submit').formValidation({
    		excluded : ':disabled',
    		framework : 'bootstrap',
    		fields : {
    			f_km : {
    				validators : {
    					notEmpty : {
    						message : "{{trans('validation.km_from_is_required')}}"
    					},
    					
    					regexp: {
    	                    regexp: /^([0-9]*[.])?[0-9]+$/i,
    	                    message: "{{trans('validation.km_from_must_be_number')}}"
    	               	},
    				}
    			},
    			
    			f_m : {
    				validators : {
    					notEmpty : {
    						message : "{{trans('validation.m_from_is_required')}}"
    					},
    					
    					regexp: {
    	                    regexp: /^([0-9]*[.])?[0-9]+$/i,
    	                    message: "{{trans('validation.m_from_must_be_number')}}"
    	               	},
    				}
    			},
    			
    			t_km : {
    				validators : {
    					notEmpty : {
    						message : "{{trans('validation.km_to_is_required')}}"
    					},
    					
    					regexp: {
    	                    regexp: /^([0-9]*[.])?[0-9]+$/i,
    	                    message: "{{trans('validation.km_to_must_be_number')}}"
    	               	},
    				}
    			},
    			
    			t_m : {
    				validators : {
    					notEmpty : {
    						message : "{{trans('validation.m_to_is_required')}}"
    					},
    					
    					regexp: {
    	                    regexp: /^([0-9]*[.])?[0-9]+$/i,
    	                    message: "{{trans('validation.m_to_must_be_number')}}"
    	               	},
    				}
    			},
    		}
    	})
    });
</script>

<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
<script src="https://code.createjs.com/tweenjs-0.6.2.min.js"></script>
<!-- Visualization zone -->
<script type="text/javascript">
    // global variable
    var stage,
        main_segment = {
            rectCommand: null,
            line_f: null,
            line_t: null,
            text_f: null,
            text_t: null
        },
        main_color = "rgba(255, 0, 0, 0.6)",
        line_color_data = ["rgb(255, 0, 0)", "rgb(0, 0, 255)"],
        other_segments = [],
        boundary_left = 1000000000,
        boundary_right = 0,
        main_segment_object,
        boundary_left_data = [],
        boundary_right_data = [];
    
    $(function(){
        init();
    });

    function getColor() {
        var alpha = getRandomNumber();
        while (alpha == 0) {
            alpha = getRandomNumber();
        }
        return "rgba(0, 0, 255, " + alpha + ")";
    }

    function getRandomNumber() {
        return Math.random().toFixed(1);
    }

    function tick(event) {
        stage.update(event);
    }

    function convertToMeter(km, m) {
        if (m > 1000) {
            m = 1000;
        }

        return +km*1000 + (+m);
    }

    /**
     * draw a rectangle
     */
    function drawRectangle(x, y, w, h, color, main_flg) {
        var rect = new createjs.Shape();
        stage.addChild(rect);
        rect.graphics.beginFill(color);
        if (main_flg) {
            main_segment_object = rect;
        }
        // Draw the rectangle, and store off the "command"
        var rectangleCommand = rect.graphics.drawRect(x, y, w, h).command;
        return rectangleCommand;
    }

    /**
     * draw a line
     */
    function drawLinePosition(x, color, position, main_flg) {
        var line = new createjs.Shape();
        var txt = new createjs.Text();
        if (main_flg)
        {
            line.graphics
                .setStrokeStyle(1, 'round')
                .setStrokeDash([20, 10], 0)
                .beginStroke(color)
                .moveTo(x, 320)
                .lineTo(x, 250)
                .endStroke()
                .command;  
            txt.font = "10px Arial";
            txt.color = "#000000";
            txt.text = position;
            txt.x = x + 5;
            txt.y = 322;
            txt.rotation = 90; 
        }
        else
        {
            line.graphics
                .setStrokeStyle(1, 'round')
                .setStrokeDash([20, 10], 0)
                .beginStroke(color)
                .moveTo(x, 80)
                .lineTo(x, 150)
                .endStroke()
                .command;
            txt.font = "10px Arial";
            txt.color = "#000000";
            txt.text = position;
            txt.x = x-5;
            txt.y = 78;
            txt.rotation = 270;
        }
        stage.addChild(line);
        stage.addChild(txt);
        return [line, txt];
    }

    function moveMainSegment(x, w, position_f, position_t) {
        main_segment.line_f.graphics.clear();
        main_segment.line_t.graphics.clear();
        main_segment.text_f.visible = false;
        main_segment.text_t.visible = false;
        main_segment.text_f.text = position_f;
        main_segment.text_t.text = position_t;
        createjs.Tween.get(main_segment.rectCommand)
            .to({x: x, w: w}, 500, createjs.Ease.quadInOut)
            .call(handleComplete);

        function handleComplete() {
            var position_f = drawLinePosition(main_segment.rectCommand.x, line_color_data[0], main_segment.text_f.text, true);
            var position_t = drawLinePosition(+main_segment.rectCommand.x + main_segment.rectCommand.w, line_color_data[0], main_segment.text_t.text, true);
            main_segment.line_f = position_f[0];
            main_segment.line_t = position_t[0];
            main_segment.text_f = position_f[1];
            main_segment.text_t = position_t[1];
        }
    }

    function changeBoundaryInfo(km_f, m_f, km_t, m_t) {
        boundary_left_data = [km_f, m_f];
        boundary_right_data = [km_t, m_t];
        boundary_left = convertToMeter(km_f, m_f);        
        boundary_right = convertToMeter(km_t, m_t);
    }

    function init() {
        $('#visualization').attr('width', $('#visualization').parent().width());
        stage = new createjs.Stage("visualization");
        createjs.Ticker.on("tick", tick);

        $('#status_bar').html('Drawing...');

        var branch_id = $('#route option:selected').val();
        
        $.ajax({
            url: '{{route('segment.in.branch')}}',
            method: 'GET',
            data: {
                "branch_id": branch_id,
                "segment_id": +'{{@$segment_id}}'
            }
        }).done(function(response){
            if (response.code == 0) {
                alert(response.description);
                return;
            }

            other_segments = response.data;
            var b_km_f, b_m_f, b_km_t, b_m_t;
            if (response.data.length > 0) {
                b_km_f = response.data[0].km_from;
                b_m_f = response.data[0].m_from;
                b_km_t = response.data[response.data.length - 1].km_to;
                b_m_t = response.data[response.data.length - 1].m_to;    
            } else if ($('input[name=f_km]').val() != '') {
                // edit case
                b_km_f = $('input[name=f_km]').val();
                b_m_f = $('input[name=f_m]').val();
                b_km_t = $('input[name=t_km]').val();
                b_m_t = $('input[name=t_m]').val();
            } else {
                b_km_f = 0;
                b_m_f = 0;
                b_km_t = 0;
                b_m_t = 0;
            }
            

            $('#km_from_boundary').val(b_km_f);
            $('#m_from_boundary').val(b_m_f);
            $('#km_to_boundary').val(b_km_t);
            $('#m_to_boundary').val(b_m_t);
            changeBoundaryInfo(b_km_f, b_m_f, b_km_t, b_m_t);
            drawOtherSegments();
            
            if (checkChainageInput()) {
                initMainSegment();
            }

            $('#status_bar').html('Visualization');
        });
    }

    function removeMainSegment() {
        if (main_segment.rectCommand) {
            main_segment_object.graphics.clear();
            main_segment.line_f.graphics.clear();
            main_segment.line_t.graphics.clear();
            main_segment.text_f.visible = false;
            main_segment.text_t.visible = false;
            main_segment = {
                rectCommand: null,
                line_f: null,
                line_t: null,
                text_f: null,
                text_t: null
            };
            main_segment_object = null;
        }
    }

    function checkChainageInput() {
        var km_f = $('input[name=f_km]').val();
        var m_f = $('input[name=f_m]').val();
        var km_t = $('input[name=t_km]').val();
        var m_t = $('input[name=t_m]').val();

        if (km_f == '' || m_f == '' || km_t == '' || m_t == '' || (!+km_f && +km_f != 0) || (!+m_f && +m_f != 0) || (!+km_t && +km_t != 0) || (!+m_t && +m_t != 0) || 
            (convertToMeter(+km_t, +m_t) - convertToMeter(+km_f, +m_f) <= 0)
        ) {
            removeMainSegment();
            return false;
        } else {
            return true;
        }   
    }

    function initMainSegment() {
        var km_f = $('input[name=f_km]').val();
        var m_f = $('input[name=f_m]').val();
        var km_t = $('input[name=t_km]').val();
        var m_t = $('input[name=t_m]').val();

        start_point = convertMeterToPixel(convertToMeter(+km_f, +m_f));
        end_point = convertMeterToPixel(convertToMeter(+km_t, +m_t));
        var rect = drawRectangle(start_point, 150, end_point - start_point, 100, main_color, true);
        var position_f = drawLinePosition(start_point, line_color_data[0], "Km" + km_f + "+" + m_f, true);
        var position_t = drawLinePosition(end_point, line_color_data[0], "Km" + km_t + "+" + m_t, true);
        main_segment = {
            rectCommand: rect,
            line_f: position_f[0],
            line_t: position_t[0],
            text_f: position_f[1],
            text_t: position_t[1],
        };
    }

    function convertMeterToPixel(m) {
        if (m < boundary_left)
        {
            return 10;
        }
        else if (m > boundary_right) {
            return 1030;
        }
        var value = (+$('#visualization').parent().width()-30) * (m - boundary_left) / (boundary_right - boundary_left);
        return +Math.round(value) + 20;
    }

    function mainSegmentRedraw() {
        if (!checkChainageInput()) {
            return;
        }
        if (!main_segment_object) {
            initMainSegment();
        } else {
            var km_f = $('input[name=f_km]').val();
            var m_f = $('input[name=f_m]').val();
            var km_t = $('input[name=t_km]').val();
            var m_t = $('input[name=t_m]').val();

            start_point = convertMeterToPixel(convertToMeter(+km_f, +m_f));
            end_point = convertMeterToPixel(convertToMeter(+km_t, +m_t));
            moveMainSegment(start_point, end_point - start_point, "Km" + km_f + "+" + m_f, "Km" + km_t + "+" + m_t);
        }
    }

    function drawOtherSegments() {
        var data,
            start_point,
            end_point,
            milestone = [];
        for (var i in other_segments) {
            data = other_segments[i];
            start_point = convertMeterToPixel(convertToMeter(data.km_from, data.m_from));
            end_point = convertMeterToPixel(convertToMeter(data.km_to, data.m_to));
            if (start_point == 0 && end_point == 0) {
                continue;
            }
            drawRectangle(
                start_point,
                150, 
                end_point - start_point, 
                100, 
                getColor()
            );
            if (milestone.indexOf(start_point) < 0) {
                drawLinePosition(start_point, line_color_data[1], "Km" + data.km_from + "+" + data.m_from);
                milestone.push(start_point);    
            }
            
            if (milestone.indexOf(end_point) < 0) {
                drawLinePosition(end_point, line_color_data[1], "Km" + data.km_to + "+" + data.m_to);
                milestone.push(end_point);    
            }    
        }
    }

    function changeBoundary() {
        var km_f = $('#km_from_boundary').val();
        var m_f = $('#m_from_boundary').val();
        var km_t = $('#km_to_boundary').val();
        var m_t = $('#m_to_boundary').val();

        if (km_f == '' || m_f == '' || km_t == '' || m_t == '' || (!+km_f && +km_f != 0) || (!+m_f && +m_f != 0) || (!+km_t && +km_t != 0) || (!+m_t && +m_t != 0) || 
            (convertToMeter(+km_t, +m_t) - convertToMeter(+km_f, +m_f) <= 0)
        ) {
            alert('{{trans('segment.Invalid Boundary')}}');
            $('#km_from_boundary').val(boundary_left_data[0]);
            $('#m_from_boundary').val(boundary_left_data[1]);
            $('#km_to_boundary').val(boundary_right_data[0]);
            $('#m_to_boundary').val(boundary_right_data[1]);
        } else {
            changeBoundaryInfo(km_f, m_f, km_t, m_t);
            stage.removeAllChildren();
            drawOtherSegments();
            if (checkChainageInput()) {
                initMainSegment();
            }
        }   
    }
</script>
<!-- End Visualization zone -->
@include('admin.segment.script');
@endpush