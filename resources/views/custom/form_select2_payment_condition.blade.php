<div class="form-group" style="width: 100%">
	<select class="select2"  onchange="{{ @$onchange }}" ng-model="{{ @$model_name }}" style="width: 100%" name="{{ $name }}">
<!-- 	    <option value="" >{{trans('back_end.all')}}</option>
	    	     -->	    	    @if(isset($items) && $items != '')
	    @foreach ($items as $item)
	        <option value="{{ $item['value'] }}" {{ ($item['value'] == $value) ? "selected" : "" }}>{{ $item['name'] }}</option>
	    @endforeach
	    @endif
	</select>
</div>