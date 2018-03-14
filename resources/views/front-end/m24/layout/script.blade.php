	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://developers.vietbando.com/V2/Scripts/MapsAPI.js?key=d8c4cfd5-cba6-4cc9-a4a6-756d5d5609ab"></script>
		
		<script>
			if (!window.jQuery) {
				document.write('<script src="/sa/js/libs/jquery-2.1.1.min.js"><\/script>');
			}
		</script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="/sa/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}
		</script>
		<!-- IMPORTANT: APP CONFIG -->
		<script src="/sa/js/app.config.js"></script>
		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
		<script src="/sa/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 
		<!-- BOOTSTRAP JS -->
		<script src="/sa/js/bootstrap/bootstrap.min.js"></script>
		<!-- CUSTOM NOTIFICATION -->
		<script src="/sa/js/notification/SmartNotification.min.js"></script>
		<!-- JARVIS WIDGETS -->
		<script src="/sa/js/smartwidgets/jarvis.widget.min.js"></script>
		<!-- JQUERY VALIDATE -->
		<script src="/sa/js/plugin/jquery-validate/jquery.validate.min.js"></script>
		<!-- JQUERY MASKED INPUT -->
		<script src="/sa/js/plugin/masked-input/jquery.maskedinput.min.js"></script>
		<!-- JQUERY SELECT2 INPUT -->
		<script src="/sa/js/plugin/select2/select2.min.js"></script>
		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="/sa/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>
		<!-- browser msie issue fix -->
		<script src="/sa/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>
		<!-- FastClick: For mobile devices: you can disable this in app.js -->
		<script src="/sa/js/plugin/fastclick/fastclick.min.js"></script>
		<!--[if IE 8]>
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
		<![endif]-->
		<script src="/front-end/js/scrollintoview.js"></script>
		<!-- M.LOADING -->
		<script src="{{ asset('/front-end/js/jquery.mloading.js') }}"></script>
		<script src="/front-end/js/helper.js"></script>
		
		<!-- MAIN APP JS FILE -->
		<script src="/sa/js/app.min.js"></script>
		<!-- <script src="/plugins/TableHeadFixer/tableHeadFixer.js"></script> -->
		<!-- <script src="/plugins/jQuery-Plugin-To-Freeze-Table-Columns-Rows-On-Scroll/js/jquery.CongelarFilaColumna.js"></script> -->
		<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
		<script src="//cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
			<!-- <script type="text/javascript" charset="utf-8" src="js/js_FixedColumns.js"></script> -->
		<script type="text/javascript">

		// $(document).ready(function() {
		// 	$(".pc-table").tableHeadFixer({"left" : 4}); 
		// });


		var user_name , password;
		var pagefunction = function() {
			// With Login
			@if (!Auth::check())
				$("#smart-mod-eg5").click(function(e) {
					webDisplayLogin();
					e.preventDefault();
				});
			@endif
		};

		function webDisplayLogin() {
			$.SmartMessageBox({
				title: "{{trans('map.Login_form')}}",
				content: "{{trans('map.Please_enter_your_user_name')}}",
				buttons: "[{{trans('map.Cancel')}}][{{trans('map.accept')}}]",
				input: "text",
				placeholder: "{{trans('map.Enter_your_user_name')}}",
				inputValue: '',
			}, function(ButtonPress, Value) {
				if (ButtonPress == "{{trans('map.Cancel')}}") {
					// alert("{{trans('map.Why_did_you_cancel_that?')}}");
					return 0;
				}
	
				Value1 = Value.toUpperCase();
				ValueOriginal = Value;
				if (ValueOriginal == '') {
					alert("{{trans('map.you_have_to_enter_your_username')}}");
					return 0;
				}
				$.SmartMessageBox({
					title : "{{trans('map.user_title')}}: <strong>" + Value1 + ",</strong>",
					content : "{{trans('map.And_now_please_provide_your_password')}}:",
					buttons : "[{{trans('map.login')}}]",
					input : "password",
					placeholder : "{{trans('map.password')}}"
				}, function(ButtonPress, Value) {
					user_name = ValueOriginal;
					password = Value;
					if (password == '') {
						alert("{{trans('map.you_have_to_enter_your_password')}}");
						return 0;
					}
					login(user_name, password);
					//alert("Username: " + ValueOriginal + " and your password is: " + Value);
				});
			});
		}
		// end pagefunction
		// load bootstrap-progress bar script and run pagefunction
		loadScript("/sa/js/plugin/bootstrap-progressbar/bootstrap-progressbar.min.js", pagefunction);

		function login(user_name,password) {
			if (user_name !='' && password != '')
			{
				$.post('map_login',{
					'user_name':user_name,
					'password' :password,
					 _token : '{!! csrf_token() !!}'
				},function(result){
					if (result.code == 200) {
						location.href = '/web_map';
					} else {
						alert("{{trans('map.login_error')}}");
					}
				},'json')	
			}
		}

		$(document).ready(function() {
			pageSetUp();
		    $('#layer_tree input[type=checkbox]').click(function() {
		        $('input[name^="option-"]').prop("checked", false);
		        $('input[name^="option-"]').prop("disabled", false);
		        $(this).prop("checked", true);
		        $(this).prop("disabled", true);
		        showLoading();
		        resetLineColor(); 

		        if ($('[name="option-crack"]').is(':checked')) {
		            $("#number1").show();
		            $("#number2").hide();
		            $("#number3").hide();
		            $("#number4").hide(); 
		        } else if ($('[name="option-rut"]').is(':checked')) {
		            $("#number2").show();
		            $("#number1").hide();
		            $("#number3").hide();
		            $("#number4").hide();
		        } else if ($('[name="option-iri"]').is(':checked')) {
		            $("#number3").show();
		            $("#number1").hide();
		            $("#number2").hide();
		            $("#number4").hide();
		        } else if ($('[name="option-mci"]').is(':checked')) {
		            $("#number4").show();
		            $("#number1").hide();
		            $("#number2").hide();
		            $("#number3").hide();
		        } else {
		            $("#number4").hide();
		            $("#number1").hide();
		            $("#number2").hide();
		            $("#number3").hide();
		        }                               
		    })

		});

	</script>
	@if (Auth::check())
		@include('front-end.m24.map_login_script')
		@include('front-end.m24.map_script')
	@else
		@include('front-end.m24.map_public_user_script')
	@endif		
	
	@stack('script')