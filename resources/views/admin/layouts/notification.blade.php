<div class="card">
  @if(Session::get('message') || $errors->any())
    <div class="card-body card-padding">
      @if(Session::get('message') && !is_array(Session::get('message')))
        <div class="{{ Session::get('class') }} alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              {{ Session::get('message') }}
        </div>
      @elseif(is_array(Session::get('message')))
      	@foreach(Session::get('message') as $key => $value)
          <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              {{ $value }}
          </div>
        @endforeach
      @endif

      @if ($errors->any())
        @foreach($errors->all() as $error)
          <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              {{ $error }}
          </div>
        @endforeach
      @endif
    </div>
  @endif
</div>