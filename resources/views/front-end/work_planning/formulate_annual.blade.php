@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('work_planning_start_new_process')
active
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li>{{trans("wp.home")}}</li>
    <li>{{trans("wp.working_planning")}}</li>
    <li>{{trans("wp.start_new_process")}}</li>
</ol>
@endsection

@section('content')
    
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-th',
        'text1' => trans('wp.work_planning'),
        'text2' => trans('wp.formulate_annual')
    ])

    <section id="widget-grid">                          
        <div class="row">  
            <div class="col-sm-12 col-md-12 col-lg-12">
                @box_open(trans("wp.info"))
                    <div>
                        <div class="widget-body">
                        	@include('front-end.work_planning.process_info', [
                        		'text_region' => $text_region,
                        		'text_year' => $text_year,
                        		'base_planning_year' => $base_planning_year
                        	])
                        </div>
                    </div>
                @box_close
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6">
                @box_open(trans("wp.budget_constraint"))
                    <div>
                        <div class="widget-body">
                        	<div class="form-horizontal">
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.total_budget'),
                        			'name' => 'total_budget',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => isset($total_budget) ? $total_budget : 0
                        		])
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.for_year_1'),
                        			'name' => 'year_1',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => isset($year_1) ? $year_1 : 0
                        		])
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.for_year_2'),
                        			'name' => 'year_2',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => isset($year_2) ? $year_2 : 0
                        		])
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.for_year_3'),
                        			'name' => 'year_3',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => isset($year_3) ? $year_3 : 0
                        		])
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.for_year_4'),
                        			'name' => 'year_4',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => isset($year_4) ? $year_4 : 0
                        		])
                        		@include('custom.inline_input', [
                        			'title' => trans('wp.for_year_5'),
                        			'name' => 'year_5',
                        			'add_on' => trans('wp.bil_vnd'),
                        			'value' => @$total_budget-(@$year_1 + @$year_2 + @$year_3 + @$year_4)
                        		])
							</div>
                        </div>
                    </div>
                @box_close
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6">
                @box_open(trans("wp.priority_criteria"))
                    <div>
                        <div class="widget-body no-padding">
                        	<div class="table-responsive">
							
								<table class="table">
									<thead>
										<tr>
											<th>#</th>
											<th> <i class="fa fa-external-link-square"></i> {{trans('wp.priority')}}</th>
											<th> <i class="fa fa-circle"></i> {{trans('wp.parameter')}}</th>
											{{-- <th> <i class="fa fa-check"></i> {{trans('wp.on_off')}}</th> --}}
										</tr>
									</thead>
									<?php 
										$arr = [
											['name' => 'MCI', 'value' => '1'],
											['name' => trans('wp.road_class'), 'value' => '2'],
											['name' => trans('wp.Traffic_Volume'), 'value' => '3'],
										];
										$arr2 = [
											['name' => '', 'value' => '0'],
											['name' => trans('wp.road_class'), 'value' => '2'],
											['name' => trans('wp.Traffic_Volume'), 'value' => '3'],
										]
									?>
									<tbody>
										<tr class="success">
											<td>1</td>
											<td>{{trans('wp.first_priority')}}</td>
											{{-- <td>MCI</td>
											<td>
												<span class="onoffswitch">
													<input value="1" type="checkbox" name="mci_criteria" class="onoffswitch-checkbox" id="mci_criteria" checked="checked">
													<label class="onoffswitch-label" for="mci_criteria">
														<span class="onoffswitch-inner" data-swchon-text="{{ trans('wp.on') }}" data-swchoff-text="{{ trans('wp.off') }}"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
											</td> --}}
											<td>{!! Form::lbSelect('first_priority', @$criteria[0], $arr, null, ['class' => 'form-control first_priority']) !!}</td>
										</tr>
										<tr class="danger">
											<td>2</td>
											<td>{{trans('wp.second_priority')}}</td>
											{{-- <td>{{trans('wp.road_class')}}</td>
											<td>
												<span class="onoffswitch">
													<input value="1" type="checkbox" name="road_class_criteria" class="onoffswitch-checkbox" id="road_class_criteria">
													<label class="onoffswitch-label" for="road_class_criteria">
														<span class="onoffswitch-inner" data-swchon-text="{{ trans('wp.on') }}" data-swchoff-text="{{ trans('wp.off') }}"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
											</td> --}}
											<td>{!! Form::lbSelect('second_priority', @$criteria[1], [['name' => '', 'value' => null]], null, ['class' => 'form-control second_priority']) !!}</td>
										</tr>
										<tr class="warning">
											<td>3</td>
											<td>{{trans('wp.third_priority')}}</td>
											{{-- <td>{{trans('wp.Traffic_Volume')}}</td>
											<td>
												<span class="onoffswitch">
													<input value="1" type="checkbox" name="tv_criteria" class="onoffswitch-checkbox" id="tv_criteria">
													<label class="onoffswitch-label" for="tv_criteria">
														<span class="onoffswitch-inner" data-swchon-text="{{ trans('wp.on') }}" data-swchoff-text="{{ trans('wp.off') }}"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
											</td> --}}
											<td>{!! Form::lbSelect('third_priority', @$criteria[2], [['name' => '', 'value' => null]], null, ['disabled', 'class' => 'form-control third_priority']) !!}</td>
										</tr>
									</tbody>
								</table>
							</div>
                        </div>
                    </div>
                @box_close
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6">
            	@box_open(trans("wp.price_escalation"))
            	<div>
            		<div class="widget-body">
            			<div class="form-horizontal">
            			{{-- {!! Form::lbText('price_esca_factor', null, trans("wp.price_esca_factor")) !!} --}}
            				@include('custom.inline_input', [
                    			'title' => trans('wp.price_esca_factor'),
                    			'name' => 'price_esca_factor',
                    			'add_on' => '%',
                    			'value' => isset($price_esca_factor) ? $price_esca_factor : 0
                    		])
            			</div>
            		</div>
            	</div>
            	@box_close
            </div>
		</div>
		<div class="well" style="text-align: right">		
			<a href="{{ $back }}" class="btn bg-color-blueLight txt-color-white">{{ trans('wp.back_condition') }}</a>
			<button class="btn bg-color-blueLight txt-color-white" onclick="setupBudgetCalculation();">{{ trans('wp.make_plan') }}</button>
		</div>		
	</div>
