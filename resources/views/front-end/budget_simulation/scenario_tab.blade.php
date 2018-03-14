@extends('front-end.layouts.app')

@section('budget_simulation')
active
@endsection

@if ($history_flg)
	@section('budget_simulation_show_history')
	active
	@endsection
@else
	@section('budget_simulation_start_new_process')
	active
	@endsection
@endif

@section('breadcrumb')
	<ol class="breadcrumb">
		<li>
			{{trans('menu.home')}}
		</li>
		<li>
			{{trans('menu.budget_simulation')}}
		</li>
		@if ($history_flg)
		<li>
			{{trans('menu.budget_show_history')}}
		</li>
		@else
		<li>
			{{trans('menu.start_process_budget')}}
		</li>
		@endif
	</ol>
@endsection

@push('css')
	<style type="text/css">
		.RCNR_legend ul,
		.RL_legend ul {
			list-style: none;
    		text-align: center;
    		margin-top: 7px;
    		padding-left: 0px;
		}
		.RCNR_legend li,
		.RL_legend li {
			display: inline-block;
			padding: 0px 4px;
		}
		.RCNR_legend li span,
		.RL_legend li span {
			float: left;
		}
		.RCNR_legend li div.number_chart,
		.RL_legend li div.number_chart {
		    display: inline-block;
		    float: left;
		    font-size: 12px;
		    margin-left: 4px;
		}
		.line-through {
			text-decoration: line-through;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-inbox',
	'text1' => trans('menu.budget_simulation'),
	'text2' => trans('budget.scenario')
])

<section id="widget-grid" class="">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-6">
			@box_open(trans('budget.process_info'))
				<div>
					<div class="widget-body">
						@include('front-end.budget_simulation.process_info', [
                    		'text_region' => $text_region,
                    		'text_year' => $text_year,
                    		'text_route' => $text_road
                    	])
						<div class="widget-footer text-right">
                            <a href="/admin/repair_methods" target="_blank">
                                {{ trans('wp.check_full_repair_methods_list') }} <i class="fa fa-arrow-right"></i> 
                            </a>
                        </div>
					</div>
				</div>
			@box_close
		</div>
		<div class="col-sm-12 col-md-12 col-lg-6">
			@box_open(trans('budget.scenario'))
				<div>
					<div class="widget-body">
						{!! 
							Form::open([
								"url" => route('user.budget.get.scenario_tab.process', $budget_simulation->id), 
								"method" => "post"
							])
						!!}
							<fieldset>
							{!! 
								Form::lbSelect(
									'scenario', 
									\Session::get('scenario'), 
									$scenario, 
									trans('budget.scenario'), 
									['onchange' => 'setUpLeftBlock()']
								) 
							!!}
							{!! 
								Form::lbText(
									'budget_constraint', 
									$budget_simulation->budget_constraint, 
									trans('budget.budget_constraint') . ' (' . trans('budget.billion_VND') . ')',
									null,
									null,
									['style' => 'display: none']
								) 
							!!} 
							{!! 
								Form::lbText(
									'target_risk_level', 
									$budget_simulation->target_risk, 
									trans('budget.target_risk_level') . ' (%)',
									null,
									null,
									['style' => 'display: none']
								) 
							!!} 
							<div class="note note-scenario-0" style="display: none">
								{{ trans('budget.non_constraint') }}
							</div>
							<div class="note note-scenario-2" style="display: none">
								{{ trans('budget.use_current_risk') }}: {!! $budget_simulation->getCurrentRisk() !!}%
							</div>
							</fieldset>
							<div class="form-actions">
								<div class="row">
									<div class="col-md-12">
										<button class="btn btn-default" type="submit" id="process">
											{{ trans('budget.run_process') }}
										</button>
									</div>
								</div>
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			@box_close
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
			@box_open(trans('budget.output'))
				<div>
					<div class="widget-body">
						<legend>
							{{ trans('budget.Cost & Risk / Repair Length') }}
						</legend>
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-6">
								<canvas id="RCNR" height="175"></canvas>
								<div id='RCNR_legend' class='RCNR_legend'></div>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-6">
								<canvas id="RL" height="175"></canvas>
								<div id='RL_legend' class='RL_legend'></div>
							</div>
						</div>
						<legend>
							{{ trans('budget.Condition Transition') }}
						</legend>
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-6">
								<canvas id="CCT" height="200"></canvas>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-6">
								<canvas id="RCT" height="200"></canvas>
							</div>
						</div>
						<div class="widget-footer">
                            <a class="btn bg-color-blueLight txt-color-white" href="/user/budget_simulation/repair_condition/{{$budget_simulation->id}}">{{ trans('budget.back_condition') }}</a>
                            <a class="btn bg-color-blueLight txt-color-white" href="javascript:void(0);" onclick="exportFile()" id="export_zone">
                                {{trans('budget.export')}}
                            </a>
                        </div>
					</div>

				</div>
				<!-- end widget div -->
			@box_close
		</div>
	</div>
