 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- #CSS Links -->
<!-- Basic Styles -->
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/font-awesome.min.css">
<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/smartadmin-production-plugins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/smartadmin-production.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/smartadmin-skins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/smartadmin-rtl.min.css"> 
<link rel="stylesheet" type="text/css" media="screen" href="/sa/css/demo.min.css">
<!-- #FAVICONS -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<!-- #GOOGLE FONT -->
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
<link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
<link rel="apple-touch-icon" sizes="76x76" href="/sa/img/splash/touch-icon-ipad.png">
<link rel="apple-touch-icon" sizes="120x120" href="/sa/img/splash/touch-icon-iphone-retina.png">
<link rel="apple-touch-icon" sizes="152x152" href="/sa/img/splash/touch-icon-ipad-retina.png">
<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<!-- Startup image for web apps -->
<link rel="apple-touch-startup-image" href="s/a/img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
<link rel="apple-touch-startup-image" href="/sa/img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
<link rel="apple-touch-startup-image" href="/sa/img/splash/iphone.png" media="screen and (max-device-width: 320px)">
<!--map-->	
<!-- Ionicons 2.0.0 -->
<link href="/front-end/css/plugins/ionicons.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/front-end/css/style_fend.css">
<!-- MLOADING -->
<link rel="stylesheet" href="{{ asset('/front-end/css/jquery.mloading.css') }}">
<link href="plugins/datatables/jquery.dataTables.css" rel="stylesheet">


