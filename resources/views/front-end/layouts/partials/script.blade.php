
<!-- END SHORTCUT AREA -->

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script type="text/javascript" data-pace-options='{ "restartOnRequestAfter": true }' src="{{ asset('/sa/js/plugin/pace/pace.min.js') }}"></script>

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
@yield('extend_loading_js')
<script type="text/javascript">
    if (!window.jQuery) {
        document.write('<script src="{{ asset('/sa/js/libs/jquery-2.1.1.min.js') }}"><\/script>');
    }
</script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript">
    if (!window.jQuery.ui) {
        document.write('<script src="{{ asset('/sa/js/libs/jquery-ui-1.10.3.min.js') }}"><\/script>');
    }
</script>

<!-- IMPORTANT: APP CONFIG -->
<script type="text/javascript" src="{{ asset('/sa/js/app.config.js') }}"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js') }}"></script> 

<!-- BOOTSTRAP JS -->
<script type="text/javascript" src="{{ asset('/sa/js/bootstrap/bootstrap.min.js') }}"></script>

<!-- CUSTOM NOTIFICATION -->
<!-- <script type="text/javascript" src="{{ asset('/sa/js/notification/SmartNotification.min.js') }}"></script> -->
<script type="text/javascript" src="{{ asset('/front-end/js/notification/SmartNotification.js') }}"></script>

<!-- JARVIS WIDGETS -->
<script type="text/javascript" src="{{ asset('/sa/js/smartwidgets/jarvis.widget.min.js') }}"></script>

<!-- EASY PIE CHARTS -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js') }}"></script>

<!-- SPARKLINES -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/sparkline/jquery.sparkline.min.js') }}"></script>

<!-- JQUERY VALIDATE -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>

<!-- JQUERY MASKED INPUT -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/masked-input/jquery.maskedinput.min.js') }}"></script>

<!-- JQUERY SELECT2 INPUT -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/select2/select2.min.js') }}"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/bootstrap-slider/bootstrap-slider.min.js') }}"></script>

<!-- browser msie issue fix -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/msie-fix/jquery.mb.browser.min.js') }}"></script>

<!-- FastClick: For mobile devices -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/fastclick/fastclick.min.js') }}"></script>

<!--[if IE 8]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->

<!-- Demo purpose only -->
<script type="text/javascript" src="{{ asset('/sa/js/demo.min.js') }}"></script>

<!-- MAIN APP JS FILE -->
<script type="text/javascript" src="{{ asset('/sa/js/app.min.js') }}"></script>

<!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
<!-- Voice command : plugin -->
<script type="text/javascript" src="{{ asset('/sa/js/speech/voicecommand.min.js') }}"></script>

<!-- SmartChat UI : plugin -->
<script type="text/javascript" src="{{ asset('/sa/js/smart-chat-ui/smart.chat.ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/sa/js/smart-chat-ui/smart.chat.manager.min.js') }}"></script>

<!-- PAGE RELATED PLUGIN(S) -->
<script type="text/javascript" src="{{ asset('/sa/js/plugin/jquery-form/jquery-form.min.js') }}"></script>

<!-- PAGE RELATED PLUGIN(S) -->
<!-- M.LOADING -->
<script src="{{ asset('/front-end/js/jquery.mloading.js') }}"></script>
<!-- JQUERY GRID -->
<script src="{{ asset('/sa/js/plugin/jqgrid/jquery.jqGrid.min.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/jqgrid/grid.locale-en.min.js') }}"></script>

<!-- DATATABLE -->
<!-- <script src="{{ asset('/sa/js/plugin/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/datatables/dataTables.colVis.min.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/datatables/dataTables.tableTools.min.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/datatables/dataTables.bootstrap.min.js') }}"></script> -->

<script src="{{ asset('/DataTables/datatables.js') }}"></script>
<script src="{{ asset('/sa/js/plugin/datatable-responsive/datatables.responsive.min.js') }}"></script>
<!-- <script src="{{ asset('/DataTables/Responsive-2.1.1/js/dataTables.responsive.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>

<script src="/front-end/js/scrollintoview.js"></script>
<!-- helper.js create color in chartjs -->
<script src="{{ asset('/front-end/js/helper.js') }}"></script>

<script type="text/javascript">
// DO NOT REMOVE : GLOBAL FUNCTIONS!
$(document).ready(function() {
    pageSetUp();
})

</script>

<!-- Your GOOGLE ANALYTICS CODE Below -->
<script type="text/javascript">
// var _gaq = _gaq || [];
// _gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
// _gaq.push(['_trackPageview']);

// (function() {
//     var ga = document.createElement('script');
//     ga.type = 'text/javascript';
//     ga.async = true;
//     ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
//     var s = document.getElementsByTagName('script')[0];
//     s.parentNode.insertBefore(ga, s);
// })();

</script>
@stack('script')
@yield('dp_script')
@yield('extend_js')
