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
                {!! trans('back_end.Manage_pavemetn_type') !!} / {{isset($pavement_type) ? trans('back_end.edit') : trans('back_end.add_new')}}
            </h1>
        </div>
    </div>

    <section id="widget-grid">
   
        <div class="row">
            <article class="col-lg-8">
                @box_open((isset($pavement_type) ? trans('back_end.edit') : trans('back_end.add_new')))
                <div>
                    <div class="widget-body">
                        @if(isset($pavement_type))
                            {!! Form::open(array("url" => "/admin/pavement_types/$pavement_type->id", "method" => "PUT", "files" => true)) !!}
                        @else
                            {!! Form::open(array("url" => "/admin/pavement_types", "method" => "POST", "files" => true)) !!}
                        @endif
                        {!! Form::lbText("name_en", @$pavement_type->name_en, trans('back_end.name_en'), trans('back_end.name_en')) !!}
                        {!! Form::lbText("name_vn", @$pavement_type->name_vn, trans('back_end.name_vn'), trans('back_end.name_vn')) !!}
                        {!! Form::lbText('code', @$pavement_type->code, trans('back_end.code'), trans('back_end.code')) !!}
                        {!! Form::lbSelect('pavement_layer', @$pavement_type->pavement_layer_id, \App\Models\mstPavementLayer::allOptionToAjax(), trans('back_end.pavement_layer')) !!}
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
