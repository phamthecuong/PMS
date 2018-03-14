@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection

@if ($history)
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
		<li>
			{{trans('menu.home')}}
		</li>
		<li>
			{{trans('menu.deterioration')}}
		</li>
		@if ($history)
		<li>{{ trans('menu.det_show_history') }}</li>
		@else
	    <li>{{ trans('menu.start_process_deterioration') }}</li>
	    @endif
	</ol>
@endsection

@push('css')
	<style type="text/css">
		.ui-jqgrid .ui-jqgrid-titlebar {
		    display: none !important;
		}
		.text-center {
		    vertical-align: middle !important;
		}
	</style>
@endpush

@section('content')

@include('front-end.layouts.partials.heading', [
	'icon' => 'fa-cube',
	'text1' => trans('deterioration.deterioration'),
	'text2' => trans('menu.data_summary_for_estimation')
])

{!!Form::open(array('method' => 'POST', 'onsubmit' =>'return dataSet()' ,'id' => 'save' , 'route' => array('get.data.summary', $session_id)))!!}
	<input type="hidden" name="crack_data_json" value="{{ $crack_data_json }}">
	<input type="hidden" name="rut_data_json" value="{{ $rut_data_json }}">
	<input type="hidden" name="iri_data_json" value="{{ $iri_data_json }}">

	<section id="widget-grid" class="">
		<div class="row">
			@if (
				$total_ac_after == 0 || $total_bt_after == 0 || $total_cc_after == 0 || 
				$rut_total_ac_after == 0 || $rut_total_bt_after == 0 || $rut_total_cc_after == 0 || 
				$iri_total_ac_after == 0 || $iri_total_bt_after == 0 || $iri_total_cc_after == 0
			)
				<article class="col-sm-12">
					<div class="alert alert-warning fade in">
						<button class="close" data-dismiss="alert">
							×
						</button>
						<i class="fa-fw fa fa-warning"></i>
						<strong>{{trans('deterioration.warning')}}</strong> {{trans('deterioration.dataset_record_not_enough_for_estimation')}}
					</div>
				</article>
			@endif

			<div class="col-sm-12 col-md-12 col-lg-12">
				@box_open(trans('menu.process_info'))
				<div>	
					<div class="widget-body">
						<ul class="list-unstyled">
							<li>
								<h1><i class="fa fa-bank"></i>&nbsp;&nbsp;<span>{{trans('deterioration.region_target')}}: </span><small>
									{{$organization}}
								</small></h1>
							</li>
							<li>
								<h1><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<span>{{trans('deterioration.year')}}: </span><small>{{$year}}</small></h1>
							</li>
						</ul>
					</div>
				</div>
				@box_close
			</div>
			<!-- NEW WIDGET START -->
			<!-- crack -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				@box_open('1. ' . trans('deterioration.cracking_ratio'))
					<div>
						<div class="widget-body">
							<!-- ibox-content -->
							<div class="ibox-content">
								<!-- table reponsive  -->
								<div class="table-responsive">
									<!--  table begin-->
									<table id="crack" class="table table-bordered text-center " style="">
		                                <thead class="text-center" >
		                                	<!-- row 1 -->
			                                <tr>
			                                    <th class="text-center" rowspan="2" colspan="2">{{trans('deterioration.consition_rank')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.AT')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.bst')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.CC')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="4">{{trans('deterioration.Total')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="2" colspan="1" style="width : 10%;">{{trans('deterioration.estimation')}}</th>
			                                </tr>
			                                <!-- row 2 -->
			                                <tr>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.after')}}</th>
			                                </tr>
		                                </thead>
		                                <tbody>
		                                <?php 
		                                	foreach ($crack_data as $dnd) 
		                                	{
		                                ?>
			                               <tr>
			                                    <td  rowspan="1" colspan="1">{{ $dnd['rank'] }}</td>
			                                    <td rowspan="1" colspan="1">{{ $dnd['condition'] }}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td class='crack_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_before'] }}</td>
			                                    <td class='crack_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_after'] }}</td>
			                                    <!--  -->
			                                    <td class='crack_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_before'] }}</td>
			                                    <td class='crack_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_after'] }}</td>
			                                    <!--  -->
			                                    <td class='crack_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_before'] }}</td>
			                                    <td class='crack_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_after'] }}</td>
			                                    <!--  -->
			                                    <td class='crack_before_total{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='crack_before_total_percent{{$dnd["rank"]}}' rowspan="1" colspan="1">%</td>
			                                    <td class='crack_after_total{{$dnd["rank"]}}' rowspan="1" colspan="1">-</td>
			                                    
			                                    <td class='crack_after_total_percent{{$dnd["rank"]}}' rowspan="1" colspan="1">-</td>
			                                    
			                                    <!--  estimation -->
			                                    <td  rowspan="1" colspan="1" > <input type="checkbox" class="checkboxCrack{{$dnd['rank']}}" name="checkboxCrack{{$dnd['rank']}}" value="{{$dnd['rank']}}"  onchange="checkCheckbox({{$dnd['rank']}} , {{$length_crack}} , 'Crack');"></td>
			                                </tr>
			                            <?php 
			                         		}
			                            ?>
			                            <!-- crack  foter total -->
				                            <tr>
			                                    <td rowspan="1" colspan="2">{{trans('deterioration.Total')}}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $total_ac_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $total_bt_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $total_cc_after }}</td>
			                                    
			                                    <td id = "total_before" rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td id = "total_after_crack" rowspan="1" colspan="1"></td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <!--  for_estimation -->
			                                    <td rowspan="1" colspan="1">-</td>
			                                    
			                                </tr>
		                                </tbody>
		                            </table>
		                            <!--  -->
	                            </div>
	                        <!--  end ibox-content -->
	                        </div>
						<!-- end widget content -->
						</div>
					<!-- end widget div -->
					</div>
				@box_close
			</article>

			<!-- WIDGET END -->
			<!-- rut -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				@box_open('2. ' . trans('deterioration.rutting_depth'))
					<div>
						<div class="widget-body">
							<div class="ibox-content">
								<div class="table-responsive">
									<table class="table table-bordered text-center" style="">
		                                <thead class="text-center" >
			                                <tr>
			                                    <th class="text-center" rowspan="2" colspan="2">{{trans('deterioration.consition_rank')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.AT')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.bst')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.CC')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="4">{{trans('deterioration.Total')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="2" colspan="1" style="width : 10%;">{{trans('deterioration.estimation')}}</th>
			                                </tr>
			                                <!-- row 2 -->
			                                <tr>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.after')}}</th>
			                                </tr>
		                                </thead>
		                                <tbody>
		                                <?php 
		                                	foreach ($rut_data as $dnd) 
		                                	{
		                                ?>
			                               <tr>
			                                    <td  rowspan="1" colspan="1">{{ $dnd['rank'] }}</td>
			                                    <td rowspan="1" colspan="1">{{ $dnd['condition'] }}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td class='rut_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_before'] }}</td>
			                                    <td class='rut_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_after'] }}</td>
			                                    <!--  -->
			                                    <td class='rut_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_before'] }}</td>
			                                    <td class='rut_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_after'] }}</td>
			                                    <!--  -->
			                                    <td class='rut_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_before'] }}</td>
			                                    <td class='rut_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_after'] }}</td>
			                                    <!-- end ac bt cc -->
			                                    <!-- total  -->
			                                    <td class='rut_total_before{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='rut_total_percent_before{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='rut_total_after{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='rut_total_percent_after{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    
			                                    <!--  estimation -->

			                                    <td  rowspan="1" colspan="1"> <input type="checkbox" class="checkboxRut{{$dnd['rank']}}" name="checkboxRut{{$dnd['rank']}}" value="{{$dnd['rank']}}" onchange="checkCheckbox({{$dnd['rank']}} , {{$length_rut}} , 'Rut');"></td>
			                                </tr>
			                            <?php 
			                         		}
			                            ?>
			                            <!-- rut footer total  -->
				                            <tr>
			                                    <td rowspan="1" colspan="2">{{ trans('deterioration.Total') }}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $rut_total_ac_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $rut_total_bt_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $rut_total_cc_after }}</td>
			                                    
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td id="total_after_rut" rowspan="1" colspan="1"></td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <!--  for_estimation -->
			                                    <td rowspan="1" colspan="1"> -</td>
			                                    
			                                </tr>
		                                </tbody>
		                        <!--  -->
		                            </table>
	                            </div>
							<!--  -->
	                        </div>
						<!-- end widget content -->
					</div>
					<!-- end widget div -->
				@box_close
			</article>
			<!-- iri -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				@box_open('3. ' . trans('deterioration.iri'))
					<div>
						<div class="widget-body">
							<div class="ibox-content">
								<div class="table-responsive">
									<table class="table table-bordered text-center" style="">
		                                <thead class="text-center" >
			                                <tr>
			                                    <th class="text-center" rowspan="2" colspan="2">{{trans('deterioration.consition_rank')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.AT')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.bst')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.CC')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="4">{{trans('deterioration.Total')}}</th>
			                                    <!--  -->
			                                    <th class="text-center" rowspan="2" colspan="1" style="width : 10%;">{{trans('deterioration.estimation')}}</th>
			                                </tr>
			                                <!-- row 2 -->
			                                <tr>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="1">{{trans('deterioration.after')}}</th>
			                                    
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.before')}}</th>
			                                    <th class="text-center" rowspan="1" colspan="2">{{trans('deterioration.after')}}</th>
			                                </tr>
		                                </thead>
		                                <tbody>
		                                <?php 
		                                	foreach ($iri_data as $dnd) 
		                                	{
		                                ?>
			                               <tr>
			                                    <td rowspan="1" colspan="1">{{ $dnd['rank'] }}</td>
			                                    <td rowspan="1" colspan="1">{{ $dnd['condition'] }}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td class='iri_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_before'] }}</td>
			                                    <td class='iri_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['AC_after'] }}</td>
			                                    <!--  -->
			                                    <td class='iri_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_before'] }}</td>
			                                    <td class='iri_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['BT_after'] }}</td>
			                                    <!--  -->
			                                    <td class='iri_before{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_before'] }}</td>
			                                    <td class='iri_after{{$dnd["rank"]}}' rowspan="1" colspan="1">{{ $dnd['CC_after'] }}</td>
			                                    <!--  -->
			                                    <td class='iri_total_before{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='iri_total_percent_before{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='iri_total_after{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <td class='iri_total_percent_after{{$dnd["rank"]}}' rowspan="1" colspan="1"></td>
			                                    <!--  estimation -->
			                                    <td  rowspan="1" colspan="1"> <input type="checkbox" class="checkboxIri{{$dnd['rank']}}" name="checkboxIri{{$dnd['rank']}}" value="{{$dnd['rank']}}"  onchange="checkCheckbox({{$dnd['rank']}} , {{$length_iri}} , 'Iri');"></td>
			                                </tr>
			                            <?php 
			                         		}
			                            ?>
			                            <!-- footer iri  -->
				                            <tr>
			                                    <td rowspan="1" colspan="2">{{trans('deterioration.Total')}}</td>
			                                    <!-- ac bt cc total  -->
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $iri_total_ac_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $iri_total_bt_after }}</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">{{ $iri_total_cc_after }}</td>
			                                    
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <td id="total_after_iri" rowspan="1" colspan="1"></td>
			                                    <td rowspan="1" colspan="1">-</td>
			                                    <!--  for_estimation -->
			                                    <td rowspan="1" colspan="1"> -</td> 
			                                </tr>
		                                </tbody>
		                            </table>
	                           </div>
	                        </div>
						<!-- end widget content -->
						</div>
					<!-- end widget div -->
					</div>
				@box_close
			</article>
		</div>
		<!-- end row -->
		<div class="well" style="text-align: right">
			@if ($history)
				<a href="/user/deterioration/benchmarking/{{$session_id}}" class="btn bg-color-blueLight txt-color-white">
					{{trans('deterioration.next_bm')}}
				</a>
			@else
				<button type="button" class="btn bg-color-blueLight txt-color-white" onclick="goBack(this)">
					{{trans('deterioration.exit')}}
				</button>
				@if (
					$total_ac_after == 0 || $total_bt_after == 0 || $total_cc_after == 0 || 
					$rut_total_ac_after == 0 || $rut_total_bt_after == 0 || $rut_total_cc_after == 0 || 
					$iri_total_ac_after == 0 || $iri_total_bt_after == 0 || $iri_total_cc_after == 0
				)
				@else
					<button id="save" type="submit" class="btn bg-color-blueLight txt-color-white">
						{{trans('deterioration.estimation_button')}}
					</button>
				@endif
			@endif
		</div>
	</section>
{!!Form::close()!!}
@endsection

