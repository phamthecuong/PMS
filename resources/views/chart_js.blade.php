@extends('front-end.layouts.app')

@section('inputting_system')
    active
@endsection

@section('side_menu_inputting')
    active
@endsection

@section('content')
	<html>
		<div>
			<canvas id="nestedDoughnut"></canvas>
		</div>	
	</html>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.js"></script>
<script>
//var ctx = document.getElementById("myChart");
// $(function() {
// 	$('#myChart').attr('width', '300px');
// 	$('#myChart').attr('height', '400px');
// });
//var myChart = new Chart(ctx, {
	$(function() {
		$('#nestedDoughnut').attr('width', '100px');
		$('#nestedDoughnut').attr('height', '400px');
	});
    var resourceChartElement = document.getElementById("nestedDoughnut");
    var resourceChart = new Chart(resourceChartElement, 
    {
    	type: "doughnut",
    	data:{
    		datasets:[
    			{
	    			backgroundColor:
	    				["#3366CC","#DC3912","#FF9900","#109618","#990099","#3B3EAC"],
	    			hoverBackgroundColor: ["#3366CC","#DC3912","#FF9900","#109618","#990099","#3B3EAC"],
	    			data: [0.0,0.0,8.31,10.43,84.69,0.84]
    			},
    			{
    				backgroundColor: ["blue","red","black"],
    				hoverBackgroundColor: ["#3366CC","#DC3912","#FF9900"],
    				data: [10,80,10]
    			},
    			{
    				backgroundColor: ["balck","yellow","black"],
    				hoverBackgroundColor: ["#3366CC","#DC3912","#FF9900"],
    				data: [10,80,10]
    			}
    		],
			labels:[
				"resource-group-1",
				"resource-group-2",
				"Data Services - Basic Database Days",
				"Data Services - Basic Database Days",
				"Azure App Service - Basic Small App Service Hours",
				"resource-group-2 - Other"
			]
    	}

    });
//});
</script>
@endpush