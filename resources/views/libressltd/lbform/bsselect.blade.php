<div class="form-group">
    {{ Form::label($title, null, ['class' => 'control-label']) }}
	<select class="form-control" name="{{ $name }}">
    	@foreach ($items as $item)
    		<option value="{{ $item['value'] }}" {{ ($item['value'] == $value) ? "selected" : "" }}>{{ $item['name'] }}</option>
    	@endforeach
	</select>
</div>
