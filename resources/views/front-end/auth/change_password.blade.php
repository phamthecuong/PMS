@extends('front-end.layouts.app')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.home')}}
        </li>
        <li>
            {{trans('menu.change_password')}}
        </li>
    </ol>
@endsection
@section('content')
    <section id="widget-grid" >
        <div class="row">
            <article class="col-md-8 col-md-offset-2">
                @box_open(trans('auth.change_password'))
                <div>
                    <div class="widget-body">
                            <form class="form-horizontal" role="form" method="POST" action="/user/change_password">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                                    <label for="old_password" class="col-md-4 control-label">{!! trans('auth.old_password') !!}</label>

                                    <div class="col-md-6">
                                        <input id="old_password" type="password" class="form-control" name="old_password" required>

                                        @if ($errors->has('old_password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('old_password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">{!! trans('auth.password') !!}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label for="password-confirm" class="col-md-4 control-label">{!! trans('auth.password_confirm') !!}</label>
                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" onclick="this.disabled = true;this.form.submit();" class="btn btn-primary">
                                           {!! trans('back_end.submit') !!}
                                        </button>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>
@endsection