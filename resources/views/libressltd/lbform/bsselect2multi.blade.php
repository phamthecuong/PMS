<div class="form-group">
    {{ Form::label($title, null, ['class' => 'control-label']) }}
	<select class="form-control  select2" name="{{ $name }}" multiple="multiple">
    	@foreach ($items as $item)
    		<option value="{{ $item['value'] }}" 
				@if (is_array($value))
	    			@foreach ($value as $v)
	    				@if ($v["value"] == $item["value"])
	    					selected
	    				@endif
	    			@endforeach
    			@endif
    		>{{ $item['name'] }}</option>
    	@endforeach
	</select>
</div>
