@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_admin_manager')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.admin_manager')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-user-md',
        'text1' => trans('menu.admin_manager'),
        'text2' => trans('back_end.admin_section_list')
    ])

    <div class="well">
        <a class="btn btn-primary" href="{!! url('admin/admin_manager/create') !!}">
            {!! trans('back_end.AddNew') !!}
        </a>
    </div>

    <section id="widget-grid">
        <div class="row">
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
                            </strong> {{ \Session::get('update') }}
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
                        <div class="alert alert-success fade in">
                            <button class="close" data-dismiss="alert">
                                ×
                            </button>
                            <i class="fa-fw fa fa-times"></i>
                            {{ \Session::get('delete') }}
                        </div>
                    </article>
                </div>
            @endif
        
            <article class="col-lg-12">
                @box_open(trans("back_end.admin_list_panel_title"))
                <div>
                    <div class="widget-body no-padding">
                        <div class="table-responsive">
                            @include("layouts.elements.table", [
                            'url' => '/ajax/admin_manager',
                            'columns' => [
                                    ['data' => 'name', 'title' => trans('back_end.name')],
                                    ['data' => 'email', 'title' => trans('back_end.email')],
                                    ['data' => 'role_name', 'title' => trans('back_end.roles')],
                                    ['data' => 'organization_name', 'title' => trans('back_end.organization')],
                                    ['data' => 'created_at', 'title' => trans('back_end.created_at')],
                                    ['data' => 'updated_at', 'title' => trans('back_end.updated_at')],
                                    ['data' => 'action', 'title' => trans('back_end.action_hander'),'hasFilter' => false,'orderable'=> false],
                                ]
                            ])
                        </div>
                    </div>
                </div>
                @box_close
            </article>
        </div>
    </section>
@endsection
