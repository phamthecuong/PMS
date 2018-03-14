@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_road_class')
    active
@endsection

@section('content')

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
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-edit fa-fw "></i>
                {!! trans('back_end.road_class')!!} / {!!isset($road_class) ? trans('back_end.edit') : trans('back_end.add_new') !!}
            </h1>
        </div>
    </div>

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-8">
                @box_open((isset($road_class) ? trans('back_end.edit') : trans('back_end.add_new')))
                <div>
                    <div class="widget-body">
                        @if(isset($road_class))
                            {!! Form::open(array("url" => "/admin/road_class/$road_class->id", "method" => "PUT", "files" => true)) !!}
                        @else
                            {!! Form::open(array("url" => "/admin/road_class", "method" => "POST", "files" => true)) !!}
                        @endif
                        {!! Form::lbText("name_en", @$road_class->name_en, trans('back_end.name_en'), trans('back_end.name_en')) !!}
                        {!! Form::lbText("name_vi", @$road_class->name_vn, trans('back_end.name_vn'), trans('back_end.name_vn')) !!}
                        {!! Form::lbText("code_id", @$road_class->code_id, trans('back_end.code_id'), trans('back_end.code_id')) !!}
                        {!! Form::lbText("text-id", @$road_class->text_id, trans('back_end.text_id'), trans('back_end.text_id')) !!}
                        <div class="widget-footer">
                            {!! Form::lbSubmit(trans('back_end.submit')) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
