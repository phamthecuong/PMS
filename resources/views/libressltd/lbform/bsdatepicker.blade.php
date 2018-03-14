<div class="form-group">
    <div class="col-sm-2" style="padding-left: 22px;">
    {{ Form::label($title, null, ['class' => 'control-label']) }}
    </div>
    <div class="col-sm-10" style="padding-left: 22px;">
    @if ($value === -1)
    <input class="form-control" type="date" name="{{ $name }}" value="" date-format="dd-MM-yyyy">
    @elseif (isset($value))
    <input class="form-control" type="date" name="{{ $name }}" value="{{ $value }}" date-format="dd-MM-yyyy">
    @else
    <input class="form-control" type="date" name="{{ $name }}" value="{{ date('Y-m-d') }}" date-format="dd-MM-yyyy">
    @endif
    </div>
</div>