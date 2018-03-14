@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_pavement_type')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.Manage_pavement_type')}}
        </li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {!! trans('back_end.Manage_pavement_type') !!}
                <span>
                    > {!! trans('back_end.general.list') !!}
            </span>
            </h1>
        </div>
    </div>

    <div class="well">
        @if(Auth::user()->hasPermission('pavement_type_management.Add'))
        <a href="{!! url('/admin/pavement_types/create') !!}" class="btn btn-success header-btn">
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
                @box_open( trans('back_end.Manage_pavemetn_type'))
                <div>
                    <div class="widget-body no-padding">
                        @include("layouts.elements.table", [
                            "url" => "/ajax/pavement_type",
                            "method" => "GET",
                            "columns" => [
                                ["data" => "id", "title" => trans('back_end.id')],
                                ["data" => "code", "title" => trans('back_end.code')],
                                [
                                    "data" => "name_" . (App::getLocale() == 'en' ? 'en' : 'vn'),
                                    "title" => trans('back_end.pavement_type_name')
                                ],
                                [
                                    "data" => "mst_pavement_layer.name_" . (App::getLocale() == 'en' ? 'en' : 'vn'),
                                    "title" => trans('back_end.pavement_layer_name')
                                ],
                                ["data" => "creater", "title" => trans('back_end.Created_by')],
                                ["data" => "updater", "title" => trans('back_end.updated_by')],
                                ["data" => "action", "title" => trans('back_end.action'), 'hasFilter' => FALSE,'orderable'=> FALSE],
                            ]
                        ])
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
