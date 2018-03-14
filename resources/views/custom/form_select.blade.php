<?php 
    $cv_name = $name;
    $cv_name = str_replace('[', '.', $cv_name);
    $cv_name = str_replace('][', '.', $cv_name);
    $cv_name = str_replace(']', '', $cv_name);
?>
<div class="form-group @if (isset($errors) && $errors->has($cv_name)) has-error @endif">
    <?php
    $attrs = array('class' => 'form-control', 'style' => 'width:100%');
    if (isset($hint))
    {
        $attrs['hint'] = $hint;
    }
    if (isset($attribute))
    {
        $attrs = array_merge($attrs, $attribute);
    }
    if (isset($disable))
    {
        $attrs['disabled'] = 'disabled';
    }
    if (isset($change))
    {
        $attrs['ng-change'] = $change;
    }
    if (isset($change_select))
    {
        $attrs['change-select'] = $change_select;
    }
    $attrs['ng-model'] = @$model_name;
    $attrs['ng-class'] = '{error: errors[\''.$name.'\'][0]}';
    
    $options= [];
    foreach ($items as $item)
    {
        $options+= [$item['value'] => $item['name']];
    }
    ?>

    @if (isset($title))
        {{ Form::label($name, $title, array('class' => 'control-label')) }}
            @if (isset($hint) && (strlen($hint) > 0))
                <a href="#" data-toggle="tooltip" title="{!! $hint !!}">
                    <i class="icon-prepend fa fa-question-circle"></i>
                </a>
            @endif
    @endif
    {{ Form::select($name, $options, $value, $attrs) }}
    <span class="help-block" style="color: #b94a48;" ng-show="errors['{{ $name }}'][0]" ng-bind="errors['{{ $name }}'][0].toString()"></span>

    @if (isset($errors) && $errors->has($cv_name))
        @foreach ($errors->get($cv_name) as $error)
            <div class="note note-error">{{ $error }}</div>
        @endforeach
    @endif
</div>
