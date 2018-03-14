@extends('front-end.layouts.app')

@section('deterioration')
active
@endsection
@section('content')
@endsection
@section('extend_js')
<script src="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.js') }}"></script>
<!-- <script src="{{ asset('/sa/js/plugin/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script> -->
<script src="{{ asset('/front-end/js/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
$( document ).ready(function() {
	$.ajax({
		type:'GET',
		url:'/user/check_flg',
		data: {
			name_step: '{{$name_step}}',
			id : '{{$id}}',
			process: '{{$process}}',
		}
	}).done(function(msg)
	{
		if (msg == "100%")
		{
			location.reload();
		}
		if ($('#progress_bar').length)
		{
			console.log(1);
			$('#progress_bar').attr('style', "width: " + msg);
		}
		else
		{
		    $.SmartMessageBox({
				title : "{{trans('deterioration.process')}}",
				content : '<div class="progress progress-md progress-striped active"><div class="progress-bar bg-color-green" id="progress_bar" data-transitiongoal="75" aria-valuenow="75" style="width: ' + msg + ';">' + msg + '</div></div>'
			});
		}
		$(".MessageBoxButtonSection").remove();
	});
	setInterval(function(){
		$.ajax({
			type:'GET',
			url:'/user/check_flg',
			data: {
				name_step: '{{$name_step}}',
				id : '{{$id}}',
				process: '{{$process}}',
			}
		}).done(function(msg)
		{
			if (msg == "100%")
			{
				location.reload();
			}
		    // console.log($('#progress_bar').html());
			if ($('#progress_bar').length)
			{
				$('#progress_bar').attr('style', "width: " + msg);
				$('#progress_bar').html(msg);
			}
			else
			{
			    $.SmartMessageBox({
					title : "{{trans('deterioration.process')}}",
					content : '<div class="progress progress-md progress-striped active"><div class="progress-bar bg-color-green" id="progress_bar" data-transitiongoal="75" aria-valuenow="75" style="width: ' + msg + ';">' + msg + '</div></div>'
				});
			}
			$(".MessageBoxButtonSection").remove();
		});
	}, {{$timer}});
});
</script>
@endsection