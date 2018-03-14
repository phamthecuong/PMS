<?php 
    $cv_name = $name;
    $cv_name = str_replace('[', '.', $cv_name);
    $cv_name = str_replace('][', '.', $cv_name);
    $cv_name = str_replace(']', '', $cv_name);
?>
<div class="form-group @if (isset($errors) && $errors->has($cv_name)) has-error @endif">
    @if(isset($title))
    {{ Form::label($title, null, ['class' => 'control-label']) }}
        @if(isset($hint))
            <a href="#" data-toggle="tooltip" title="{!! $hint !!}">
                <i class="icon-prepend fa fa-question-circle"></i>
            </a>
        @endif
    @endif
    <?php
        $attrs = array('class' => 'form-control');
        if (isset($place_holder))
        {
            $attrs['placeholder'] = $place_holder;
        }
        if (isset($validation))
        {
            $attrs = array_merge($attrs, $validation);
        }
        if (isset($disabled))
        {
            $attrs['ng-disabled'] = $disabled;
        }
        if (isset($change))
        {
            $attrs['ng-change'] = $change;
        }
        $attrs['step'] = 'any';
        $attrs['ng-model'] = @$model_name;
        $attrs['ng-class'] = '{error: errors[\''.$name.'\'][0]}';
    ?>
    {{ Form::number($name, $value, $attrs) }}
    <span class="help-block" style="color: #b94a48;" ng-show="errors['{{ $name }}'][0]" ng-bind="errors['{{ $name }}'][0].toString()"></span>

        <?php
        $name = str_replace('[', '.', $name);
        $name = str_replace('][', '.', $name);
        $name = str_replace(']', '', $name);
        ?>
    @if (isset($errors) && $errors->has($cv_name))
        @foreach ($errors->get($cv_name) as $error)
            <div class="note note-error">{{ $error }}</div>
        @endforeach
    @endif
</div>