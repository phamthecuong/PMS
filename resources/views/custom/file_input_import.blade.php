<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    <?php
    $attrs = array('class' => 'form-control',);
    if (isset($attribute)) {
        $attrs = array_merge($attrs, $attribute );
    }
    ?>

    {{ Form::label($name, $title, array('class' => 'control-label')) }}
    {{ Form::file($name, $attrs) }}

    @if (isset($errors) && $errors->has($name))
        @foreach ($errors->get($name) as $error)
            <div class="note note-error" style="color: #b52735">{{ $error }}</div>
        @endforeach
    {{--@elseif(\Session::has('limited'))--}}
                {{--<div class="note note-error" style="color: red">{{ \Session::get('limited') }}</div>--}}
    @endif
</div>
