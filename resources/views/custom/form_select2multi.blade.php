<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    @if (isset($title) && strlen($title) > 0)
        {{ Form::label($name, $title, ['class' => 'control-label']) }}
        @if (isset($hint) && (strlen($hint) > 0))
            <a href="#" data-toggle="tooltip" title="{!! $hint !!}">
                <i class="icon-prepend fa fa-question-circle"></i>
            </a>
        @endif
    @endif
    <select class="form-control select2" name="{{ $name }}" multiple="multiple">
        @foreach ($items as $item)
            <option value="{{ $item['value'] }}"
                @if (is_array($value))
                    @foreach ($value as $v)
                        @if ($v == $item["value"])
                        selected
                        @endif
                    @endforeach
                @endif
            >{{ $item['name'] }}</option>
        @endforeach
    </select>
    @if (isset($errors) && $errors->has($name))
        @foreach ($errors->get($name) as $error)
            <div class="note note-error">{{ $error }}</div>
        @endforeach
    @endif
</div>