@endsection

@push('script')
	<script type="text/javascript">

		$(document).ready(function() {
			@if ($status == 1)
				$('[name="total_budget"]').attr('disabled', 'disabled');
				$('[name="year_1"]').attr('disabled', 'disabled');
				$('[name="year_2"]').attr('disabled', 'disabled');
				$('[name="year_3"]').attr('disabled', 'disabled');
				$('[name="year_4"]').attr('disabled', 'disabled');
				$('[name="price_esca_factor"]').attr('disabled', 'disabled');
				$('.first_priority').attr('disabled', 'disabled');
				$('.second_priority').attr('disabled', 'disabled');
				$('.third_priority').attr('disabled', 'disabled');
			@endif
			$('[name="year_5"]').attr('disabled', 'disabled');
			$('[name="total_budget"]').change(calculateYear5);
			$('[name="year_1"]').change(calculateYear5);
			$('[name="year_2"]').change(calculateYear5);
			$('[name="year_3"]').change(calculateYear5);
			$('[name="year_4"]').change(calculateYear5);

			var first_priority = $('.first_priority').val();
			var div_first = $('.first_priority').parents();
	        var op_first = " ";
			$.ajax({
                type:'get',
                url:'{!!URL::to("ajax/work/formulate_annual_year/criteria")!!}',
                data:{'first_priority':first_priority},
                success:function(data){
                    for(var i=0;i<data.length;i++){
	                    op_first+='<option value="'+data[i].value+'">'+data[i].name+'</option>';
                   	}

                   	div_first.find('.second_priority').html(" ");
                   	div_first.find('.second_priority').append(op_first);
                   	$('.second_priority').val({{ @$criteria[1] }});
                },
                error:function(){

                }
            });

            var second_priority = {{ isset($criteria[1]) ? $criteria[1] : 0}};
            var status = {{ $status }};
            var div_second = $('.second_priority').parents();
            var op_second = " ";
            $.ajax({
                type:'get',
                url:'{!!URL::to("ajax/work/formulate_annual_year/criteria")!!}',
                data:{
                	'first_priority': first_priority,
                	'second_priority': second_priority,
            	},
                success:function(data){       
                	if (second_priority != 0)
                	{
                		$('.third_priority').removeAttr('disabled');
                		for(var i=0;i<data.length;i++){
		                    op_second+='<option value="'+data[i].value+'">'+data[i].name+'</option>';
	                   	}
                	}
                	else
                	{
                		$('.third_priority').attr('disabled', 'disabled');
                		
                	}
                	if (status != 0)
                	{
                		$('.third_priority').attr('disabled', 'disabled');
                	}
                	div_second.find('.third_priority').html(" ");
                   	div_second.find('.third_priority').append(op_second);
                   	$('.third_priority').val({{ @$criteria[2] }});
                    
                },
                error:function(){

                }
            });


			$(document).on('change','.first_priority',function(){
	            var first_priority = $(this).val();
	            var div = $(this).parents();
	            var op = " ";
	            $.ajax({
	                type:'get',
	                url:'{!!URL::to("ajax/work/formulate_annual_year/criteria")!!}',
	                data:{'first_priority':first_priority},
	                success:function(data){
	                    for(var i=0;i<data.length;i++){
		                    op+='<option value="'+data[i].value+'">'+data[i].name+'</option>';
	                   	}
	                   	div.find('.second_priority').html(" ");
	                   	div.find('.second_priority').append(op);
	                   	$('.third_priority').html(" ");
	                   	$('.third_priority').attr('disabled', 'disabled');
	                },
	                error:function(){

	                }
	            });
	        });

	        $(document).on('change','.second_priority',function(){
	        	var first_priority = $('.first_priority').val();
	            var second_priority = $(this).val();
	            var div = $(this).parents();
	            var op = " ";
	            $.ajax({
	                type:'get',
	                url:'{!!URL::to("ajax/work/formulate_annual_year/criteria")!!}',
	                data:{
	                	'first_priority': first_priority,
	                	'second_priority': second_priority,
	            	},
	                success:function(data){
	                	if (second_priority != '0')
	                	{
	                		$('.third_priority').removeAttr('disabled');
	                		for(var i=0;i<data.length;i++){
			                    op+='<option value="'+data[i].value+'">'+data[i].name+'</option>';
		                   	}
	                	}
	                	else
	                	{
	                		$('.third_priority').attr('disabled', 'disabled');
	                		
	                	}
	                	div.find('.third_priority').html(" ");
	                   	div.find('.third_priority').append(op);
	                    
	                },
	                error:function(){

	                }
	            });
	        });
		})

		function setupBudgetCalculation() {
			var total_budget = $('[name="total_budget"]').val(),
				year_1 = $('[name="year_1"]').val(),
				year_2 = $('[name="year_2"]').val(),
				year_3 = $('[name="year_3"]').val(),
				year_4 = $('[name="year_4"]').val(),
				price_esca_factor = $('[name="price_esca_factor"]').val();
				// mci_criteria = $('[name="mci_criteria"').is(':checked'),
				// road_class_criteria = $('[name="road_class_criteria"').is(':checked'),
				// tv_criteria = $('[name="tv_criteria"').is(':checked');
				first_priority = $('[name="first_priority"]').val();
				second_priority = $('[name="second_priority"]').val();
				third_priority = $('[name="third_priority"]').val();

			if (!checkNumber([total_budget, year_1, year_2, year_3, year_4])) {
				return false;
			}
			if (!checkPriceEscaFactor(price_esca_factor)) {
				return false;
			}
			if ((+total_budget) == 0) {
				alertSmart('{{trans('wp.total_budget_need_greater_than_0')}}');
				return false;
			}
			if (+year_1 + (+year_2) + (+year_3) + (+year_4) > (+total_budget)) {
				alertSmart('{{trans('wp.budget_for_year_exceed_total_budget')}}');
				return false;
			}
			showLoading();
			var url = '{{route('ajax.wp.formulate.annual.year')}}';
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                	session_id: '{{$session_id}}',
                    total_budget: total_budget,
                    year_1: year_1,
                    year_2: year_2,
                    year_3: year_3,
                    year_4: year_4,
                    price_esca_factor: +price_esca_factor,
                    first_priority: first_priority,
                    second_priority: second_priority,
                    third_priority: third_priority
                }
            })
            .done(function(response) {
                if (response.code == 200) {
                	location.href = '/user/work_planning/result/' + "{{$session_id}}";
                } else {
                	alert(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            })
		}

		function checkNumber(arr) {
			for (var i in arr) {
				if (isNaN(arr[i]) || arr[i].trim().length == 0 || arr[i] < 0) {
					alertSmart('{{trans('wp.budget_should_be_number')}}');
					return false;
				}
			}
			return true;
		}

		function checkPriceEscaFactor(price) {
			if (isNaN(price) || price.trim().length == 0 || price < 0) {
				alertSmart('{{trans('wp.price_esca_factor_should_be_number')}}');
				return false;
			}
			return true;
		}
		function alertSmart(content) {
			$.SmartMessageBox({
				title : "{{trans('wp.error')}}",
				content : content,
				buttons : "[{{trans('wp.ok')}}]"
			});
		}

		function calculateYear5()
		{
			var total_budget = $('[name="total_budget"]').val(),
				year_1 = $('[name="year_1"]').val(),
				year_2 = $('[name="year_2"]').val(),
				year_3 = $('[name="year_3"]').val(),
				year_4 = $('[name="year_4"]').val();
			$('[name="year_5"]').val((+total_budget) - (+year_1) - (+year_2) - (+year_3) - (+year_4));
		}

	</script>
@endpush
