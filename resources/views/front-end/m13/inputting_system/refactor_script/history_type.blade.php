<template id="history">
	<article class="col-sm-12 sortable-grid ui-sortable">
        <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false" data-widget-editbutton="false"data-widget-fullscreenbutton="false" data-widget-this.colorsbutton="false" data-widget-deletebutton="false" role="widget">
            <header role="heading">
                <span class="widget-icon"> <i class="fa fa-history" aria-hidden="true"></i></span>
                <h2>{{trans('inputting.histoty_survey')}}</h2>
                <ul class="nav nav-tabs pull-right in" id="myTab">
                    <li class="active">
                        <a data-toggle="tab" href="#s1" aria-expanded="true"><i class="fa fa-info-circle" aria-hidden="true"></i><span class="hidden-mobile hidden-tablet"> {{trans('inputting.RMD')}}</span></a>
                    </li>
                    <li class="">
                        <a data-toggle="tab" href="#s2" aria-expanded="false"><i class="fa fa-history" aria-hidden="true"></i><span class="hidden-mobile hidden-tablet"> {{trans('inputting.MH')}}</span></a>
                    </li>
                    <li class="">
                        <a data-toggle="tab" href="#s3" aria-expanded="false"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="hidden-mobile hidden-tablet"> {{trans('inputting.TV')}}</span></a>
                    </li>
                </ul>
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
            </header>

            <div class="no-padding" role="content">
                <div class="widget-body" style="position: relative;">
                    <!-- content -->
                    <div id="myTabContent" class="tab-content" >
                        <div class="tab-pane fade padding-10 no-padding-bottom active in" id="s1">
                            <canvas style="" id="RMD_history" ref="RMD_history"></canvas>
                            <p class="note_RMD">{{trans('inputting.no_data_RMD')}}</p>
                        </div>
                        <!-- end s1 tab pane -->
                        <div class="tab-pane fade" id="s2">
                            <canvas style="" id="MH_history" ref="MH_history"></canvas>
                            <p class="note_MH">{{trans('inputting.no_data_MH')}}</p>
                        </div>
                        <!-- end s2 tab pane -->
                        <div class="tab-pane fade" id="s3">
                            <canvas style="" id="TV_history" ref="TV_history"></canvas>
                            <p class="note_TV">{{trans('inputting.no_data_TV')}}</p>
                        </div>
                        <!-- end s3 tab pane -->
                    </div>
                    <!-- end content -->
                    <div class="popup_info_history" style="position: absolute; width: auto; height: auto; border:1px solid black; display: none;">
                        <div style="" id="detail_history"></div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>

