
@if (isset ($method_name) )
	<td bgcolor="{{$method_name[4]}}">
		{{$method_name[5]}}
	</td>
@else
	<td bgcolor = 'white'>
		{{trans('wp.no_repair_method')}}
	</td>
@endif



