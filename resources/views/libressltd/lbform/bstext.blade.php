
    <div class="form-group">
        {{ Form::label($title, null, ['class' => 'control-label']) }}
        @if (isset($hin))
        	<small>{{ $hin }}</small>
        @endif
        <?php
            $place_holder = $title;
        	$attrs = array('class' => 'form-control');
    		if (isset($place_holder))
    		{
    			$attrs['placeholder'] = $place_holder;
    		}
    		if (isset($validation))
    		{
    			$attrs = array_merge_recursive($attrs, $validation);
    		}
        ?>
        {{ Form::text($name, $value, $attrs) }}
    </div>