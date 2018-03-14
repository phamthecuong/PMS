<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8">
		<title>PMS</title>
		@include('front-end.m24.layout.htmlheader')
    </head>

    <body class="smart-style-2  fixed-header container_custom">
	    @include('front-end.m24.layout.head')
		@include('front-end.m24.layout.aside')
		<div id="main" role="main">
			<!-- RIBBON -->
			@include('front-end.m24.layout.ribbon')
			<!-- END RIBBON -->
			<!-- #MAIN CONTENT -->
			<div id="content" style="padding: 0px;">
				@yield('body.content')	
			</div>
		</div>
		@include('front-end.m24.layout.script')
    </body>
</html>

