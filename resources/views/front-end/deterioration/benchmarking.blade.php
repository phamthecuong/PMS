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
	'text2' => trans('deterioration.benchmarking')
])

<section id="widget-grid">
    <div class="row">
    	<article class="col-sm-12 col-md-12 col-lg-5">
	    	@box_open(trans('deterioration.deterioration_evaluation_benchmarking'))
	    		<div>
	                <div class="widget-body">
	                	<form class="form-horizontal">
		                    <legend>{!! trans('deterioration.deterioration_evaluation_benchmarking') !!}</legend>
	                        <div class="form-group">
								<label class="col-md-3">{!!trans('deterioration.target_region')!!}</label>
								<div class="col-md-9">
									<?php $lang = (App::getLocale() == 'en') ? 'en' : 'vn' ?>
                                    <?= @\App\Models\tblOrganization::find(@$deterioration->organization_id)->{"name_{$lang}"} ?>
								</div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-md-3">{!!trans('deterioration.year_of_dataset')!!}</label>
	                            <div class="col-md-9">
	                                <p>{{$deterioration->year_of_dataset}}</p>
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
	                        <legend>{!!trans('deterioration.estimation_result_bench')!!}</legend>
	                        <h3>{!! trans('deterioration.hazard_parameter') !!}</h3>
	                        <table id="hazard_parameter" class="table table-striped table-bordered table-hover" width="100%">
	                        	<thead>
	                        		<tr>
	                        			<th data-hide="phone">{{trans('deterioration.condition_rank')}}</th>
	                        			<th data-hide="phone">{{trans('deterioration.hazard_parameter')}}</th>
	                        			<th data-hide="phone">{{trans('deterioration.t_value')}}</th>
	                        		</tr>
	                        	</thead>
	                        	<tbody>

	                        	</tbody>
	                        </table>
	                    </form>
	                </div>
	            </div>
	        @box_close
    	</article>
    	<article class="col-sm-12 col-md-12 col-lg-7">
    		<div class="jarviswidget" id="wid-id-4" data-widget-sortable="false" data-widget-deletebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-custombutton="false">
                <header>
                	<div class="widget-toolbar" style="padding: 0px !important">
                        <ul class="nav nav-tabs pull-right in" id="myTab" style="border: none;">
							<li class="active" value="1">
								<input type="hidden" id="performance" value="0">
								<a data-toggle="tab" href="#s1" style="">
									<span class="hidden-mobile hidden-tablet">
										{{trans('deterioration.p_curve')}}
									</span>
								</a>
							</li>

							<li value="2">
								<input type="hidden" id="performance" value="1">
								<a data-toggle="tab" href="#s2" style="">
									<span class="hidden-mobile hidden-tablet">
										{{trans('deterioration.p_probabilities')}}
									</span>
								</a>
							</li>

							<li value="3">
								<input type="hidden" id="performance" value="2">
								<a data-toggle="tab" href="#s3" style="">
									<span class="hidden-mobile hidden-tablet">
										{{trans('deterioration.markov_matrix')}}
									</span>
								</a>
							</li>
							
						</ul>
                    </div>
                </header>
                <div>
	                <div class="widget-body">
		                <div id="myTabContent" class="tab-content">
		                	<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1" style="padding: 8px;">
	                    		<canvas id="canvas" height="900" width="1350"></canvas>
	                    		<div id='performance_curve_legend' class='performance_curve_legend'></div>
	                    	</div>
	                    	<div class="tab-pane fade" id="s2" style="">
	                    		<canvas id="canvas2" height="900" width="1350"></canvas>
	                    	</div>
	                    	<div class="tab-pane fade" id="s3" style="margin-left: 3px; margin-right: 3px;overflow-x: auto;">
	                    		<h3 style="text-align: center;">{{trans('deterioration.markov_transition_probabilities')}}</h3>
	                    		<table id="matrix" class="table table-striped table-bordered table-hover" width="100%">
	                    			<tbody>
	                    			
	                    			</tbody>
	                    		</table>
                			</div>
	                    </div>
		                <div class="widget-footer">
		                	<a class="btn btn-danger" href="{{ route('data.summary', ['session_id' => $deterioration->id]) }}">
		                		{!!trans('deterioration.back_data')!!}
		                	</a>
			                <a class="btn btn-danger" class="save" id="save">
			                	{!!trans('deterioration.export')!!}
			                </a>
			                <a class="btn btn-danger" href="/user/deterioration/pavement_type/{{$deterioration->id}}">
			                	{!!trans('deterioration.next_pt')!!}
			                </a>
			            </div>
                	</div>
            	</div>
            </div>
		</article>
