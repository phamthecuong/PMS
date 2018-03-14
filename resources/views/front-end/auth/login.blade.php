<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title>{{ trans('login.title_login') }}</title>
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		
		<!-- #CSS Links -->
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/font-awesome.min.css') }}">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/smartadmin-production-plugins.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/smartadmin-production.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/smartadmin-skins.min.css') }}">

		<!-- SmartAdmin RTL Support -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/smartadmin-rtl.min.css') }}"> 

		<!-- We recommend you use "your_style.css') }}" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/your_style.css') }}"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sa/css/demo.min.css') }}">

		<!-- #FAVICONS -->
		<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
		<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

		<!-- #GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

		<!-- #APP SCREEN / ICONS -->
		<!-- Specifying a Webpage Icon for Web Clip 
			 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
		<link rel="apple-touch-icon" href="{{ asset('sa/img/splash/sptouch-icon-iphone.png') }}">
		<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('sa/img/splash/touch-icon-ipad.png') }}">
		<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('sa/img/splash/touch-icon-iphone-retina.png') }}">
		<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('sa/img/splash/touch-icon-ipad-retina.png') }}">
		
		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		
		<!-- Startup image for web apps -->
		<link rel="apple-touch-startup-image" href="{{ asset('sa/img/splash/ipad-landscape.png') }}" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="{{ asset('sa/img/splash/ipad-portrait.png') }}" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="{{ asset('sa/img/splash/iphone.png') }}" media="screen and (max-device-width: 320px)">

	</head>
	
	<body class="animated fadeInDown">
		<div id="main" role="main">
			<!-- MAIN CONTENT -->
			<div id="content" class="container" style="padding-top: 80px">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4"></div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
						@include("front-end.layouts.partials.notication")
						<div class="well no-padding">
							{!! Form::open(array( 'method' => 'post', 'url' => route('user.dologin'), 'id' => 'login-form', 'class' =>'smart-form client-form' )) !!}
								<header>
									{{ trans('login.sign_in') }}
								</header>

								<fieldset>
									
									<section>
										<label class="label">{!! trans('back_end.user_name') !!}</label>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="text" name="username" placeholder="{!! trans('back_end.user_name') !!}">
											<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> {{ trans('login.please_enter_username') }}</b></label>
									</section>

									<section>
										<label class="label">{!! trans('back_end.password') !!}</label>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="password" name="password" placeholder="{!! trans('back_end.password') !!}">
											<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> {{ trans('login.enter_your_password') }}</b> </label>
									</section>

									<section>
										<label class="checkbox">
											<input type="checkbox" name="remember" checked="">
											<i></i>{{ trans('login.stay_signed_in') }}</label>
									</section>
								</fieldset>
								<footer>
									<button type="submit" class="btn btn-primary">
										{{ trans('login.sign_in') }}
									</button>
								</footer>
							{!! Form::close() !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4"></div>
				</div>
			</div>

		</div>

		<!--================================================== -->	

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script src="{{ asset('sa/js/plugin/pace/pace.min.js') }}"></script>

	    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
	    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script> if (!window.jQuery) { document.write('<script src="{{ asset('sa/js/libs/jquery-2.1.1.min.js') }}"><\/script>');} </script>

	    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script> if (!window.jQuery.ui) { document.write('<script src="{{ asset('sa/js/libs/jquery-ui-1.10.3.min.js') }}"><\/script>');} </script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="{{ asset('sa/js/app.config.js') }}"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events 		
		<script src="{{ asset('sa/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js') }}"></script> -->

		<!-- BOOTSTRAP JS -->		
		<script src="{{ asset('sa/js/bootstrap/bootstrap.min.js') }}"></script>

		<!-- JQUERY VALIDATE -->
		<script src="{{ asset('sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
		
		<!-- JQUERY MASKED INPUT -->
		<script src="{{ asset('sa/js/plugin/masked-input/jquery.maskedinput.min.js') }}"></script>
		
		<!--[if IE 8]>
			
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
			
		<![endif]-->

		<!-- MAIN APP JS FILE -->
		<script src="{{ asset('sa/js/app.min.js') }}"></script>

		<script type="text/javascript">
			runAllForms();

			$(function() {
				// Validation
				$("#login-form").validate({
					// Rules for form validation
					rules : {
						username : {
							required : true,
						},
						password : {
							required : true,
							minlength : 3,
							maxlength : 20
						}
					},

					// Messages for form validation
					messages : {
						username : {
							required : "{{ trans('login.please_enter_username') }}",
						},
						password : {
							required : "{{ trans('login.please_enter_your_password') }}"
						}
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});
			});
		</script>

	</body>
</html>