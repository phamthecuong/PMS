<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
<script>


    segment_select.on('change', getDataSegment);
    $("#zoom_zone").contextmenu(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
	var 
        segement_id,
        stage = new createjs.Stage("streching_area"),
        stage_zoom = new createjs.Stage('zoom_zone'),
		color = {RMD: 'rgba(0, 0, 255, 0.8)', MH: 'rgba(255, 0, 0, 0.8)'},
        type = ['RMD','MH','TV'],
        boundary_right,
        boundary_left,
        height_lane = 5, //height default section (px)
        axis_left_lane0 = 5, // axis y default lane 0 (px)
        axis_right_lane0 = axis_left_lane0 + height_lane,
        mid_height = ($('#streching_area').height())/2,
        default_width_limit = 11380,// (m)
        width_zoom_zone = $('#streching_area').parent().width() - 13;
        direction_text = {en:['L', 'R'], vn: ['T', 'P']};
    var
        limit_left_px = 0, //(px default limit_left)
        limit_right_px = 100, //(px defalut limit_right)
        width_px = 0, // (px zoom zone)
        limit_left_change,
        limit_right_change,
        limit_left,
        limit_right,
        width_y = 55, //(px)
        width_lane = 40, // width default lane (px) 
        offset_first = 67, //140_postion rect y lane 0 (px)
        offset_second = offset_first + width_lane, //180_postion rect y lane 0 (px)
        space_left = 140, // (px)
        space_right = space_left + width_lane,
        RMD_show = true,
        MH_show = true,
        TV_show = true,
        limit_left_header = 0,
        limit_right_header = 100,
        width_header = 0;

    $(function() {
        $('#streching_area').attr('width', width_zoom_zone);

        $('#zoom_zone').attr('width', width_zoom_zone);

        $('#RMD_history').attr('width', width_zoom_zone);
        $('#RMD_history').attr('height', '500px');

        $('#MH_history').attr('width', width_zoom_zone);
        $('#MH_history').attr('height', '500px');

        $('#TV_history').attr('width', width_zoom_zone);
        $('#TV_history').attr('height', '500px'); 
        //drap
        $('#zoom').draggable({
            axis: "x",
            containment: "#streching_area",
            cursor: "crosshair",
            drag: function( event, ui ) {
                limit_left_header = ui.position.left - 1;
                if (width_px != 0) {
                    limit_right_header = limit_left_header + width_header;
                } else {
                    limit_right_header = limit_left_header + 100;
                }
                $('.from_zoom').text('Km'+getInfoZoom(limit_left_header, true).km+'+'+getInfoZoom(limit_left_header, true).m +' - '+ 'Km'+getInfoZoom(limit_right_header, true).km+'+'+getInfoZoom(limit_right_header, true).m);
            },
            stop: function( event, ui ) {
                limit_left_px = ui.position.left - 1;
                if (width_px != 0) {
                    limit_right_px = limit_left_px + width_px;
                    limit_left = convertPixelToMeter(limit_left_px);
                    limit_right =  convertPixelToMeter(limit_right_px);
                } else {
                    limit_right_px = limit_left_px + 100;
                    limit_left = convertPixelToMeter(limit_left_px);
                    limit_right =  convertPixelToMeter(limit_right_px);
                }
               
                getDataZoomZone(limit_left, limit_right);
            }
        });
        //resize    
        $( "#zoom" ).resizable({
            handles: "e, w",
            minWidth: 10,
            containment: '#streching_area',
            resize: function( event, ui ) {
                width_header = ui.size.width;
                limit_left_header = ui.position.left;
                limit_right_header = limit_left_header + width_header;
                
                $('.from_zoom').text('Km'+getInfoZoom(limit_left_header, true).km+'+'+getInfoZoom(limit_left_header, true).m +' - '+ 'Km'+getInfoZoom(limit_right_header, true).km+'+'+getInfoZoom(limit_right_header, true).m);
            },
            stop: function(e, ui) {
                width_px = ui.size.width;
                limit_left_px = ui.position.left;
                limit_right_px = ui.size.width + ui.position.left;
                
                limit_left = convertPixelToMeter(limit_left_px);
                limit_right =  convertPixelToMeter(limit_right_px);

                getDataZoomZone(limit_left, limit_right);
            }
        });
        //click show hide data type
        hideShowData();
    });

    function hideShowData() {
        $('#RMD_hide').click(function(){
            RMD_show = ($('#RMD_hide').is(':checked')) ? true : false;
            reloadDataZoomZone();
        });

        $('#MH_hide').click(function() {
            MH_show = ($('#MH_hide').is(':checked')) ? true : false;
            reloadDataZoomZone();
        });

        $('#TV_hide').click(function() {
            TV_show = ($('#TV_hide').is(':checked')) ? true : false;
            reloadDataZoomZone();
        });
    }

    function getDataZoomZone(limit_left, limit_right) {
        var zoomZoneSelect = $('#zoom_zone');
        var url = 'ajax/frontend/getDataZoomZone';
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                segment_id: segement_id,
                limit_left: limit_left,
                limit_right: limit_right,
            }
        })
        .done(function(response) {
            stage_zoom.removeAllChildren();
            removeElementHistory();
            
            var lane_no = response.segment_info.lane_no;
            var width_axis_y = (lane_no + 1) * width_y;

            zoomZoneSelect.attr('height', (lane_no + 1)*50 + 20);
            space_left = (zoomZoneSelect.height() / 2) - width_lane / 2;
            console.log("space left", space_left);
            space_right = space_left + width_lane;
            offset_first = (zoomZoneSelect.height() / 2) - 5*width_lane / 2 + 7;
            offset_second = offset_first + width_lane;
           
            changeLimitInfo(limit_left, limit_right);   
            addCheckbox(lane_no); 

            drawInfoZoom( 
                    'Km ' + getInfoZoom(limit_left_px, true).km + '+' + getInfoZoom(limit_left_px, true).m, 
                    65, 
                    space_right + (lane_no / 2)*width_lane + 5,
                    'white',
                    stage_zoom
                );
            drawInfoZoom( 
                    'Km ' + getInfoZoom(limit_right_px, true).km + '+' + getInfoZoom(limit_right_px, true).m, 
                    width_zoom_zone - 114, 
                    space_right + (lane_no / 2)*width_lane + 5,
                    'white',
                    stage_zoom
                );

            if (lane_no == 1) {
                if (RMD_show == true) {
                    drawZoomType(response, 'RMD', 1, 1);;
                }
                if (MH_show == true) {
                    drawZoomType(response, 'MH', 1, 1);
                }
                if (TV_show == true) {
                    drawZoomType(response, 'TV', 1, 1);
                }
            }
           
            for ( var lane_post = 1; lane_post <= lane_no; lane_post++ ) {
                //draw axist
                if ( lane_no > 1 && lane_post <= lane_no / 2  ) {
                    drawAxisX(lane_post, lane_no);
                    if (RMD_show == true) {
                        drawZoomType(response, 'RMD', lane_post, lane_no);
                    }
                    if (MH_show == true) {
                        drawZoomType(response, 'MH', lane_post, lane_no);
                    }
                    if (TV_show == true) {
                        drawZoomType(response, 'TV', lane_post, lane_no);
                    }
                } 
            }
            drawAxisY(width_axis_y); // fixed axist Y
            drawLaneSinger();// draw lane singer
            stage_zoom.update();
            
            getDataHistory(segement_id, lane_pos_number, direction);  //show history
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }
   
    function drawZoomType(data_type, type, lane_post, lane_no) {
        for (var x in data_type[type]) {
            var data = data_type[type][x];
           
            var x = convertMeterToPixelZoom(data.from) + 62 ; // start from
            var y = ((width_lane - 5) - convertWidthLane(data.lane_width)) / 2;
           
            var start_point = convertMeterToPixelZoom(data.from);
            var end_point = convertMeterToPixelZoom(data.to);
            
            if (type !== 'TV') {
                if (data.lane_pos_number == lane_post) {
                    if (data.direction == 1) { // left
                        var y_rect = offset_first - lane_post * width_lane + y;
                        if (type == 'MH') { // set distance MH
                            if (data.direction_running == 0) {
                                y_rect = y_rect - convertWidthLane(data.distance);
                            
                            } else if (data.direction_running == 1) {
                                y_rect = y_rect + convertWidthLane(data.distance);
                            }    
                        }
                        drawRectangle(
                            x,
                            y_rect, 
                            end_point - start_point,
                            convertWidthLane(data.lane_width) ,
                            color[type], 
                            true,
                            data,
                            type
                        )

                    } else if (data.direction == 2) {  //right
                        var y_rect = offset_second + lane_post*width_lane - width_lane + y;
                        if (type == 'MH') { // set distance MH
                            if (data.direction_running == 0) { 
                                y_rect = y_rect - convertWidthLane(data.distance);
                            
                            } else if (data.direction_running == 1) {
                                y_rect = y_rect + convertWidthLane(data.distance);
                            }    
                        }
                        drawRectangle(
                            x,
                            y_rect, 
                            end_point - start_point,
                            convertWidthLane(data.lane_width) ,
                            color[type], 
                            true,
                            data,
                            type
                        )
                    }

                } else if (data.lane_pos_number == 0 && data.direction == 3) {  //singer
                    var y_rect = offset_first  + y;
                    if (type == "MH") { // set distance MH
                        if (data.direction_running == 0) {
                            y_rect = y_rect - convertWidthLane(data.distance);
                        
                        }else if (data.direction_running == 1) {
                            y_rect = y_rect + convertWidthLane(data.distance);
                        }    
                    }
                    drawRectangle(
                        x,
                        y_rect, 
                        end_point - start_point,
                        convertWidthLane(data.lane_width) ,
                        color[type], 
                        true,
                        data,
                        type
                    )
                }
            
            } else { // draw TV
                //var y_text_offset = 90;
                drawTV(
                    convertMeterToPixelZoom(data.station) + width_lane + 10, 
                    offset_first - (lane_no / 2) * width_lane + 60,
                    true,
                    data
                );
            }
        }
    }

	function getDataSegment() {
        var url = '/ajax/frontend/data_segment';
        segement_id =  $('#segment option:selected').val();

        $.ajax({
            url: url,
            method: 'GET',
            data: {
                segment_id: segement_id
            }
        })
        .done(function(response) {
            console.log(response);
            // info segement
            $('#km_from').html('km'+ response.info.km_from + '+' + response.info.m_from);
            $('#km_to').html('km'+ response.info.km_to + '+' + response.info.m_to);

            changeBoundaryInfo(
                    response.info.km_from , 
                    response.info.m_from, 
                    response.info.km_to,
                    response.info.m_to
                );
            mainDraw(response);
            reloadDataZoomZone();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }
    
    function mainDraw(data) {
        stage.removeAllChildren();
        var no_lane = data.info.no_lane;
        
        for (var x in type) {
            var data_type = data[type[x]];
            if (type[x] == 'TV') {
                if (jQuery.isEmptyObject(data_type) == false) {
                    
                    for (var i in data_type) {
                        var start_point = convertMeterToPixel(
                                            convertToMeter(
                                                data_type[i].km_station,
                                                data_type[i].m_station
                                            )
                                        );
                        var y_TV = axis_left_lane0 - (no_lane / 2) * height_lane + 30;
                        drawTV(
                            start_point - 15, 
                            y_TV , 
                            false, 
                            data_type[i].id
                        );
                    }
                }

            } else if (type[x] != 'info')  {
                if (jQuery.isEmptyObject(data_type) == false) {
                    
                    for (var i in data_type) {
                        DrawData(
                            data_type[i], 
                            color[type[x]], 
                            height_lane,
                            no_lane, 
                            type[x]
                        );
                    }
                }
            }
        }
        stage.update();
    }

    function DrawData(data , color, height_lane, no_lane, type) {
        var start_point = convertMeterToPixel(convertToMeter(data.km_from, data.m_from));
        var end_point = convertMeterToPixel(convertToMeter(data.km_to, data.m_to));
        
        for (var no = 0; no <= no_lane / 2; no ++) {
            if (data.lane_pos_number == no) {
                if (data.direction == 1) { // left
                    drawRectangle(
                        start_point, 
                        axis_left_lane0 - no - (no - 1) * height_lane - 10,
                        end_point - start_point,
                        height_lane, 
                        color,
                        false,
                        data,
                        type
                    );

                } else if (data.direction == 2) { //right
                    drawRectangle(
                        start_point, 
                        axis_right_lane0 + no + (no - 1) * height_lane + 5,
                        end_point - start_point, 
                        height_lane, 
                        color,
                        false,
                        data,
                        type
                    );

                } else if (data.direction == 3 && data.lane_pos_number == 0){ // singer
                    drawRectangle(
                        start_point,
                        axis_left_lane0,
                        end_point - start_point, 
                        height_lane, 
                        color,
                        false,
                        data,
                        type
                    );
                } 
            }
        }
    }   

    /** 
        draw lane singer 
    */   

    function drawLaneSinger() {
        lineX(5, width_zoom_zone, space_left, 'yellow', 10, 10);
        text('0', 20, space_left + 10, "white");
        lineX(5, width_zoom_zone, space_right, 'yellow', 10, 10);
    }

    /** 
        draw axist X 
    */   

    function drawAxisX(lane_post, lane_no) {
        var locale = "<?php echo App::isLocale('en') ? 'en' : 'vn';  ?>";
        lineX(
            5, 
            width_zoom_zone, 
            space_left - lane_post * width_lane, 
            'white', 
            5, 
            2 
        );
        text(  // left
            direction_text[locale][0] + '' + lane_post, 
            20, 
            space_left - lane_post * width_lane + 10, 
            'white'
        );
        lineX(
            5, 
            width_zoom_zone, 
            space_right + lane_post * width_lane, 
            'white', 
            5, 
            2
        );
        text(  // right
            direction_text[locale][1]+' '+lane_post, 
            20, 
            space_right + lane_post * width_lane - 20,
            'white'
        );
    }

    /** 
        draw axist Y limit
    */ 
    
    function drawAxisY(width_axis_y) {
        lineY(  // start
            60, 
            space_right - width_axis_y / 2,
            space_left + width_axis_y / 2,
            'white', 
            5, 
            2
        );
        lineY(  // end
            width_zoom_zone - 35, 
            space_right - width_axis_y / 2, 
            space_left + width_axis_y / 2, 
            'white', 
            5, 
            2
        );
    }

    function text(position, x, y, color) {
        var label = new createjs.Text(position, 'solid 5px Arial', color);
        label.x = x;
        label.y = y;
        stage_zoom.addChild(label);
    }

    function drawRectangle(x, y, w, h, color, flash, data , type) {
        var rect = new createjs.Shape();
		rect.graphics.setStrokeStyle(0.5)
                    .beginStroke('black')
                    .beginFill(color)
					.drawRect(x, y, w, h);
		rect.y = mid_height;
        if (flash) {
                console.log("draw ractangle from zoome zone"+ x + y + w +h +color);
            stage_zoom.addChild(rect);
            rect.addEventListener('click', function (e) {
                if (e.nativeEvent.button == 2) {
                    if (type == 'MH') {
                        clickRContextmenu('mh', data.id);
                    } else if (type == 'RMD') {
                        clickRContextmenu('ri', data.id);
                    }  
                } else {
                    if (type == 'MH') {
                       showModalMH(data.id);
                    } else if (type == 'RMD') {
                        showModalRI(data.id);
                    }    
                } 
            })
            rect.addEventListener('mouseover', function(e) {
                stage_zoom.cursor = "pointer";
                var x_left = (e.stageX > 800) ? e.stageX - 150 : e.stageX ; 

                $('.popup_info').show().css({'margin-left': x_left, 'margin-top': y + 80 });
                $('#detail').text('Km ' + getInfoZoom(data.from, false).km +'+'+ getInfoZoom(data.from, false).m +' - '+'Km ' +getInfoZoom(data.to, false).km +'+'+ getInfoZoom(data.to, false).m);
            })
            rect.addEventListener('mouseout', function() {
                $('.popup_info').hide();
            })
            stage_zoom.enableMouseOver();
        } else {
            stage.addChild(rect);
        }
    }

    function drawTV(x, y,flash, data) {   
        img = new Image();
        img.src = "/front-end/img/flash.ico";
        r_mc = new createjs.Bitmap(img);
        r_mc.y = y;
        r_mc.x = x;
        r_mc.scaleX = 0.1; 
        r_mc.scaleY = 0.1; 
        if (flash) {
            stage_zoom.addChild(r_mc);
            r_mc.addEventListener('click', function (e) {
                if (e.nativeEvent.button == 2) {
                    clickRContextmenu('tv', data.id);
                } else {
                    showModalTV(data.id);
                }
            })
            r_mc.addEventListener('mouseover', function(e) {
                stage_zoom.cursor = "pointer";
                $('.popup_info').show().css({'margin-left': e.stageX, 'margin-top': y+20});
                $('#detail').text(
                        'Km ' + getInfoZoom(data.station, false).km 
                        +'+'+ getInfoZoom(data.station, false).m
                    );
            })
            r_mc.addEventListener('mouseout', function() {
                $('.popup_info').hide();
            })
            stage_zoom.enableMouseOver();
        } else {
            stage.addChild(r_mc);
        }
    }

    /** 
        draw line X 
    */   
    function lineX(x1, x2, y, color, w, h) {
        var line = new createjs.Shape();
        line.graphics
                .setStrokeStyle(1)
                .beginStroke(color)
                .setStrokeDash([w, h], 0)
                .moveTo(x1, y)
                .lineTo(x2, y)
                .endStroke()
        stage_zoom.addChild(line);
    }

    /** 
        draw axist line Y 
    */   
    function lineY(x, y1, y2, color, w, h) {
         var line = new createjs.Shape();
        line.graphics
                .setStrokeStyle(1)
                .beginStroke(color)
                .setStrokeDash([w, h], 0)
                .moveTo(x, y1)
                .lineTo(x, y2)
                .endStroke()
        stage_zoom.addChild(line);
    }

    /**
    *   draw arrow
    */

    function triangle(x, y, color, flash) {
        var coord_arrow = new createjs.Shape();
        var axis_center_x = 9;
        var axis_start_y = 1;
        var arrwidth = 5;
        var arrxtnd = 5;
        coord_arrow.graphics.beginFill(color);
        coord_arrow.graphics.moveTo(axis_center_x, axis_start_y-arrwidth/2)
                            .lineTo(axis_center_x + arrwidth, axis_start_y + arrwidth + arrxtnd)
                            .lineTo(axis_center_x - arrwidth, axis_start_y + arrwidth + arrxtnd)
                            .lineTo(axis_center_x, axis_start_y-arrwidth/2);
        coord_arrow.graphics.endFill();
        if (flash) {
            coord_arrow.rotation  = 90;
        }
        coord_arrow.y = y;
        coord_arrow.x = x;
        stage_zoom.addChild(coord_arrow);
    }

    function convertMeterToPixel(m) {
        var value = ((width_zoom_zone - 2) * (m-boundary_left)) / (boundary_right - boundary_left);
        return +Math.round(value);
    }

    function convertToMeter(km, m) {
        return +km*1000 + (+m);
    }

    function changeBoundaryInfo(km_from, m_from, km_to, m_to) {
        boundary_left = convertToMeter(km_from, m_from);
        boundary_right = convertToMeter(km_to, m_to);
    }

    // -------------- start zoom zone -----------

    function reloadDataZoomZone() {
        $('.from_zoom').text(
            'Km'+getInfoZoom(limit_left_header, true).km+'+'+
            getInfoZoom(limit_left_header, true).m +' - '+ 
            'Km'+getInfoZoom(limit_right_header, true).km+'+'+
            getInfoZoom(limit_right_header, true).m
        );
        if(limit_left_px != 0 || limit_right_px != 100) {

            getDataZoomZone(convertPixelToMeter(limit_left_px), convertPixelToMeter(limit_right_px)); 
        } else {
            console.log("check convertPixelToMeter", convertPixelToMeter(100));
            limit_left = boundary_left;
            limit_right = convertPixelToMeter(limit_right_px);
            getDataZoomZone(limit_left, limit_right);    
        } 
    }

    function convertWidthLane(m) {
        // width_lane: 5m = 40px;
        return +Math.round((35/5)*m);
    }

    function drawInfoZoom(position, x, y, color, $stage) {
        $stage.removeChild(label_info);
        var label_info = new createjs.Text(position, '15px solid Arial', color);
        label_info.x = x;
        label_info.y = y;
        $stage.addChild(label_info);
    }

    function getInfoZoom(px, flash) {
        if (flash) {
            var px = ((px*(boundary_right - boundary_left)) / width_zoom_zone) + boundary_left;
        }
        var km = Math.floor(px / 1000);
        var m = Math.floor(px - km*1000);
        return {km: km, m: m};
    }

    function convertPixelToMeter(px) {
        console.log("boundary_right:" + boundary_right + "boundary_left:" + boundary_left + "width_zoom_zone:" + width_zoom_zone);
        var m =  ((px*(boundary_right - boundary_left)) / width_zoom_zone) + boundary_left;
        return Math.round(m);
    }

    function changeLimitInfo(limit_left, limit_right) {
        limit_left_change = limit_left;
        limit_right_change = limit_right;
    }

    function convertMeterToPixelZoom(m) {
        var value = ((width_zoom_zone - 100) * (m - limit_left_change)) / (limit_right_change - limit_left_change);
        console.log("width_zoom_zone", width_zoom_zone);
        return +Math.round(value);
    }

    function addCheckbox(lane_no) {
        var html = '';
        var container = $('#checkbox');
        container.css('margin-left' , width_zoom_zone - 26 + "px")
        container.empty();
        
        if (lane_no / 2 < lane_pos_number) {
            lane_pos_number = 0;
            direction = 3;
        }
        var paddingTopDefault = space_left + 10; // lane 0;
        html += checkbox(0, 3, paddingTopDefault); // lane 0 defalult
        for (var c = 1; c <= lane_no / 2; c ++) {
            var top_left  = paddingTopDefault - c * width_lane;
            var top_right = paddingTopDefault + c * width_lane;
            html += checkbox(c, 1, top_left);;
            html += checkbox(c, 2, top_right);
        }
        container.html(html);

        $("input[name='checkbox']").on('change', function() {
            $(this).prop('checked', true);
            $("input[name='checkbox']").not(this).prop('checked', false);  
        });
        
        $("input[name='checkbox']").click(function() {
            if ($(this).is(':checked')) {
                removeElementHistory();
                direction       = $(this).data('direction');    
                lane_pos_number = $(this).data('lanePosNumber');
                getDataHistory(segement_id, lane_pos_number, direction);
            }
        });
    }

    function checkbox(lane_pos, direct, padding_top) {
        var html = '';
        html += "<label class='checkbox'><input type='checkbox' data-direction="+ direct +" data-lanePosNumber="+ lane_pos +"name='checkbox'"; 
        if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
        } else {
            html += "/>"
        }
        html += "<i style='position:absolute; top:"+ padding_top +"px !important;'></i></label>";
        return html; 
    }
//------end zoom zone -----------

</script>

@include('front-end.m13.inputting_system.script.history_type')