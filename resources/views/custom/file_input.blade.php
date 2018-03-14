<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    <?php
    	$attrs = array('class' => 'form-control', 'accept' => 'image/*');
		if (isset($attribute))
		{
			$attrs = array_merge($attrs, $attribute);
		}
    ?>

    {{ Form::label($name, $title, array('class' => 'control-label')) }}
    {{ Form::file($name, $attrs) }}
					
	@if (isset($image_url) && strlen($image_url) > 0)					
		<div>				
			<img src="{{$image_url}}" width="100px" height="100px" />			
		</div>				
	@endif					
						
	@if (isset($errors) && $errors->has($name))					
		@foreach ($errors->get($name) as $error)				
			<div class="note note-error">{{ $error }}</div>			
		@endforeach				
	@endif	
</div>
