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
        'text2' => trans('back_end.import_more_data')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                @box_open(trans("back_end.select_pc_credential"))  
                <div>
                    <div class="widget-body">
                        {!! Form::open(["url" => "/admin/migrate_pc/create", "method" => "post"]) !!}
                            <fieldset>
                                {!! Form::lbSelect2('pc_file', '', $pc_list, trans('back_end.pc_file')) !!}
                                {!! Form::lbSelect2('image_file', '', $image_list, trans('back_end.image_file')) !!}
                            </fieldset> 
                            <div class="widget-footer"> 
                                {!! Form::lbSubmit(trans('back_end.run'), ['onclick' => 'showLoading()']) !!}
                            </div>
                        {!! Form::close() !!}   
                    </div>
                </div>
                @box_close  
            </article>
        </div>
    </section>
@endsection