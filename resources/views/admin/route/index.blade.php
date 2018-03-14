@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_route_manage')
    active
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {!! trans('back_end.Manage Route') !!}
                <span>
                    > {!! trans('back_end.general.list') !!}
            </span>
            </h1>
        </div>
    </div>

    <div class="well">
        @if(Auth::user()->hasPermission('route_management.Add'))
            <a href="{!! url('/admin/routes/create') !!}" class="btn btn-success header-btn">
                {!! trans('back_end.add_new') !!}
            </a>
        @endif
    </div>

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans('back_end.Manage Route'))
                <div>
                    <div class="widget-body no-padding">
                        @include("layouts.elements.table", [
                            "url" => "/ajax/route",
                            "method" => "GET",
                            "columns" => [
                                ["data" => "mst_road_category.classification", "title" => trans('back_end.Road category')],
                                ["data" => "rcName", "title" => trans('back_end.name')],
                                ["data" => "branch_number", "title" => trans('back_end.name_branch')],
                                ["data" => "road_number", "title" => trans('back_end.Road Number')],
                                ["data" => "road_number_supplement", "title" => trans('back_end.Road Number Supplement')],
                                ["data" => "action", "title" => trans('back_end.action'), 'hasFilter' => FAlSE],
                            ]
                        ])
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
