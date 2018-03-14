@extends('admin.layouts.app')
@section('contentheader_title')
    {{trans('back_end.manager_segment')}}
@endsection

@section('contentheader_link')
    <li>
    	<a href="/manager_segment"><i class="fa fa-dashboard"></i>{{ trans('back_end.manager_segment') }}</a>
   	</li>
   	<li>
    	<a href="#" onclick="return false;">{{ trans('back_end.merge') }}</a>
   	</li>
@endsection

@section('main-content')
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
    
    .box {
    	box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.1);
    	margin-bottom: 0px
    }
    
    .boxbody {
    	padding: 5px !important;
    }
    
    .form-group {
    	padding: 5px !important;
    }
    
    label {
	     margin-bottom: 0px; 
	}
</style>
<div class="theme-showcase">
    
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{$title}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-2" style="padding-left: 0px;">
            	<button type="button" class="btn btn-block btn-primary" onclick="check_segemnt();">{{trans('back_end.merge')}}</button>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered" id="users-table">
                <thead>
                    <tr role='row'>
                        <th rowspan="2" colspan="1"></th>
                        <th rowspan="2"colspan="1">{{trans('back_end.id')}}</th>
                        <th rowspan="2" class="sorting_asc" tabindex="0" aria-controls="users-table" colspan="1" aria-sort="ascending">{{trans('back_end.name_segment')}}</th>
                        <th colspan="2" rowspan="1" style="text-align: center">{{trans('back_end.chainage')}}</th>
                    </tr>
                    <tr role='row'>
                        <th class="sorting" tabindex="0" aria-controls="users-table" rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.from')}} (km + m)</th>
                        <th class="sorting" tabindex="0" aria-controls="users-table" rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.to')}} (km + m)</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="box-footer">

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center"> {{trans('segment.info_segment')}} </h4>
			</div>
			<div class="modal-body row">
				<div class="row">
					<div class="col-md-6">
						<div class="box box-info">
							<div class="box-header">
								<h3 class="box-title">{{trans('segment.sub_segment_1')}}</h3>
							</div>
							
							<div class="boxbody">
								<form class="form-inline">
									<div class="form-group">
										<div class="col-md-4">
								    		<label>{{trans('segment.name_en')}} :</label>
								    	</div>
								    	<div class="col-md-8">
								    		<input type="text" class="form-control info" name="name_en_first" style="width: 100%;">
								    	</div>
								  	</div>
								  	
								  	<div class="form-group">
								  		<div class="col-md-4">
											<label>{{trans('segment.name_vi')}} :</label>
										</div>
										<div class="col-md-8">
											<input type="text" class="form-control info" name="name_vi_first" style="width: 100%;">
										</div>
								  	</div>
								  	
								  	<div class="form-group">
								  		<div class="col-md-2">
								  			<label>{{trans('back_end.from')}} :</label>	
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="km_from_first">
											<label>Km</label>
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="m_from_first">							    	
								    		<label>m</label>
								  		</div>
								  	</div>
								  	<br />
									<div class="form-group">
										<div class="col-md-2">
								  			<label>{{trans('back_end.to')}} :</label>	
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="km_to_first">
											<label>Km</label>
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="m_to_first">							    	
								    		<label>m</label>
								  		</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="box box-info">
							<div class="box-header">
								<h3 class="box-title">{{trans('segment.sub_segment_2')}}</h3>
							</div>
							
							<div class="boxbody">
								<form class="form-inline">
									<div class="form-group">
										<div class="col-md-4">
								    		<label>{{trans('segment.name_en')}} :</label>
								    	</div>
								    	<div class="col-md-8">
								    		<input type="text" class="form-control info" name="name_en_last" style="width: 100%;">
								    	</div>
								  	</div>
								  	
								  	<div class="form-group">
								  		<div class="col-md-4">
											<label>{{trans('segment.name_vi')}} :</label>
										</div>
										<div class="col-md-8">
											<input type="text" class="form-control info" name="name_vi_last" style="width: 100%;">
										</div>
								  	</div>
								  	
								  	<div class="form-group">
								  		<div class="col-md-2">
								  			<label>{{trans('back_end.from')}} :</label>	
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="km_from_last">
											<label>Km</label>
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="m_from_last">							    	
								    		<label>m</label>
								  		</div>
								  	</div>
								  	<br />
									<div class="form-group">
										<div class="col-md-2">
								  			<label>{{trans('back_end.to')}} :</label>	
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="km_to_last">
											<label>Km</label>
								  		</div>
								  		
								  		<div class="col-md-5">
								  			<input type="text" class="form-control text_number" name="m_to_last">							    	
								    		<label>m</label>
								  		</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-4">
						<div class="form-group">
			                <label>{{ trans('segment.date_effected') }}:</label>
			                <div class="input-group date">
			                  	<div class="input-group-addon">
			                    	<i class="fa fa-calendar"></i>
			                  	</div>
			                  	<input type="text" class="form-control pull-right" id="datepicker">
			                </div>
			             </div>
					</div>
		            <div class="col-md-7"></div>
				</div>
          	</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('menu.close')}}</button>
                <button type="button" class="btn btn-primary" onclick="split_segment({{$branch_id.','.$sb_id}});">{{trans('menu.save')}}</button>
          </div>
		</div>
	</div>
