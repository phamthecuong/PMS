<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    <label for="{{$name}}" class="control-label">{{$title}}</label>    
    <input class="form-control" name="{{$name}}" type="file" multiple accept="video/mp4,video/x-m4v,video/*" />

	@if (isset($errors) && $errors->has($name))
		@foreach ($errors->get($name) as $error)
			<span class="help-block">
	            <strong>{{ $error }}</strong>
	        </span>
		@endforeach
	@endif
</div>
