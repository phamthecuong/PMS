@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_repair_metrix')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.repair_matrix')}}
        </li>
        <li>{{ trans('menu.matrix_setting') }}</li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-table',
        'text1' => trans('menu.repair_matrix'),
        'text2' => trans('back_end.list')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open(trans('back_end.repair_matrix_title'))
                <div>
                    <div class="widget-body no-padding">
                        @include("layouts.elements.table", [
                            "url" => "/ajax/repair_matrix",
                            "method" => "GET",
                            "columns" => [
                                ["data" => "name", "title" => trans('back_end.repair_matrix_name'), 'hasFilter' => FALSE],
                                ["data" => "type", "title" => trans('back_end.repair_matrix_type'), 'hasFilter' => FALSE],
                                ["data" => "updatedBy", "title" => trans('back_end.updated_by'), 'hasFilter' => FALSE],
                                ["data" => "updated_at", "title" => trans('back_end.updated_at'), 'hasFilter' => FALSE],
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