</div>
@endsection
@section('js_extend')
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/natural.js" type="text/javascript"></script>
<script src="{{ asset('/js/bootbox.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
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
    	
		$('#form').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			icon : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
			fields : {
				name_vi : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_vi_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_vi_valid_format')}}"
		               	},
					}
				},

				name_en : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_en_required')}}"
						},
						
						regexp: {
		                    regexp: /^[a-z0-9A-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
		                    message: "{{trans('validation.name_en_valid_format')}}"
		               	},
					}
				},
				
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
    
    $(function() {
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
            processing: true,
            serverSide: true,
            ajax: '{!! route('get.ajax.table.data', array("name" => $case)) !!}?{{@$custom_branch}}&{{$custom_sb}}',
            columns: x,
            columnDefs: [{
                'type': 'natural-asc', 
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'width':'1%',
                'className': 'dt-body-center',
                "aaSorting": [[ 3, "esc" ]],
                'render': function (data, type, full, meta) {
                	return '<input type="checkbox">';
                }
            }],
            'rowCallback': function(row, data, dataIndex) {
	            var rowId = data['id'];
	            if($.inArray(rowId, rows_selected) !== -1) {
	                $(row).find('input[type="checkbox"]').prop('checked', true);
	                $(row).addClass('selected');
		        }
            }
        });
        
        $('#users-table tbody').on('click', 'input[type="checkbox"]', function(e) {
			var $row = $(this).closest('tr');
			var data = table.row($row).data();
			var rowId = data['id'];
			var index = $.inArray(rowId, rows_selected);
			
			if(this.checked && index === -1) {
				rows_selected.push(rowId);
			} else if (!this.checked && index !== -1) {
			    rows_selected.splice(index, 1);
			}
			
			if(this.checked) {
				$('tr').removeClass('selected');
				$('#users-table tbody input[type="checkbox"]:checked').trigger('click');
                $(this).find('input[type="checkbox"]').trigger('click');
			    $row.addClass('selected');
			} else {
			    $row.removeClass('selected');
			}
		});
			
		$('#users-table').on('click', 'tbody td, thead th:first-child', function(e) {
			$(this).parent().find('input[type="checkbox"]').trigger('click');
		});
		
		
		$('#frm-users-table').on('submit', function(e) {
			var form = this;
			$.each(rows_selected, function(index, rowId) {
				$(form).append(
			    	$('<input>')
			            .attr('type', 'hidden')
			            .attr('name', 'id[]')
			            .val(rowId)
			     );
			});
		});
	});
	
	function get_list_segment_split() {
		var segment = [];
    	var data = $('#users-table tbody .selected').closest("tr").find("td:nth-child(2)").each(function(i, selected) {
    		segment[i] = $(selected).text();
    	});
    	
    	return segment;
	}	
	
	function check_segemnt() {
		var segment = get_list_segment_split();
    	
    	if (segment.length < 1) {
    		bootbox.alert("{{ trans('validation.please_choose_segemnt') }}");
    	} else {
    		$("#myModal").modal('show');
    	}
	}
    
    function split_segment(branch_id, sb_id) {
    	var segment = get_list_segment_split();
		var fv = $('#form').data('formValidation'),
        $container = $('#form');

		fv.validateContainer($container);
		var isValid = fv.isValidContainer($container);
		
		if (isValid !== false && isValid !== null) {
    		$("#myModal").modal('hide');
    		bootbox.confirm({
		    	message: "{{trans('menu.are_you_sure')}}",
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
		        	var date_effected = $('#date_effected').val();
		        	var name_vi = $('#name_vi').val();
		        	var name_en = $('#name_en').val();
		        	
		        	if (result == true) {
			        	$.post("{{route('segment.split')}}",
		   				{
		   					"segment": segment,
		   					"branch_id": branch_id,
		   					"sb_id": sb_id,
		   					'name_en': name_en,
		   					"name_vi": name_vi,
		   					"date_effected": date_effected
		   				},
		   				
		       			function(data) {
		       				if (data.code == 200) {
		       					location.href = '/segment/split/' + data.segment_id + '?success=true';
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
@endsection