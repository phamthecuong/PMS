<!-- <div class="box-body"> -->
    <div class="form-group">
            <?php //dd($value);?>
        {{ Form::label($title, null, ['class' => 'control-label']) }}
            <?php
            $disable = '';
            if ($name == 'branch' && $value != null )
            {
                $disable = 'disabled';
            }
            //dd(empty($value));
            ?>
        	<select class="form-control select2" {{$disable}} name="{{ $name }}">
            	@foreach ($items as $item)
            		<option value="{{ $item['value'] }}" {{ ($item['value'] == $value) ? "selected" : "" }}>{{ $item['name'] }}</option>
            	@endforeach
        	</select>
    </div>
<!-- </div> -->