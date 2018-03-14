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

@section('contentheader_link')
    <li>
    	<a href="/manager_segment"><i class="fa fa-dashboard"></i>{{ trans('back_end.manager_segment') }}</a>
   	</li>
   	<li>
    	<a href="#" onclick="return false;">{{ trans('back_end.merge_segment') }}</a>
   	</li>
@endsection

@section('content')
 @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-ellipsis-h',
        'text1' => trans('menu.manager_segment'),
        'text2' => trans('back_end.merge_segment')
    ])
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
    
    table.dataTable thead .sorting_asc:after {
	    display:none !important;
	}
</style>
<div class="theme-showcase">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{$title}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-2" style="padding-left: 0px; padding-bottom: 10px;">
            	<button type="button" class="btn btn-block btn-primary" onclick="check_segemnt();">{{trans('back_end.merge_segment')}}</button>
            </div>
        </div>
    </div>
</div>


 <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans('back_end.list_segment_merge'))
                <div>
                    <div class="widget-body">
						<table class="table table-bordered" id="users-table">
			                <thead>
			                    <tr role='row'>
			                        <th rowspan="2" colspan="1" class="sorting_disabled"><input name="select_all" value="1" type="checkbox"></th>
			                        <!-- <th rowspan="2" colspan="1">#</th> -->
			                        <th rowspan="2"colspan="1" class="sorting_disabled">{{trans('back_end.id')}}</th>
			                        <th rowspan="2" class="sorting_disabled" tabindex="0" aria-controls="users-table" colspan="1" aria-sort="ascending">{{trans('back_end.name_segment')}}</th>
			                        <th colspan="2" rowspan="1" style="text-align: center" class="sorting_disabled">{{trans('back_end.chainage')}}</th>
			                    </tr>
			                    <tr role='row'>
			                        <th class="sorting_disabled" tabindex="0" aria-controls="users-table" rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.from')}} (km + m)</th>
			                        <th class="sorting_disabled" tabindex="0" aria-controls="users-table" rowspan="1" colspan="1" style="text-align: center">{{trans('back_end.to')}} (km + m)</th>
			                    </tr>
			                </thead>
			            </table>
			            <div class="widget-footer">
                            <!-- <div class="col-md-2" style="margin-right: 0px;">
				            	<button type="button" class="btn btn-block btn-primary" onclick="check_segemnt();">{{trans('back_end.merge_segment')}}</button>
				            </div> -->
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
			<div class="modal-body">
				<form role="form" id="form">
	              <div class="box-body">
	                <div class="form-group">
	                  <label for="exampleInputEmail1">{{ trans('segment.name_vi') }}</label>
	                  <input type="text" class="form-control" id="name_vi" name="name_vi" placeholder="{{ trans('segment.name_vi') }}">
	                </div>
	                
	                <div class="form-group">
	                  <label for="exampleInputEmail1">{{ trans('segment.name_en') }}</label>
	                  <input type="text" class="form-control" id="name_en" name="name_en" placeholder="{{ trans('segment.name_en') }}">
	                </div>
	                
	                <div class="form-group">
	                  <label for="exampleInputPassword1">{{ trans('segment.date_effected') }}</label>
	                  <input type="text" class="form-control" id="date_effected"  name="date_effected" placeholder="{{ trans('segment.date_effected') }}">
	                </div>
	              </div>
	            </form>
          	</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('menu.close')}}</button>
                <button type="button" class="btn btn-primary" onclick="merge_segment({{$branch_id.','.$sb_id}});">{{trans('menu.save')}}</button>
          </div>
		</div>
	</div>
