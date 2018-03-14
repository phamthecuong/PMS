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

@push('css')
<style>
    table.dataTable.select tbody tr,
    table.dataTable thead th:first-child {
        cursor: pointer;
    }
    .selected{
        background-color: #B0BED9;
    }
    tbody tr{
        cursor: pointer;
    }
    
    .form-control.text_number {
    	width: 60% !important;
    }
    
    .form-group {
    	width: 100% !important;
    }
    
    .boxbody {
    	padding: 5px !important;
    }
    .form-horizontal .control-label {
	    text-align: left !important;
	}
</style>
@endpush

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-ellipsis-h',
        'text1' => trans('menu.manager_segment'),
        'text2' => trans('back_end.list_title')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans('back_end.list_segment_in_system'))
                <div>
                    <div class="widget-body">
                        @if (Auth::user()->hasPermission('segment_management.add'))
                            <a class="btn btn-success header-btn" href="/admin/manager_segment/create">
                                {{trans('back_end.add_new_segment')}}
                            </a>
                        @endif
                        <hr class="simple">
                        <div>
                            <p style="color: red;">*{{trans('back_end.choose_only')}}</p>
                        </div>
                        <div>
                            @if (Auth::user()->hasPermission('segment_management.edit'))
                                <a href="javascript:void(0);" class="btn btn-default" id="edit">
                                    {{trans('back_end.edit_segments')}}
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('segment_management.delete'))
                                <a href="javascript:void(0);" class="btn btn-primary" onclick="disable()" id="delete">
                                    {{trans('back_end.delete_segment')}}
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('segment_management.merge'))
                                <a href="javascript:void(0);" class="btn btn-success" onclick="redirect('merge');">
                                    {{trans('back_end.merge_segment')}}
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('segment_management.split'))
                                <a href="javascript:void(0);" class="btn btn-info" onclick="check_segemnt();">
                                    {{trans('back_end.split_segment')}}
                                </a>
                            @endif
                        </div>
                        <hr class="simple">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="users-table">
                                <thead>
                                    <tr role='row'>
                                        <th rowspan="2" colspan="1">{{trans('back_end.id')}}</th>
                                        <th rowspan="2" colspan="1">{{trans('back_end.name_segment')}}</th>
                                        <th rowspan="2" colspan="1">{{trans('back_end.name_route')}}</th>
                                        <th rowspan="2" colspan="1">{{trans('back_end.name_branch')}}</th>
                                        <th rowspan="2" colspan="1">{{trans('back_end.rmb_segment')}}</th>
                                        <th rowspan="2" colspan="1">{{trans('back_end.sb_segment')}}</th>
                                        <th colspan="4" rowspan="1" style="text-align: center">{{trans('back_end.chainages')}}</th>
                                    </tr>
                                    <tr role='row'>
                                        <th rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.from')}}</th>
                                        <th rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.to')}}</th>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr role='row filter'>
                                        <td></td>
                                        <td>
                                            <input type="text" id="seg_name" onchange="seg_name()" class="form-control form-filter input-sm" name="order_id"> 
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <select name="order_status" onchange="route_search()" id="route" class="form-control form-filter input-sm">
                                                <option value="0">{{trans('back_end.all')}}</option>
                                                @foreach($tblBranch as $t)
                                                    @if (App::getLocale() == 'en')
                                                        <option value="{{$t->id}}">{{$t->name_en}}</option>
                                                    @else
                                                        <option value="{{$t->id}}">{{$t->name_vn}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <!-- <select name="order_status" onchange="branch_search()" id="branch" class="form-control form-filter input-sm">
                                                <option value="0">{{trans('back_end.all')}}</option>
                                                @foreach($road_number as $t)
                                                    @if (App::getLocale() == 'en')
                                                        <option class="branch{{$t->route_id}}" value="{{$t->id}}">{{$t->branch_number}}</option>
                                                    @else
                                                        <option class="branch{{$t->route_id}}" value="{{$t->id}}">{{$t->branch_number}}</option>
                                                    @endif
                                                @endforeach
                                            </select> -->
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <select name="order_status" id="RMB" onchange="RMB_search()" class="form-control form-filter input-sm">
                                                <option value="0">{{trans('back_end.all')}}</option>
                                                @foreach($tblOrganization as $t)
                                                    @if (App::getLocale() == 'en')
                                                        @if($t->parent_id == 0)
                                                            <option value="{{$t->id}}">{{$t->name_en}}</option>
                                                        @endif
                                                    @else
                                                        @if($t->parent_id == 0)
                                                            <option value="{{$t->id}}">{{$t->name_vn}}</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <select name="order_status" id="SB" onchange="SB_search()" class="form-control form-filter input-sm">
                                                <option value="0">{{trans('back_end.all')}}</option>
                                                @foreach($sub as $t)
                                                    @if (App::getLocale() == 'en')
                                                        @if($t->parent_id != 0)
                                                            <option value="{{$t->id}}">{{$t->name_en}}</option>
                                                        @endif
                                                    @else
                                                        @if($t->parent_id != 0)
                                                            option value="{{$t->id}}">{{$t->name_vn}}</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" id="km_f" onchange="km_f()" class="form-control form-filter input-sm" name="km_f"> 
                                        </td>
                                        <td>
                                            <input type="number" id="km_t" onchange="km_t()" class="form-control form-filter input-sm" name="km_t"> 
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @box_close
            </article>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog" role="document">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    					<span aria-hidden="true">&times;</span>
    				</button>
    				<h4 class="modal-title" id="myModalLabel" style="text-align: center"> {{trans('segment.info_segment')}} </h4>
    			</div>
    			<div class="modal-body" id="form">
    				<form class="form-inline" id="point_split">
    	              	<div class="boxbody">
    						<div class="form-group">
    							<div class="col-md-4" style="padding-left: 0px">
    					  			<label>{{trans('segment.input_point_to_split')}} :</label>	
    					  		</div>
    					  		
    					  		<div class="col-md-4">
    					  			<input type="text" class="form-control text_number" name="km_mid" onchange="add_info_new_segment();">
    								<label>Km</label>
    					  		</div>
    					  		
    					  		<div class="col-md-4">
    					  			<input type="text" class="form-control text_number" name="m_mid" onchange="add_info_new_segment();">							    	
    					    		<label>m</label>
    					  		</div>
    					  		
    					  		<input type="hidden" name="km_from" value="1" />
    					  		<input type="hidden" name="km_to" value="1" />
    						</div>
    					</div>
    				</form>
    				<div class="show_hide">
    					<h4 id="segment_1"></h4>
    					<form class="form-horizontal" id="form_segment_1">
    		              	<div class="box-body">
    		                	<div class="form-group">
    		                  		<label class="col-sm-4 control-label">{{trans('segment.name_en')}} :</label>
    		                  		<div class="col-sm-8">
    		                    		<input type="text" class="form-control" placeholder="{{trans('segment.name_en')}}" name="name_en_segment_1">
    		                  		</div>
    		                	</div>
    		                	
    		                	<div class="form-group">
    		                  		<label class="col-sm-4 control-label">{{trans('segment.name_vi')}} :</label>
    		                  		<div class="col-sm-8">
    		                    		<input type="text" class="form-control" placeholder="{{trans('segment.name_vi')}}" name="name_vi_segment_1">
    		                  		</div>
    		                	</div>
    		              	</div>
    		              <!-- /.box-body -->
    		            </form>
    		        
    					<h4 id="segment_2"></h4>
    		            <form class="form-horizontal" id="form_segment_2">
    		              	<div class="box-body">
    		                	<div class="form-group">
    		                  		<label class="col-sm-4 control-label">{{trans('segment.name_en')}} :</label>
    		                  		<div class="col-sm-8">
    		                    		<input type="text" class="form-control" placeholder="{{trans('segment.name_en')}}" name="name_en_segment_2">
    		                  		</div>
    		                	</div>
    		                	
    		                	<div class="form-group">
    		                  		<label class="col-sm-4 control-label">{{trans('segment.name_vi')}} :</label>
    		                  		<div class="col-sm-8">
    		                    		<input type="text" class="form-control" placeholder="{{trans('segment.name_vi')}}" name="name_vi_segment_2">
    		                  		</div>
    		                	</div>
    		              	</div>
    		              <!-- /.box-body -->
    		            </form>
    		        
    		            <form class="form-horizontal" id="form">
    		              	<div class="box-body">
    		                	<div class="form-group">
    		                  		<label class="col-sm-4 control-label">{{trans('segment.date_effected')}} :</label>
    		                  		<div class="col-sm-8">
    		                    		<input type="text" class="form-control" placeholder="{{trans('segment.date_effected')}}" id="date_effected" name="date_effected">
    		                  		</div>
    		                	</div>
    		              <!-- /.box-body -->
    		              	</div>
    		            </form>
    	            </div>
              	</div>
    			<div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('menu.close')}}</button>
                    <button type="button" class="btn btn-primary show_hide" onclick="split_segment();">{{trans('menu.save')}}</button>
              </div>
    		</div>
    	</div>
    </div>