<style>
	#main {
		padding-bottom: 0px !important;
	}
	.login-info a span {
		text-transform: none !important;
	}
	#search_map {
		padding-top: 0px;		
		font-size: 13px;
	}
	#search_map .form-control {
		height: 25px;
		padding: 2px 5px;
	}
	#search_map > .form-group {
		border: 1px solid #d3d3d3;
		background: #e6e6e6;
		margin: 0;
		padding: 2px;
	}
	#search_map .form-group > div {
		padding-left: 5px;
		padding-right: 5px;
	}
	#search_map .form-group div div[class*="col-sm-"] {
		padding-left: 5px;
		padding-right: 0;
		text-align: left;
	}
	#search_map .form-group div label[class*="col-sm-"] {
		padding-right: 0px;
		margin-top: 5px;
		margin-bottom: 0;
		line-height: 20px;
		text-align: left !important;
    	padding-left: 10px;
	}
	#search_map .form-group select {
		background: #f9f9f9;
		font-size: 13px;
	}
	#search_map .form-group select option {
		background: #f3f3f3;
		border: 1px solid #f9f9f9;
	}
	#search_map .form-group .select2-container {
		width: 250px !important;;
		font-size: 13px;
	}
	#search_map .form-group #s2id_sb_id{
		width: 250px !important;
	}

	#map {
	  height: -moz-calc(100vh - 88px) !important;
	  height: -webkit-calc(100vh - 88px) !important;
	  height: calc(100vh - 88px) !important;
	 }
	.frm-search {
	 	width: 390px;
	 	height: block;
	 }

	#left-list {
		top:105px;
		position: absolute;
		left: 0px;
	}
	#left-list ul li {
	 	height: 30px;
	 }
	#left-list ul li img{
		width: 30px;
	    height: auto;
	    margin-right: 5px;
	}
	
	#table-list {
		top: 294px;
		position: absolute;
		margin-left: 16px;
	}
	#table-list tr {
		height: 25px;
	}
	#table-list img {
		width:20px; 
		height: 20px; 
		margin-right: 5px;
	}
	.nav-demo-btn {
		background: white;
	}
	.nav-demo-btn:hover {
		background: white;
	}
	.activate {
		height: 130px;
	}
	.activate form {
		padding-left: 2px;
		height: 5px;
		padding-bottom: 5px;
	}
	.activate .list_ck{
		margin-left: -17px;
	}
	.container_custom {
	    width: 100%;
		margin: 0 auto;
		background: #eee;
		height: calc(100vh - 83px) !important;
		overflow: hidden;
		padding: 0;

	}
	.user_login
	{
		text-align:left;
		font-size:10px;
		font-weight: bold;
		position: absolute;
		left: 5px;
		color: black;
	}	
	.boundary2 {
	    float: left;
	    width: 40px;
	    height: 80px;
	    margin: 3px;
	    padding-left: 0px;
	}
	.boundary2 img {
	    height: 20px;
	    width: 20px;
	    padding-right: 5px;
	    padding-bottom: 10px;
	    padding-left: 5px;
	}
	.boundary2 tr td {
		padding-bottom: 6px;
	}
	.boundary3 {
	    float: right;
	    width: 125px;
	    height: 80px;
	    margin: 10px;
	    margin-top: -85px;
	    position: absolute;
	    bottom: -3px;
	    right: -119px;
	}
	.boundary3 table {
		
		height: 87px;
	}
	.boundary3 table tr td {
		padding-bottom: 9px;
	} 
	.nav-demo-btn {
		display: block;
	    padding: 6px 5px;
	    margin: 5px 10px;
	    /* width: auto; */
	    border-radius: 5px;
	    -webkit-border-radius: 5px;
	    -moz-border-radius: 5px;
	    font-size: 12px;
	    white-space: normal;
	}
	.selectedSection {
	    background: red !IMPORTANT;
    	color: #fff !IMPORTANT;
	}

	.quadrat {
	    -webkit-animation: horizontal-line-effect 1s infinite; /* Safari 4+ */
	    -moz-animation:    horizontal-line-effect 1s infinite; /* Fx 5+ */
	    -o-animation:      horizontal-line-effect 1s infinite; /* Opera 12+ */
	  	animation:         horizontal-line-effect 1s infinite; /* IE 10+, Fx 29+ */
	}
	@keyframes horizontal-line-effect {
		0%, 49% {
		    /*background-color: rgb(117,209,63);*/
		    border: 3px solid #e50000;
		}
		50%, 100% {
		    /*background-color: #e50000;*/
		    border: 3px solid rgb(117,209,63);
		}
	}
	@-webkit-keyframes horizontal-line-effect {
		0%, 49% {
		    /*background-color: rgb(117,209,63);*/
		    border: 3px solid #e50000;
		}
		50%, 100% {
		    /*background-color: #e50000;*/
		    border: 3px solid rgb(117,209,63);
		}
	}
	@-o-keyframes horizontal-line-effect {
		0%, 49% {
		    /*background-color: rgb(117,209,63);*/
		    border: 3px solid #e50000;
		}
		50%, 100% {
		    /*background-color: #e50000;*/
		    border: 3px solid rgb(117,209,63);
		}
	}
	@-moz-keyframes horizontal-line-effect {
		0%, 49% {
		    /*background-color: rgb(117,209,63);*/
		    border: 3px solid #e50000;
		}
		50%, 100% {
		    /*background-color: #e50000;*/
		    border: 3px solid rgb(117,209,63);
		}

	}

	.fixed-header #main-pc {
	    margin-top: 49px;
	}
	
	#main-pc {
	    margin-left: 220px;
	    padding: 0;
	    padding-bottom: 52px;
	    min-height: 500px;
	    position: relative;
	}
	.info_checkbox {
		font-size: 10px;
		margin-left: -10px; 
		padding-bottom: 30px;
	}
	.info_checkbox img {
		width: 15px;
		height: 15px;
	}
	.text_info{
		float: left;
		padding-left: 24px;
    	margin-left: -10px;
	}
	.table.dataTable.no-footer,
	.dataTables_wrapper.no-footer .dataTables_scrollBody {
		border-bottom: none !important;
	}
	table.DTFC_Cloned {
		margin-top: 0px !important;
	}
	.uppercase {
		text-transform: uppercase;
	}
	.center {
		text-align: center;
	}
</style>