@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_repair_method')
    active
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.Manage_repair_method')}}
        </li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {!! trans('back_end.repair_method') !!}
                <span>
                    > {!! trans('back_end.general.list') !!}
            </span>
            </h1>
        </div>
    </div>

    <div class="well">
        @if(Auth::user()->hasPermission('repair_method_management.Add'))
        <a href="{!! url('/admin/repair_methods/create') !!}" class="btn btn-success header-btn">
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
                @box_open(trans('back_end.repair_method'))
                <div>
                    <div class="widget-body no-padding">
                        <?php
                            $columns = [
                                [
                                    "data" => "name_" . (App::getLocale() == 'en' ? 'en' : 'vn'),
                                    "title" => trans('back_end.repair_method_name')
                                ],
                                [
                                    "data" => "color",
                                    "title" => trans('back_end.repair_method_color'),
                                    "hasFilter" => false,
                                    'orderable' => false
                                ],
                                [
                                    "data" => "surface.code_name",
                                    "title" => trans('back_end.pavement_type_name')
                                ],
                                [
                                    "data" => "unit.code_name",
                                    "title" => trans('back_end.method_unit')
                                ],
                            ];

                            $organizations = \App\Models\tblOrganization::listRMBByUserRole();
                            foreach ($organizations as $rec) 
                            {

                                $columns[] = [
                                    "data" => "cost_" . $rec->code_id, 
                                    "title" =>  trans('back_end.cost_for') . ' ' . $rec->{"name_" . (App::getLocale() == 'en' ? 'en' : 'vn')} . ' (1000VND)',
                                    "hasFilter" => false,
                                    'orderable' => false
                                ];
                            }

                            $columns[] = [
                                "data" => "classification.name_" . (App::getLocale() == 'en' ? 'en' : 'vn'),
                                "title" => trans('back_end.repair_classifications')
                            ];

                            $columns[] = ["data" => "updater", "title" => trans('back_end.updated_by')];
                            $columns[] = ["data" => "action", "title" => trans('back_end.action'), 'hasFilter' => FALSE, 'orderable' => false];


                        ?>
                        @include("layouts.elements.table", [
                            "url" => "/ajax/repair_method",
                            "method" => "GET",
                            "columns" => $columns
                        ])
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
