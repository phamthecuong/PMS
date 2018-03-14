<div class="form-group @if (isset($errors) && $errors->has($name)) has-error @endif">
    <?php
    $attrs = array('class' => 'form-control');
    if (isset($placeholder))
    {
        $attrs['placeholder'] = $placeholder;
    }
    if (isset($hint))
    {
        $attrs['hint'] = $hint;
    }
    if (isset($validation))
    {
        $attrs = array_merge($attrs, $validation);
    }
    if (isset($attribute))
    {
        $attrs = array_merge($attrs, $attribute);
    }
    $attrs['ng-model'] = @$model_name;
    $attrs['ng-class'] = '{error: errors.'.$name.'[0]}';
    ?>
    @if (isset($title))
        {{ Form::label($name, $title, array('class' => 'control-label')) }}
        @if (isset($hint) && (strlen($hint) > 0))
            <a href="#" data-toggle="tooltip" title="{!! $hint !!}">
                <i class="icon-prepend fa fa-question-circle"></i>
            </a>
        @endif
    @endif
    {{ Form::textarea($name, $value, $attrs) }}
    <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $name }}[0]" ng-bind="errors.{{ $name }}[0].toString()"></span>

    @if ($errors->has($name))
        @foreach ($errors->get($name) as $error)
            <div class="note note-error">{{ $error }}</div>
        @endforeach
    @endif
</div>