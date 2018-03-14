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
	    #matrix{
	        border: none;

	    }
	    #matrix tbody tr td{
	        border-top-style: none;
	        border-right-style: none;
	        border-bottom-style: none;
	        border-left-style: none;
	        background: #ffffff;
	    }
	    #matrix tbody{
	        /*border-top-style: groove;*/
	        border-right-style: groove;
	        /*border-bottom-style: groove;*/
	        border-left-style: groove;
	    }
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
	'text2' => trans('deterioration.pavement')
])

<section id="widget-grid" class="">
    <div class="row">
    	<article class="col-sm-12 col-md-12 col-lg-5">
    		@box_open(trans('deterioration.deterioration_evaluation_pavement'))
				<div>
	                <div class="widget-body">
	                	<form class="form-horizontal">
	                        <legend>{!!trans('deterioration.deterioration_evaluation_pavement')!!}</legend>
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
	                            <label class="col-md-3">
	                            	{!!trans('deterioration.year_of_dataset')!!}
	                            </label>
	                            <div class="col-md-9">
	                                <p>{{@$deterioration->year_of_dataset}}</p>
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
	                        <legend>{!!trans('deterioration.estimation_result_payvement')!!}</legend>
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
	                            <label class="col-md-3 control-label">{!!trans('deterioration.log_likelohood')!!}=
	                            </label>
	                            <div class="col-md-9">
	                                <input class="form-control" id="log" value="{{$log}}" disabled="" placeholder="{!!trans('deterioration.log_likelohood')!!}" type="text">
	                            </div>
	                        </div>
	                        <h3>{!! trans('deterioration.heterogeneity_parameter') !!}</h3>
	                        <div class="form-group">
	                            <label class="col-md-3 control-label">
                                    AC = 
                                </label>
	                            <div class="col-md-9">
	                                <input class="form-control" disabled="" id="as" placeholder="{!!trans('deterioration.year_of_dataset')!!}" value="{{trim($as)}}" type="text">
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-md-3 control-label">BST =
	                            </label>
	                            <div class="col-md-9">
	                                <input class="form-control" id="bst" value="{{trim($bst)}}" disabled="" placeholder="{!!trans('deterioration.log_likelohood')!!}" type="text">
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-md-3 control-label">CC =
	                            </label>
	                            <div class="col-md-9">
	                                <input class="form-control" id="cc" value="{{trim($cc)}}" disabled="" placeholder="{!!trans('deterioration.log_likelohood')!!}" type="text">
	                            </div>
	                        </div>
	                    </form>
	                </div>
	            </div>
	        @box_close
    	</article>
    	<article class="col-sm-12 col-md-12 col-lg-7">
    		<div class="jarviswidget" id="wid-id-4" data-widget-sortable="false" data-widget-deletebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-custombutton="false">
    			<header>	
                    <div class="widget-toolbar">
                        <ul class="nav nav-tabs pull-right in" id="myTab" style="border: none;">
							<li class="active" value="1">
								<input type="hidden" id="performance" value="0">
								<a data-toggle="tab" href="#s1">
									<span class="hidden-mobile hidden-tablet">
										{{trans('deterioration.p_curve')}}
									</span>
								</a>
							</li>

							<li value="2">
								<input type="hidden" id="performance" value="1">
								<a data-toggle="tab" href="#s2">
									<span class="hidden-mobile hidden-tablet">
										{{trans('deterioration.p_probabilities')}}
									</span>
								</a>
							</li>

							<li value="3">
								<input type="hidden" id="performance" value="2">
								<a data-toggle="tab" href="#s3"><!-- <i class="fa fa-dollar"></i> --> <span class="hidden-mobile hidden-tablet">{{trans('deterioration.markov_matrix')}}</span></a>
							</li>
							<li value="4">
								<input type="hidden" id="performance" value="3">
								<a data-toggle="tab" href="#s4"><!-- <i class="fa fa-dollar"></i> --> <span class="hidden-mobile hidden-tablet">{{trans('deterioration.all_curves')}}</span></a>
							</li>
							
						</ul>
                    </div>
                    <div class="widget-toolbar">
                		<div class="btn-group" style="color: red; border: none; margin-top: 0px;">
                            <select class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown" name="gender" style="width: 90px;margin-top: 0px;" id="route" onchange="change_data_chart()">
                                <option value="0">{{ trans('deterioraton.AC') }}</option>
                                <option value="1">{{ trans('deterioration.bst') }}</option>
                                <option value="2">{{ trans('deterioration.CC') }}</option>
                            </select>
                        </div>
                    </div>
                </header>
                <div>
	                <div class="widget-body">
		                <div id="myTabContent" class="tab-content">
		                	<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1" style="padding: 8px;">
	                    		<canvas id="canvas"></canvas>
	                    		<div id='performance_curve_legend' class='performance_curve_legend'></div>
	                    	</div>
	                    	<div class="tab-pane fade" id="s2">
	                    		<canvas id="canvas2"></canvas>
	                    	</div>
	                    	<div class="tab-pane fade" id="s3" style="margin-left: 3px; margin-right: 3px;overflow-x: auto;">
	                    		<h3 style="text-align: center;">{{trans('deterioration.markov_transition_probabilities')}}</h3>
	                    		<table id="matrix" class="table table-striped table-bordered table-hover" width="100%">
	                    			<tbody>
	                    			</tbody>
	                    		</table>
                			</div>
                			<div class="tab-pane fade" id="s4">
	                    		<canvas id="canvas3"></canvas>
	                    		<div id='performance_curve_all_legend' class='performance_curve_legend'></div>
	                    	</div>
	                    </div>
	                
		                <div class="widget-footer">
		                	<a class="btn btn-danger" href="/user/deterioration/benchmarking/{{$deterioration->id}}">
		                		{!!trans('deterioration.back_bm')!!}
		                	</a>
			                <a class="btn btn-danger" id="save">
			                	{!!trans('deterioration.export')!!}
			                </a>
			        		<a class="btn btn-danger" href="/user/deterioration/route/{{$deterioration->id}}">
			        			{!!trans('deterioration.next_route')!!}
			        		</a>
		                </div>
		            </div>
                </div>
            </div>
    	</article>
	</div>
