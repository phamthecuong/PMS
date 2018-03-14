@extends('admin.layouts.app_datatable')
@section('contentheader_title')
    {{trans('back_end.auth_DB')}}
@endsection

@section('contentheader_link')
    <li><a href="/SB"><i class="fa fa-dashboard"></i> {{ trans('back_end.auth_DB') }}</a></li>
@endsection

@section('main-content')
<div class="theme-showcase">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"> {{ trans('back_end.list_SB') }} {{$name_rmd}}</h3>
        </div>
        <div class="box-body">
             <div class="col-md-12 ">
                @include('admin.layouts.partials.notification')
            </div>
            <?php Session::forget('message');?>
        </div>
        <div class="box-body">
            @include('admin.templates.datatable')
        </div>
        <div class="box-footer">
            <div class="col-md-2" style="padding-left: 0px;">
                    <a class="btn btn-block btn-primary" href="{{ route('sbcreate',[$id] )}}">{{trans('back_end.add_new')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection