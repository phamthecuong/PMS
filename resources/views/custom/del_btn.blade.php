<div style="display: inline-block;">
	{{ Form::open(array('route' => $route, 'method' => 'delete', 'onsubmit' => "return confirm('$confirm')")) }}
		<button class="btn btn-xs btn-default" type="submit" >{{$title}}</button>					
	{{ Form::close() }}				
</div>		
