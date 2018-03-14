@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('migrate_pc')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.migrate_pc')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-server',
        'text1' => trans('menu.migrate_pc'),
        'text2' => trans('back_end.list_pc_processing')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open("")
                <div>   
                    <div class="widget-body no-padding">
                        @include("layouts.elements.table", [
                            'url' => '/ajax/pc/migrate_process',
                            'columns' => [
                                ['data' => 'created_at', 'title' => trans('back_end.created_at'), 'hasFilter' => false],
                                ['data' => 'data_file', 'title' => trans('back_end.data_file'), 'hasFilter' => false],
                                ['data' => 'progress', 'title' => trans('back_end.progress'), 'hasFilter' => false],
                                ['data' => 'creator_name', 'title' => trans('back_end.Created_by')],

                            ],
                            'reloadTime' => 1000
                        ])
                        <div class="widget-footer">
                            <a class="btn btn-danger" href="/admin/migrate_pc/create">
                                {!!trans('back_end.import_more_data')!!}
                            </a>
                        </div>
                    </div>
                </div>
                @box_close
            </article>
        </div>
    </section>

@endsection

@push('css')
    <style type="text/css">
        .pace {
            display: none !important;
        }
    </style>
@endpush
