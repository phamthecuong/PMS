<article>
@if(Session::get('message') || $errors->any())
	@if(Session::get('message'))
        <div class="alert {{ Session::get('class') }} fade in">
			<button class="close" data-dismiss="alert">×</button>
			<i class="fa-fw fa fa-times"></i>
			<strong>    </strong> {{ Session::get('message') }}
		</div>
    @endif
     
    <!-- @if ($errors->any())
        @foreach($errors->all() as $error)
          <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              {{ $error }}
          </div>
        @endforeach
    @endif -->
@endif
</article>