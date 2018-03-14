<div class="form-group">
    {{ Form::label($title, null, ['class' => 'control-label']) }}
    @foreach ($items as $item)
  	<div class="radio">
    	<label>
      		<input type="radio" ng-model= "{{@$scope_form.'.'.	@$model}}" ng-class="{!! @$ng_class !!}" name="{{$name}}" id="optionsRadios1" value="{{ $item['value'] }}" {{ ($value == $item['value']) ? 'checked' : '' }}>
      		{{ $item['name'] }}
    	</label>
  	</div>
  	@endforeach
	<span class="help-block" style="color: #b94a48;" ng-show="errors.{{@$model}}[0]" ng-bind="errors.{{@$model}}[0].toString()"></span>
</div>

