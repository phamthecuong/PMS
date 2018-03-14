<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
	<?php
    	$attrs = array('class' => 'form-control input');
		if (isset($place_holder))
		{
			$attrs['placeholder'] = $place_holder;
		}
		if (isset($validation))
		{
			$attrs = array_merge($attrs, $validation);
		}
		if (isset($attribute))
		{
			$attrs = array_merge($attrs, $attribute);
		}
    ?>
    @if (isset($title) && (strlen($title) > 0))
    	{{ Form::label($name, $title, array('class' => 'control-label')) }}
        @if (isset($hin) && (strlen($hin) > 0))
        	<a href="#" data-toggle="tooltip" title="{!! $hin !!}">
        		<i class="icon-prepend fa fa-question-circle"></i>
        	</a>
        @endif
    @endif
    {{ Form::text($name, $value, $attrs) }}
    @if (isset($errors) && $errors->has($name))
    	@foreach ($errors->get($name) as $error)
			<div class="note note-error">{{ $error }}</div>
		@endforeach
    @endif
</div>