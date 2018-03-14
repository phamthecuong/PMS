{{ Form::open(array('route' => $route, 'method' => 'delete', 'onsubmit' => "return confirm('$confirm')")) }}
<button class="btn btn-xs btn-danger" title="delete" type="submit" > <i class="fa fa-trash-o" aria-hidden="true"></i></button>
{{ Form::close() }}
