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
                {{ trans('back_end.Manage Route') }}
            </h1>
        </div>
    </div>

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-12">
                @box_open((isset($branch) ? trans('back_end.edit') : trans('back_end.add_new')))
                <div>
                    <div class="widget-body">
                        @if(isset($branch))
                            {!! Form::open(array("url" => "/admin/routes/$branch->id", "method" => "PUT", "files" => true)) !!}
                        @else
                            {!! Form::open(array("url" => "/admin/routes", "method" => "POST", "files" => true)) !!}
                        @endif
                        {!! Form::lbSelect('r_category', @$branch->road_category, \App\Models\mstRoadCategory::allOptionToAjax(), trans('back_end.Road category')) !!}
                        {!! Form::lbText("name_en", @$branch->name_en, trans('back_end.name_en'), trans('back_end.name_en')) !!}
                        {!! Form::lbText("name_vi", @$branch->name_vn, trans('back_end.name_vn'), trans('back_end.name_vn')) !!}
                        {!! Form::lbNumber('branch_number', @$branch->branch_number, trans('back_end.name_branch'), '1', '2') !!}
                        {!! Form::lbNumber('road_number', @$branch->road_number, trans('back_end.Road Number'), '1', '10') !!}
                        {!! Form::lbSelect('r_number_supplement', @$branch->road_number_supplement, \App\Models\mstRoadNumberSupplement::allOptionToAjax(1), trans('back_end.Road Number Supplement')) !!}
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
