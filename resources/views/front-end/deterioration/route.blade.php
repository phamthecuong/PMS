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

@push('css')
    <style type="text/css">
        .performance_curve_legend ul {
            list-style: none;
            text-align: center;
        }
        .performance_curve_legend li {
            display: inline-block;
            padding: 0px 4px;
        }
        .performance_curve_legend li span {
            display: inline-block;
            background-color: green;
            height: 2px;
            margin-bottom: 4px;
            width: 15px;
            margin-right: 5px;
        }
    </style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-cube',
    'text1' => trans('deterioration.deterioration'),
    'text2' => trans('deterioration.route')
])

<section id="widget-grid" class="">
    <div class="row">
        <article class="col-sm-12 col-md-12 col-lg-5">
            <!-- Route -->
            @box_open(trans('deterioration.route'))
                <div>
                    <div class="widget-body">
                        <form class="form-horizontal">
                            <legend>{!!trans('deterioration.route')!!}</legend>
                            <div class="form-group">
                                <label class="col-md-3">{!!trans('deterioration.target_region')!!}</label>
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
                                <label class="col-md-3 padding-top-7">
                                    {!!trans('deterioration.distress_type')!!}
                                </label>
                                <div class="col-md-9">
                                    <select name="" id="distress_type" onchange="distress_change()" class="form-control">
                                        <option value="1">{{ trans('deterioration.cracking_ratio') }}</option>
                                        <option value="2">{{ trans('deterioration.rut') }}</option>
                                        <option value="3">{{ trans('deterioration.iri') }}</option>
                                    </select><i></i>
                                </div>
                            </div>
                            <legend>{!!trans('deterioration.estimation_result_route')!!}</legend>
                            <h3>{!! trans('deterioration.dispersion_parameter') !!}</h3>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    &Phi; = 
                                </label>
                                <div class="col-md-9">
                                    <input class="form-control" disabled="" id="muy" placeholder="{!!trans('deterioration.year_of_dataset')!!}" value="{{$muy}}" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    {!!trans('deterioration.log_likelohood')!!}=
                                </label>
                                <div class="col-md-9">
                                    <input class="form-control" id="log" value="{{$log}}" disabled="" placeholder="{!!trans('deterioration.log_likelohood')!!}" type="text">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @box_close
            <!-- end widget -->

            <div class="jarviswidget" id="wid-id-ac-bst" data-widget-sortable="false" data-widget-deletebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-custombutton="false">
                <header>
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>{!!trans('deterioration.ac-bst')!!}</h2>
                    <div class="widget-toolbar">
                        <div class="btn-group" style="color: red">
                            <select class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown" name="gender" style="width: 90px;" id="pavement_type" onchange="drawDataTable()">
                                <option value="ac">{!!trans('deterioration.ac')!!}</option>
                                <option value="bst">{!!trans('deterioration.bst')!!}</option>
                            </select>
                        </div>
                    </div>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="ast" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>
                                    <th data-hide="phone">{!!trans('deterioration.route')!!}</th>
                                    <th data-class="expand">{!!trans('deterioration.data_number')!!}</th>
                                    <th data-hide="phone">{!!trans('deterioration.epsilon')!!}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
        <article class="col-sm-12 col-md-12 col-lg-7">
            <div class="jarviswidget" id="wid-id-4" data-widget-sortable="false" data-widget-deletebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-custombutton="false">
                <header>
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>{!!trans('deterioration.performance')!!}</h2>
                    <div class="widget-toolbar">
                        <div class="btn-group" style="color: red">
                            <select class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown" name="gender" style="width: 200px;" id="route" onchange="change_data_chart()">
                                <option value="as_all">{!!trans('deterioration.ac_all')!!}</option>
                                <option value="bst_all">{!!trans('deterioration.bst_all')!!}</option>
                                @foreach($select_all as $s)
                                    @if (App::getLocale() == 'en')
                                        <option value="{{$s->id}}">{{$s->name_en}}</option>
                                    @else
                                        <option value="{{$s->id}}">{{$s->name_vn}}</option>
                                    @endif
                                @endforeach
                            </select></label>
                        </div>
                    </div>
                </header>
                <div class="widget-body">
                    <canvas id="canvas"></canvas>
                    <div id='performance_curve_legend' class='performance_curve_legend'></div>
                    <div class="widget-footer">
                        <a class="btn btn-danger" href="/user/deterioration/pavement_type/{{$deterioration->id}}">
                            {!!trans('deterioration.back_pt')!!}
                        </a>
                        <a class="btn btn-danger" id="save">
                            {!!trans('deterioration.export')!!}
                        </a>
                        <a class="btn btn-danger" href="/user/deterioration/section/{{$deterioration->id}}">
                            {!!trans('deterioration.next_100km')!!}
                        </a>
                    </div>
                </div>
            </div>
            <!-- BST -->
            <!--  -->
        </article>
    </div>
