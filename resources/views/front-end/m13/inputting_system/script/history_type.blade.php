<script>
	var $stage_RMD = new createjs.Stage("RMD_history");
	var $stage_MH = new createjs.Stage("MH_history");
	var $stage_TV = new createjs.Stage("TV_history");
	var axis_center_x = 20;
	var axis_center_y = 90;
	var xaxis_width = width_zoom_zone - 70;
	var yaxis_width = 450;
	var axis_start_x = 5;
	var axis_start_y = 50;
	var labels_x = [];
	var x_axis = 50;
    var y_axis = -30;
    var lane_pos_number = 1; // defalut lane 0
    var direction = 1; // default lane 0

	function getDataHistory(segment_id, lane_pos_number, direction) {
		$.get('ajax/frontend/getDataHistory',
			{
				segment_id: segment_id,
				lane_pos_number: lane_pos_number,
				direction: direction,
				from: convertPixelToMeter(limit_left_px),
				to: convertPixelToMeter(limit_right_px)
			}, function(res) {
				removeElementHistory();
				loadNote();
				for (var type in res) {
					if (type == 'RMD' && RMD_show == true) {
						$('.note_RMD').css('display', 'none')
						drawAxis(res, 'RMD', $stage_RMD);
					} 
					if (type == 'MH' && MH_show == true) {
						$('.note_MH').css('display', 'none')
						drawAxis(res, 'MH', $stage_MH);
					} 
					if(type == 'TV' && TV_show == true) {
						$('.note_TV').css('display', 'none')
						drawAxis(res, 'TV', $stage_TV);
					}
				}
			}, "json"
		);
	}

	function loadNote() {
		$('.note_RMD').css('display', 'block');
		$('.note_MH').css('display', 'block');
		$('.note_TV').css('display', 'block');
	}

	function drawAxis(history_data, type_data , $stage) {
		var coord_xaxis = new createjs.Shape();
		$stage.addChild(coord_xaxis);
		var coord_yaxis = new createjs.Shape();
		$stage.addChild(coord_yaxis);
		var coord_arrow_x = new createjs.Shape();
		$stage.addChild(coord_arrow_x);
		var coord_arrow_y = new createjs.Shape();
		$stage.addChild(coord_arrow_y);
		var coord_xaxis_lines = new createjs.Shape();
		$stage.addChild(coord_xaxis_lines);
		var coord_yaxis_lines = new createjs.Shape();
		$stage.addChild(coord_yaxis_lines);
		// info routine
		drawInfoZoom( 
	            'Km ' + getInfoZoom(limit_left_px, true).km + '+' + getInfoZoom(limit_left_px, true).m, 
	            axis_center_y, 
	            axis_center_x + 10,
	            'black',
	            $stage
	        );

		drawInfoZoom( 
	            'Km ' + getInfoZoom(limit_right_px, true).km + '+' + getInfoZoom(limit_right_px, true).m, 
	            width_zoom_zone - 100, 
	           	axis_center_x + 10,
	            'black',
	            $stage
	        );

		// draw data survey
		var survey_year = (Object.keys(history_data[type_data])).reverse();
		var now_data = "<?php echo  App::isLocale('en') ? 'Latest' : 'Mới nhất' ?>"
			survey_year.unshift(now_data);
			yaxis_width = (survey_year.length)* 90;
		for ( var i = 0; i< survey_year.length; i++) {
			var s = survey_year[i];
			var y_now_data = 50;
			text_axis(x_axis, i, s, $stage, type_data);
			if (i == 0) {  // draw now data
				for (var year in history_data[type_data]) {
					var data = history_data[type_data][year];
					for (var k in data) {
						if (type_data == 'TV') {
							var station = convertMeterToPixelAxis(data[k].station);
							drawTVAxis(
									station,
									y_now_data + 35,
									false,
									data[k],
									$stage
								)
						} else {
							var start = convertMeterToPixelAxis(data[k].from);
							console.log("start" + start);
							var end = convertMeterToPixelAxis(data[k].to);
							drawRectangle_axis(
									start, 
									y_now_data, 
									end-start, 
									10, 
									$stage, 
									color[type_data], 
									data[k], 
									type_data,
									false
								);
							lineY_axis(start, y_axis + 90, y_axis + 190 + (i-1)*y_now_data, $stage);
							lineY_axis(end, y_axis + 90, y_axis + 190 + (i-1)*y_now_data, $stage);
						}
					}
				}
			}
			else { // draw data survey year
				//var x = 125;
				var y = y_now_data + i* 25;
				var data = history_data[type_data][s];
				for (var k in data) {
					if (type_data == 'TV') {
						var station = convertMeterToPixelAxis(data[k].station);
						drawTVAxis(
								station,
								y + i*30 + 30,
								true,
								data[k],
								$stage
							)
					} else {
						var start = convertMeterToPixelAxis(data[k].from);
						var end = convertMeterToPixelAxis(data[k].to) ;
						drawRectangle_axis(
								start, 
								y, 
								end-start, 
								10, 
								$stage, 
								color[type_data], 
								data[k], 
								type_data, 
								true
							);
						lineY_axis(start, y_axis + 90, y_axis + 190 + (i-1)*y_now_data, $stage);
						lineY_axis(end, y_axis + 90, y_axis + 190 + (i-1)*y_now_data, $stage);	
					}
				}	
			}
		}

		var axis_strokewidth = 2;
		coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('#000');
		coord_xaxis.graphics.moveTo(axis_start_x, axis_center_y).lineTo(axis_start_x+xaxis_width, axis_center_y);
		coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('#000');
		coord_xaxis.graphics.moveTo(axis_center_x, axis_start_y).lineTo(axis_center_x, axis_start_y+yaxis_width);

		//draw coordsys arrow for x-axis
		var arrwidth = 5;
		var arrxtnd = 5;
		coord_xaxis.graphics.beginFill('black');
		coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('black');
		coord_xaxis.graphics.moveTo(axis_center_x, axis_start_y-arrwidth/2).lineTo(axis_center_x+arrwidth, axis_start_y+arrwidth+arrxtnd).lineTo(axis_center_x-arrwidth, axis_start_y+arrwidth+arrxtnd).lineTo(axis_center_x, axis_start_y-arrwidth/2);
		coord_xaxis.graphics.endFill();

		// draw coordsys arrow for y-axis
		coord_xaxis.graphics.beginFill('#000');
		coord_xaxis.graphics.beginStroke('#000');
		coord_xaxis.graphics.moveTo(axis_start_x+xaxis_width+arrwidth/2, axis_center_y).lineTo(axis_start_x+xaxis_width-arrwidth-arrxtnd, axis_center_y+arrwidth).lineTo(axis_start_x+xaxis_width-arrwidth-arrxtnd, axis_center_y-arrwidth).lineTo(axis_start_x+xaxis_width+arrwidth/2, axis_center_y);
		coord_xaxis.graphics.endFill();

		coord_xaxis.x =  x_axis;
		coord_xaxis.y =  y_axis;
		$stage.update(); 
	}
	
	function LineX_axis(x1, x2, y, $stage) {
	    var lineX = new createjs.Shape();
	    lineX.graphics
	            .setStrokeStyle(1)
	            .beginStroke('#0000')
	            .setStrokeDash([5, 2], 0)
	            .moveTo(x1, y)
	            .lineTo(x2, y)
	            .endStroke()
	    $stage.addChild(lineX);
	}

    /** 
        draw axist line lineY_axis 
    */   
    function lineY_axis(x, y1, y2, $stage) {
        var lineY = new createjs.Shape();
        lineY.graphics
                .setStrokeStyle(1)
                .beginStroke('#0000')
                .setStrokeDash([10, 5], 0)
                .moveTo(x, y1)
                .lineTo(x, y2)
                .endStroke()
         $stage.addChild(lineY);
    }

	function drawRectangle_axis(x_rec, y_rec, w, h, $stage, color, data, type, flash) {
	    var rect = new createjs.Shape();
		rect.graphics.setStrokeStyle(0.5)
	                .beginStroke('black')
	                .beginFill(color)
					.drawRect(x_rec, y_rec, w, h);
		rect.y = y_rec;
	   
	    if (flash) {
	    	rect.addEventListener('click', function() {
		    	if (type == "MH") {
		    		showModalMHS(data.id);
		    	}else if(type == "RMD") {
		    		showModalRIS(data.id);
		    	}
	    	});
	    	$stage.cursor = "pointer";
	    	rect.addEventListener('mouseover', function(e) {
                if (e.stageX > 800) {
                    var x_left = e.stageX-150;
                }else {
                    var x_left = e.stageX;
                }
                $('.popup_info_history').show().css({'left': x_left +10, 'top': y_rec + 100, 'color': 'black'});
                $('#detail_history').text('Km ' + getInfoZoom(data.from, false).km +'+'+ getInfoZoom(data.from, false).m +' - '+'Km ' +getInfoZoom(data.to, false).km +'+'+ getInfoZoom(data.to, false).m);
            })
            rect.addEventListener('mouseout', function() {
                $('.popup_info_history').hide();
            })
            $stage.enableMouseOver();
	    } 
	    $stage.addChild(rect);
	}

  	function text_axis(x_axis, i, text, $stage, type) {
  		var y_text = 100 + i * 50;
  		labels_x[i] = new createjs.Text('x', '14px Arial', '#333');
		labels_x[i].x = x_axis+10;
	    labels_x[i].y = y_text; 
	    stage.addChild(labels_x[i]);
	    labels_x[i].text = text;
	    labels_x[i].textAlign = 'right';
	    $stage.addChild(labels_x[i]);
  		if (type == 'TV') {
  			var line = new createjs.Shape();
	        line.graphics
	            .setStrokeStyle(1)
	            .beginStroke('black')
	            .setStrokeDash([2, 5], 0)
	            .moveTo(x_axis+20, y_text + 10)
	            .lineTo(xaxis_width + 50, y_text + 10)
	            .endStroke()
			$stage.addChild(line);	
  		}
  	}

  	function convertMeterToPixelAxis(m) {
  		var value = (width_zoom_zone - 100) * (m-convertPixelToMeter(limit_left_px)) / (convertPixelToMeter(limit_right_px) - convertPixelToMeter(limit_left_px));
        return +Math.round(value) + 70 ;
  	}

    function drawTVAxis(x, y,flash, data, $stage) {   
        img = new Image();
        img.src = "/front-end/img/flash.ico";
        r_mc = new createjs.Bitmap(img);
        r_mc.y = y;
        r_mc.x = x;
        r_mc.scaleX = 0.1; 
        r_mc.scaleY = 0.1; 
        $stage.addChild(r_mc);
        if (flash) {
            r_mc.addEventListener('click', function() {
                showModalTVS(data.id);
            });
            r_mc.cursor = "pointer";
            r_mc.addEventListener('mouseover', function(e) {
                $('.popup_info_history').show().css({'left': e.stageX, 'top': y+20, 'color': 'black'});
                $('#detail_history').text('Km ' + getInfoZoom(data.station, false).km +'+'+ getInfoZoom(data.station, false).m);
            })
            r_mc.addEventListener('mouseout', function() {
                $('.popup_info_history').hide();
            })

            $stage.enableMouseOver();
        } 
    }

    function removeElementHistory() {
        $stage_RMD.removeAllChildren();
        $stage_MH.removeAllChildren();
        $stage_TV.removeAllChildren();
        $stage_RMD.update();
        $stage_MH.update();
        $stage_TV.update();
    }
</script>