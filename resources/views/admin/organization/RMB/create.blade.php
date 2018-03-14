@extends('admin.layouts.app')
@if(!isset($user))
@section('contentheader_title')
    {{trans('back_end.auth_organization')}}
@endsection

@section('contentheader_link')
    <li><a href="/organization"><i class="fa fa-dashboard"></i> {{ trans('back_end.organization') }}</a></li>
    <li><a href="/organization/create">{{ trans('back_end.add_new') }}</a></li>
@endsection
@else
@section('contentheader_title')
    {{trans('back_end.auth_organization')}}
@endsection

@section('contentheader_link')
    <li><a href="/organization"><i class="fa fa-dashboard"></i> {{ trans('back_end.organization') }}</a></li>
    <li><a href="/organization/{{$id}}/edit">{{ trans('back_end.edit') }}</a></li>
@endsection
@endif
@section('main-content')

@if(isset($user))
        {!!Form::open(array('method' => 'PUT', 'id' => 'submit' , 'route' => array('organization.update', $id)))!!}
    @else
        {!!Form::open(array('method' => 'POST', 'id' => 'submit' , 'route' => array('organization.store')))!!}
    @endif
<?php
    $values = '';
    if(isset($user))
    {
        $values = $user;
    }
?>
<ul>
  @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
</ul>
<div class="theme-showcase">
   
    <div class="box">
        <div class="box-header">
            @if(!isset($user))
            <h3 class="box-title">{!!trans('back_end.add_new_RMB')!!}</h3>
            @else
            <h3 class="box-title">{!!trans('back_end.edit_RMB')!!}</h3>
            @endif
        </div>

        <div class="box-body">
            <div class="col-md-12 ">
                @include('admin.layouts.partials.notification')
            </div>
            <?php Session::forget('message');?>
        </div>

        @if(!isset($user))
            <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-2">
                    <label for="text" class="control-label">{!!trans('back_end.name_vn')!!}</label>
                            </div>
                            <div class="col-sm-10">
                    <input class="form-control" placeholder="name vn" name="name_vn" type="text" id="name_vn">
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-2">
                    <label for="Confirm Password" class="control-label">{!!trans('back_end.name_en')!!}</label>
                            </div>
                            <div class="col-sm-10">
                    <input class="form-control" placeholder="name en" name="name_en" type="text">
                    </div>
                </div>
            </div>
        @endif

        @if(isset($user))
            <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-2">
                    <label for="text" class="control-label">{!!trans('back_end.name_vn')!!}</label>
                            </div>
                            <div class="col-sm-10">
                    <input class="form-control" placeholder="name vn" name="name_vn" type="text" id="name_vn" value="{{$name_vn}}">
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-2">
                    <label for="Confirm Password" class="control-label">{!!trans('back_end.name_en')!!}</label>
                            </div>
                            <div class="col-sm-10">
                    <input class="form-control" placeholder="name en" name="name_en" type="text" value="{{$name_en}}">
                    </div>
                </div>
            </div>
            
        @endif
        <div class="box-footer">
            <div class="col-md-2 col-md-offset-2">
            @if(!isset($user))
                <button type="submit" class="btn btn-block btn-primary" id="save">{{trans('back_end.save')}}</button>
            @else
                <button type="submit" class="btn btn-block btn-primary" id="save">{{trans('back_end.save')}}</button>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('js_extend')
<script>
    $(function () {
        $(".select2").select2();
    });
</script>
@endsection