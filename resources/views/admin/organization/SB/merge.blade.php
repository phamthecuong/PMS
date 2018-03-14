@extends('admin.layouts.app_datatable')
@section('contentheader_title')
    {{trans('back_end.auth_organization')}}
@endsection

@section('contentheader_link')
    <li><a href="/SB"><i class="fa fa-dashboard"></i> {{ trans('back_end.auth_DB') }}</a></li>
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
</style>
<div class="theme-showcase">
    
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('back_end.merge_SB')}}</h3>
            <!-- name select SB = SB -->
        </div>
        <div class="box-body">
            <div class="col-md-2" style="padding-left: 0px;">
            	<button type="button" class="btn btn-block btn-primary" onclick="open_form();">{{trans('back_end.merge_SB')}}</button>
            </div>
        </div>
        <div class="box-body" style="overflow: auto">
            <table class="table table-bordered" id="users-table">
                <thead>
                    <tr role='row'>{!! csrf_field() !!}
                        <th rowspan="1" colspan="1"><input name="select_all" value="1" type="checkbox"></th>
                        <th rowspan="1" colspan="1">{{trans('back_end.name')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="box-footer">

        </div>
    </div>
</div>
<!--  -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center"> {{trans('segment.info_organization')}} </h4>
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
                <button type="button" class="btn btn-primary" onclick="merge({{ $RMD }});">{{trans('menu.save')}}</button>
          </div>
		</div>
	</div>
</div>
<!-- Bootstrap 3.3.4 -->
<link href="/css/bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Font Awesome Icons -->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!-- Ionicons -->
<!-- <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" /> -->

<!-- DataTables -->
<link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css" type="text/css">
<!-- iCheck -->
<link href="/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
<!-- datepicker -->
<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
<!-- icheck -->
<link href="/plugins/iCheck/all.css" rel="stylesheet" type="text/css" />


<!-- Select2 -->
<link rel="stylesheet" href="/plugins/select2/select2.min.css" type="text/css">
<!-- AdminLTE Skins. We have chosen the skin-blue for this starter
page. However, you can choose any other skin. Make sure you
apply the skin class to the body tag so the changes take effect.
-->
<link href="/css/skins/skin-blue.css" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="/css/AdminLTE.css" rel="stylesheet" type="text/css" />

<!-- alret lưu  -->
<link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css" type="text/css">
<!-- iCheck -->
<link href="/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
<!-- datepicker -->
<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
<!-- icheck -->
<link href="/plugins/iCheck/all.css" rel="stylesheet" type="text/css" />


<!-- end -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/js/bootstrap.min.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="/js/app.min.js" type="text/javascript"></script>
<!-- DataTables -->
<script src="/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<!-- Select2 -->
<script src="/plugins/select2/select2.full.min.js" type="text/javascript"></script>
<!-- Bootbox -->
<script src="/plugins/bootbox/js/bootbox.min.js" type="text/javascript"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script> -->
<!-- check-box -->
<script src="/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js" /></script> -->
<!-- date picker -->
<script src="/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<!-- validate -->
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/natural.js" type="text/javascript"></script>
<script type="text/javascript">

/*function  updateDataTableSelectAllCtrl*/
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
			if('indeterminate' in chkbox_select_all){
	 		chkbox_select_all.indeterminate = true;
			}
		}
	}
/*end updateDataTableSelectAllCtrl*/
	// console.log({{@$custom}}+'ddd');
/* begin validate*/
	$(document).ready(function() {


		$("tr td:contains('{{$SB}}')" )
		.closest("tr")
		.find('input[type="checkbox"]')
		.attr('checked', true);

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
/*end validate*/
	

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
            ajax: '{!! route('get.ajax.table.data', array("name" => $case))!!}?{{@$custom}}',
            columns: x,
            'columnDefs': [{
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'width':'1%',
                'className': 'dt-body-center',
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
        // $('#users-table').on( 'draw.dt', function () {
		    // $("tr td:contains('{{$SB}}')" )
			// .closest("tr")
			// .find('input[type="checkbox"]')
			// .attr('checked', true);
// 
		// });
        $('#users-table tbody').on('click', 'input[type="checkbox"]', function(e) {

        	console.log(2);
        	// la
			var $row = $(this).closest('tr');
			// console.log($row);
			var data = table.row($row).data();
			// console.log(data);
			var rowId = data['id'];
			// consolog
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
		
		function open_form() {
			var segment = [];
	    	var data = $('#users-table tbody .selected').closest("tr").find("td:nth-child(2)").each(function(i, selected) {
	    		segment[i] = $(selected).text();
	    	});
	    	
	    	if (segment.length < 2) {
	    		// $("#myModal").modal('hide');
	    		bootbox.alert("{{ trans('validation.please_choose_2_sb') }}");
	    	} else {
				$("#myModal").modal('show');
			}
		}

		function merge(RMD) {
			// console.log('ngocduc1');
	    	var segment = [];
	    	var data = $('#users-table tbody .selected').closest("tr").find("td:nth-child(2)").each(function(i, selected) {
	    		segment[i] = $(selected).text();
	    	});
	    	
    		var fv = $('#form').data('formValidation'),
	        $container = $('#form');
			// console.log($container);
			fv.validateContainer($container);
			var isValid = fv.isValidContainer($container);
			// console.log(isValid);return;
    		if (isValid !== false && isValid !== null) {
    			// console.log('ngocduc3');return;
	    		$("#myModal").modal('hide');
	    		bootbox.confirm({
			    	message: "{{trans('menu.are_you_sure_merge_organization')}}",
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
				        	$.post("{{route('SB.Merge')}}",
			   				{
			   					"segment": segment,
			   					"RMD_id": RMD,
			   					"name_en": name_en,
			   					"name_vi": name_vi,
			   					"date_effected": date_effected
			   				},
			   				
			       			function(data) {
			       				if (data.code == 200) {
			       					// alert('dfgsdfh');
			       					// bootbox.alert(data.description);
			       					location.href = '/SB/' + data.RMB_id +'?name=' + name_vi ;
			       				} else {
			       					bootbox.alert(data.description);
			       				}
							}
							,"json" );
							// console.log(1111111111111111);
						} else {
							// console.log("dmmmm");
							bootbox.hideAll();
							return false;
						}	
			        }
			    });
	    	}
	    	// else{cosole.log("taisao");
	    	// }
    	// }
    }
</script>
@endsection
@section('js_extend')
<script >
	// console.log(12);
</script>
@endsection