@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_back_end_user_manager')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.user_manager')}}
        </li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-user-md',
        'text1' => trans('menu.user_manager'),
        'text2' => trans('back_end.user_section_change_password')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                {!!Form::open(array('method' => 'POST', 'id' => 'submit' , 'route' => array('user.change.password', $id)))!!}
                @box_open(trans("back_end.user_section_change_password"))
                <div>
                    <div class="widget-body">
                        @include('custom.password',[
                             'name' => "password",
                             'title' => trans('back_end.password'),
                             'placeholder' => '',
                             'hin' => trans('back_end.password_constraint')
                         ])
                        @include('custom.password',[
                            'name' => "confirmPassword",
                            'title' => trans('back_end.confirmPassword'),
                            'placeholder' => '',
                            'hin' => trans('back_end.password_constraint')
                        ])
                        <div class="widget-footer">
                            {!! Form::lbSubmit(trans('back_end.submit')) !!}
                        </div>
                    </div>
                </div>
                @box_close()
                {!! Form::close() !!}
            </article>
        </div>
    </section>
@endsection