<script type="text/javascript">
	var $stageRMD = new createjs.Stage();
	var $stageMH  = new createjs.Stage();
	var $stageTV  = new createjs.Stage();

	Vue.component("history", {
		template: '#history',
		data: function() {
			return {
				axisCenterX: 20,
				axisCenterY: 90,
				xAxisWidth: '',
				yAxisWidth: 450,
				axisStartX: 5,
				axisStartY: 50,
				labelsX: [],
				xAxis: 50,
			    yAxis: -30,
			    lanePosNumber: 1, // defalut lane 0
			    direction: 1, // default lane 0,
			    boundaryObj: {},
			    limitPxObj: {},
			    segmentId: '',
			    colors: {},
			    widthCanvas: ''
			}
		},

		created: function() {
			this.colors = this.$store.getters.getColor;
		},

		mounted: function() {	
			$stageRMD.canvas = this.$refs.RMD_history;
			$stageMH.canvas  = this.$refs.MH_history;
			$stageTV.canvas  = this.$refs.TV_history;

			var vm = this;
			this.$root.$on('drawHistory', function(data) {
				//vm.getDataHistory(data.segmentId, data.lanePosNumber, data.direction);
			});
			this.$root.$on('sendParamsHistory', function(data) {
				vm.widthCanvas = data.widthCanvas;
				vm.xAxisWidth  = data.widthCanvas - 70;
				vm.limitLeft   = data.limitLeft;
				vm.limitRight  = data.limitRight

				vm.getDataHistory(data.segmentId, data.limitLeft, data.limitRight);
			});
		},

		methods: {
			getDataHistory: function(segmentId, from, to) {
				var url  = 'ajax/frontend/getDataHistory';
				var data = {
					segment_id: segmentId,
					lane_pos_number: this.lanePosNumber,
					direction: this.direction,
					from: from,
					to: to
				}

				var vm = this;
				axios.get(url, {params: data})
				.then(function(res) {
					return res.data;
				})
				.then(function(res){
					console.log("res data history", res);
					vm.clearCache();
					vm.update();
					
					//vm.drawAxis(res, 'RMD', $stageRMD);
					vm.drawAxis(res, 'MH', $stageMH);
					//vm.drawAxis(res, 'TV', $stageTV);
				})
				.catch(function(err) {
					console.log("err", err);
				})
			},

			convertMeterToPixel(m) {
		  		var value = (this.widthCanvas - 100) * (m - this.limitLeft) / 
		  					(this.limitRight - this.limitLeft);
		        return +Math.round(value) + 70 ;
		  	},

			clearCache: function() {
				$stageRMD.removeAllChildren();
		        $stageMH.removeAllChildren();
		        $stageTV.removeAllChildren();
			},

			update: function() {
				$stageRMD.update();
		        $stageMH.update();
		        $stageTV.update();
			},

		  	drawAxis: function(historyData, typeData , $stage) {
				var coord_xaxis 	  = new createjs.Shape();
				var coord_yaxis 	  = new createjs.Shape();
				var coord_arrow_x 	  = new createjs.Shape();
				var coord_arrow_y	  = new createjs.Shape();
				var coord_xaxis_lines = new createjs.Shape();
				var coord_yaxis_lines = new createjs.Shape();

				$stage.addChild(coord_xaxis);
				$stage.addChild(coord_yaxis);
				$stage.addChild(coord_arrow_x);
				$stage.addChild(coord_arrow_y);
				$stage.addChild(coord_xaxis_lines);
				$stage.addChild(coord_yaxis_lines);
				// info routine
				// drawInfoZoom( 
			 //            'Km ' + getInfoZoom(limit_left_px, true).km + '+' + getInfoZoom(limit_left_px, true).m, 
			 //            this.axisCenterY, 
			 //            this.axisCenterX + 10,
			 //            'black',
			 //            $stage
			 //        );

				// drawInfoZoom( 
			 //            'Km ' + getInfoZoom(limit_right_px, true).km + '+' + getInfoZoom(limit_right_px, true).m, 
			 //            width_zoom_zone - 100, 
			 //           	this.axisCenterX + 10,
			 //            'black',
			 //            $stage
			 //        );

				// draw data survey
				var surveyYear = (Object.keys(historyData[typeData])).reverse();
				var now_data = "<?php echo  App::isLocale('en') ? 'Latest' : 'Mới nhất' ?>"
				surveyYear.unshift(now_data);
				this.yAxisWidth = (surveyYear.length) * 90;

				for ( var i = 0; i < surveyYear.length; i ++) {
					var s = surveyYear[i];
					var y_now_data = 50;
					this.drawText(this.Xaxis, i, s, $stage, typeData);
					if (i == 0) {  // draw now data
						
						for (var year in historyData[typeData]) {
							var data = historyData[typeData][year];
							
							for (var k in data) {
								if (typeData == 'TV') {
									var station = this.convertMeterToPixel(data[k].station);
									this.drawTV(
										station,
										y_now_data + 35,
										false,
										data[k],
										$stage
									)

								} else {
									var start = this.convertMeterToPixel(data[k].from);
									var end = this.convertMeterToPixel(data[k].to);
									console.log("start:"+ start + "end:" + end);
									this.drawRectangle(
										start, 
										y_now_data, 
										end - start, 
										10, 
										$stage, 
										this.colors[typeData], 
										data[k], 
										typeData,
										false
									);

									this.drawlineY(start, this.yAxis + 90, this.yAxis + 190 + (i - 1) * y_now_data, $stage);
									this.drawlineY(end, this.yAxis + 90, this.yAxis + 190 + (i - 1) * y_now_data, $stage);
								}
							}
						}
					}
					else { // draw data survey year
						//var x = 125;
						var y = y_now_data + i* 25;
						var data = historyData[typeData][s];
						
						for (var k in data) {
							if (typeData == 'TV') {
								var station = this.convertMeterToPixel(data[k].station);
								this.drawTV(
									station,
									y + i * 30 + 30,
									true,
									data[k],
									$stage
								)

							} else {
								var start = this.convertMeterToPixel(data[k].from);
								var end = this.convertMeterToPixel(data[k].to) ;
								this.drawRectangle(
										start, 
										y, 
										end - start, 
										10, 
										$stage, 
										this.colors[typeData], 
										data[k], 
										typeData, 
										true
									);

								this.drawlineY(start, this.yAxis + 90, this.yAxis + 190 + (i - 1) * y_now_data, $stage);
								this.drawlineY(end, this.yAxis + 90, this.yAxis + 190 + (i - 1) * y_now_data, $stage);	
							}
						}	
					}
				}

				var axis_strokewidth = 2;
				coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('#000');
				coord_xaxis.graphics.moveTo(this.axisStartX, this.axisCenterY).lineTo(this.axisStartX + this.xAxisWidth, this.axisCenterY);
				coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('#000');
				coord_xaxis.graphics.moveTo(this.axisCenterX, this.axisStartY).lineTo(this.axisCenterX, this.axisStartY + this.yAxisWidth);

				//draw coordsys arrow for x-axis
				var arrwidth = 5;
				var arrxtnd = 5;
				coord_xaxis.graphics.beginFill('black');
				coord_xaxis.graphics.setStrokeStyle(axis_strokewidth,'round').beginStroke('black');
				coord_xaxis.graphics.moveTo(this.axisCenterX, this.axisStartY - arrwidth/2).lineTo(this.axisCenterX + arrwidth, this.axisStartY + arrwidth + arrxtnd).lineTo(this.axisCenterX - arrwidth, this.axisStartY + arrwidth + arrxtnd).lineTo(this.axisCenterX, this.axisStartY - arrwidth / 2);
				coord_xaxis.graphics.endFill();

				// draw coordsys arrow for y-axis
				coord_xaxis.graphics.beginFill('#000');
				coord_xaxis.graphics.beginStroke('#000');
				coord_xaxis.graphics.moveTo(this.axisStartX + this.xAxisWidth + arrwidth / 2, this.axisCenterY).lineTo(this.axisStartX + this.xAxisWidth - arrwidth - arrxtnd, this.axisCenterY + arrwidth).lineTo(this.axisStartX + this.xAxisWidth - arrwidth - arrxtnd, this.axisCenterY - arrwidth).lineTo(this.axisStartX + this.xAxisWidth + arrwidth / 2, this.axisCenterY);
				coord_xaxis.graphics.endFill();

				coord_xaxis.x =  this.Xaxis;
				coord_xaxis.y =  this.Yaxis;
				$stage.update(); 
			},

			convertMeterToPixe: function(m) {
		  		var value = (width_zoom_zone - 100) * (m - convertPixelToMeter(limit_left_px)) / 
		  					(convertPixelToMeter(limit_right_px) - convertPixelToMeter(limit_left_px));
		        return +Math.round(value) + 70 ;
		  	},

		  	drawLineX: function (x1, x2, y, $stage) {
			    var lineX = new createjs.Shape();
			    lineX.graphics
			            .setStrokeStyle(1)
			            .beginStroke('#0000')
			            .setStrokeDash([5, 2], 0)
			            .moveTo(x1, y)
			            .lineTo(x2, y)
			            .endStroke()
			    $stage.addChild(lineX);
			},
  
		    drawlineY: function (x, y1, y2, $stage) {
		        var lineY = new createjs.Shape();
		        lineY.graphics
		                .setStrokeStyle(1)
		                .beginStroke('#0000')
		                .setStrokeDash([10, 5], 0)
		                .moveTo(x, y1)
		                .lineTo(x, y2)
		                .endStroke()
		        $stage.addChild(lineY);
		    },

		  	drawText: function(x_axis, i, text, $stage, type) {
		  		var txt_y = 100 + i * 50;
		  		this.labelsX[i] = new createjs.Text('x', '14px Arial', '#333');
				this.labelsX[i].x = x_axis + 10;
			    this.labelsX[i].y = txt_y; 
			    stage.addChild(this.labelsX[i]);

			    this.labelsX[i].text = text;
			    this.labelsX[i].textAlign = 'right';
			    $stage.addChild(this.labelsX[i]);

		  		if (type == 'TV') {
		  			var line = new createjs.Shape();
			        line.graphics
			            .setStrokeStyle(1)
			            .beginStroke('black')
			            .setStrokeDash([2, 5], 0)
			            .moveTo(x_axis + 20, txt_y + 10)
			            .lineTo(this.xAxisWidth + 50, txt_y + 10)
			            .endStroke()
					$stage.addChild(line);	
		  		}
		  	},

			drawTV: function(x, y, event, data, $stage) {
				img = new Image();
		        img.src = "/front-end/img/flash.ico";
		        r_mc = new createjs.Bitmap(img);
		        r_mc.y = y;
		        r_mc.x = x;
		        r_mc.scaleX = 0.1; 
		        r_mc.scaleY = 0.1; 
		        $stage.addChild(r_mc);

		        if (event) {
		            r_mc.addEventListener('click', function() {
		                //showModalTVS(data.id);
		            });
		            r_mc.cursor = "pointer";
		            r_mc.addEventListener('mouseover', function(e) {
		                // $('.popup_info_history').show().css({ 'left': e.stageX, 'top': y + 20, 'color': 'black' });
		                // $('#detail_history').text(	'Km ' 
		                // 							+ getInfoZoom(data.station, false).km 
		            				// 				+ '+' 
		            				// 				+ getInfoZoom(data.station, false).m
		            				// 			);
		            })
		            r_mc.addEventListener('mouseout', function() {
		                //$('.popup_info_history').hide();
		            })

		            $stage.enableMouseOver();
		        }
			},

			drawRectangle: function (x_rec, y_rec, w, h, $stage, colors, data, type, event) {
			    var rect = new createjs.Shape();
				rect.graphics
					.setStrokeStyle(0.5)
			        .beginStroke('black')
			        .beginFill(colors)
					.drawRect(x_rec, y_rec, w, h);
				rect.y = y_rec;
			   	console.log("x_rec:"+ x_rec + "y_rec:" + y_rec);
			    if (event) {
			    	rect.addEventListener('click', function() {
				    	if (type == "MH") {
				    		//showModalMHS(data.id);
				    	} else if(type == "RMD") {
				    		//showModalRIS(data.id);
				    	}
			    	});
			    	$stage.cursor = "pointer";
			    	rect.addEventListener('mouseover', function(e) {
		                var x_left = (e.stageX > 800) ? e.stageX - 150 : e.stageX 
		                // $('.popup_info_history').show().css({ 'left': x_left + 10, 'top': y_rec + 100, 'color': 'black' });
		                // $('#detail_history').text(	'Km ' 
		                // 							+ getInfoZoom(data.from, false).km 
		            				// 				+ '+'
		            				// 				+ getInfoZoom(data.from, false).m 
		            				// 				+ ' - '
		            				// 				+ 'Km ' 
		            				// 				+ getInfoZoom(data.to, false).km 
		            				// 				+ '+'
		            				// 				+ getInfoZoom(data.to, false).m
		                // 						);
		            })
		            rect.addEventListener('mouseout', function() {
		                $('.popup_info_history').hide();
		            })
		            $stage.enableMouseOver();
			    } 
			    $stage.addChild(rect);
			}
		}
	})
</script>		