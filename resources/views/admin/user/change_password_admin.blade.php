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
        'text2' => trans('back_end.admin_section_change_password')
    ])

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                {!!Form::open(array('method' => 'POST', 'id' => 'submit' , 'route' => array('admin.change.password', $id)))!!}
                @box_open(trans("back_end.admin_section_change_password"))
                <div>
                    <div class="widget-body">
                        @include('custom.password',[
                             'name' => "password",
                             'title' => trans('back_end.password'),
                             'placeholder' => trans('back_end.password'),
                             'hin' => trans('back_end.password_constraint')
                         ])
                        @include('custom.password',[
                            'name' => "confirmPassword",
                            'title' => trans('back_end.confirmPassword'),
                            'placeholder' => trans('back_end.confirmPassword'),
                            'hin' => trans('back_end.password_constraint')
                        ])
                        <div class="widget-footer">
                            {!! Form::lbSubmit() !!}
                        </div>
                    </div>
                </div>
                @box_close()
                {!! Form::close() !!}
            </article>
        </div>
    </section>
@endsection
