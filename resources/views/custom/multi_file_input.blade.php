<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    <label for="{{$name}}" class="control-label">{{$title}}</label>    
    <input class="form-control" id="files" name="{{$name}}[]" type="file" multiple accept="image/*" />
    <output id="result" />

	@if (isset($errors) && $errors->has($name))
		@foreach ($errors->get($name) as $error)
			<span class="help-block">
	            <strong>{{ $error }}</strong>
	        </span>
		@endforeach
	@endif
</div>

<script type="text/javascript">
	window.onload = function() {
		//Check File API support
		if (window.File && window.FileList && window.FileReader) {
			var filesInput = document.getElementById("files");
			filesInput.addEventListener("change", function(event) {
				var files = event.target.files;
				var output = document.getElementById("result");
				output.innerHTML = '';
				for (var i = 0; i < files.length; i++) {
					var file = files[i];
					// Only pics
					if (!file.type.match('image')) {
						continue;						
					}

					var picReader = new FileReader();
					picReader.addEventListener("load",function(event) {
						var picFile = event.target;
						var span = document.createElement("span");
						span.innerHTML = "<img style='height: 100px;margin: 10px' src='" + picFile.result + "'" + "title='" + picFile.name + "'/>";
						output.insertBefore(span, null);
					});
					// Read the image
					picReader.readAsDataURL(file);
				}
			});
		} else {
			console.log("Your browser does not support File API");
		}
	}
</script>

