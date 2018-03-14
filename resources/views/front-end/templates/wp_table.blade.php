<div class = 'col-lg-12'>
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

</div>	
 