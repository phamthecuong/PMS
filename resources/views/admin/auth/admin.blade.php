<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>AdminLTE | Log in</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="{{asset('/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="{{asset('/admin/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{asset('/admin/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">
            <div class="header">
                {!! trans('back_end.management_system_pavement_condition') !!}
            </div>
            {!! Form::open(array( 'method' => 'POST', 'route' => array('back.end'))) !!}
                <div class="body bg-gray">
                    @include('admin.layouts.notification')
                    <?php Session::forget('message'); ?>
                    <div class="form-group">
                        <i class="glyphicon glyphicon-user"></i>
                        <input type="text" name="email" class="form-control" placeholder="{!! trans('back_end.user_name') !!}"/>
                    </div>
                    <div class="form-group">
                        <i class="glyphicon glyphicon-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="{!! trans('back_end.password') !!}"/>
                    </div>
                        <a href="/language/vi"><img src="/img/vn.png" alt="User Image"/></a>
                        <a href="/language/en"><img src="/img/en.png" alt="User Image"/></a>
                </div>
                <div class="footer">
                    <button type="submit" class="btn bg-olive btn-block" style="font-size: 18px;">
                        {!! trans('back_end.login') !!}
                    </button>
                </div>
                {!! Form::close() !!}
        </div>

        <!--jQuery 2.0.2 -->
        <script src="{{asset('/admin/js/jquery.min.js') }}"></script>
        <!-- Bootstrap-->
        <script src="{{asset('/admin/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script>
			(function(i, s, o, g, r, a, m) {
				i['GoogleAnalyticsObject'] = r;
				i[r] = i[r] ||
				function() {
					(i[r].q = i[r].q || []).push(arguments)
				}, i[r].l = 1 * new Date();
				a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
				a.async = 1;
				a.src = g;
				m.parentNode.insertBefore(a, m)
			})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

			ga('create', 'UA-74146506-1', 'auto');
			ga('send', 'pageview');

        </script>
    </body>
</html>