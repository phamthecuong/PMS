<meta charset="utf-8">
<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

<title> PMS </title>
<meta name="description" content="">
<meta name="author" content="">

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Basic Styles -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/font-awesome.min.css') }}">
{{-- <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/> --}}

<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/smartadmin-production-plugins.min.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/smartadmin-production.min.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/smartadmin-skins.min.css') }}">

<!-- SmartAdmin RTL Support -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/smartadmin-rtl.min.css') }}"> 

<!-- We recommend you use "your_style.css') }}" to override SmartAdmin
 specific styles this will also ensure you retrain your customization with each SmartAdmin update.
<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css') }}"> -->

<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/sa/css/demo.min.css') }}">

<!-- FAVICONS -->
<link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">

<!-- GOOGLE FONT -->
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
<!-- MLOADING -->
<link rel="stylesheet" href="{{ asset('/front-end/css/jquery.mloading.css') }}">
<!-- Specifying a Webpage Icon for Web Clip 
 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
<link rel="apple-touch-icon" href="{{ asset('/sa/img/splash/sptouch-icon-iphone.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/sa/img/splash/touch-icon-ipad.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/sa/img/splash/touch-icon-iphone-retina.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/sa/img/splash/touch-icon-ipad-retina.png') }}">

<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<!-- Startup image for web apps -->
<link rel="apple-touch-startup-image" href="{{ asset('/sa/img/splash/ipad-landscape.png') }}" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
<link rel="apple-touch-startup-image" href="{{ asset('/sa/img/splash/ipad-portrait.png') }}" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
<link rel="apple-touch-startup-image" href="{{ asset('/sa/img/splash/iphone.png') }}" media="screen and (max-device-width: 320px)">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css">
<!-- toadstr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
	.disabled {
	    pointer-events:none;
	}
	.padding-top-7 {
		padding-top: 7px;
	}
	.menu-on-top .menu-item-parent {
	    white-space: normal !important;
	    font-size: 10px !important;
	    line-height: 11px;
	}
	.note-error {
		color: #b94a48;
	}
	.jarviswidget-delete-btn,
	.widget-icon,
	.demo {
		display: none !important;
	}
	.align-right {
		text-align: right;
	}
	.dropdown-menu>li.dt-button:not(.active)>a:focus {
		background-color: #ffffff !important;
		color: #000000 !important;
	}
	.wp-error {
		width: 40px;
	    display: block;
	    height: 25px;   
	}
	.wp-error-1 {
		background: red;	
	}
	.wp-error-2 {
		background: orange;	
	}
	.wp-error-3 {
		background: blue;	
	}
	.wp-error-4 {
		background: purple;	
	}
	.wp-error-5 {
		background: black;	
	}
	.menu-on-top nav ul ul {
		width: 250px !important;
	}
	table.dataTable thead th {
		font-size: 11px !important;
		vertical-align: middle;
	}
	th.hasinput {
		position: relative;
	}
	th.hasinput .hint-custom {
		position: absolute;
	    right: 1px;
	    top: 1px;
	}
	.mloading-bar {
		white-space: pre-wrap;
	}
	.wp_history_item {
		font-style: oblique;
	}
	.menu-on-top ul ul .menu-item-parent {
		min-width: 150px !important;
		max-width: 200px !important;
		text-align: left !important;
	}
	.red-panel {
		background-color: #b94a48 !important;
		color: white !important;
	}
</style>
@stack("css")