</div>
@endsection
@push('script')
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/natural.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
<script src="{{ asset('/js/bootbox.min.js') }}" type="text/javascript"></script>
<script>
	function updateDataTableSelectAllCtrl(table) {
   		var $table             = table.table().node();
   		var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   		var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   		var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   		if($chkbox_checked.length === 0) {
      		chkbox_select_all.checked = false;
	     	if('indeterminate' in chkbox_select_all) {
	         	chkbox_select_all.indeterminate = false;
	      	}

   		} else if ($chkbox_checked.length === $chkbox_all.length) {
      		chkbox_select_all.checked = true;
		    if('indeterminate' in chkbox_select_all){
		        chkbox_select_all.indeterminate = false;
		    }

  		} else {
      		chkbox_select_all.checked = true;
      		if('indeterminate' in chkbox_select_all) {
         		chkbox_select_all.indeterminate = true;
      		}
   		}
	}
    
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
        	"bFilter": true,
            type: "GET",
            processing: true,
            language: {
			    search: "{{trans('segment.search')}}",
			    sInfo: "{{trans('segment.entries_to_show ')}} _TOTAL_ {{trans('segment.got_a_total_of ')}} (_START_ to _END_)",
			    sEmptyTable: "{{trans('segment.no_data_available_in_table')}}",
			    oPaginate: {
			        sPrevious: "{{trans('segment.previous_page')}}",
			        sNext: "{{trans('segement.next_page')}}"
			    },
			    sProcessing: "{{trans('segment.dataTables_is_currently_busy')}}"
			},
            paging: false,
            serverSide: true,
            ajax: '{!! route('get.ajax.table.data', array("name" => $case)) !!}?{{@$custom_branch}}&{{$custom_sb}}',
            columns: x,
            columnDefs: [
	            {
	                'targets': 0,
	                // 'searchable':false,
	                'orderable':false,
	                "bSortable": false,
	                'width':'1%',
	                'className': 'dt-body-center',
	                'render': function (data, type, full, meta) {
	                	return '<input type="checkbox">';
	                }
	            },
            ],
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
			    $row.addClass('selected');
			} else {
			    $row.removeClass('selected');
			}
			
			updateDataTableSelectAllCtrl(table);
		    e.stopPropagation();
		});
			
		$('#users-table').on('click', 'tbody td, thead th:first-child', function(e) {
			$(this).parent().find('input[type="checkbox"]').trigger('click');
		});
			
		$('thead input[name="select_all"]', table.table().container()).on('click', function(e) {
			if(this.checked) {
		    	$('#users-table tbody input[type="checkbox"]:not(:checked)').trigger('click');
		  	} else {
		     	$('#users-table tbody input[type="checkbox"]:checked').trigger('click');
		  	}
		
		    e.stopPropagation();
	   	});
			
		table.on('draw', function() {
	      	updateDataTableSelectAllCtrl(table);
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
	
	function get_list_segment_merge() {
		var segment = [];
    	var data = $('#users-table tbody .selected').closest("tr").find("td:nth-child(2)").each(function(i, selected) {
    		segment[i] = $(selected).text();
    	});
    	
    	return segment;
	}	
	
	function check_segemnt() {
		var segment = get_list_segment_merge();
    	
    	if (segment.length < 2) {
    		bootbox.dialog({
	  			message: "{{ trans('validation.please_choose_2_segemnt') }}",
	  			buttons: {
	    			cancel: {
	      				label: "{{trans('bootbox.ok')}}",
	      				className: "btn-primary",
	      				callback: function() {
	        				bootbox.hideAll();
							return false;
	      				}
	    			},
	  			}
			});
    	} else {
    		$("#myModal").modal('show');
    	}
	}
    
    function merge_segment(branch_id, sb_id) {
    	var segment = get_list_segment_merge();
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
			        	$.post("{{route('segment.merge')}}",
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
		       					location.href = '/admin/manager_segment';
		       				} else {
		       					bootbox.dialog({
						  			message: data.description,
						  			buttons: {
						    			cancel: {
						      				label: "{{trans('bootbox.ok')}}",
						      				className: "btn-primary",
						      				callback: function() {
						        				bootbox.hideAll();
												return false;
						      				}
						    			},
						  			}
								});
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