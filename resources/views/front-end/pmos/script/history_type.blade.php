<script>
	
    var pc_info = new createjs.Stage("PcInfo");
    var rut_ave_info = new createjs.Stage('RutAve_Info');
    var rut_max_info = new createjs.Stage('RutMax_Info');
    var iri_info = new createjs.Stage('IRI_Info');
    var mci_info = new createjs.Stage('MCI_Info');
    var left_tv = document.getElementById("leftChart").getContext('2d');
    var right_tv = document.getElementById("rightChart").getContext('2d');
    //
	var stage_PMOS = new createjs.Stage("PMOS_history");
	var $stage_RMD = new createjs.Stage("RMD_history");
	var $stage_MH = new createjs.Stage("MH_history");
	var $stage_TV = new createjs.Stage("TV_history");
	var axis_center_x = 15;
	var axis_center_y = 90;
	var xaxis_width = width_zoom_zone - 70;
	var yaxis_width = 450;
	var axis_start_x = 5;
	var axis_start_y = 0;
	var labels_x = [];
	var x_axis = 50;
    var y_axis = -30;
    var lane_pos_number = 1; // defalut lane 0
    var direction = 1; // default lane 0
    var colorSet =  ['#3F48CC', '#22B14C', '#FF7F27', '#ED1C24'];
    var width_lane_pmos = 5;
    var width_zoom_zone_pmos = 960;
    var mid_lane,
        mid_history;
    var data_left;
    var data_right;
    var chartLeft,
        chartRight;

    function getDataHistory(segment_id, lane_pos_number, direction) {
        $.get('ajax/frontend/getDataPmosHistory',
            {
                segment_id: segment_id,
                lane_pos_number: lane_pos_number,
                direction: direction,
                from: convertPixelToMeter(limit_left_px),
                to: convertPixelToMeter(limit_right_px)
            }, function(res) {
                removeElementHistory();
                loadNote();
                var lane_no = res.lane_no;
                console.log(res.data);
                $('#MH_history').attr('height', (lane_no + 5)*50);
                mid_history = ($('#MH_history').height())/2;
                console.log(mid_history);
                for (var type in res) {
                    $('.note_MH').css('display', 'none');
                    if (MH_RS == true) {//Repaired Surface 
                        for (var lane_post = 1; lane_post <= lane_no; lane_post++) 
                        {
                            if (lane_no > 1 && lane_post <= lane_no / 2) 
                            {
                                drawAxis(res.data, 'MH', lane_post, lane_no, mid_history, $stage_MH);
                            }
                        }
                    }
                    if (MH_RC == true) {//Repair Classification 
                        for (var lane_post = 1; lane_post <= lane_no; lane_post++) 
                        {
                            if (lane_no > 1 && lane_post <= lane_no / 2) 
                            {
                                drawAxis(res.data, 'MH', lane_post, lane_no, mid_history, $stage_MH, true);
                            }
                        }
                    } 
                }
            }, "json"
        );
    }

    function getData_TV(segment_id, branch_id, sb, rmb) {
        // console.log('branch:' + branch_id);
        $.get('ajax/frontend/getData_TV',
            {
                rmb_id: rmb,
                sb_id: sb,
                branch_id: branch_id,
                segment_id: segment_id,
                from: convertPixelToMeter(limit_left_px),
                to: convertPixelToMeter(limit_right_px)
            }, function(res) {
                // console.log(res);
                var set_color = {
                    left_bg: 'rgba(255, 230, 170, 0.2)', left_bd: 'rgba(255,205,86,1)',
                    right_bg: 'rgba(34, 177, 76, 0.2)', right_bd: 'rgba(34 , 177 , 76, 1)'
                }
                if (res.length != 0) {
                    $('.note_TV').css('display', 'none');
                    $('#rightChart').css('display', 'block');
                    $('#leftChart').css({'display': 'block','margin-bottom': '10px'});
                    createChart(res.left, res.set, left_tv, 'L', set_color['left_bg'], set_color['left_bd']);
                    createChart(res.right, res.set, right_tv, 'R', set_color['right_bg'], set_color['right_bd']);
                }
                else
                {
                    $('.note_TV').css('display', 'block');
                    $('#rightChart').css('display', 'none');
                    $('#leftChart').css('display', 'none');
                }
                
            }, "json"
        );
    }

	function createChart(data, mimax, name, $state, bg, bd)
    {
        var dataSet = [];
        for (var i = 0; i < data.length; i++) {
            dataSet.push({x : data[i].station,y : data[i].total, heavy: data[i].heavy}); 
        }

        var chart = ($state == 'L') ? chartLeft : chartRight;
        
        if (chart) {
            chart.destroy();
        }
        Chart.defaults.global.legend.display = false;
        Chart.defaults.global.elements.rectangle = false;
        name.canvas.width = width_zoom_zone - 30;
        var min = mimax.min;
        var max = mimax.max;

        chart = new Chart(name, {
            type: 'scatter',
            data: {
              datasets: [{
                label: "Test",
                data: dataSet,
                backgroundColor: bg,
                borderColor: bd,
                lineTension: 0,
              }]
             },
             options: {
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var allData = data.datasets[tooltipItem.datasetIndex].data;
                            var stt = allData.length;
                            var from = allData[tooltipItem.index].x;
                            var stringData = from.toString();
                            var total = allData[tooltipItem.index].y;
                            var heavy = allData[tooltipItem.index].heavy;
                            var st_to = Number(stringData.substr(-3));
                            var st_from = (from - st_to)/1000;
                            if (tooltipItem.index == 0 || tooltipItem.index == (stt - 1)) 
                            {
                                return 'KM'+ st_from + '+' + st_to ;
                            }
                            else
                            {
                                return 'KM'+ st_from + '+' + st_to + " - TOTAL: " + total + " - HEAVY: " + heavy;
                            }
                        }
                    }
                },
                responsive: true,
                    scales: {
                        xAxes: [{
                          type: 'linear',
                          display: false,
                          ticks: {min: min, max: max}

                        }],
                        yAxes: [{
                          display: true,
                          ticks: {min: 0}
                        }]
                    }
                }
            });
        if ($state == 'L') {
            chartLeft = chart;
        } else {
            chartRight = chart;
        }
    }

	function getDataPMoS(segment_id, lane_pos_number, direction) {
		$.get('ajax/frontend/getDataPmos',
			{
				segment_id: segment_id,
				lane_pos_number: lane_pos_number,
				direction: direction,
				from: convertPixelToMeter(limit_left_px),
				to: convertPixelToMeter(limit_right_px)
			}, function(res) {
			removeElementPMos();
    		var lane_no = res.lane_no;
    		$('#PcInfo,#RutAve_Info,#RutMax_Info,#IRI_Info,#MCI_Info').attr('width', width_zoom_zone);
            $('#PcInfo,#RutAve_Info,#RutMax_Info,#IRI_Info,#MCI_Info').attr('height', (lane_no + 1)*30);
            mid_lane = ($('#PcInfo').height())/2;
	        data_left = ($('#PcInfo').height())/2 - width_lane_pmos/2 - 12;
	        data_right = data_left + width_lane_pmos + 22;
	        var width_axis_y = (lane_no + 1) * width_y;
            for ( var lane_post = 1; lane_post <= lane_no; lane_post++ ) {
                if ( lane_no > 1 && lane_post <= lane_no / 2  ) 
                {
                	drawLaneText(lane_post, lane_no, pc_info);
                	drawLaneText(lane_post, lane_no, rut_ave_info);
                	drawLaneText(lane_post, lane_no, rut_max_info);
                	drawLaneText(lane_post, lane_no, iri_info);
                	drawLaneText(lane_post, lane_no, mci_info);
                    drawShapes(res['data'], lane_post, lane_no);
                    drawLane_Singer(pc_info);
                    drawLane_Singer(rut_ave_info);
                    drawLane_Singer(rut_max_info);
                    drawLane_Singer(iri_info);
                    drawLane_Singer(mci_info);
                    drawAxisYY(width_axis_y, pc_info); 
                    drawAxisYY(width_axis_y, rut_ave_info); 
                    drawAxisYY(width_axis_y, rut_max_info); 
                    drawAxisYY(width_axis_y, iri_info); 
                    drawAxisYY(width_axis_y, mci_info);
                } 
                else if (lane_no == 1) 
                {

                }
            }
             
            updateElementPMos();
			}, "json"
		);
	}
	function drawAxisYY(width_axis_y, $state) {
        lineYY($state, 55, data_right - width_axis_y/2, data_left + width_axis_y/2, '#cccccc', 1, 0);// start
        //lineYY($state, width_zoom_zone - 35, data_right - width_axis_y/2, data_left + width_axis_y/2, 'black', 5, 2);// end
    }
    function lineYY($state, x, y1, y2, color, w, h) {
        var line = new createjs.Shape();
        line.graphics
                .setStrokeStyle(1)
                .beginStroke(color)
                .setStrokeDash([w, h], 0)
                .moveTo(x, y1)
                .lineTo(x, y2)
                .endStroke()
        $state.addChild(line);
    }
	function drawShapes(response, lane_post, lane_no){
	    for (i=0;i<response.length;i++){
	    	if (response[i].m_from == 0 ) {
    			var data_from = response[i].km_from + '000';
    			var data_to = response[i].km_to + response[i].m_to;
    		}
    		else if(response[i].m_to == 1000)
    		{
    			var data_from = response[i].km_from + response[i].m_from;
    			var data_to = response[i].km_to + '999.999';
    		}
    		else {
    			var data_from = response[i].km_from + response[i].m_from;
    			var data_to = response[i].km_to + response[i].m_to;
    		}	
    		var height = 20;
        	var data = response[i];
        	//Draw left
            if (data.lane_pos_no == lane_post) {
                if (data.direction == 1) {
    				var start_point = convertMeterToPixelPM(data_from);
        			var end_point = convertMeterToPixelPM(data_to);
		    		var x = convertMeterToPixelPM(data_from) + 60;
                	var y = data_left - 25* data.lane_pos_no;
			        drawDataPMoS(
			        	data,
			        	x,
			        	y,
			        	end_point - start_point,
			        	height,
			        	0
			        );
                } 
                //Draw right
                else if (data.direction == 2) {  
    				var start_point = convertMeterToPixelPM(data_from);
        			var end_point = convertMeterToPixelPM(data_to);
		    		var x = convertMeterToPixelPM(data_from) + 60;
		    		var y = data_right + 25*data.lane_pos_no - 18;
			        drawDataPMoS(
			        	data,
			        	x,
			        	y,
			        	end_point - start_point,
			        	height,
			        	35
			        );
                }
                else if (data.direction == 3) {  //singer
                    var start_point = convertMeterToPixelPM(data_from);
                    var end_point = convertMeterToPixelPM(data_to);
                    var x = convertMeterToPixelPM(data_from) + 60;
                    var y = data_left + 5;
                    drawDataPMoS(
                        data,
                        x,
                        y,
                        end_point - start_point,
                        height,
                        35
                    );
                }
            } 
	    }
    }

	function drawLaneText(lane_post, lane_no, $state) {
        var locale = "<?php echo App::isLocale('en') ? 'en' : 'vn';  ?>";
        textLane(direction_text[locale][0]+'-'+lane_post, 20, data_left - 25* lane_post + 2, 'white', $state);// left
        textLane(0, 20, data_left + 7, 'white', $state);// left
        textLane(direction_text[locale][1]+'-'+lane_post, 20, data_right + 25 * lane_post - 16 ,'white', $state);// right
    }

    function textLane(position, x, y, color, $state) {
        var txt = new createjs.Text(position, "12px Arial", "#000");
        txt.x = x;
        txt.y = y;
        $state.addChild(txt);
    }

	function drawLane_Singer($state) {
        lineX_PM($state, 0, width_zoom_zone, data_left, '#cccccc', 10, 0);
        lineX_PM($state, 0, width_zoom_zone, data_left + 30, '#cccccc', 10, 0);
    }
    
    function lineX_PM($state, x1, x2, y, color, w, h) {
        var line = new createjs.Shape();
        line.graphics
                .setStrokeStyle(1)
                .beginStroke(color)
                .setStrokeDash([w, h], 0)
                .moveTo(x1, y)
                .lineTo(x2, y)
                .endStroke()
        $state.addChild(line);
    }
	
	function drawDataPMoS( data , x , y , width , height, x_rec ){
	 	// Crack
        var crack = new createjs.Shape();
        
        //console.log(data);
        if (data.cracking >= 0 && data.cracking < 10) 
        {
            crack.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[0]).drawRect(x,y,width,height);
        }
        else if (data.cracking >= 10 && data.cracking < 20) 
        {
            crack.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[1]).drawRect(x,y,width,height);
        }
        else if (data.cracking >= 20 && data.cracking < 40) 
        {
            crack.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[2]).drawRect(x,y,width,height);
        }
        else if (data.cracking >= 40 ) 
        {
            crack.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[3]).drawRect(x,y,width,height);
        }
        //crack.y = mid_data;
        pc_info.addChild(crack);

        getMessage(data, y,data.cracking , crack, pc_info, 'crack');
       // Rut Average
       var rutAverage = new createjs.Shape();
        //console.log(data);
        if (data.rutting_ave >= 0 && data.rutting_ave < 20) 
        {
            rutAverage.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[0]).drawRect(x,y,width,height);
        }
        else if (data.rutting_ave >= 20 && data.rutting_ave < 30) 
        {
            rutAverage.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[1]).drawRect(x,y,width,height);
        }
        else if (data.rutting_ave >= 30 && data.rutting_ave < 50) 
        {
            rutAverage.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill("orange").drawRect(x,y,width,height);
        }
        else if (data.rutting_ave >= 50 ) 
        {
            rutAverage.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill("red").drawRect(x,y,width,height);
        }
        //rutAverage.y = mid_data;
        rut_ave_info.addChild(rutAverage);
        
        getMessage(data, y, data.rutting_ave, rutAverage, rut_ave_info, 'ave');
        // Rut Max
        var rutMax = new createjs.Shape();
        //console.log(data);
        if (data.rutting_max >= 0 && data.rutting_max < 20) 
        {
            rutMax.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[0]).drawRect(x,y,width,height);
        }
        else if (data.rutting_max >= 20 && data.rutting_max < 30) 
        {
            rutMax.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[1]).drawRect(x,y,width,height);
        }
        else if (data.rutting_max >= 30 && data.rutting_max < 50) 
        {
            rutMax.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[2]).drawRect(x,y,width,height);
        }
        else if (data.rutting_max >= 50 ) 
        {
            rutMax.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[3]).drawRect(x,y,width,height);
        }
        rut_max_info.addChild(rutMax);
        getMessage(data, y, data.rutting_max, rutMax, rut_max_info, 'max');
        //IRI
        var iri = new createjs.Shape();
        //console.log(data);
        if (data.IRI >= 0 && data.IRI < 4) 
        {
            iri.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[0]).drawRect(x,y,width,height);
        }
        else if (data.IRI >= 4 && data.IRI < 6) 
        {
            iri.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[1]).drawRect(x,y,width,height);
        }
        else if (data.IRI >= 6 && data.IRI < 10) 
        {
            iri.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[2]).drawRect(x,y,width,height);
        }
        else if (data.IRI >= 10 ) 
        {
            iri.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[3]).drawRect(x,y,width,height);
        }
        
        iri_info.addChild(iri);
        getMessage(data, y, data.IRI, iri, iri_info, 'iri');
        //MCI
        var mci = new createjs.Shape();
        //console.log(data);
        if (data.MCI > 5) {
            mci.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[0]).drawRect(x,y,width,height);
        }
        else if (data.MCI > 4 && data.MCI <= 5) {
            mci.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[1]).drawRect(x,y,width,height);
        }
        else if (data.MCI > 3 && data.MCI <= 4) {
            mci.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[2]).drawRect(x,y,width,height);
        }
        else if (data.MCI <= 3 ) {
            mci.graphics.setStrokeStyle(0.5).beginStroke('black').beginFill(colorSet[3]).drawRect(x,y,width,height);
        }
        mci_info.addChild(mci);
        getMessage(data, y, data.MCI, mci, mci_info, 'mci');  
    }

    function getAlign(data)
    {
    	if(data[0].direction == 2 && data[0].lane_pos_no == 1) {
    		$(".laneCheck").replaceWith("<span class='laneCheck'>R-1</span>");
    	}
    	else if (data[0].direction == 2 && data[0].lane_pos_no == 2) {
    		$(".laneCheck").replaceWith("<span class='laneCheck'>R-2</span>");
    	}
    	else if (data[0].direction == 1 && data[0].lane_pos_no == 1) {
    		$(".laneCheck").replaceWith("<span class='laneCheck'>L-1</span>");
    	}
    	else if (data[0].direction == 1 && data[0].lane_pos_no == 2) {
    		$(".laneCheck").replaceWith("<span class='laneCheck'>L-2</span>");
    	}
    	else
    	{
    		$(".laneCheck").replaceWith("<span class='laneCheck'>0</span>");
    	}	
    } 

    function getMessage(data, y, detail, $state, $state_get, msg)
	{
		$state_get.cursor = "pointer";
		$state.addEventListener("mouseover", function(e) {
        	if (e.stageX > 800) 
        	{
                var x_left = e.stageX - 100;
                var div_msg = '.popup_info_' + msg + '_fix';
                var div_detail = '#detail_' + msg + '_fix';
            }
            else 
            {
                var x_left = e.stageX;
                var div_msg = '.popup_info_' + msg;
                var div_detail = '#detail_' + msg;
            }
            $(div_msg).show().css({'left': x_left - 33, 'top': y - 65, 'color': 'white'});
            $(div_detail).html('KM' + data.km_from + '+' + data.m_from + ' - KM' + data.km_to + '+' + data.m_to + '<br>' + detail + '<br>[' + data.condition_month + '/' + data.condition_year +']');
        })

        $state.addEventListener("mouseout", function(event) { 
        	$('.popup_info_' + msg).hide();
        	$('.popup_info_' + msg + '_fix').hide();
        })
        $state_get.enableMouseOver();
	}

	function loadNote() {
		$('.note_MH').css('display', 'block');
	}

	function add_text(x_axis, i, text, $stage, type) {
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

	function drawAxis(history_data, type_data , lane_post, lane_no, mid_history, $stage, type_mh = false) {
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
	
		// draw data survey
		var survey_year = (Object.keys(history_data[type_data])).reverse();
			yaxis_width = (survey_year.length)* 120;
		for ( var i = 0; i< survey_year.length; i++) {
			var s = survey_year[i];
			var y_now_data = 5*lane_post;
			text_axis(x_axis, i, s, $stage, type_data);
			drawAxisX_History($stage, i);
			//var x = 125;
			var y = y_now_data + i* 50 + 30;
			var data = history_data[type_data][s];
            console.log(data);
			for (var k in data) 
            {
				var start = convertMeterToPixelAxis(data[k].from);
				if (data[k].lat == null) 
                {
					var end = convertMeterToPixelAxis(data[k].to);
				}
				else
				{
					var end = convertMeterToPixelAxis(data[k].latest);
				}
                if (type_mh) {
                    if (data[k].classification == 1) {
                        setColor = color[type_data + '_1'];
                    }
                    else if (data[k].classification == 2) 
                    {
                        setColor = color[type_data +'_2'];
                    }
                    else if (data[k].classification == 3) 
                    {
                        setColor = color[type_data +'_3'];
                    }
                    else if(data[k].classification == 4)
                    {
                        setColor = color[type_data +'_4'];
                    } 
                }
				else 
                {
                    if (data[k].pavement_type == 34) {
                    setColor = color[type_data];
                    }
                    else if (data[k].pavement_type == 6) 
                    {
                        setColor = color[type_data +'1'];
                    }
                    else if (data[k].pavement_type == 5) 
                    {
                        setColor = color[type_data +'2'];
                    }
                    else
                    {
                        type_data == 'MH' ? setColor = color['MHOther'] : setColor = color['RMDOther'];
                    }     
                }
                if (data[k].lane_pos_number == lane_post) {
                    if (data[k].direction == 1) { 
                        drawRectangle_axis(
                                start, 
                                y - lane_post*17, 
                                end-start, 
                                20, 
                                $stage, 
                                setColor, 
                                data[k], 
                                type_data, 
                                true,
                                i
                            );
                    }
                }
                if (data[k].lane_pos_number == lane_post) {
                    if (data[k].direction == 2) { 
                        drawRectangle_axis(
                                start, 
                                y - 10 + 7*lane_post, 
                                end-start, 
                                20, 
                                $stage, 
                                setColor, 
                                data[k], 
                                type_data, 
                                true,
                                i
                            );
                    }
                }	
			}	
		}

		var axis_strokewidth = 2;
		coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('#cccccc');
		coord_xaxis.graphics.moveTo(axis_center_x, axis_start_y).lineTo(axis_center_x, axis_start_y+yaxis_width);


		coord_xaxis.x =  x_axis;
		coord_xaxis.y =  y_axis;
		$stage.update(); 
	}
	function drawAxisX_History($stage, i) {
		lineX_PM($stage_MH, 10, width_zoom_zone, 110 + i*110, '#cccccc', 10, 0);
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

	function drawRectangle_axis(x_rec, y_rec, w, h, $stage, color, data, type, flash, msg) {
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
                    var x_left = e.stageX-120;
                }else {
                    var x_left = e.stageX;
                }
                var category = data.r_category == '' ? 'NaN': data.r_category;
                var duration = data.repair_duration == '' ? 'NaN': data.repair_duration;
                $('.popup_info_mh').show().css({'left': x_left - 30, 'top': y_rec - 80 + (msg*15), 'color': 'black'});
                $('#detail_mh').html('KM' + getInfoZoom(data.from, false).km +'+'+ getInfoZoom(data.from, false).m +' - '+'KM' +getInfoZoom(data.to, false).km +'+'+ getInfoZoom(data.to, false).m + '<br>Repair Category: ' + category + '<br>[' + data.completion_date + ']<br>' + 'Repair Duration: ' + duration +' (month)');
            })
            rect.addEventListener('mouseout', function() {
                $('.popup_info_mh').hide();
            })
            $stage.enableMouseOver();
	    } 
	    $stage.addChild(rect);
	}

  	function text_axis(x_axis, i, text, $stage, type) {
  		var y_text = 40 + i * 90;
  		labels_x[i] = new createjs.Text('x', '14px Arial', '#333');
		labels_x[i].x = x_axis;
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
    function removeElementPMos()
    {
    	pc_info.removeAllChildren();
		rut_ave_info.removeAllChildren();
		rut_max_info.removeAllChildren();
		iri_info.removeAllChildren();
		mci_info.removeAllChildren();
    }
    function updateElementPMos()
    {
    	pc_info.update();
        rut_ave_info.update();
	    rut_max_info.update();
	    iri_info.update();
	    mci_info.update();
    }
</script>