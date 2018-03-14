<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
	@if (isset($title))
		<label class="control-label">
			<i class="icon-append fa fa-calendar"></i>&nbsp;
			{{ $title }}
			@if (isset($hint) && (strlen($hint) > 0))
		    	<a href="#" data-toggle="tooltip" title="{!! $hint !!}">
		    		<i class="icon-prepend fa fa-question-circle"></i>
		    	</a>
		    @endif
		</label>
	@endif

    <?php
    	$attrs = array('class' => 'form-control ympicker');
		if (isset($place_holder))
		{
			$attrs['placeholder'] = $place_holder;
		}
		if (isset($validation))
		{
			$attrs = array_merge($attrs, $validation);
		}
		if (isset($disabled))
		{
			$attrs['ng-disabled'] = $disabled;
		}
		$attrs['ng-model'] = @$model_name;
		$attrs['ng-class'] = '{error: errors.'.$name.'[0]}';
    ?>
    {{ Form::text($name, $value, $attrs) }}
    <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $name }}[0]" ng-bind="errors.{{ $name }}[0].toString()"></span>
	
    @if ($errors->has($name))
    	@foreach ($errors->get($name) as $error)
			<div class="note note-error">{{ $error }}</div>
		@endforeach
    @endif
</div>