@extends('front-end.layouts.app')

<?php
$import_active;
        if ($prefix_url == 'road_inventories')
        {
            $import_active = 'road_inventory';
        }
        else if($prefix_url == 'maintenance_history')
        {
            $import_active = 'maintenance_history';
        }
        else
        {
            $import_active = 'traffic_volume';
        }
    $menu_active = strlen(strstr(@$active, "Other")) > 0 ? "SideMenu_Other" : "SideMenu_RoadAsset";
?>
@section('side_menu_'.$import_active)
    active
@endsection

@section('inputting_system')
    active
@endsection

@section('side_menu_import_'.$import_active)
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.inputting_system')}}
        </li>
        <li>
            {{trans('menu.'. $ribbon)}}
        </li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {{ trans('menu.'.$prefix_url) }}
                <span>>
                    {!! trans("back_end.import") !!}
            </span>
            </h1>
        </div>
    </div>

    <section id="widget-grid" class="">
        <div class="row">
            @if (\Session::has('success'))
                <div class="row">
                    <article class="col-lg-12">
                        <div class="alert alert-success fade in">
                            <button class="close" data-dismiss="alert">
                                ×
                            </button>
                            <i class="fa-fw fa fa-times"></i>
                          {{ \Session::get('success') }}
                        </div>
                    </article>
                </div>
            @endif
            @if (\Session::has('empty'))
                <div class="row">
                    <article class="col-lg-6">
                        <div class="alert alert-warning fade in">
                            <button class="close" data-dismiss="alert">
                                ×
                            </button>
                            <i class="fa-fw fa fa-times"></i>
                          {{ \Session::get('empty') }}
                        </div>
                    </article>
                </div>
            @endif
            <article class="col-lg-6">
                @box_open("")
                <div>
                    {!! Form::open(array('url' => "/".$prefix_url."/validate", 'method' => "post", 'files' => true)) !!}
                    <div class="widget-body">
                            @include('custom.file_input_import', ['name' => 'file', 'title' => trans('back_end.import_file_upload').'| '.trans('back_end.import_file_accept')])
                            <div class="row" style="margin-bottom: 20px">
                                <div class="col-lg-4">
                                    {!! trans('back_end.dowload') !!}
                                </div>
                                <div class="col-lg-4">
                                    <a href="{{ url('/'.$prefix_url.'/download_en') }}" ><img src="{{ url('/sa/img/blank.gif') }}" class="flag flag-us" alt="United States"> {!! trans('back_end.download_en') !!}</a>
                                </div>
                                <div class="col-lg-4">
                                    <a href="{{ url('/'.$prefix_url.'/download_vi') }}" ><img src="{{ url('/sa/img/blank.gif') }}" class="flag flag-vn" alt="United States"> {!! trans('back_end.download_vi') !!}</a>
                                </div>
                            </div>
                            <div class="widget-footer">
                                {!! Form::lbSubmit(trans('back_end.import_upload'), ['onclick' => 'showLoading()']) !!}
                            </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                @box_close
            </article>
        </div>
    </section>

@endsection
@push('css')
<style>
    .form-control, .input-lg, .input-sm, .input-xs {
        height: auto !important;
    }
</style>
@endpush
