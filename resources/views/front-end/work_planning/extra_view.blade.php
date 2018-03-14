@if (in_array($type, [2, 3]))

	<div class='col-lg-12'>
	 	<div class='table-responsive'>
	        <p>{{trans('wp.forecast_index')}}</p>
	        <table class='table table-bordered '  style='background-color: #eef7f8;'>
	            <thead >
	                <tr>
	                    <th>#</th>
	                    <th>{{ trans('wp.first_year_prediction') }}</th>
	                    <th>{{ trans('wp.second_year_prediction') }}</th>
	                    <th>{{ trans('wp.third_year_prediction') }}</th>
	                    <th>{{ trans('wp.fourth_year_prediction') }}</th>
	                    <th>{{ trans('wp.fifth_year_prediction') }}</th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>{{ trans('wp.concerning_year') }}</td>
	                    <td>{{$data[31]}}</td>
	                    <td>{{$data[36]}}</td>
	                    <td>{{$data[41]}}</td>
	                    <td>{{$data[46]}}</td>
	                    <td>{{$data[51]}}</td>
	                </tr>
	                <tr>
	                    <td>{{ trans('wp.cracking_ratio_%') }}</td>
	                    <td>{{ round($data[32], 2) }}</td>
	                    <td>{{ round($data[37], 2) }}</td>
	                    <td>{{ round($data[42], 2) }}</td>
	                    <td>{{ round($data[47], 2) }}</td>
	                    <td>{{ round($data[52], 2) }}</td>
	                </tr>
	                <tr>
	                    <td>{{ trans('wp.rutting_depth_mm') }}</td>
	                    <td>{{ round($data[33], 2)}}</td>
	                    <td>{{ round($data[38], 2)}}</td>
	                    <td>{{ round($data[43], 2)}}</td>
	                    <td>{{ round($data[48], 2)}}</td>
	                    <td>{{ round($data[53], 2)}}</td>
	                </tr>
	                <tr>
	                    <td>{{ trans('wp.(IRI,mm/m)') }}</td>
	                    <td>{{ round($data[34], 2)}}</td>
	                    <td>{{ round($data[39], 2)}}</td>
	                    <td>{{ round($data[44], 2)}}</td>
	                    <td>{{ round($data[49], 2)}}</td>
	                    <td>{{ round($data[54], 2)}}</td>
	                </tr>
	                <tr>
	                    <td>MCI</td>
	                    <td>{{ max(round($data[35], 1), 0) }}</td>
	                    <td>{{ max(round($data[40], 1), 0) }}</td>
	                    <td>{{ max(round($data[45], 1), 0) }}</td>
	                    <td>{{ max(round($data[50], 1), 0) }}</td>
	                    <td>{{ max(round($data[55], 1), 0) }}</td>
	                </tr>
	                @if ($type == 3)
	                <tr>
	                	<td>{{trans('wp.repair_method')}}</td>

	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[56])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
                			'method_name' => @$method_final[intval($data[57])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
                			'method_name' => @$method_final[intval($data[58])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
                			'method_name' => @$method_final[intval($data[59])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
                			'method_name' => @$method_final[intval($data[60])]
	                	])
	                </tr>
	                <tr>
	                	<td>{{trans('wp.repair_classification')}}</td>
	                	<td>{{ @$method_final[intval($data[56])][6] }}</td>
	                	<td>{{ @$method_final[intval($data[57])][6] }}</td>
	                	<td>{{ @$method_final[intval($data[58])][6] }}</td>
	                	<td>{{ @$method_final[intval($data[59])][6] }}</td>
	                	<td>{{ @$method_final[intval($data[60])][6] }}</td>
	                </tr>
	                <tr>
	                	<td>{{ trans('wp.unit_cost') }}</td>
	                	<td>{{ number_format(@$method_final[intval($data[56])][$data['rmb_id'] - 1]) }}</td>
	                	<td>{{ number_format(@$method_final[intval($data[57])][$data['rmb_id'] - 1]) }}</td>
	                	<td>{{ number_format(@$method_final[intval($data[58])][$data['rmb_id'] - 1]) }}</td>
	                	<td>{{ number_format(@$method_final[intval($data[59])][$data['rmb_id'] - 1]) }}</td>
	                	<td>{{ number_format(@$method_final[intval($data[60])][$data['rmb_id'] - 1]) }}</td>
	                </tr>
	                <tr>
	                	<td>{{trans('wp.quantity')}}</td>
	                	<td>{{ intval($data[61]) }}</td>
	                	<td>{{ intval($data[62]) }}</td>
	                	<td>{{ intval($data[63]) }}</td>
	                	<td>{{ intval($data[64]) }}</td>
	                	<td>{{ intval($data[65]) }}</td>
	                </tr>
	                <tr>
	                	<td>{{trans('wp.unit_of_quantity')}}</td>
	                	<td>{{ @$unit[$method_final[intval($data[56])][7]] }}</td>
	                	<td>{{ @$unit[$method_final[intval($data[57])][7]] }}</td>
	                	<td>{{ @$unit[$method_final[intval($data[58])][7]] }}</td>
	                	<td>{{ @$unit[$method_final[intval($data[59])][7]] }}</td>
	                	<td>{{ @$unit[$method_final[intval($data[60])][7]] }}</td>
	                </tr>
	                <tr>
	                	<td>{{trans('wp.Amount')}} (VND)</td>
	                	<td>{{ number_format(intval($data[66])) }}</td>
	                	<td>{{ number_format(intval($data[67])) }}</td>
	                	<td>{{ number_format(intval($data[68])) }}</td>
	                	<td>{{ number_format(intval($data[69])) }}</td>
	                	<td>{{ number_format(intval($data[70])) }}</td>
	                </tr>
	                @endif
	            </tbody>
	        </table>
	    </div>
	</div>

@endif


    <div class='col-lg-4' style ='padding-top :15px;padding-bottom: 15px;'>	                	
		<div class='table-responsive'> 
			<p>{{trans('wp.Latest_Repair')}}</p>                    
		    <table class='table table-bordered' style='background-color: #eef7f8'>
		        <thead>
		            <tr>
		                <th>{{trans('wp.Name')}}</th>
		                <th>{{trans('wp.Value')}}</th>
		            </tr>
		        </thead>
		        <tbody>
		            <tr>
		                <td>{{trans('wp.LatestRepair')}}</td>
		                <td>{{$data['latest_repair_time']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.RepairCategory')}}</td>
		                <td>{{$data['repair_category']}}</td>						               
		            </tr>
		            <tr>
		                <td>{{trans('wp.RepairClassification')}}</td>
		                <td>{{$data['repair_classification']}}</td>              
		            </tr>
		            <tr>
		                <td>{{trans('wp.pavement_thickness')}}</td>
		                <td>{{$data['pavement_thickness']}}</td>              
		            </tr>
		        </tbody>
		    </table>     
		</div>
	</div>
	<div class='col-lg-4' style ='padding-top :15px;padding-bottom: 15px;'>	
		<div class='table-responsive'>
		   <p> {{trans('wp.Traffic_Volume')}} </p>
		    <table class='table table-bordered' style='background-color: #eef7f8'>
		        <thead>
		            <tr>
		                <th>{{trans('wp.Name')}}</th>
		                <th>{{trans('wp.Value')}}</th>
		            </tr>
		        </thead>
		        <tbody>
		            <tr>
		                <td>{{trans('wp.TrafficSurveyYear')}}</td>
		                <td>{{$data['traffic_survey_year']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.TotalTrafficVolume')}}</td>
		                <td>{{$data['total_traffic_volume']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.HeavyTrafficVolume')}}</td>
		                <td>{{$data['heavy_traffic']}}</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
    </div>

	<div class = 'col-lg-4' style ='padding-top :15px;padding-bottom: 15px;'>
		<div class='table-responsive'>
			<p>{{trans('wp.Result_Of_Pavement_Condition_Survey')}}</p> 
		    <table class='table table-bordered' style='background-color: #eef7f8'>
		        <thead>
		            <tr>
		                <th>{{trans('wp.Name')}}</th>
		                <th>{{trans('wp.Value')}}</th>
		            </tr>
		        </thead>
		        <tbody>
		            <tr>
		                <td>{{trans('wp.SurveyedYear/Month')}}</td>
		                <td>{{$data['pc_survey_time']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.SurveyedLane')}}</td>
		                <td>{{$data['survey_lane']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.PavementType')}}</td>
		                <td>{{$data['pavement_type']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Crack,%)')}}</td>
		                <td>{{$data['cracking']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Patching,%)')}}</td>
		                <td>{{$data['patching']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Pothole,%)')}}</td>
		                <td>{{$data['pothole']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Total,%)')}}</td>
		                <td>{{$data['cracking_ratio']}}</td>
		            </tr>
		             <tr>
		                <td>{{trans('wp.(Max,mm)')}}</td>
		                <td>{{$data['rut_max']}}</td>
		            </tr> 
		            <tr>
		                <td>{{trans('wp.(Average,mm)')}}</td>
		                <td>{{$data['rut_avg']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(IRI,mm/m)')}}</td>
		                <td>{{$data['iri']}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.MCI')}}</td>
		                <td>{{$data['mci']}}</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</div>

	<!-- <div class = 'col-lg-6' style ='padding-top:15px;'>
		<div class='table-responsive'>
		    <p>5.Resutl of Deterioration Evaluation</p>
		    <table class='table table-bordered' style='background-color: #eef7f8'>
		        <thead>
		            <tr>
		                <th>#</th>
		                <th>Crack</th>
		                <th>Rutting</th>
		                <th>IRI</th>
		            </tr>
		        </thead>
		        <tbody>
		            <tr>
		                <td>Epsilon</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		            <tr>
		                <td>Accumlate_Year_1</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		            <tr>
		                <td>Accumlate_Year_2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		            <tr>
		                <td>Accumlate_Year_3</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		            <tr>
		                <td>Accumlate_Year_4</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		            <tr>
		                <td>Accumlate_Year_5</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		             <tr>
		                <td>Accumlate_Year_6</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		             <tr>
		                <td>Accumlate_Year_7</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		                <td>Row 2</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</div> -->


 