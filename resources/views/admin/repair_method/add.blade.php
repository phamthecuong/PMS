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
                    | {!! isset($repair_method) ? trans('back_end.edit') : trans('back_end.add_new') !!}
            </span>
            </h1>
        </div>
    </div>

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-8">
                @box_open((isset($repair_method) ? trans('back_end.edit') : trans('back_end.add_new')))
                <div>
                    <div class="widget-body">
                        @if(isset($repair_method))
                            {!! Form::open(array("url" => "/admin/repair_methods/$repair_method->id", "method" => "PUT", "files" => true)) !!}
                        @else
                            {!! Form::open(array("url" => "/admin/repair_methods", "method" => "POST", "files" => true)) !!}
                        @endif
                        {!! Form::lbSelect('pavement_type', @$repair_method->pavement_type, @$pavement, trans('back_end.pavement_type')) !!}
                        {!! Form::lbText("name_en", @$repair_method->name_en, trans('back_end.name_en'), trans('back_end.name_en')) !!}
                        {!! Form::lbText("name_vi", @$repair_method->name_vn, trans('back_end.name_vn'), trans('back_end.name_vn')  ) !!}
                        {!! Form::lbText("code", @$repair_method->code, trans('back_end.repair_method_color')) !!}
                        {!! Form::lbSelect('unit', @$repair_method->unit_id, \App\Models\mstMethodUnit::allToOption(), trans('back_end.unit')) !!}
                        {!! Form::lbSelect('classification', @$repair_method->classification_id, \App\Models\tblRClassification::allToOption(), trans('back_end.Classification')) !!}
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
