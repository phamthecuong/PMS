<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
<script>
    segment_select.on('change', getDataSegment);
    route_select.on('change', getDataBranch);
    sb_select.on('change', getDataSB);
    rmb_select.on('change', getDataRMD);
    $("#zoom_zone").contextmenu(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
	var 
        segement_id,
        branch_id,
        sb,
        rmb = 1,
        stage = new createjs.Stage("streching_area"),
        stage_zoom = new createjs.Stage('zoom_zone'),
        pc_lane = new createjs.Stage('PcLane'),
		color = {
            RMD: 'rgba(0, 0, 255, 0.8)', RMD1: 'rgba(103, 51, 141, 0.8)', RMD2: 'rgba(230, 5, 215, 0.8)', RMDOther: 'rgba(127, 127, 127, 0.8)',
            MH: 'rgba(255, 0, 0, 0.8)', MH1: 'rgba(242, 101, 14, 0.8)', MH2: 'rgba(57, 12, 12, 0.8)', MH_1: 'rgb(6, 231, 43)', 
            MH_2: 'rgb(80, 200, 120)', MH_3: 'rgb(255, 215, 0)', MH_4: 'rgb(11, 56, 97)', MHOther: 'rgba(195, 176, 145, 0.8)'
        },
        type = ['RMD','MH','TV'],
        boundary_right,
        boundary_left,
        height_lane = 5, //height default section (px)
        axis_left_lane0 = 5, // axis y default lane 0 (px)
        axis_right_lane0 = axis_left_lane0 + height_lane,
        mid_height =($('#streching_area').height())/2,
        default_width_limit = 11380,// (m)
        width_zoom_zone = $('#streching_area').parent().width() - 13,
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
        MH_pavement = false,
        MH_classification = true,
        MH_RS = true,
        MH_RC = false,
        TV_show = true,
        limit_left_header = 0,
        limit_right_header = 100,
        width_header = 0;

    $(function() {
        $('#streching_area').attr('width', width_zoom_zone);
        $('#content_zoom').css('width', width_zoom_zone);

        $('#zoom_zone').attr('width', width_zoom_zone);

        $('#RMD_history').attr('width', width_zoom_zone);

        $('#MH_history').attr('width', width_zoom_zone);
       // $('#MH_history').attr('height', '400px');

        $('#TV_history').attr('width', width_zoom_zone);
        //drap
        $('#zoom').draggable({
            axis: "x",
            containment: "#content_zoom",
            cursor: "crosshair",
            drag: function( event, ui ) {
                limit_left_header = ui.position.left;
                if (width_px != 0) {
                    limit_right_header = limit_left_header + width_header;
                } else {
                    limit_right_header = limit_left_header + 100;
                }
                $('.from_zoom').text('Km'+getInfoZoom(limit_left_header, true).km+'+'+getInfoZoom(limit_left_header, true).m +' - '+ 'Km'+getInfoZoom(limit_right_header, true).km+'+'+getInfoZoom(limit_right_header, true).m);
            },
            stop: function( event, ui ) {
                limit_left_px = ui.position.left;
                if (width_px != 0) {
                    limit_right_px = limit_left_px + width_px;
                    limit_left = convertPixelToMeter(limit_left_px);
                    limit_right =  convertPixelToMeter(limit_right_px);
                } else {
                    limit_right_px = limit_left_px + 100;
                    limit_left = convertPixelToMeter(limit_left_px);
                    limit_right =  convertPixelToMeter(limit_right_px);
                }
                getData_TV(segement_id, branch_id, sb, rmb);  //show TV    
                getDataZoomZone(limit_left, limit_right);

            }
        });
        //resize    
        $( "#zoom" ).resizable({
            handles: "e, w",
            minWidth: 10,
            containment: '#content_zoom',
            resize: function( event, ui ) {
                limit_left_header = ui.position.left;
                width_header = ui.size.width;
                limit_right_header = limit_left_header + width_header;
                $('.from_zoom').text('Km'+getInfoZoom(limit_left_header, true).km+'+'+getInfoZoom(limit_left_header, true).m +' - '+ 'Km'+getInfoZoom(limit_right_header, true).km+'+'+getInfoZoom(limit_right_header, true).m);
            },
            stop: function(e, ui) {
                limit_left_px = ui.position.left;
                width_px = ui.size.width;
                limit_right_px = ui.size.width + ui.position.left;
                limit_left = convertPixelToMeter(limit_left_px);
                limit_right =  convertPixelToMeter(limit_right_px);
                getData_TV(segement_id, branch_id, sb, rmb);  //show TV
                getDataZoomZone(limit_left, limit_right);
            }
        });
        //click show hide data type
        hideShowData();
    });

    function hideShowData() {
        $('#MH_RS').click(function() {
            $('#MH_RC').attr('checked', false);
            $('.mh_1').css({'background': '#FF0000'}).text("AC"); 
            $('.mh_2').css({'background': '#FF9933'}).text("BST"); 
            $('.mh_3').css({'background': '#390B0B'}).text("CC"); 
            $('.mh_4').css({'background': '#C3B091'}).text("Other");  
            MH_RS = true;
            MH_RC = false;
            reloadDataZoomZone();
        });
        $('#MH_RC').click(function() {
            $('#MH_RS').attr('checked', false); 
            $('.mh_1').css({'background': '#06E72B'}).text("PM-B"); 
            $('.mh_2').css({'background': '#50C878'}).text("ER"); 
            $('.mh_3').css({'background': '#FFD700'}).text("RM"); 
            $('.mh_4').css({'background': '#0B3861'}).text("PM-M"); 
            MH_RS = false;
            MH_RC = true;
            reloadDataZoomZone();
        });
        var $tdCheckbox = $('#MH_classification');
        $('#RMD_hide').click(function(){
            if ($('#RMD_hide').is(':checked')) {
                RMD_show = true;
            } else {
                RMD_show = false;
            }
            reloadDataZoomZone();
        });
        $('#MH_pavement').click(function() {
            $('#MH_set').attr('checked', true);
            if ($('#MH_pavement').is(':checked')) 
            {
                $('#MH_classification').attr('checked', false); 
                MH_pavement = true;
                MH_classification = false;
            }
            else
            {
                MH_pavement = false;
            }
            reloadDataZoomZone();
        });
        $('#MH_classification').click(function() {
            $('#MH_set').attr('checked', true);
            if ($('#MH_classification').is(':checked')) 
            {
                $('#MH_pavement').attr('checked', false); 
                MH_pavement = false;
                MH_classification = true;
            }
            else 
            {
                MH_classification = false;
            }
            reloadDataZoomZone();
        });

        $('#MH_set').click(function(){
            if (!this.checked) {
                MH_pavement = false;
                MH_classification = false;
            }
            else
            {
                MH_pavement = false;
                MH_classification = true;
            }
            $tdCheckbox.prop('checked', this.checked);
            reloadDataZoomZone();
        }); 
        $('#TV_hide').click(function() {
            if ($('#TV_hide').is(':checked')) {
                TV_show = true;
            } else {
                TV_show = false;
            }
            reloadDataZoomZone();
        });
    }

    function getDataZoomZone(limit_left, limit_right) {

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
            // set height, width zoom zone
            $('#zoom_zone').attr('height', (lane_no + 1)*50 + 20);
            space_left = ($('#zoom_zone').height())/2 - width_lane/2;
            space_right = space_left + width_lane;
            offset_first = (($('#zoom_zone').height())/2) - 5*width_lane/2 + 7;
            offset_second = offset_first + width_lane;
            var width_axis_y = (lane_no + 1) * width_y;
            //change limit zoom zone
            changeLimitInfo(limit_left, limit_right);   
            // generate checbox
            addCheckbox(lane_no); 
            // info segment
            drawInfoZoom( 
                    'Km ' + getInfoZoom(limit_left_px, true).km + '+' + getInfoZoom(limit_left_px, true).m, 
                    65, 
                    space_right + (lane_no/2)*width_lane + 5,
                    'white',
                    stage_zoom
                );
            drawInfoZoom( 
                    'Km ' + getInfoZoom(limit_right_px, true).km + '+' + getInfoZoom(limit_right_px, true).m, 
                    width_zoom_zone - 114, 
                    space_right + (lane_no/2)*width_lane + 5,
                    'white',
                    stage_zoom
                );
           
            for ( var lane_post = 1; lane_post <= lane_no; lane_post++ ) {
                //draw axist
                if ( lane_no > 1 && lane_post <= lane_no / 2  ) {
                    drawAxisX(lane_post, lane_no);
                    if (RMD_show == true) {
                        drawZoomType(response, 'RMD', lane_post, lane_no);
                    }
                    if (MH_pavement == true) {
                        drawZoomType(response, 'MH', lane_post, lane_no);
                    } 
                    if (MH_classification == true) {
                        drawZoomType(response, 'MH', lane_post, lane_no, true);
                    }
                    if (TV_show == true) {
                        drawZoomType(response, 'TV', lane_post, lane_no);
                    }
                } else if (lane_no == 1) {
                    if (RMD_show == true) {
                        drawZoomType(response, 'RMD', 1, 1);;
                    }
                    if (MH_pavement == true) {
                        drawZoomType(response, 'MH', 1, 1);
                    } 
                    if (MH_classification == true) {
                        drawZoomType(response, 'MH', 1, 1, true);
                    }
                    if (TV_show == true) {
                        drawZoomType(response, 'TV', 1, 1);
                    }
                }
            }
            drawAxisY(width_axis_y); // fixed axist Y
            drawLaneSinger();// draw lane singer
            stage_zoom.update();
            
            getDataHistory(segement_id, lane_pos_number, direction);  //show history
            getDataPMoS(segement_id, lane_pos_number, direction);  //show PC 
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }
    function drawZoomType(data_type, type, lane_post, lane_no, type_mh = false) {
        for (var x in data_type[type]) {
            var data = data_type[type][x];
            var x = convertMeterToPixelZoom(data.from) + 62 ; // start from
            var y = ((width_lane - 5) - convertWidthLane(data.lane_width))/2;
            var start_point = convertMeterToPixelZoom(data.from);
            var end_point = convertMeterToPixelZoom(data.to);
            if (type == 'MH' || type == 'RMD') {
                if (type_mh) {
                    if (data.classification == 1) {
                        setColor = color[type + '_1'];
                    }
                    else if (data.classification == 2) 
                    {
                        setColor = color[type +'_2'];
                    }
                    else if (data.classification == 3) 
                    {
                        setColor = color[type +'_3'];
                    }
                    else if(data.classification == 4)
                    {
                        setColor = color[type +'_4'];
                    } 
                }
                else
                {
                   if (data.pavement_type == 34) {
                    setColor = color[type];
                    }
                    else if (data.pavement_type == 6) 
                    {
                        setColor = color[type +'1'];
                    }
                    else if (data.pavement_type == 5) 
                    {
                        setColor = color[type +'2'];
                    }
                    else
                    {
                        type == 'MH' ? setColor = color['MHOther'] : setColor = color['RMDOther'];
                    } 
                }
                
                if (data.lane_pos_number == lane_post) {
                    if (data.direction == 1) { // left
                        var y_rect = offset_first - lane_post*width_lane + y;
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
                            setColor, 
                            true,
                            data,
                            type
                        )
                    } else if (data.direction == 2) {  //right
                        var y_rect = offset_second + lane_post*width_lane - width_lane + y
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
                            setColor, 
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
                        setColor, 
                        true,
                        data,
                        type
                    )
                }
            } else { // draw TV
                var y_text_offset = 90;
                drawTV(
                    convertMeterToPixelZoom(data.station) + width_lane + 10, 
                    offset_first - (lane_no/2)*width_lane + 60,
                    true,
                    data
                );
            }
        }
    }
    function getDataBranch() {
        var route_branch_id =  $('#road_name option:selected').val();
        branch_id = route_branch_id;
    }
    function getDataSB() {
        var sb_id =  $('#sb_id option:selected').val();
        sb = sb_id;
    }
    function getDataRMD() {
        var rmb_id =  $('#rmb_id option:selected').val();
        rmb = rmb_id;
    }
	function getDataSegment() {
        var sg_id =  $('#segment option:selected').val();
        var url = '/ajax/frontend/data_segment';
        segement_id = sg_id;
        $.ajax({
            url: url,
            method: 'GET',
            data: {segment_id: sg_id}
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
            getData_TV(segement_id, branch_id, sb, rmb);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }
    
    function mainDraw(data) {
        stage.removeAllChildren();
        var no_lane = data.info.no_lane;
        for (var x in type) {
            if (type[x] == 'TV') {
                var data_type = data[type[x]];
                if (jQuery.isEmptyObject(data_type) == false) {
                    for (var i in data_type) {
                        var start_point = convertMeterToPixel(
                                            convertToMeter(
                                                data_type[i].km_station,
                                                data_type[i].m_station
                                            )
                                        );
                        var y_TV = axis_left_lane0 - (no_lane/2)*height_lane + 30;
                        //console.log(y_TV);
                        drawTV(
                            start_point - 15, 
                            y_TV , 
                            false, 
                            data_type[i].id
                        );
                    }
                }
            } else if (type[x] != 'info')  {
                var data_type = data[type[x]];
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
        lineX(5, width_zoom_zone, space_left - lane_post * width_lane, 'white', 5, 2 );
        text(direction_text[locale][0]+' '+lane_post, 20, space_left - lane_post * width_lane + 10, 'white');// left
        lineX(5, width_zoom_zone, space_right + lane_post * width_lane, 'white', 5, 2);
        text(direction_text[locale][1]+' '+lane_post, 20, space_right + lane_post * width_lane - 20,'white');// right
    }

    /** 
        draw axist Y limit
    */ 
    
    function drawAxisY(width_axis_y) {
        lineY(60, space_right - width_axis_y/2, space_left + width_axis_y/2, 'white', 5, 2);// start
        lineY(width_zoom_zone - 35, space_right - width_axis_y/2, space_left + width_axis_y/2, 'white', 5, 2);// end
    }

    function text(position, x, y, color) {
        var label = new createjs.Text(position, 'solid 5px Arial', color);
        stage_zoom.addChild(label);
        label.x = x;
        label.y = y;
    }

    function drawRectangle(x, y, w, h, color, flash, data , type) {
        var rect = new createjs.Shape();
		rect.graphics.setStrokeStyle(0.5)
                    .beginStroke('black')
                    .beginFill(color)
					.drawRect(x, y, w, h);
		rect.y = mid_height;
        if (flash) {
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
                var category = data.r_category == '' ? 'NaN': data.r_category;
                var duration = data.repair_duration == '' ? 'NaN': data.repair_duration;
                stage_zoom.cursor = "pointer";
                if (e.stageX > 800) {
                    var x_left = e.stageX - 120;
                    var div_msg = 'popup_info_fix';
                    var div_detail = 'detail_fix';
                    var left = x_left - 20;
                }else {
                    var x_left = e.stageX;
                    var div_msg = 'popup_info';
                    var div_detail = 'detail';
                    var left = x_left - 30;
                }
                if (type == 'MH') {
                    $('.' + div_msg).show().css({'margin-left': left, 'margin-top': y - 10});
                    $('#' + div_detail).html('KM' + getInfoZoom(data.from, false).km +'+'+ getInfoZoom(data.from, false).m +' - '+'KM' +getInfoZoom(data.to, false).km +'+'+ getInfoZoom(data.to, false).m + '<br>Repair Category: ' + category + '<br>[' + data.completion_date + ']<br>' + 'Repair Duration: ' + duration +' (month)');
                }
                else if (type == 'RMD') {
                    $('.' + div_msg).show().css({'margin-left': left, 'margin-top': y + 10});
                    $('#' + div_detail).html('KM' + getInfoZoom(data.from, false).km +'+'+ getInfoZoom(data.from, false).m +' - '+'KM' +getInfoZoom(data.to, false).km +'+'+ getInfoZoom(data.to, false).m + '<br>Year of Const: ' + data.year_of_const + '<br>Year of Operation: ' + data.service_start_year);
                }
            })
            rect.addEventListener('mouseout', function() {
                $('.popup_info').hide();
                $('.popup_info_fix').hide();
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
                $('.popup_info').show().css({'margin-left': e.stageX - 30, 'margin-top': y - 25});
                $('#detail').text('Km ' + getInfoZoom(data.station, false).km +'+'+ getInfoZoom(data.station, false).m);
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
        $('.from_zoom').text('Km'+getInfoZoom(limit_left_header, true).km+'+'+getInfoZoom(limit_left_header, true).m +' - '+ 'Km'+getInfoZoom(limit_right_header, true).km+'+'+getInfoZoom(limit_right_header, true).m);
        // lane_pos_number = 0;
        // direction = 3;
        if(limit_left_px != 0 || limit_right_px != 100) {
            getDataZoomZone(convertPixelToMeter(limit_left_px), convertPixelToMeter(limit_right_px)); 
        } else {
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
        var label_info = new createjs.Text(position, '15px solid Arial', color);
        $stage.removeChild(label_info);
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
        var m =  ((px*(boundary_right - boundary_left)) / width_zoom_zone) + boundary_left;
        return Math.round(m);
    }

    function changeLimitInfo(limit_left, limit_right) {
        limit_left_change = limit_left;
        limit_right_change = limit_right;
    }

    function convertMeterToPixelZoom(m) {
        var value = ((width_zoom_zone - 100) * (m-limit_left_change)) / (limit_right_change - limit_left_change);
        return +Math.round(value);
    }

    function convertMeterToPixelPM(m) {
        var value = ((width_zoom_zone - 60) * (m-limit_left_change)) / (limit_right_change - limit_left_change);
        return +Math.round(value);
    }

    function addCheckbox(lane_no) {
        var container = $('#checkbox');
            //container.css('margin-left' , width_zoom_zone + 295 + "px")
            //container.css({'right' : 25 + "px", 'position': 'absolute', 'z-index' : 1} );
            container.css({'left' : 0 + "px", 'position': 'absolute', 'z-index' : 1} );
            container.empty();
        var html = '';
        if (lane_no/2 < lane_pos_number) {
            lane_pos_number = 0;
            direction = 3;
        }
        var padding_top_default = space_left + 10; // lane 0;
        html +=  checkbox(0, 3, padding_top_default); // lane 0 defalult
        for (var c = 1; c <= lane_no/2; c++) {
            var top_left = padding_top_default - c*width_lane;
            var top_right = padding_top_default + c*width_lane;
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
                direction = $(this).data('direction');    
                lane_pos_number = $(this).data('lane_pos_number');
                flash_checkbox = lane_pos_number;
                getDataHistory(segement_id, lane_pos_number, direction);
                getDataPMoS(segement_id, lane_pos_number, direction);
            }
        });
    }

    function checkbox(lane_pos, direct, padding_top) {
        var html = '';
        if (direct == 1) {
            var setAg = 'L-' + lane_pos;
            html += "<label class='checkbox'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i style='position: relative; top: "+ (53 - 28*lane_pos) +"px; left:"+ (40*lane_pos - 40)  +"px;'><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
           
        }
        if (direct == 3) {
            var setAg = '0';
            html += "<label class='checkbox'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i style='position: relative; top: 63px; left: 0px;'><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
        }
        if (direct == 2) {
            var setAg = 'R-' + lane_pos;
            html += "<label class='checkbox'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i style='position: relative; top: "+ (88 - 28*lane_pos) +"px; left:"+ (40*lane_pos - 40)  +"px;'><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
        }
        return html; 
    }

    function checkbox(lane_pos, direct, padding_top) {
        var html = '';
        if (direct == 1) {
            var setAg = 'L-' + lane_pos;
            html += "<label class='checkbox' style='position: relative; top: "+ 27 +"px; left:"+ (40*lane_pos - 39)  +"px;'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
           
        }
        if (direct == 3) {
            var setAg = '0';
            html += "<label class='checkbox' style='position: relative; top: 53px; left: 2px;'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
        }
        if (direct == 2) {
            var setAg = 'R-' + lane_pos;
            html += "<label class='checkbox' style='position: relative; top: "+ 80 +"px; left:"+ (40*lane_pos - 39)  +"px;'><input type='checkbox' data-direction="+ direct +" data-lane_pos_number="+ lane_pos +" name='checkbox' "; 
            if (lane_pos == lane_pos_number && direct == direction) {
            html += "checked />";
            } else {
                html += "/>"
            }
            html += "<i><span style='position: absolute; top: -5px; width: 20px; font-size:11px; left: 15px;'>"+ setAg +"</span></i></label>";
        }
        return html; 
    }

//------end zoom zone -----------

</script>
@include('front-end.pmos.script.history_type')