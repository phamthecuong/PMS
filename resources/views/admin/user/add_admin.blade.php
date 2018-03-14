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
        'text2' => isset($admin) ? trans('back_end.edit') : trans('back_end.add_new')
    ])

    @if (\Session::has('exist'))
        <div class="row">
            <article class="col-lg-12">
                <div class="alert alert-warning fade in">
                    <button class="close" data-dismiss="alert">
                        ×
                    </button>
                    <i class="fa-fw fa fa-times"></i>
                    <strong>{{ trans('admin.Error') }}!</strong> {{ \Session::get('exist') }}
                </div>
            </article>
        </div>
    @endif

    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                @box_open(isset($admin) ? trans('back_end.admin_section_edit') : trans('back_end.admin_section_add'))
                <div>
                    @if (isset($admin))
                        {!! Form::open(array("url" => "admin/admin_manager/$admin->id", "method" => "put", "files" => true)) !!}
                    @else
                        {!! Form::open(array('url' => "admin/admin_manager", 'method' => "post", 'files' => true)) !!}
                    @endif
                    <div class="widget-body">
                        {!! Form::lbText("name",@$admin->name, trans('back_end.user_name'), trans('back_end.user_name')) !!}
                        {!! Form::lbText("email",@$admin->email, trans('back_end.email'), trans('back_end.email')) !!}
                        @if(!isset($admin))
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
                        @endif
                        <?php 
                            $role_id =  (@$admin && @$admin->roles()) ? @$admin->roles()->first()->id : '';
                         ?>
                        @include('custom.form_select',
                            ['name' => "roles",
                             'value' => $role_id,
                             'title' => trans('back_end.roles'),
                             'items' => App\Models\Role::getListRoleAdmin(),
                             'hin' => trans('back_end.hint_roles'),
                             ])
                        @include('custom.form_select',
                            ['name' => "organization",
                             'value' => @$admin->organizations->id,
                             'title' => trans('back_end.organization'),
                             'items' => [],
                             'hin' => trans('back_end.hint_organization'),
                             ])
                        <div class="widget-footer">
                            {!! Form::lbSubmit() !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                @box_close()
            </article>
        </div>
    </section>
@endsection
@push('script')
<script>
    var role_select = $('[name="roles"]');
    var organization_select = $('[name="organization"]');
    var count = 1;
    loadOrganization();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    function reloadOptions(selector, options) {
        selector.empty();
        var opts = [];
        $.each(options, function (ix, val) {
            var option = $('<option>').text(val.title).val(val.value);
            opts.push(option);
        });
        selector.html(opts);
    }

    function loadOrganization() {
        var role_id = +role_select.val();
        var url = '/ajax/role/' + role_id + '/organization';
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: '',
                title: '{{ trans("back_end.select_a_Organization") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['organization_name']
                });
            }
            reloadOptions(organization_select, data);
            if (count == 1) {
                var selected_id = "{{@$admin->organizations->id ? @$admin->organizations->id : -1}}";
                organization_select.val(selected_id);    
            }
            count ++;
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }
    $('document').ready(function(){
        setOnChangeEvent();
    });
    function setOnChangeEvent() {
        role_select.change(loadOrganization);
    }
</script>
@endpush