</section>

@endsection

@push('script')
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.0/Chart.bundle.min.js"></script> -->
<script type="text/javascript" src="{{ asset('/front-end/chartjs/Chart.bundle.1.2.js') }}"></script>
<script type="text/javascript" src="{{ asset('/front-end/chartjs/utils.js') }}"></script>
<script type="text/javascript" src="{{ asset('/front-end/chartjs/groupable.js') }}"></script>
<script type="text/javascript">
	var cri_flg0 = {{$budget_simulation->output_0_flg}},
		cri_flg1 = {{$budget_simulation->output_1_flg}},
		cri_flg2 = {{$budget_simulation->output_2_flg}},
		cri_flg3 = {{$budget_simulation->output_3_flg}},
		session_id = '{{$budget_simulation->id}}',
		RCNRchart,
		RLchart,
		CCTchart,
		RCTchart,
		repair_classification = <?php echo json_encode(\App\Models\tblRClassification::all()->pluck("name_$lang", 'id')) ?>;

	$(document).ready(function(){
		setUpLeftBlock();
	});

	function setUpLeftBlock() {
		var show_chart = false;
		var selected_scenario = +$('select[name="scenario"]').val();
		switch (selected_scenario) {
			case 0:
				$('input[name="budget_constraint"]').hide().parent().hide();
				$('input[name="target_risk_level"]').hide().parent().hide();
				if (cri_flg0 == 2) {
					$('button#process').hide();
					show_chart = true;
				} else {
					$('button#process').show();
					show_chart = false;
				}
				$('.note-scenario-0').show();
				$('.note-scenario-2').hide();
				break;
			case 1:
				$('input[name="budget_constraint"]').show().parent().show();
				$('input[name="target_risk_level"]').hide().parent().hide();
				if (cri_flg1 == 2) {
					// $('button#process').hide();
					show_chart = true;
				} else {
					show_chart = false;
				}
				$('button#process').show();
				$('.note-scenario-0').hide();
				$('.note-scenario-2').hide();
				break;
			case 2:
				$('input[name="budget_constraint"]').hide().parent().hide();
				$('input[name="target_risk_level"]').hide().parent().hide();
				if (cri_flg2 == 2) {
					$('button#process').hide();
					show_chart = true;
				} else {
					$('button#process').show();
					show_chart = false;
				}
				$('.note-scenario-0').hide();
				$('.note-scenario-2').show();
				break;
			case 3:
				$('input[name="budget_constraint"]').hide().parent().hide();
				$('input[name="target_risk_level"]').show().parent().show();
				if (cri_flg3 == 2) {
					show_chart = true;
				} else {
					show_chart = false;
				}
				$('button#process').show();
				$('.note-scenario-0').hide();
				$('.note-scenario-2').hide();
				break;
			default:
				alert(selected_scenario);
				break;
		}
		// show chart
		if (show_chart) {
			drawChart();
			$('#export_zone').show();
		} else {
			if (RCNRchart) {
				RCNRchart.destroy();
				$('#RCNR_legend').html('');
			}
			if (RLchart) {
				RLchart.destroy();
				$('#RL_legend').html('');
			}
			if (CCTchart) {
				CCTchart.destroy();
			}
			if (RCTchart) {
				RCTchart.destroy();
			}
			$('#export_zone').hide();
		}
	}

	function drawChart() {
		drawRCNRChart();
		drawRLChart();
		drawCCTChart();
		drawRCTChart();
	}

	function drawRCNRChart() {
		var type = 'RCNR';
		var selected_scenario = +$('select[name="scenario"]').val();
		$.ajax({
			type: 'GET',
			url: '/user/budget_simulation/get_chart_data',
			data: {
				id: session_id,
				scenario: selected_scenario,
				type: type
			}
		}).done(function(res){
			var labels = res.labels;
			var risk = res.risk;
			var chart_data = [];
			var colors = randomColor(Object.keys(res.bar_chart_data).length + 1);

			chart_data.push({
				type: 'line',
                label: 'risk',
                yAxisID: "y-axis-1",
                data: risk,
                backgroundColor: 'red',
                borderColor: 'red',
                borderWidth: '1',
                fill: false
			});
			var index = 1;
			for (var i in res.bar_chart_data) {
				chart_data.push({
					type: 'groupableBar',
	                label: repair_classification[i],
	                yAxisID: "y-axis-0",
	                data: res.bar_chart_data[i],
	                backgroundColor: colors[index],
                	borderColor: colors[index]
				});
				index++;
			}

			var ctx = document.getElementById(type).getContext("2d");
			if (RCNRchart) {
				RCNRchart.destroy();
			}

			RCNRchart = new Chart(ctx, {
			    type: 'bar',
			    options: {
				    scales: {
				      	yAxes: [{
				        	position: "left",
				        	id: "y-axis-0",
				        	gridLines: {
			                    display:false
			                },
			                stacked: true,
			                scaleLabel: {
	                            display: true,
	                            labelString: '{{trans('budget.cost(billion_vnd)')}}'
	                        },
	                        ticks: {
						        beginAtZero:true,
						        mirror:false,
						        suggestedMin: 0,
						        suggestedMax: 1,
						    },
				      	}, {
				        	position: "right",
				        	id: "y-axis-1",
				        	ticks: {
			                    max: 100,
			                    min: 0,
			                    callback: function(value, index, values) {
			                        return value + '%';
			                    },
			                },
			                scaleLabel: {
	                            display: true,
	                            labelString: '{{trans('budget.risk')}}',
	                        }
				      	}],
				      	xAxes: [{
			                gridLines: {
			                    display: false
			                },
			                ticks: {
		                        // autoSkip: true,
		                        // autoSkipPadding: 50
		                        callback: function(value, index, values) {
			                        return (value%5 == 0) ? value : '';
			                    },
			                    maxRotation: 0
		                    },
		                    scaleLabel: {
	                            display: true,
	                            labelString: '{{trans('budget.year_text')}}'
	                        }
			            }],
				    },
				    title: {
			            display: true,
			            text: '{{trans('budget.cost_n_risk')}}'
			        },
			        legend: {
			        	display: false,
			        	position: 'bottom'
			        },
			        legendCallback: function(chart) {
				        var html = '<ul class="1-legend">';
				        var legend_data = chart.legend.legendItems;
				        for (var i in legend_data) {
				        	if (i == 0) continue;
				        	html+= '<li><span style="width: 45px;height: 15px;background-color:' + legend_data[i].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[i].datasetIndex + '\'' + ', \'rcnr\')"></span><div class="number_chart" id="rcnr-' + chart.legend.legendItems[i].datasetIndex + '">' + legend_data[i].text + '</div></li>';
				        }
				        html+= '<li><span style="margin-top: 8px;width: 45px;height: 2px;background-color:' + legend_data[0].fillStyle + '"></span><div class="number_chart">' + legend_data[0].text + '</div></li>';  

				        return html;
			        }
			  	},
			    data: {
			        labels: labels,
			        datasets: chart_data
			    }
			});
			document.getElementById('RCNR_legend').innerHTML = RCNRchart.generateLegend();
		});
	}

	function drawRLChart() {
		var type = 'RL';
		var selected_scenario = +$('select[name="scenario"]').val();
		$.ajax({
			type: 'GET',
			url: '/user/budget_simulation/get_chart_data',
			data: {
				id: session_id,
				scenario: selected_scenario,
				type: type
			}
		}).done(function(res){
			var labels = res.labels;
			var chart_data = [];
			var colors = randomColor(Object.keys(res.bar_chart_data).length + 1);

			var index = 1;
			for (var i in res.bar_chart_data) {
				chart_data.push({
					type: 'groupableBar',
	                label: repair_classification[i],
	                data: res.bar_chart_data[i],
	                backgroundColor: colors[index],
                	borderColor: colors[index]
				});
				index++;
			}

			var ctx = document.getElementById(type).getContext("2d");
			if (RLchart) {
				RLchart.destroy();
			}
			RLchart = new Chart(ctx, {
			    type: 'bar',
			    options: {
				    scales: {
				      	yAxes: [{
			                stacked: true,
			                scaleLabel: {
	                            display: true,
	                            labelString: '{{trans('budget.repair_length_km')}}'
	                        },
	                        ticks: {
						        beginAtZero: true,
						        mirror: false,
						        suggestedMin: 0,
						        suggestedMax: 1,
						    },
				      	}],
				      	xAxes: [{
			                gridLines: {
			                    display:false
			                },
			                ticks: {
		                        callback: function(value, index, values) {
			                        return (value%5 == 0) ? value : '';
			                    },
			                    maxRotation: 0
		                    },
		                    scaleLabel: {
	                            display: true,
	                            labelString: '{{trans('budget.year_text')}}'
	                        }
			            }],
				    },
				    title: {
			            display: true,
			            text: '{{trans('budget.repair_length')}}'
			        },
			        legend: {
			        	display: false,
			        	position: 'bottom'
			        },
			        legendCallback: function(chart) {
				        var html = '<ul class="1-legend">';
				        var legend_data = chart.legend.legendItems;
				        for (var i in legend_data) {
				        	html+= '<li><span style="width: 45px;height: 15px;background-color:' + legend_data[i].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[i].datasetIndex + '\'' + ', \'rl\')"></span><div class="number_chart" id="rl-' + chart.legend.legendItems[i].datasetIndex + '">' + legend_data[i].text + '</div></li>';
				        }
				        
				        return html;
			        }
			  	},
			    data: {
			        labels: labels,
			        datasets: chart_data
			    }
			});
			document.getElementById('RL_legend').innerHTML = RLchart.generateLegend();
		});
	}

	function drawCCTChart() {
		var type = 'CCT';
		var selected_scenario = +$('select[name="scenario"]').val();
		$.ajax({
			type: 'GET',
			url: '/user/budget_simulation/get_chart_data',
			data: {
				id: session_id,
				scenario: selected_scenario,
				type: type
			}
		}).done(function(res){
			var labels = res.labels;
			var chart_data = [];
			var colors = randomColor(Object.keys(res.area_chart_data).length);

			var index = 0;
			for (var i in res.area_chart_data) {
				chart_data.push({
					type: 'line',
	                label: i,
	                data: res.area_chart_data[i],
	                backgroundColor: colors[index],
                	borderColor: colors[index]
				});
				index++;
			}

			var ctx = document.getElementById(type).getContext("2d");
			if (CCTchart) {
				CCTchart.destroy();
			}
			CCTchart = new Chart(ctx, {
			    type: 'bar',
			    options: {
				    scales: {
				      	yAxes: [{
				      	    stacked: true,
				      	    ticks: {
					           	min: 0,
					           	max: 100,
					           	callback: function(value) {
					               return value + "%"
					           	}
					       	},
					       	gridLines: {
			                    display: false
			                },
				      	}],
				      	xAxes: [{
			                gridLines: {
			                    display: false
			                },
			                ticks: {
		                        callback: function(value, index, values) {
			                        return (value%5 == 0) ? value : '';
			                    },
			                    maxRotation: 0
		                    }
			            }],
				    },
				    elements: {
	                    point:{
	                        radius: 0
	                    }
	                },
	                legend: {
	                    position: 'bottom',
	                },
	                title: {
			            display: true,
			            text: '{{trans('budget.crack_condition_transition')}}'
			        },
			  	},
			    data: {
			        labels: labels,
			        datasets: chart_data
			    }
			});
		});
	}

	function drawRCTChart() {
		var type = 'RCT';
		var selected_scenario = +$('select[name="scenario"]').val();
		$.ajax({
			type: 'GET',
			url: '/user/budget_simulation/get_chart_data',
			data: {
				id: session_id,
				scenario: selected_scenario,
				type: type
			}
		}).done(function(res){
			var labels = res.labels;
			var chart_data = [];
			var colors = randomColor(Object.keys(res.area_chart_data).length);

			var index = 0;
			for (var i in res.area_chart_data) {
				chart_data.push({
					type: 'line',
	                label: i,
	                data: res.area_chart_data[i],
	                backgroundColor: colors[index],
                	borderColor: colors[index]
				});
				index++;
			}

			var ctx = document.getElementById(type).getContext("2d");
			if (RCTchart) {
				RCTchart.destroy();
			}
			RCTchart = new Chart(ctx, {
			    type: 'bar',
			    options: {
				    scales: {
				      	yAxes: [{
				      	    stacked: true,
				      	    ticks: {
					           	min: 0,
					           	max: 100,
					           	callback: function(value) {
					               return value + "%"
					           	}
					       	},
					       	gridLines: {
			                    display: false
			                },
				      	}],
				      	xAxes: [{
			                gridLines: {
			                    display: false
			                },
			                ticks: {
		                        callback: function(value, index, values) {
			                        return (value%5 == 0) ? value : '';
			                    },
			                    maxRotation: 0
		                    }
			            }],
				    },
				    elements: {
	                    point:{
	                        radius: 0
	                    }
	                },
	                legend: {
	                    position: 'bottom',
	                },
	                title: {
			            display: true,
			            text: '{{trans('budget.rut_condition_transition')}}'
			        },
			  	},
			    data: {
			        labels: labels,
			        datasets: chart_data
			    }
			});
		});
	}

	updateDataset = function(e, datasetIndex, prefix) {
        var index = datasetIndex;
        var ci = e.view.RCNRchart;
        var meta = ci.getDatasetMeta(index);

        // See controller.isDatasetVisible comment
        meta.hidden = meta.hidden === null? !ci.data.datasets[index].hidden : null;

        // We hid a dataset ... rerender the chart
        ci.update();
        $('#' + prefix + '-' + datasetIndex).toggleClass('line-through');
    };

    function exportFile() {
    	location.href = '/user/budget_simulation/export_file/' + session_id + '/' + $('select[name="scenario"]').val();
    }
</script>
@endpush