@push('script')

	<script type="text/javascript">	
		$(document).ready(function() {
			// setup csrf for all ajax call
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('input[name="_token"]').val()
			    }
			});
			
			// 		set total col  ac.after + bt.after + cc.after ----------------------------------------------------------------------------
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				var total = 0;
				$('td.crack_after'+i).each(function()
				{
					total += parseInt($(this).text() );
					
				});
				$('td.crack_after_total'+i).text(total);
			}
			// 	set value total in all row in col total.after -------------------------------------------------------------------
			var total_cr_after = 0;
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				$('td.crack_after_total'+i).each(function()
				{
					total_cr_after += parseInt( $(this).text() );
					
				});
			}
			$('td#total_after_crack').text(total_cr_after);
			// 		set value percent total  after   ---------------------------------------------------
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				var value_total = parseInt($('td.crack_after_total'+i).text());
				var value = value_total/total_cr_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.crack_after_total_percent'+i).text(value+'%');
			}
			// set total col  ac.before + bt.before + cc.before ----------------------------------------------------------------------------
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				var total = 0;
				$('td.crack_before'+i).each(function()
				{
					total += parseInt($(this).text() );
				});
				$('td.crack_before_total'+i).text(total);
			}
			// 		set value total in all row in col total.before -------------------------------------------------------------------
			var total_cr_before = 0;
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				$('td.crack_before_total'+i).each(function()
				{
					total_cr_before += parseInt( $(this).text() );
					
				});
			}
			// 		set value percent total  before   ---------------------------------------------------
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				
				var value_total = parseInt($('td.crack_before_total'+i).text());
				var value = value_total/total_cr_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.crack_before_total_percent'+i).text(value+'%');
			}
			// $('input[class^="checkboxCrack').attr('checked','checked');
			$('input.checkboxCrack1').click(function () {
		        return false;
		    });
			
			for (var i = 1; i <= {{$crack_selected_rank}}; i++ )
			{
				$('input.checkboxCrack'+i).attr('checked','checked');
			}

			// $('input.checkboxCrack2').attr('checked','checked');
			$('input.checkboxCrack2').click(function () {
		        return false;
		    });
			// rut ==================================
			// 		set total col  ac.after + bt.after + cc.after ----------------------------------------------------------------------------
			for (var i = 1; i <= {{$length_rut}} ; i++ )
			{
				var total = 0;
				$('td.rut_after'+i).each(function()
				{
					total += parseInt($(this).text() );
				});
				$('td.rut_total_after'+i).text(total);
			} 
			// 	set value total in all row in col total.after -------------------------------------------------------------------
			var total_rut_after = 0;
			for (var i=1; i<={{$length_rut}} ; i++ )
			{
				$('td.rut_total_after'+i).each(function()
				{
					total_rut_after += parseInt( $(this).text() );
				});
			}
			$('td#total_after_rut').text(total_rut_after);
			// 		set value percent total  after   ---------------------------------------------------
			for (var i=1; i<={{$length_rut}} ; i++ )
			{
				var value_total = parseInt($('td.rut_total_after'+i).text());
				var value = value_total/total_rut_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.rut_total_percent_after'+i).text(value+'%');
			}
			// set total col  ac.before + bt.before + cc.before ----------------------------------------------------------------------------
			for (var i=1; i<={{$length_rut}} ; i++ )
			{
				var total = 0;
				$('td.rut_before'+i).each(function()
				{
					total += parseInt($(this).text() );
					
				});
				$('td.rut_total_before'+i).text(total);
			}
			// 		set value percent total  before   ---------------------------------------------------
			for (var i=1; i<={{$length_rut}} ; i++ )
			{
				
				var value_total = parseInt($('td.rut_total_before'+i).text());
				var value = value_total/total_rut_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.rut_total_percent_before'+i).text(value+'%');
			}
			// $('input[class^="checkboxRut').attr('checked','checked');
			$('input.checkboxRut1').click(function () {
		        return false;
		    });
			
			for (var i = 1; i <= {{$rut_selected_rank}}; i++)
			{
				$('input.checkboxRut'+i).attr('checked','checked');
			}
			
			
			// $('input.checkboxRut2').attr('checked','checked');
			$('input.checkboxRut2').click(function () {
		        return false;
		    });
			// iri =================================
			// 		set total col  ac.after + bt.after + cc.after ----------------------------------------------------------------------------
			for (var i=1; i<={{$length_iri}} ; i++ )
			{
				var total = 0;
				$('td.iri_after'+i).each(function()
				{
					total += parseInt($(this).text() );
				});
				$('td.iri_total_after'+i).text(total);
			} 
			// 	set value total in all row in col total.after -------------------------------------------------------------------
			var total_iri_after = 0;
			for (var i=1; i<={{$length_iri}} ; i++ )
			{
				$('td.iri_total_after'+i).each(function()
				{
					total_iri_after += parseInt( $(this).text() );
				});
			}
			$('td#total_after_iri').text(total_iri_after);
			// 		set value percent total  after   ---------------------------------------------------
			for (var i=1; i<={{$length_iri}} ; i++ )
			{
				var value_total = parseInt($('td.iri_total_after'+i).text());
				var value = value_total/total_iri_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.iri_total_percent_after'+i).text(value+'%');
			}
			// set total col  ac.before + bt.before + cc.before ----------------------------------------------------------------------------
			for (var i=1; i<={{$length_iri}} ; i++ )
			{
				var total = 0;
				$('td.iri_before'+i).each(function()
				{
					total += parseInt($(this).text() );
				});
				$('td.iri_total_before'+i).text(total);
			}
			// 		set value percent total  before   ---------------------------------------------------
			for (var i=1; i<={{$length_iri}} ; i++ )
			{
				
				var value_total = parseInt($('td.iri_total_before'+i).text());
				var value = value_total/total_iri_after;
				value *= 100 ;
				// value = value.toFixed(4);
				value = value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0] ;
				$('td.iri_total_percent_before'+i).text(value+'%');
			}
			
			for (var i = 1; i <= {{$iri_selected_rank}}; i++)
			{
				$('input.checkboxIri'+i).attr('checked','checked');
			}

			$('input.checkboxIri1').click(function () {
		        return false;
		    });

			// $('input.checkboxIri2').attr('checked','checked');
			$('input.checkboxIri2').click(function () {
		        return false;
		    });
			
			@if ($history)
				$('input[class^="checkboxIri').attr("disabled", true);
				$('input[class^="checkboxRut').attr("disabled", true);
				$('input[class^="checkboxCrack').attr("disabled", true);
			@endif
		});
		
		function checkCheckbox(dnd ,length ,value )
		{
			// console.log(value) ;
			// return false;
			// console.log(dnd);
			// console.log( $('input.checkbox'+value+dnd).prop('checked'));
			if ( $('input.checkbox'+value+dnd).prop('checked') == true)
			{
				// console.log(1);
				for (var i=1; i<=dnd ; i++ )
				{
					
					$('input.checkbox'+value+i).prop('checked', true);
				}
			}
			else
			{
				
				for (var i=dnd; i<= length; i++ )
				{
					$('input.checkbox'+value+i).prop('checked', false);
				}
			}
		}
		
		

		function dataSet()
		{
			showLoading();
			// $("#crack").find("[type=checkbox]" ).each(function()
			var crackTotal = 0;
			for (var i=1; i<={{$length_crack}} ; i++ )
			{
				if ( $('input.checkboxCrack'+i).prop('checked')==true)
				{
					crackTotal += 1;
				}
			}
			
			if (crackTotal < 5)
			{
				$.SmartMessageBox({
					title : "{{trans('deterioration.error')}}",
					content : "{{trans('deterioration.please_choose_at_least_5_crack')}}",
					buttons : "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}
			else if (crackTotal > 7)
			{
				$.SmartMessageBox({
					title : "{{trans('deterioration.error')}}",
					content : "{{trans('deterioration.maximum_crack_is_7')}}",
					buttons : "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}


			var rutTotal = 0;
			for (var i=1; i<={{$length_rut}} ; i++ )
			{
				if ( $('input.checkboxRut'+i).prop('checked')==true)
				{
					rutTotal += 1;
				}
			}
			// console.log(rutTotal);
			if (rutTotal < 5)
			{
				$.SmartMessageBox({
					title : "{{trans('deterioration.error')}}",
					content : "{{trans('deterioration.please_choose_at_least_5_rut')}}",
					buttons : "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}
			else if (rutTotal > 7)
			{
				$.SmartMessageBox({
					title : "{{trans('deterioration.error')}}",
					content : "{{trans('deterioration.maximum_rut_is_7')}}",
					buttons : "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}

			var iriTotal = 0;
			for (var i=1; i<= {{$length_iri}}; i++)
			{
				if ($('input.checkboxIri'+i).prop('checked') == true)
				{
					iriTotal += 1;
				}
			}
			
			if (iriTotal < 5)
			{
				$.SmartMessageBox({
					title: "{{trans('deterioration.error')}}",
					content: "{{trans('deterioration.please_choose_at_least_5_iri')}}",
					buttons: "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}
			else if (iriTotal > 7)
			{
				$.SmartMessageBox({
					title : "{{trans('deterioration.error')}}",
					content : "{{trans('deterioration.maximum_iri_is_7')}}",
					buttons : "[{{trans('deterioration.ok')}}]"
				});
				hideLoading();
				return false;
			}

			return true;
		}
		
		function goBack(btn)
		{
			$(btn).addClass('disabled');
			
			showLoading();

			$.post("{{ route('get.back', ['session_id' => $session_id]) }}", function(){
				location.href = "{{ route('deterioration.init') }}";
			});
		}	
	</script>
@endpush
