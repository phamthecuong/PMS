@extends('front-end.layouts.app')

@section('home')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}} / {{trans('menu.modules_in_the_system')}}
        </li>
    </ol>
@endsection

@push('css')
<style>
    img {
        height: 238px;
        width: 100%;
    }

    .product-image {
        text-align: center;
    }

    .product-info.smart-form {
        padding-top: 35px !important;
    }

    .btn.btn-success {
        width: 100%;
    }

    .product-deatil {
        padding-left: 9px !important;
        padding-right: 9px !important;
    }

    .product-info.smart-form .btn {
        margin-left: 0;
    }
</style>
@endpush

@section('content')



    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title txt-color-blueDark"><i class="fa fa-lg fa-fw fa-home"></i> {{trans('menu.home')}}
                <span>
        </div>
    </div>
    <!-- widget grid -->
    <section id="widget-grid">
        <!-- row -->
        <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-1"
             data-widget-editbutton="false" role="widget">
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <!-- <h2>{{trans('menu.pms_components')}} </h2> -->
                <a href="javascript:void(0);" class="btn bg-color-greenDark txt-color-white btn-xs"
                   style="float: right; margin: 5px">{{trans('menu.abount_pms_system')}}</a>
            </header>

            <div class="row">
                    @include('front-end.templates.module_block', [
                        'image' => asset('img/icon/det_icon.png'),
                        'title' => trans('menu.deterioration'),
                        'about' => trans('menu.deterioration_about'),
                        'link' => route('deterioration.init'),
                        'permission' => 'deterioration.deterioration'
                    ])

                    @include('front-end.templates.module_block', [
                        'image' => asset('img/icon/bud_icon.png'),
                        'title' => trans('menu.budget_simulation'),
                        'about' => trans('menu.budget_simulation_about'),
                        'link' => route('user.budget.init'),
                        'permission' => 'budget_simulation.budget_simulation'
                    ])

                    @include('front-end.templates.module_block', [
                        'image' => asset('front-end/image/home.png'),
                        'title' => trans('menu.backend'),
                        'about' => trans('menu.back_end_about'),
                        'link' => '/admin/routes',
                        'permission' => 'route_management.View'
                    ])

                    @include('front-end.templates.module_block', [
                        'image' => asset('img/icon/wp_icon.png'),
                        'title' => trans('menu.work_planning'),
                        'about' => trans('menu.work_planning_about'),
                        'link' => '/user/work_planning/init',
                        'permission' => 'work_planning.work_planning'
                    ])
                
                    @include('front-end.templates.module_block', [
                        'image' => asset('img/icon/input_icon.png'),
                        'title' => trans('menu.inputting_system'),
                        'about' => trans('menu.inputting_system_about'),
                        'link' => '/inputting',
                        'permission' => 'RMD.View'
                    ])
               
                @include('front-end.templates.module_block', [
                        'image' => asset('img/icon/map_icon.png'),
                        'title' => trans('menu.web_map'),
                        'about' => trans('menu.display_system_about'),
                        'link' => '/web_map'
                    ])
            </div>
        </div>
    </section>
@endsection

@push('script')
<!-- Bootbox -->
<script src="{{ asset('/plugins/bootbox/js/bootbox.min.js') }}" type="text/javascript"></script>
<script>
    function check_user_role() {
        // $.ajax({
        // 	type: 'GET',
        // 	url: '/user/check_user_role',
        // }).done(function (msg) {
        // 	if (msg == 'superadmin') {
        // 		window.location.href = '/admin_manager';
        // 	} else if (msg == 'admin') {
        // 		window.location.href = '/user_manager';
        // 	} else {
        // 		bootbox.alert("{{ trans('validation.user_not_role') }}");
        // 	}
        // });
    }
</script>
@endpush