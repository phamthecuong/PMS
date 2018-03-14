<!--
	author: NGUYEN HOANG ANH
	parameters:
	$name
	$title
	$hin
	$placeholder
	$attribute
-->

<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
	<?php
    	$attrs = array('class' => 'form-control');
		if (isset($place_holder))
		{
			$attrs['placeholder'] = $placeholder;
		}
		if (isset($attribute))
		{
			$attrs = array_merge($attrs, $attribute);
		}
    ?>

	{{ Form::label($name, $title, array('class' => 'control-label')) }}
    {{ Form::password($name, $attrs) }}

    @if (isset($hin) && (strlen($hin) > 0))
	    <div class="note">
	        {{ $hin }}
	    </div>
    @endif
    @if (isset($errors) && $errors->has($name))
    	@foreach ($errors->get($name) as $error)
			<div class="note note-error">{{ $error }}</div>
		@endforeach
    @endif
</div>
