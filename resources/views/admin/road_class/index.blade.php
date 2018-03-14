@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_road_class')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.road_class_manager')}}
        </li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {!! trans('back_end.road_class') !!}
                <span>
                    > {!! trans('back_end.general.list') !!}
            </span>
            </h1>
        </div>
    </div>

    <div class="well">
        @if(Auth::user()->hasPermission('road_class_management.Add'))
            <a href="{!! url('/admin/road_class/create') !!}" class="btn btn-success header-btn">
                {!! trans('back_end.add_new') !!}
            </a>
        @endif
    </div>

    <section id="widget-grid">
    @if (\Session::has('warning'))
            <div class="row">
                <article class="col-lg-12">
                    <div class="alert alert-success fade in">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        {{ \Session::get('warning') }}
                    </div>
                </article>
            </div>
        @endif
        @if (\Session::has('update'))
            <div class="row">
                <article class="col-lg-12">
                    <div class="alert alert-success fade in">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        {{ \Session::get('update') }}
                    </div>
                </article>
            </div>
        @endif
        @if (\Session::has('change'))
            <div class="row">
                <article class="col-lg-12">
                    <div class="alert alert-success fade in">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        </strong> {{ \Session::get('change') }}
                    </div>
                </article>
            </div>
        @endif
        @if (\Session::has('delete'))
            <div class="row">
                <article class="col-lg-12">
                    <div class="alert alert-danger fade in">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        {{ \Session::get('delete') }}
                    </div>
                </article>
            </div>
        @endif
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans('back_end.road_class'))
                <div>
                    <div class="widget-body no-padding">
                        @include("layouts.elements.table", [
                            "url" => "/ajax/road_class",
                            "method" => "GET",
                            "columns" => [
                                ["data" => "id", "title" => trans('back_end.id')],
                                ["data" => "name", "title" => trans('back_end.name')],
                                ["data" => "creater", "title" => trans('back_end.Created_by')],
                                ["data" => "updater", "title" => trans('back_end.updated_by')],
                                ["data" => "action", "title" => trans('back_end.action'), 'hasFilter' => FALSE],
                            ]
                        ])
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
