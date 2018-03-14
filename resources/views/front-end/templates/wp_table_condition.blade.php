<div class="row" style="padding: 10px !important">
	<div class='col-lg-12'>
	 	<div class='table-responsive'>
	        <p>{{trans('wp.Prediction')}}</p>
	        <table class='table table-bordered '  style='background-color: #eef7f8;'>
	            <thead >
	                <tr>
	                    <th>#</th>
	                    <th>1st Year Prediction</th>
	                    <th>2nd Year Prediction</th>
	                    <th>3rd Year Prediction</th>
	                    <th>4th Year Prediction</th>
	                    <th>5th Year Prediction</th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>Concerning year(yyyy)</td>
	                    <td>{{$data[31]}}</td>
	                    <td>{{$data[36]}}</td>
	                    <td>{{$data[41]}}</td>
	                    <td>{{$data[46]}}</td>
	                    <td>{{$data[51]}}</td>
	                </tr>
	                <tr>
	                    <td>Cracking ratio (total %)</td>
	                    <td>{{$data[32]}}</td>
	                    <td>{{$data[37]}}</td>
	                    <td>{{$data[42]}}</td>
	                    <td>{{$data[47]}}</td>
	                    <td>{{$data[52]}}</td>
	                </tr>
	                <tr>
	                    <td>Rutting depth(mm)</td>
	                    <td>{{$data[33]}}</td>
	                    <td>{{$data[38]}}</td>
	                    <td>{{$data[43]}}</td>
	                    <td>{{$data[48]}}</td>
	                    <td>{{$data[53]}}</td>
	                </tr>
	                <tr>
	                    <td>IRI (mm/m)</td>
	                    <td>{{$data[34]}}</td>
	                    <td>{{$data[39]}}</td>
	                    <td>{{$data[44]}}</td>
	                    <td>{{$data[49]}}</td>
	                    <td>{{$data[54]}}</td>
	                </tr>
	                <tr>
	                    <td>MCI</td>
	                    <td>{{$data[35]}}</td>
	                    <td>{{$data[40]}}</td>
	                    <td>{{$data[45]}}</td>
	                    <td>{{$data[50]}}</td>
	                    <td>{{$data[55]}}</td>
	                </tr>
	                <tr>
	                	<td>{{trans('wp.repair_method')}}</td>

	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[start_index + 1])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[start_index + 2])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[start_index + 3])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[start_index + 4])]
	                	])
	                	@include('front-end.templates.wp_table_method', [
	                		'method_name' => @$method_final[intval($data[start_index + 5])]
	                	])
	                </tr>
	                <tr>
	                	<td>{{trans('wp.price')}}</td>
	                	<td>{{@$method_final[intval($data[start_index + 1])][0] * $data[12] }}</td>
	                	<td>{{@$method_final[intval($data[start_index + 2])][0] * $data[12] }}</td>
	                	<td>{{@$method_final[intval($data[start_index + 3])][0] * $data[12] }}</td>
	                	<td>{{@$method_final[intval($data[start_index + 4])][0] * $data[12] }}</td>
	                	<td>{{@$method_final[intval($data[start_index + 5])][0] * $data[12] }}</td>
	                </tr>

	            </tbody>
	        </table>
	    </div>
	</div>

    <div class='col-lg-4' style ='padding-top :15px;'>	                	
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
		                <td>{{$data[13]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.RepairLane')}}</td>
		                <td>{{$data[14]}}</td>
		               
		            </tr>
		            <tr>
		                <td>{{trans('wp.RepairMethod')}}</td>
		                <td>{{$data[15]}}</td>						               
		            </tr>
		            <tr>
		                <td>{{trans('wp.RepairClassification(*)')}}</td>
		                <td>{{$data[16]}}</td>              
		            </tr>
		        </tbody>
		    </table>    
		</div>
	</div>
	<div class='col-lg-4' style ='padding-top :15px;'>	
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
		                <td>{{$data[17]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.TotalTrafficVolume')}}</td>
		                <td>{{$data[18]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.HeavyTrafficVolume')}}</td>
		                <td>{{$data[19]}}</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
    </div>

	<div class = 'col-lg-4' style ='padding-top :15px;'>
		<div class='table-responsive' style="padding-bottom: 20px">
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
		                <td>{{$data[20]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.SurveyedLane')}}</td>
		                <td>{{$data[21]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.PavementType')}}</td>
		                <td>{{$data[22]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Crack,%)')}}</td>
		                <td>{{$data[23]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Patching,%)')}}</td>
		                <td>{{$data[24]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Pothole,%)')}}</td>
		                <td>{{$data[25]}}</td>
		            </tr>
		            <tr>
		                <td>{{trans('wp.(Total,%)')}}</td>
		                <td>{{$data[26]}}</td>
		            </tr>
		             <tr>
		                <td>{{trans('wp.(Max,mm)')}}</td>
		                <td>{{$data[27]}}</td>
		            </tr> 
		            <tr>
		                <td>{{trans('wp.(Average,mm)')}}</td>
		                <td>{{$data[28]}}</td>
		            </tr>
		             <tr>
		                <td>{{trans('wp.(IRI,mm/m)')}}</td>
		                <td>{{$data[29]}}</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</div>
</div>	
 