</section>
@endsection

@push('script')
	<script type="text/javascript" src="{{ asset('/front-end/chartjs/Chart.bundle.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/front-end/chartjs/utils.js') }}"></script>
	<script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>
	<script type="text/javascript">
		var chart;
		var chart2;
		var chart3;
		window.onload = get_data_performance();
		pageSetUp();

		function getPTColor() 
		{
	        // var letters = '0123456789ABCDEF';
	        // var color = '#';
	        // for (var i = 0; i < 6; i++ ) {
	        //     color += letters[Math.floor(Math.random() * 16)];
	        // }

	        var color = ['#FF1320', '#28FF83', '#C2FF02', '#2342FF'];
	        return color;
	    }

	    function getYLabel()
	    {
	    	switch (+$('#distress_type').val()) 
            {
            	case 1:
            		return '{{ trans("deterioration.cracking_ratio") }}(%)';
            	case 3:
            		return '{{ trans("deterioration.iri") }}(mm/m)';
            	case 2:
            		return '{{ trans("deterioration.rut") }}(mm)';
            	default: 
            		return '';
            }
	    }

	    function getTitleChart()
	    {
	    	if ($("#myTab .active #performance").val() == 0) {
				return "{{trans('deterioration.performance')}}";
			}
			else if ($("#myTab .active #performance").val() == 1) {
				return "{{trans('deterioration.probabilities')}}";
			}
			else if ($("#myTab .active #performance").val() == 2) {
				return "{{trans('deterioration.markov')}}";
			}
			else if ($("#myTab .active #performance").val() == 3) {
				return "{{trans('deterioration.performance')}}";
			} else {
				return '';
			}
	    }

	    function getPavementType() {
	    	if ($("#route").val() == 0) {
				return @if (\App::isLocale('en')) 'AC' @else 'BTN' @endif;
			} else if ($("#route").val() == 1) {
				return @if (\App::isLocale('en')) 'BST' @else 'LN' @endif;
			} else if ($("#route").val() == 2) {
				return @if (\App::isLocale('en')) 'CC' @else 'BTXM' @endif;
			} else {
				return '';
			}
	    }

	    function convertToChartData(msg) {
	    	var step = 0,
	    		chart_data = [];

	    	for (var i in msg) {

	    		for (var j in msg[i]) {
	    			if (!chart_data[i]) chart_data[i] = [];
	    			chart_data[i].push({
						x: msg[i][j][0],
						y: -msg[i][j][1]
					});
	    		}
	    		var new_step = getStepChart(msg[i]);
	    		if (new_step > step) {
	    			step = new_step;
	    		}
	    	}
	    	return [chart_data, step];
	    }

		function get_data_performance()
		{
			// click save export excel
			$('#save').click(function(){
				var option = $('#distress_type').val();
				var id = "<?php echo Request::segment(4) ?>";
				location.href = '/user/pavement_type/'+id+'/'+option;
			});
		
			//curve
			$.ajax({
				type: 'GET',
				url:'/user/get_data_pavement_performance',
				data:{
					deterioration:'{{$deterioration->id}}',
					distress_type: $("#distress_type").val(),
					route: $("#route").val(),
					performance:$("#myTab .active #performance").val(),
				}			
			}).done( function(msg) {
				var name = getPavementType();
				var title_chart = getTitleChart();
				var chart_data = convertToChartData(msg);
				var color = getPTColor();
				var y_label = getYLabel();
				var config = {
		            type: 'line',
		            data: {
		                datasets: [{
				        	borderColor: color[0],
				        	backgroundColor: color[0],
				            label: "BM",
				            borderWidth: 2,
							data: chart_data[0][0],
							fill: false,
							borderDash: [10,5]
			        	},{
				        	borderColor: color[+$("#route").val()+1],
				        	backgroundColor: color[+$("#route").val()+1],
				            label: name,
				            borderWidth: 2,
							data: chart_data[0][1],
							fill: false,
			        	}
			        	]
		            },
		            options: {
		                responsive: true,
		                title:{
		                    display:true,
		                    text: title_chart,
		                },
		                legend: {
		                    display: false
		                },
		                tooltips: {
		                    intersect: true,
		                    callbacks: {
		                        label: function(tooltipItem, data) 
		                        {
		                            var i, label = '', l = data.datasets.length;
		                            for (i = 0; i < l; i += 1) 
		                            {
		                                if (i == tooltipItem['datasetIndex'])
		                                {
		                                    label = data.datasets[i].label + ' : ' + tooltipItem.xLabel;
		                                }
		                            }
		                            return label;
		                        },
		                    }
		                },
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
		                            userCallback: function(tick) {
		                                return tick;
		                            },
		                            stepSize: chart_data[1],
		                        }
		                    }],
		                    yAxes: [{
		                        display: true,
		                        type: 'linear',
		                        scaleLabel: {
		                            display: true,
		                            labelString: y_label
		                        },
		                        
		                        ticks: {
		                            userCallback: function(tick) {
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

				};
				if (chart) {
					chart.destroy();
				}
				var ctx = document.getElementById("canvas").getContext("2d");
	            ctx.canvas.height = 200;
	            chart = new Chart(ctx, config);
	            document.getElementById('performance_curve_legend').innerHTML = chart.generateLegend();
			});
			// Probabilities
			$.ajax({
				type:'GET',
				url: '/user/get_data_pavement_probabilities',
				data:{
					deterioration:'{{$deterioration->id}}',
					distress_type: $("#distress_type").val(),
					route: $("#route").val(),
				}
			}).done(function(msg) {
				var color;
				color = randomColor(msg[0].length);
				var array = [];
				for (var i = 0; i < msg[0].length; i++) 
				{
					var cache = {
						label: msg[2][i],
						borderColor: color[i],
						backgroundColor: color[i],
						data: msg[0][i],
						radius:0,
					}
					array.push(cache);
				}
				var config = {
					type: 'line',
					data: {
						labels: msg[1],
						datasets: array,
					},
					options: {
						responsive: true,
						title:{
							display: true,
							text: "{{trans('deterioration.probabilities')}}"
						},
						legend: {
		                    position: 'bottom',
		                },
						tooltips: {
							mode: 'index',
						},
						hover: {
							mode: 'index'
						},
						enabled: false,
						scales: {
							xAxes: [{
								// stacked: true,
								scaleLabel: {
									display: true,
									// labelString: "{{trans('deterioration.type')}}"
								},
								ticks:{
									autoSkip: false,
				                    maxRotation: 0,
				                    minRotation: 0,
									min:0,
									max: 100,
									userCallback: function(tick) {
										if (tick%5==0)
										{
											if (tick == 0)
											{
												return '';
											}
											else
											{
	                                		return tick;
	                                	}
	                                	}
	         							// return tick;
	                            	},
								}
							}],
							yAxes: [{
								stacked: true,
								scaleLabel: {
									display: true,
									// labelString: '%'
								},
								ticks:{
									min:0,
									max:100,
									
								}
							}]
						}
					}
				};
				if (chart2) {
					chart2.destroy();
				}
				var ctx = document.getElementById("canvas2").getContext("2d");
				ctx.canvas.height = 200;
				chart2 = new Chart(ctx, config);
			});
			// matrix
			$.ajax({
				type:'GET',
				url: '/user/get_data_pavement_matrix',
				data: {
					deterioration:'{{$deterioration->id}}',
					distress_type: $("#distress_type").val(),
					route: $("#route").val(),
				}
			}).done(function(msg) {
				var add_html_table = '';
				for (var i = 0; i < msg.length; i++) 
				{
					add_html_table = add_html_table + "<tr>"
					for (var j = 0; j < msg[i].length; j++) 
					{
						add_html_table = add_html_table + "<td>" + msg[i][j] + "</td>"
					}
					add_html_table = add_html_table + "</tr>"
				}
				$("#matrix tbody tr").remove();
				$("#matrix tbody").append(add_html_table);
			});
			// all curves
			$.ajax({
				type: 'GET',
				url:'/user/get_all_curves',
				data:{
					deterioration:'{{$deterioration->id}}',
					distress_type: $("#distress_type").val()
				}			
			}).done( function(msg) {
				var title_chart = getTitleChart();
				var chart_data = convertToChartData(msg);
				var color = getPTColor(4);
				var y_label = getYLabel();
				var config = {
		            type: 'line',
		            data: {
		                datasets: [{
				        	borderColor: color[0],
				        	backgroundColor: color[0],
				            label: "BM",
				            borderWidth: 2,
							data: chart_data[0][0],
							fill: false,
							borderDash: [10,5]
			        	},{
				        	borderColor: color[1],
				        	backgroundColor: color[1],
				            label: @if (\App::isLocale('en')) 'AC' @else 'BTN' @endif,
				            borderWidth: 2,
							data: chart_data[0][1],
							fill: false,
			        	},{
				        	borderColor: color[2],
				        	backgroundColor: color[2],
				            label: @if (\App::isLocale('en')) 'BST' @else 'LN' @endif,
				            borderWidth: 2,
							data: chart_data[0][2],
							fill: false,
			        	},{
				        	borderColor: color[3],
				        	backgroundColor: color[3],
				            label: @if (\App::isLocale('en')) 'CC' @else 'BTXM' @endif,
				            borderWidth: 2,
							data: chart_data[0][3],
							fill: false,
			        	}
			        	]
		            },
		            options: {
		                responsive: true,
		                title:{
		                    display:true,
		                    text: title_chart,
		                },
		                legend: {
		                    display: false
		                },
		                tooltips: {
		                    intersect: true,
		                    callbacks: {
		                        label: function(tooltipItem, data) 
		                        {
		                            var i, label = '', l = data.datasets.length;
		                            for (i = 0; i < l; i += 1) 
		                            {
		                                if (i == tooltipItem['datasetIndex'])
		                                {
		                                    label = data.datasets[i].label + ' : ' + tooltipItem.xLabel;
		                                }
		                            }
		                            return label;
		                        },
		                    }
		                },
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
		                            userCallback: function(tick) {
		                                return tick;
		                            },
		                            stepSize: chart_data[1],
		                        }
		                    }],
		                    yAxes: [{
		                        display: true,
		                        type: 'linear',
		                        scaleLabel: {
		                            display: true,
		                            labelString: y_label
		                        },
		                        
		                        ticks: {
		                            userCallback: function(tick) {
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
				};
				if (chart3) {
					chart3.destroy();
				}
				var ctx = document.getElementById("canvas3").getContext("2d");
	            ctx.canvas.height = 200;
	            chart3 = new Chart(ctx, config);
	            document.getElementById('performance_curve_all_legend').innerHTML = chart3.generateLegend();
			});
		}

		function change_data_chart()
		{
			chart.destroy();
			chart2.destroy();
			get_data_performance();
		}

		function distress_change()
		{
			chart.destroy();
			chart2.destroy();
			$("#matrix tbody tr").remove();
			get_data_performance();
			$.ajax({
				type:'GET',
				url: '/user/get_data_pavement_disstress',
				data: {
					distress_type: $("#distress_type").val(),
					deterioration: '{{$deterioration->id}}'
			},
			}).done(function (msg){
				$("#muy").val(msg[0][0]);
				$("#log").val(msg[0][1]);
				$("#as").val(msg[0][2]);
				$("#bst").val(msg[0][3]);
				$("#cc").val(msg[0][4]);
			});
		}	
	</script>
@endpush
