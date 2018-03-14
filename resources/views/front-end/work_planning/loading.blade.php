@extends('front-end.layouts.app')

@section('work_planning')
active
@endsection

@section('content')
@endsection

@push('script')
	<script src="{{ asset('/front-end/js/jQuery-MultiSelect-master/jquery.multiselect.js') }}"></script>
	<script src="{{ asset('/front-end/js/datepicker/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('/sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
	<script type="text/javascript">
	$( document ).ready(function() {
		function reloadProgress() {
			$.ajax({
				type:'GET',
				url: '/user/work_planning/check_flg',
				data: {
					id : '{{$id}}',
					type: '{{ @$type }}',
					list: '{{ @$list }}'
				}
			}).done(function(msg) {
				if (msg == "100%") {
					location.reload();
				} if ($('#progress_bar').length) {
					$('#progress_bar').attr('style', "width: " + msg);
					$('#progress_bar').html(msg);
				} else {
				    $.SmartMessageBox({
						title : "{{trans('wp.process')}}",
						content : '<div class="progress progress-md progress-striped active"><div class="progress-bar bg-color-green" id="progress_bar" data-transitiongoal="75" aria-valuenow="75" style="width: ' + msg + ';">' + msg + '</div></div>'
					});
				}
				$(".MessageBoxButtonSection").remove();
			});	
		}
		reloadProgress();
		setInterval(reloadProgress, {{$timer}});
	});
	</script>
@endpush
