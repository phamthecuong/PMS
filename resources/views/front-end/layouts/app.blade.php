
<!DOCTYPE html>
<html lang="en-us">
    <head>
    @include("front-end.layouts.partials.htmlheader")
    </head>
    <body class="menu-on-top">
        @include("front-end.layouts.partials.mainheader")
        @include("front-end.layouts.partials.sidebar")
        <div id="main" role="main">
            <div id="ribbon">

                <span class="ribbon-button-alignment"> 
                    <span id="refresh" class="btn btn-ribbon" data-action="" data-title=""  onclick="location.reload()">
                        <i class="fa fa-refresh"></i>
                    </span> 
                </span>
                @yield("breadcrumb")
            </div>
            <div id="content">
            	@include("front-end.layouts.partials.notication")
                @yield("content")
            </div>
        </div>
        @include("front-end.layouts.partials.footer")

        <div id="shortcut">
            <ul>
                <li>
                    <a href="inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-envelope fa-4x"></i> <span>Mail <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
                </li>
                <li>
                    <a href="calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
                </li>
                <li>
                    <a href="gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
                </li>
                <li>
                    <a href="invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Invoice <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
                </li>
                <li>
                    <a href="gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
                </li>
                <li>
                    <a href="profile.html" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
                </li>
            </ul>
        </div>
        @include("front-end.layouts.partials.script")
    </body>
    <script type="text/javascript">
        localStorage.clear();
        $( document ).ready(function() {
            // complete();
            $('[data-toggle="tooltip"]').tooltip();
            
            $('.ympicker').datepicker( {
                // changeMonth: true,
                // changeYear: true,
                // showButtonPanel: true,
                startView: "year", 
                minViewMode: "months",
                format: 'mm/yyyy',
                // onClose: function(dateText, inst) { 
                //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                // }
            });

            $('.customDatepicker').datepicker( {
                // changeMonth: true,
                // changeYear: true,
                // showButtonPanel: true,
                // startView: "year", 
                // minViewMode: "months",
                format: 'dd-mm-yyyy',
                // onClose: function(dateText, inst) { 
                //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                // }
            });


        });
        // function change_data_notification()
        // {
        //     $.ajax({
        //         type : 'GET',
        //         url : '/user/change_data_notification',
        //     });
        // }
        // function complete()
        // {
        //     $.ajax({
        //         type : 'GET',
        //         url : '/user/load_ajax_notification',
        //         data : {
        //             type: 'complete',
        //         }
        //     }).done(function(msg){
        //         $("#notification ul").remove();
        //         var html = '';
        //         html += '<ul class="notification-body">';
        //         for (var i = 0; i < msg.length; i++)
        //         {
        //             html += '<li>';
        //             html += '<span class="padding-10" style="height:38px;">';
        //             html += '<em class="badge padding-5 no-border-radius bg-color-blueLight pull-left margin-right-5"><i class="fa fa-road fa-fw fa-2x"></i></em>'
        //             html += '<a href = ' + msg[i][2] + ' style="line-height:34px; padding-left:10px;">';
        //             html += '<span>';
        //             html += msg[i][0];
        //             html += '<br>';
        //             html += '</span>';
        //             html += '</a>';
        //             html += '</span>';
        //             html += '</li>';
        //         }
        //         html += '<input type="hidden" id="check_refresh_notification" value="0" >';
        //         html += '</ul>';
        //         $("#notification").append(html);
        //     });
        // }
        
        // function running()
        // {
        //     $.ajax({
        //         type : 'GET',
        //         url : '/user/load_ajax_notification',
        //         data : {
        //             type: 'running',
        //         }
        //     }).done(function(msg){
        //         $("#notification ul").remove();
        //         var html = '';
        //         html += '<ul class="notification-body">';
        //         for (var i = 0; i < msg.length; i++)
        //         {
        //             html += '<li>';
        //             html += '<span>';
        //             html += '<div class="bar-holder no-padding">';
        //             html += '<a href = ' + msg[i][1] + '>';
        //             html += '<p class="margin-bottom-5">';
        //             html += '<i class="fa fa-road"></i>';
        //             html += msg[i][0];
        //             html += '</p>';
        //             html += '<div class="progress progress-md progress-striped">';
        //             html += '<div class="progress-bar bg-color-teal" style="width: ' + msg[i][2] + '%;">' + msg[i][2] + '% </div>';
        //             html += '</div>';
        //             // html += '<span>';
        //             // html += msg[i][0];
        //             // html += '</span>';
        //             html += '</a>';
        //             html += '</div>';
        //             html += '</span>';
        //             html += '</li>';
        //         }
        //         html += '<input type="hidden" id="check_refresh_notification" value="1" >';
        //         html += '</ul>'
        //         $("#notification").append(html);
        //         // console.log(msg);
        //     });
        // }
        // function refresh()
        // {
        //     if ($("#check_refresh_notification").val() == 0)
        //     {
        //         complete();
        //     }
        //     else if ($("#check_refresh_notification").val() == 1)
        //     {
        //         running();
        //     }
        // }
    </script>
</html>