</section>
@endsection

@push('script')
	<script type="text/javascript" src="{{ asset('/front-end/chartjs/Chart.bundle.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/front-end/chartjs/utils.js') }}"></script>
	<script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>
	<script type="text/javascript">
		var chart, transition_chart;
		$( document ).ready(function() {
			load_data_ajax();
		});

		function load_data_ajax() {	
			//click save export excel
			$('#save').click(function(){
				var option = $('#distress_type').val();
				var id = "<?php echo  Request::segment(4) ?>";
				location.href = '/user/benmarking/'+id+'/'+option;
				
			});
		

			//curve
			$.ajax({
				type: 'GET',
				url: '/user/get_data_benchmarking_hazard',
				data: {
					distress_type: $("#distress_type").val(),
					deterioration: '{{$deterioration->id}}'
				}
			}).done(function (msg) {
				//add data table
				var add_data_html;
				for (var i = 0; i < msg[0].length - 1; i++) 
				{
					add_data_html = add_data_html + "<tr>";
					add_data_html = add_data_html + "<td>" + (i+1) + "</td>";
					add_data_html = add_data_html + "<td>" + (msg[1][i] ? msg[1][i] : '-') + "</td>";
					add_data_html = add_data_html + "<td>" + (msg[2][i] ? msg[2][i] : '-') + "</td>";
					add_data_html = add_data_html + "</tr>";
				}
				$("#hazard_parameter tbody").append(add_data_html);
				//draw chart
				var cache = [];
				var step = getStepChart(msg[3]);
				if (step == 0)
				{
				    step = 5;
				}
				for (var i = 0; i < msg[3].length; i++) 
				{
					var data_cache = {
						x: msg[3][i],
						y: -msg[0][i]
					};
					cache.push(data_cache);
				}
				var ta = [];
	            for (var k = 0; k < cache.length; k++)
	            {
	                ta.push(cache[k]);
	            }

	            var y_label;
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
				var config = {
		            type: 'line',
		            data: {
		                datasets: [{
				        	borderColor: 'green',
				        	backgroundColor: 'green',
				            label: 'BM',
				            borderWidth: 2,
							data: ta,
							fill:false,
			        	}]
		            },
		            options: {
		                responsive: true,
		                title:{
		                    display:true,
		                    text: "{{trans('deterioration.title_chart_curve_benchmarking')}}",
		                },
		                legend: {
		                    // position: 'bottom',
		                    display: false,
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
		                            stepSize: step,
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
		                            // stepSize: 1,
		                        },
		                    }]
		                }
		            }
				};

				var ctx = document.getElementById("canvas").getContext("2d");
	            if (chart) {
	            	chart.destroy();
	            }
	            chart = new Chart(ctx, config);
	            document.getElementById('performance_curve_legend').innerHTML = chart.generateLegend();
			});
			//probabilities
			$.ajax({
				type: 'GET',
				url: '/user/get_data_benchmarking_probabilities',
				data: {
					distress_type: $("#distress_type").val(),
					deterioration: '{{$deterioration->id}}'
				}
			}).done(function(msg){
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
						radius: 0,
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
	    				// maintainAspectRatio: true,
						title:{
							display:true,
							text:"{{trans('deterioration.probabilities')}}"
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
								ticks:{
									min:0,
									max:100,
									userCallback: function(tick) {
			                            return tick + '%';
		                            },	
								},
							}]
						}
					}
				};
				var ctx = document.getElementById("canvas2").getContext("2d");
				if (transition_chart) {
	            	transition_chart.destroy();
	            }
				transition_chart = new Chart(ctx, config);
			});
			// matrix
			$.ajax({
				type:'GET',
				url: '/user/get_data_benchmarking_matrix',
				data: {
					distress_type: $("#distress_type").val(),
					deterioration: '{{$deterioration->id}}'
				}
			}).done(function(msg){
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
		}
		
		function distress_change() {
		    $("#hazard_parameter tbody tr").remove();
		    $("#matrix tbody tr").remove();
			chart.destroy();
			load_data_ajax();
		}
	</script>
@endpush