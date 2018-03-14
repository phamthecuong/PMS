@extends('front-end.layouts.app')

@section('side_menu_pms_dataset')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('menu.home')}}</li>
        <li>{{trans('menu.pms_dataset_title')}}</li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-database',
    'text1' => trans('menu.pms_dataset_title'),
    'text2' => trans('menu.run_pms_process')
])

<section id="widget-grid">              
    <div class="row">           
        <article class="col-lg-6">
            @box_open(trans("pms_dataset.pms_data_panel"))  
            <div>
                <div class="widget-body">
                    {!! Form::open(["url" => "/user/pms_dataset", "method" => "post"]) !!}
                        <fieldset>
                            {!! Form::lbSelect2('year', '', $items, trans('pms_dataset.year_to_generate')) !!}  
                            <div class="note">
                                <strong>{{trans('pms_dataset.attention')}}</strong> {{trans('pms_dataset.choose_existed_year_to_overwrite_data')}}
                            </div>    
                            <br>
                        </fieldset> 
                        <div class="widget-footer"> 
                            {!! Form::lbSubmit(trans('pms_dataset.run'), ['onclick' => 'showLoading()']) !!}
                        </div>  
                    {!! Form::close() !!}   
                </div>
            </div>
            @box_close      
        </article>
    </div>              
</section>                  
@endsection