</section>
@endsection

@push('script')
    <script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.tableTools.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/sa/js/plugin/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/sa/js/plugin/datatable-responsive/datatables.responsive.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/front-end/chartjs/Chart.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/front-end/chartjs/utils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            distress_change();
        });
        var chart,
            ac_data,
            bst_data;
               
        $('#save').click(function(){
            var option = $('#distress_type').val();
            var id = "<?php echo  Request::segment(4) ?>";
            location.href = '/user/route/'+id+'/'+option;                
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

        $('#ast').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                "t"+
                "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth" : true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "preDrawCallback" : function() {
                if (!responsiveHelper_dt_basic) {
                    responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#ast'), breakpointDefinition);
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
                               
        function get_ajax_data()
        {
            $.ajax({
                type: "GET",
                url: '/user/get_data_chart_deterioration',
                data: {
                    select: $("#route").val(),
                    distress_type: '{{$deterioration->distress_type}}',
                    deterioration_id: '{{$id}}',
                    select_distress_type: $("#distress_type").val()
                }
            }).done(function(msg){
                switch (+$('#distress_type').val()) 
                {
                    case 1:
                        y_label = '{{ trans("deterioration.cracking_ratio") }}(%)';
                        break;
                    case 3:
                        y_label = '{{ trans("deterioration.iri") }}(mm/m)';
                        break;
                    case 2:
                        y_label = '{{ trans("deterioration.rut") }}(mm)';
                        break;
                    default: break;
                }
                var max_Ox = 0;
                var chart_data = [];
                var color = randomColor(msg.length);
                for (var i = 0; i < msg.length; i++) 
                {
                    if (msg[i][msg[i].length-1]['name'] != null)
                    {
                        var array_persen = [];
        
                        if ($("#distress_type").val() == '1')
                        {
                            @foreach($distress_type_select['crack'] as $d)
                                var array = '{{@$d["from"]}}';
                                array_persen.push(array);
                            @endforeach
                        }
                        else if ($("#distress_type").val() == '3')
                        {
                            @foreach($distress_type_select['iri'] as $d)
                                var array = '{{@$d["from"]}}';
                                array_persen.push(array);
                            @endforeach
                        }
                        else
                        {
                            @foreach($distress_type_select['rut'] as $d)
                                var array = '{{@$d["from"]}}';
                                array_persen.push(array);
                            @endforeach
                        }
                        var array = [];
                        var cache = [];

                        for (var j = 0; j < msg[i].length; j++)
                        {
                            var t = msg[i][j];

                            // if (msg[i][msg[i].length-1]['name'] == 'BM_AC' || msg[i][msg[i].length-1]['name'] == 'BM_BST'|| msg[i][msg[i].length-1]['name'] == 'BM_LN'|| msg[i][msg[i].length-1]['name'] == 'BM_BTN') {
                                if (t > max_Ox) {
                                    max_Ox = t;
                                }
                            // }

                            var data_cache = {
                                x: t,
                                y: -array_persen[j],
                            };
                            
                            cache.push(data_cache);
                        }
                        var ta = [];
                        for (var k = 0; k < cache.length; k++)
                        {
                            ta.push(cache[k]);
                        }
                        var datas = {
                            label: msg[i][msg[i].length-1]['name'],
                            // lineTension: 0,
                            // pointBorderWidth: 0,
                            pointRadius: 2,
                            // pointHitRadius: 0,
                            borderWidth: 2,
                            backgroundColor: color[i],
                            borderColor: color[i],
                            data: ta,
                            fill: false,
                        };

                        chart_data.push(datas);
                    }
                }
                
                var config = {
                    type: 'line',
                    data: {
                        // labels: ["January", "February", "March", "April", "May", "June", "July"],
                        datasets: chart_data
                    },
                    options: {
                        responsive: true,
                        title:{
                            display:true,
                            // text:'{{trans('deterioration.chart')}}'
                        },
                        legend: {
                            // position: 'bottom',
                            display: false
                        },
                        tooltips: {
                            // mode: 'label',
                            intersect: true,

                            callbacks: {
                                label: function(tooltipItem, data) {
                                    // console.log(tooltipItem);
                                    // return -Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
                                    //     return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                                    // });
                                    // console.log(data.datasets[1].label);
                                    var i, label = '', l = data.datasets.length;
                                    for (i = 0; i < l; i += 1) {
                                        if (i == tooltipItem['datasetIndex'])
                                        {
                                            // label = data.datasets[i].label + ' : ' + -Number(tooltipItem.xLabel).toFixed(0);
                                            label = data.datasets[i].label + ' : ' + tooltipItem.xLabel;
                                            // label = data.datasets[i].label + 1;
                                        }
                                    }
                                    return label;
                                },
                                // title: function (tooltipItem, data) { return data.labels[tooltipItem[0].index]; },
                            }
                        },
                        // hover: {
                        //     mode: 'nearest',
                        //     intersect: true
                        // },
                        scales: {
                            xAxes: [{
                                display: true,
                                type: 'linear',
                                position: 'top',
                                scaleLabel: {
                                    display: true,
                                    labelString: '{{trans("deterioration.year")}}'
                                },
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: round5(0.2*(max_Ox+5)),
                                    max: round5(max_Ox + 5),
                                    userCallback: function(tick) {
                                        // var remain = tick / (Math.pow(10, Math.floor(Chart.helpers.log10(tick))));
                                        // if (remain === 1 || remain === 2 || remain === 5) {
                                            // return tick.toString() + "Year";
                                        // }
                                        return tick;
                                    },
                                    
                                }
                            }],
                            yAxes: [{
                                display: true,
                                type: 'linear',
                                // position: 'top',
                                scaleLabel: {
                                    display: true,
                                    labelString: y_label
                                },
                                
                                ticks: {
                                    userCallback: function(tick) {
                                        // return -tick.toString() + "%";
                                        if (tick != 0)
                                        {
                                            return -tick.toString();
                                        }
                                        else
                                        {
                                            return " ";
                                        }
                                    },
                                },
                            }]
                        }
                    }
                }
            
                var ctx = document.getElementById("canvas").getContext("2d");
                ctx.canvas.height = 200;
                if (chart != null)
                {
                    chart.destroy();
                }
                chart = new Chart(ctx, config);
                document.getElementById('performance_curve_legend').innerHTML = chart.generateLegend();
            });
        }
            
        // chart
        function change_data_chart()
        {
            get_ajax_data();
        }

        function distress_change()
        {
            // chart.destroy();
            $.ajax({
                type: 'GET',
                url : '/user/get_data_chart_deterioration_with_distress',
                data: {
                    select_distress_type: $("#distress_type").val(),
                    deterioration: '{{$deterioration->id}}'
                },
            }).done(function(msg){
                $("#muy").val(msg[0]);
                $("#log").val(msg[1]);
                $("#route option").remove();

                ac_data = msg[2];
                bst_data = msg[3];
                drawDataTable();
                
                $("#route").append("<option value='as_all'>" + '{!!trans('deterioration.ac_all')!!}' + "</option>");
                $("#route").append("<option value='bst_all'>" + '{!!trans('deterioration.bst_all')!!}' +"</option>");
                
                for (var i = 0; i< msg[4].length; i++)
                {
                    if (msg[4][i]['name_vn'] != null)
                    {
                        @if (App::getLocale() == 'en')
                            $("#route").append("<option value='" + msg[4][i]['id'] + "'>" + msg[4][i]['name_en'] + "</option>");
                        @else
                            $("#route").append("<option value='" + msg[4][i]['id'] + "'>" + msg[4][i]['name_vn'] + "</option>");
                        @endif
                    }
                }
                
                get_ajax_data();
            });   
        }

        function drawDataTable() {
            var ast_table = $('#ast').DataTable();
            ast_table.order( [ 1, 'desc' ]);
            ast_table.rows().remove().draw();
            var data;
            if ($('#pavement_type').val() == 'ac') {
                data = ac_data;
            } else {
                data = bst_data;
            }

            for (var i = 0; i < data.length; i++) {
                if (data[i][0] != null) {
                    ast_table.row.add([data[i][0], data[i][1], data[i][2]]).draw();
                }
            }
        }

        function round5(x) {
            return Math.ceil(x/5)*5;
        }

    </script>

@endpush