@endsection

@push('script')
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/bootbox.min.js') }}" type="text/javascript"></script>

<script>
    function updateDataTableSelectAllCtrl(table){
        var $table             = table.table().node();
        var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
        var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    }

    function seg_name()
    {
        $('#users-table').DataTable().draw();
    }

    function route_search()
    {
        var route = $('#route').val();
        // $.ajax({
            // type: "GET",
            // url: '/get_data_header_branch_segment',
            // data: {route: route},
        // }).done(function( msg ) {
            // var route = $('#branch option');
            // route.remove();
            // if ($('#route').val() == 0)
            // {
                // $('#branch').append('<option value="0">{{trans("back_end.all")}}</option>')
            // }
            // for (i = 0; i < msg.length; i++ )
            // {
                // $('#branch').append('<option value="'+ msg[i]['id'] + '">' + msg[i]['name'] + '</option>')
            // }
            // $('#users-table').DataTable().draw();
        // });
        $('#users-table').DataTable().draw();
        
    }

    function branch_search()
    {
        $('#users-table').DataTable().draw();
    }

    function RMB_search()
    {
        var rmb = $('#RMB').val();
        $.ajax({
            type: "GET",
            url: '/get_data_header_sb_segment',
            data: {rmb: rmb},
        }).done(function( msg ) {
            var route = $('#SB option');
            route.remove();
            // if ($('#RMB').val() == 0)
            // {
                $('#SB').append('<option value="0">{{trans("back_end.all")}}</option>')
            //}
            for (i = 0; i < msg.length; i++ )
            {
                $('#SB').append('<option value="'+ msg[i]['id'] + '">' + msg[i]['name'] + '</option>')
            }
            $('#users-table').DataTable().draw();
        });
        // $('#users-table').DataTable().draw();
    }

    function SB_search()
    {
        $('#users-table').DataTable().draw();
    }

    function km_f()
    {
        $('#users-table').DataTable().draw();
    }

    function km_t()
    {
        $('#users-table').DataTable().draw();
    }

    $(function() {
        RMB_search();
    	// $('input').on("keypress", function(e) {
            // if (e.keyCode == 13) {
                // var inputs = $(this).parents("form").eq(0).find(":input");
                // var idx = inputs.index(this);
        // 
                // if (idx == inputs.length - 1) {
                    // inputs[0].select()
                // } else {
                    // inputs[idx + 1].focus();
                    // inputs[idx + 1].select();x`
                // }
                // return false;
            // }
        // });
		
		$('#point_split').keypress(function(event){
		    if (event.keyCode === 10 || event.keyCode === 13) 
		        event.preventDefault();
		});
    	
    	
    	// var flag = 0;
        var rows_selected = [];
        var x = {!! json_encode(App\Http\Controllers\Controller::GetConfigTable($case)) !!};
        var json = "[";
        x.forEach(function(object) {
            json = json + "{data: " + "'" + object.data + "'" + 
                ", name: " + "'" + object.name + "'" + 
                ",orderable: " + '"' + object.orderable + "'" +
                ",searchable: " + '"' + object.searchable + "'" +
                "},";
        });
        json = json + "]";
        var table = $('#users-table').DataTable({
            type: "GET",
            processing: false,
            serverSide: true,
            language: {
                "lengthMenu":"{{trans('segment.Show')}} _MENU_ {{trans('segment.entries')}}",
                search: "{{trans('segment.search')}}",
                sInfo: "{{trans('segment.entries_to_show ')}}(_START_ to _END_) {{trans('segment.got_a_total_of ')}} _TOTAL_ ",
                sEmptyTable: "{{trans('segment.no_data_available_in_table')}}",
                oPaginate: {
                    sPrevious: "{{trans('segment.previous_page')}}",
                    sNext: "{{trans('segement.next_page')}}"
                },
                sProcessing: "{{trans('segment.dataTables_is_currently_busy')}}"
            },
            ajax: {
                'url': '{!! route('get.ajax.table.data', array("name" => $case)) !!}',
                'data': function (d) {
                    d.segment_name = $('#seg_name').val();
                    d.route = $('#route').val();
                    d.branch = $('#branch').val();
                    d.rmb = $('#RMB').val();
                    d.sb = $('#SB').val();
                    d.km_f = $('#km_f').val();
                    d.km_t = $('#km_t').val();
                }
            },
            "bFilter" : false,
            columns: x,
            // 'columnDefs': [{
                // 'targets': 0,
                // 'searchable':false,
                // 'orderable':false,
                // 'width':'1%',
                // 'className': 'dt-body-center',
                // 'render': function (data, type, full, meta){
                // return '<input type="checkbox">';
                // }
            // }],
            initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var input = document.createElement("input");
                // console.log(input);
                $(input).appendTo($(column.footer()).empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? val : '', true, false).draw();
                });
            });
            },
            'rowCallback': function(row, data, dataIndex){
             // Get row ID
             var rowId = data[0];
             // If row ID is in the list of selected row IDs
             if($.inArray(rowId, rows_selected) !== -1){
                $(row).find('input[type="checkbox"]').prop('checked', true);
                $(row).addClass('selected');
             }
             }
        });
        // Handle click on checkbox
        
        $('#users-table tbody').on('click', 'tr', function(e) {
            var $row = $(this).closest('tr');
            // Get row data
            var data = table.row($row).data();
            // Get row ID
            $('input[name=km_from]').val(data['km_f']);
            $('input[name=km_to]').val(data['km_t']);
           	// this.flag++;
           	
            function a(data)
            {
                this.data = data;
            }
            var rowId = data[0];
            // Determine whether row ID is in the list of selected row IDs 
            var index = $.inArray(rowId, rows_selected);
            if(this.checked && index === -1){
                rows_selected.push(rowId);
            }
            $('#users-table tbody input[type="checkbox"]:checked').trigger('click');
            if(this){
            	// $('input[name=km_from]').val(from[0]);
				// $('input[name=km_to]').val(to[0]);		
            	
                $('tr').removeClass('selected');
                $(this).find('input[type="checkbox"]').trigger('click');
                $row.addClass('selected');
            } else {
                $row.removeClass('selected');
            }
    
            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(table);
    
            // Prevent click event from propagating to parent
            e.stopPropagation();
       });
       $('#users-table').on('click', 'tbody td, thead th:first-child', function(e){
          $(this).parent().find('input[type="checkbox"]').trigger('click');
       });
       
       // Handle click on "Select all" control
       // $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
       //    if(this.checked){
       //        $('#users-table tbody input[type="checkbox"]:not(:checked)').trigger('click');
       //        $('#users-table tbody input[type="checkbox"]:checked').trigger('click');
       //    } else {
       //        $('#users-table tbody input[type="checkbox"]:not(:checked)').trigger('click');
       //        $('#users-table tbody input[type="checkbox"]:checked').trigger('click');
       //    }
    
       //    // Prevent click event from propagating to parent
       //    e.stopPropagation();
       // });
       
       // Handle table draw event
       table.on('draw', function(){
          // Update state of "Select all" control
          updateDataTableSelectAllCtrl(table);
       });
       
        function getId()
        {
            var test = $('#users-table tbody .selected');
            var data = table.row(test).data();
            if (data == null)
            {
                return 0;
            }
          return data['id'];
        }
       
        $('#edit').on('click' , function (e) {
        var id = getId();
        if(id == 0)
        {
            bootbox.alert("{!!trans('back_end.must_choose_one_row')!!}", function(){ 
                // console.log('This was logged in the callback!');
                return true; 
            }).init(function(){
                @if (App::getLocale() == 'en')
                    $('.modal-footer .btn.btn-primary').text('Ok')
                @else
                    $('.modal-footer .btn.btn-primary').text('Đồng ý')
                @endif
            });
        }
        else
        {
            window.location.href = '/admin/manager_segment/' + id + '/edit';
        }
        });
      
        $('#delete').on('click' , function (e) {
            $('#delete').attr('disabled', 'disabled');
            var id = getId();
            $.ajax({
                type: "GET",
                url: '/check_segment_exist',
                data: {id: id},
            }).done(function( msg ) {
                console.log(msg);
                if (msg == '1')
                {
                    bootbox.alert("{!!trans('back_end.segment_information')!!}", function(){ 
                    jQuery("#delete").removeAttr("disabled");
                    return true; 
                    }).init(function(){
                        @if (App::getLocale() == 'en')
                            $('.modal-footer .btn.btn-primary').text('OK')
                        @else
                            $('.modal-footer .btn.btn-primary').text('Đồng ý')
                        @endif
                    });
                }
                else if(id == 0)
                {
                    bootbox.alert("{!!trans('back_end.must_choose_one_row')!!}", function(){ 
                    jQuery("#delete").removeAttr("disabled");
                    return true; 
                    }).init(function(){
                        @if (App::getLocale() == 'en')
                            $('.modal-footer .btn.btn-primary').text('OK')
                        @else
                            $('.modal-footer .btn.btn-primary').text('Đồng ý')
                        @endif
                    });
                }
                else
                {
                    @if (App::getLocale() == 'en')
                        var yes = 'Yes';
                        var no = 'No';
                    @else
                        var yes = 'Đồng ý';
                        var no = 'Không';
                    @endif
                    bootbox.confirm({
                        message: "{!!trans('back_end.sure_want_delete')!!}",
                        buttons: {
                            confirm: {
                                label: yes,
                                className: 'btn-success'
                            },
                            cancel: {
                                label: no,
                                className: 'btn-danger'
                            }
                        },
                        callback: function(result){
                        if(result)
                        {
                            window.location.href = '/admin/manager_segment/' + id + '/delete';
                        }
                        else
                        {
                            jQuery("#delete").removeAttr("disabled");
                            return true;
                        }
                    }});
                }
            });
    	});

    	data = get_info_segment();
    	$('.show_hide').hide();
    	$('#date_effected').datepicker({
			changeMonth : true,
			changeYear : true,
			autoclose : "true",
			format : "dd-mm-yyyy",
			// startDate : new Date(),
			onClose : function(dateText, inst) {
				$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
			}
		}).on('changeDate', function(e) {
	        $('#form').formValidation('revalidateField', 'date_effected');
	
	    });
    	var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1;
		
		var yyyy = today.getFullYear();
		if(dd<10){
		    dd ='0'+dd;
		} 
		if(mm<10){
		    mm ='0'+mm;
		}
		today = dd+'-'+mm+'-'+yyyy;
    	
		$('#point_split').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			fields : {
				km_from : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.data_required')}}"
						},
					}
				},
				
				km_to : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.data_required')}}"
						},
					}
				},
				
				km_mid : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.data_required')}}"
						},
						
						regexp: {
		                    regexp: /^([0-9]*[.])?[0-9]+$/i,
		                    message: "{{trans('validation.data_valid_format')}}"
		               	},
		               	
		               	between: {
	                        min: 'km_from',
	                        max: 'km_to',
	                        message: "{{trans('validation.data_valid_beetween')}}"
	                    }
					}
				},

				m_mid : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.data_required')}}"
						},
						
						regexp: {
		                    regexp: /^([0-9]*[.])?[0-9]+$/i,
		                    message: "{{trans('validation.data_valid_format')}}"
		               	},
		               	
		               	stringLength: {
	                        max: 4,
	                        message: "{{trans('validation.data_max_string')}}"
	                    }
					}
				},
			}
		});
		
		$('#form_segment_1').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			fields : {
				name_vi_segment_1 : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_vi_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_\.ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_vi_valid_format')}}"
		               	},
					}
				},

				name_en_segment_1 : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_en_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_\.ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_en_valid_format')}}"
		               	},
					}
				},
			}
		});
		
		$('#form_segment_2').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			fields : {
				name_vi_segment_2 : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_vi_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_\.ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_vi_valid_format')}}"
		               	},
					}
				},

				name_en_segment_2 : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_en_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_\.ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_en_valid_format')}}"
		               	},
					}
				},
			}
		});
		
		$('#form').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			fields : {
				date_effected : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.date_effected_required')}}"
						},
						
						date: {
		                    message: "{{trans('validation.date_effected_valid_format')}}",
		                    format: 'DD-MM-YYYY',
		                    // min: today,
		                },
					}
				}
			}
		})
	});
    
    function redirect(action) {
    	var segment_id = $('#users-table tbody .selected').closest("tr").find("td:nth-child(1)").text();
    	if (segment_id.trim() != '') {
    		location.href = "/segment/" + action + '/' + segment_id;	
    	} else {
    		 bootbox.alert("{{ trans('validation.please_choose_segemnt') }}");
    	}
    }
    
    
    function disable()
    {
        $('#delete').attr('disabled', 'disabled');
    }
    
    function get_info_segment() {
    	var data = [];
    	
		var info_from =  $('#users-table tbody .selected').closest("tr").find("td:nth-child(7)").text();
    	var from = info_from.split(' + ');
    	data['km_from'] = from[0];
    	data['m_from'] = from[1];
    	
    	var info_to =  $('#users-table tbody .selected').closest("tr").find("td:nth-child(8)").text();
		var to = info_to.split(' + ');
		data['km_to'] = to[0];
		data['m_to'] = to[1];
		
		return data;
    }
    
    function clear_input() {
    	$('#date_effected').val('');
    	$('input[name=name_vi_segment_1]').val('');
    	$('input[name=name_en_segment_1]').val('');
    	$('input[name=name_vi_segment_2]').val('');
    	$('input[name=name_en_segment_2]').val('');
    	
    	$('input[name=km_mid]').val('');
    	$('input[name=m_mid]').val('');
    	
    	$('#segment_1').html('');
		$('#segment_2').html('');
		
		$('.show_hide').hide();
		// if (this.flag > 1) {
			// $('#point_split').formValidation('revalidateField', 'km_mid');
	    	// $('#point_split').formValidation('revalidateField', 'm_mid');
	   	// }
    }
    
    function add_info_new_segment() {
    	// $('#point_split').formValidation('revalidateField', 'km_mid');
    	// $('#point_split').formValidation('revalidateField', 'm_mid');
    	
    	var fv = $('#point_split').data('formValidation'),
        $container = $('#point_split');

		fv.validateContainer($container);
		var isValid = fv.isValidContainer($container);
		
		if (isValid !== false && isValid !== null) {
			var km_point = $('input[name=km_mid]').val();
	    	var m_point = $('input[name=m_mid]').val();
	    	
	    	var info_from =  $('#users-table tbody .selected').closest("tr").find("td:nth-child(7)").text();
	    	var from = info_from.split(' + ');
	    	var text_from = from[0] + ' km ' + ' + ' + from[1] + ' m';
	    	
	    	var info_to =  $('#users-table tbody .selected').closest("tr").find("td:nth-child(8)").text();
			var to = info_to.split(' + ');
			var text_to = to[0] + ' km ' + ' + ' + to[1] + ' m';
			
			var text_point = km_point + ' km' + ' + ' + m_point + ' m';
			var text_segment_1 = "{{trans('segment.segment_1_has_chainage')}}" + ': <label><span> ' + text_from + ' </span> ' + "{{trans('segment.to')}}" + ' <span> ' + text_point + ' </span></label>';
			var text_segment_2 = "{{trans('segment.segment_2_has_chainage')}}" + ': <label><span> ' + text_point + ' </span> ' + "{{trans('segment.to')}}" + ' <span> ' + text_to + ' </span></label>';
			
			$('#segment_1').html(text_segment_1);
			$('#segment_2').html(text_segment_2);
			$('.show_hide').show(1000);
		}
    }
    
    function get_list_segment_split() {
		var segment = [];
    	var data = $('#users-table tbody .selected').closest("tr").find("td:nth-child(1)").each(function(i, selected) {
    		segment[i] = $(selected).text();
    	});
    	
    	return segment;
	}	
	
	function check_segemnt() {
		var segment = get_list_segment_split();
    	
    	if (segment.length < 1) {
    		bootbox.alert("{{ trans('validation.please_choose_segemnt') }}");
    	} else {
    		clear_input();
    		$("#myModal").modal('show');
    	}
	}
	
	function check_valid(array_selected) {
		var flag = true;
		$.each(array_selected, function(i, selected) {
			var fv = $(selected).data('formValidation'),
	        $container = $(selected);
	
			fv.validateContainer($container);
			var isValid = fv.isValidContainer($container);
			
			if (isValid === false || isValid === null) {
				flag = false;
				return false;
			}
    	});
    	
    	return flag;
	}
    function split_segment() {
    	var array_valid = ['#point_split', '#form_segment_1', '#form_segment_2', '#form'];
    	var valid = check_valid(array_valid);
    	
    	var flag = true;
    	$.each(array_valid, function(i, selected) {
			var fv = $(selected).data('formValidation'),
	        $container = $(selected);
	
			fv.validateContainer($container);
    	});
    	
		if (valid) {
    		$("#myModal").modal('hide');
    		bootbox.confirm({
		    	message: "{{trans('menu.are_you_sure_split_segment')}}",
		    	buttons: {
			        confirm: {
			            label: "{{trans('menu.save')}}",
			            className: 'btn-primary'
			        },
			        cancel: {
			        	label: "{{trans('menu.close')}}",
			            className: 'btn-default'
			            
			        }
			    },
		        callback: function(result) {
		        	var data = get_info_segment();
		        	
		        	var segment = $('#users-table tbody .selected').closest("tr").find("td:nth-child(1)").text();
		        	var date_effected = $('#date_effected').val();
		        	var name_vi_segment_1 = $('input[name=name_vi_segment_1]').val();
		        	var name_en_segment_1 = $('input[name=name_en_segment_1]').val();
		        	var name_vi_segment_2 = $('input[name=name_vi_segment_2]').val();
		        	var name_en_segment_2 = $('input[name=name_en_segment_2]').val();
		        	
			    	var km_mid = $('input[name=km_mid]').val();
			    	var m_mid = $('input[name=m_mid]').val();
			    	
		        	if (result == true) {
			        	$.post("{{route('segment.split')}}",
		   				{
		   					"segment_id": segment,
		   					"km_first": data['km_from'],
		   					"m_first": data['m_from'],
		   					'km_mid': km_mid,
		   					"m_mid": m_mid,
	   						'km_last': data['km_to'],
		   					"m_last": data['m_to'],
		   					"name_vi_segment_1": name_vi_segment_1,
		   					"name_en_segment_1": name_en_segment_1,
		   					"name_vi_segment_2": name_vi_segment_2,
		   					"name_en_segment_2": name_en_segment_2,
		   					"date_effected": date_effected,
		   				},
		   				
		       			function(data) {
		       				if (data.code == 200) {
		       					location.href = '/admin/manager_segment';
		       				} else {
		       					bootbox.alert(data.description);
		       				}
						}
						,"json" );
					} else {
						bootbox.hideAll();
						return false;
					}	
		        }
		    });
    	}
    }
</script>
@endpush