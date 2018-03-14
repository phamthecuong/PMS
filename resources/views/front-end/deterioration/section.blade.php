@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection

@if (\Session::has("history-" . $deterioration->id))
    @section('deterioration_show_history')
    active
    @endsection
@else
    @section('deterioration_new_process')
    active
    @endsection
@endif

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('deterioration.home')}}</li>
        <li>{{trans('deterioration.deterioration')}}</li>
        @if (\Session::has("history-" . $deterioration->id))
            <li>{{ trans('menu.det_show_history') }}</li>
        @else
            <li>{{ trans('menu.start_process_deterioration') }}</li>
        @endif
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-cube',
    'text1' => trans('deterioration.deterioration'),
    'text2' => trans('deterioration.section')
])

<section id="widget-grid" class="">
    <div class="row">
        <article class="col-sm-12 col-md-12 col-lg-6">
            @box_open(trans('deterioration.section'))
            <div>
                <div class="widget-body">
                    <form class="form-horizontal">
                        <legend>{!!trans('deterioration.route')!!}</legend>
                        <div class="form-group">
                            <label class="col-md-3">
                                {!!trans('deterioration.target_region')!!}
                            </label>
                            <div class="col-md-9">
                                @if (App::getLocale() == 'en')
                                    <?= @\App\Models\tblOrganization::find(@$deterioration->organization_id)->name_en ?>
                                @else
                                    <?= @\App\Models\tblOrganization::find(@$deterioration->organization_id)->name_vn ?>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3">{!!trans('deterioration.year_of_dataset')!!}</label>
                            <div class="col-md-9">
                                {{$deterioration->year_of_dataset}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 padding-top-7">{!!trans('deterioration.distress_type')!!}</label>
                            <div class="col-md-9">                
                                <select name="" id="distress_type" onchange="distress_change()" class="form-control">
                                    <option value="1">{{ trans('deterioration.cracking_ratio') }}</option>
                                    <option value="2">{{ trans('deterioration.rut') }}</option>
                                    <option value="3">{{ trans('deterioration.iri') }}</option>
                                </select><i></i>
                            </div>
                        </div>
                        <legend>{!!trans('deterioration.estimation_result_section')!!}</legend>
                        <h3>{!! trans('deterioration.dispersion_parameter') !!}</h3>
                            
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                &Phi; = 
                            </label>
                            <div class="col-md-9">
                                <input class="form-control" id="muy" placeholder="{!!trans('deterioration.year_of_dataset')!!}" value="{{@$muy}}" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{!!trans('deterioration.log_likelohood')!!}=
                            </label>
                            <div class="col-md-9">
                                <input class="form-control" id="log" value="{{@$log}}" placeholder="{!!trans('deterioration.log_likelohood')!!}" type="text">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @box_close
        </article>
        <article class="col-sm-12 col-md-12 col-lg-6">
            <!-- AS -->
            <div class="jarviswidget" id="wid-id-4" data-widget-sortable="false" data-widget-deletebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-custombutton="false">
                <header>
                    <h2></h2>
                    <div class="widget-toolbar">
                        <div class="btn-group" style="color: red">
                            <select class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown" name="gender" style="width: 90px;" id="route" onchange="distress_change()">
                                <option value="0">{!!trans('deterioration.ac')!!}</option>
                                <option value="1">{!!trans('deterioration.bst')!!}</option>
                            </select>
                        </div>
                    </div>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        
                    	<table id="ast_bst" class="table table-striped table-bordered table-hover" width="100%">
                    		<thead>                         
                                <tr>
                                	<th>{!!trans('deterioration.region')!!}</th>
                                    <th>{!!trans('deterioration.route')!!}</th>
                                    <th>{!!trans('deterioration.right/left')!!}</th>
                                    <th>{!!trans('deterioration.km')!!}</th>
                                    <th>{!!trans('deterioration.data_number')!!}</th>
                                    <th>{!!trans('deterioration.epsilon')!!}</th>
                                </tr>
                            </thead>
                            <tbody>
                    			
                            </tbody>
                    	</table>
                        <div class="widget-footer">
                            <a class="btn btn-danger" href="/user/deterioration/route/{{$deterioration->id}}">
                                {!!trans('deterioration.back_route')!!}
                            </a>
                            <a class="btn btn-danger" id="save">{!!trans('deterioration.save')!!}</a>
                            <a class="btn btn-danger" onclick="ExportDBAdmin()" id="export_to_admin_DB"> 
                                {!!trans('deterioration.export_to_admin_DB')!!}
                            </a>
                            <a class="btn btn-danger" href="/">{!!trans('deterioration.exit')!!}</a>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>
<style type="text/css">
    
</style>
@endsection
@section('extend_js')
<script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.colVis.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.tableTools.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/sa/js/plugin/datatable-responsive/datatables.responsive.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/front-end/chartjs/Chart.bundle.js') }}"></script>
<script type="text/javascript" src="{{ asset('/front-end/chartjs/utils.js') }}"></script>
<!-- Bootbox -->
<script src="{{ asset('/plugins/bootbox/js/bootbox.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    $( document ).ready(function() {
        distress_change();
    });
    //click save export excel
    $('#save').click(function(){
        var option = $('#distress_type').val();
        var id = "<?php echo  Request::segment(4) ?>";
        //var id = '10896bf5-b9d4-4555-a2a4-81c9008b3ebb';
        location.href = '/user/section/'+id+'/'+option;
            
    });

	pageSetUp();
	var responsiveHelper_dt_basic = undefined;
    var responsiveHelper_datatable_fixed_column = undefined;
    var responsiveHelper_datatable_col_reorder = undefined;
    var responsiveHelper_datatable_tabletools = undefined;
    var breakpointDefinition = {
        tablet : 1024,
        phone : 480
    };
    $.fn.DataTable.ext.pager.numbers_length = 5;

    var t = $('#ast_bst').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                "t"+
                "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth" : true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "preDrawCallback" : function() {
                if (!responsiveHelper_dt_basic) {
                    responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#ast_bst'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_dt_basic.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_basic.respond();
            },
            "oLanguage": 
            {
                "oPaginate": 
                {
                    "sNext": '>',
                    "sLast": '>>',
                    "sFirst": '<<',
                    "sPrevious": '<'
                },
                sSearch: '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>',
                @if (App::getLocale() != 'en')
                sProcessing: "Đang xử lý...",
                sLengthMenu: "Xem _MENU_ mục",
                sZeroRecords: "Không tìm thấy dòng nào phù hợp",
                sInfo: "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                sInfoEmpty: "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)",
                sInfoPostFix: "",
                sSearch: "Tìm:",
                sUrl: "",
                buttons: {
                    colvis: 'Cột hiển thị',
                    copy: 'Sao chép'
                }
                @endif
            }
        });

    function distress_change() {
        showLoading();
    	var table = $('#ast_bst').DataTable();
    	$.ajax({
    		type:'GET',
    		url: '/user/get_data_table_section',
    		data: {
    			route: $("#route").val(),
    			distress: $("#distress_type").val(),
                deterioration:'{{$deterioration->id}}'
    		}
    	}).done(function(msg){
    		var table = $('#ast_bst').DataTable();
    		var rows = table
            .rows()
            .remove()
            .draw();
    		for (var i = 0; i < msg.length; i++) 
    		{
    			table.row.add([
    				msg[i][0],
    				msg[i][1],
    				msg[i][2],
    				msg[i][3],
    				msg[i][4],
    				msg[i][5]
				]);
    		}
            table
            .rows()
            .draw();
            hideLoading();
    	});
    }

    // Export to admin DB
    function ExportDBAdmin() {
        $.ajax({
            type: 'GET',
            url: '/user/export_to_admin_db',
            data: {
                id: '{{$deterioration->id}}'
            }
        }).done(function (msg) {
            if (msg == 'success')
            {
                bootbox.alert("{{ trans('validation.export_to_admin_db_success') }}");
            }
        });
    }
</script>
@endsection