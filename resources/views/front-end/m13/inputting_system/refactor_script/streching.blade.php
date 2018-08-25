<template id="streching">
	<article class="col-xs-12" id="col-full">
        @box_open(trans('back_end.zoom_zone'))
        <div>
            <div class="widget-body" id="widget-body" style="padding-bottom: 0px !important;">
	            <div ref="containerCanvas">
	                <div class="info">
	                    <div id="km_from" class="col-xs-4" style="text-align: left;">{{trans('inputting.km_from')}}</div>
	                    <div id="km_to" class="col-xs-offset-4 col-xs-4" style="text-align:right; padding-right:12px;">{{trans('inputting.km_to')}}</div>
	                </div>
	                <canvas style="background:white; border-right: 1px dashed blue; border-left: 1px dashed blue;" ref="streching_area" id="streching_area"></canvas>
	                <div ref="zoom" id="zoom"></div>
	            </div>
            </div>
            <zoom-zone :width-canvas="widthCanvas"></zoom-zone>
        </div>
        @box_close()
    </article>
</template>

<template id="zoomZoneCmp">
	<div class="widget-body" style="padding-top: 0px;">
        <div>
            <h3>{{trans('inputting.cross_sectional_profile:')}}
            	<span class="from_zoom" style="padding-left: 300px;"></span> 
            </h3>
        </div>
        <div style="float: left; padding-right: 5px;">
            <canvas style="" ref="zoom_zone" id="zoom_zone" ></canvas>
        </div>
        <div id="checkbox" class="smart-form" style="position: relative !important;"></div>
        <div class="popup_info" style="position: relative; width: auto; height: auto; background: #F0FFF0; display: none;">
            <div style="position: absolute;top: 0px;" id="detail"></div>
        </div>
    </div>
    <div class="col-xs-offset-4 col-xs-8">
        <div class="row">
            <div class="smart-form" style="margin-top: 8px;">
                <section>
                    <div class="inline-group" >
                        <label class="checkbox">
                            <input type="checkbox" id="RMD_hide"  checked="checked">
                            <i></i><div class="note"  style="background: blue; color: blue"><span style="width: 176px;">{{trans('inputting.RMD')}}</span></div>
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" id="MH_hide" checked="checked">
                            <i></i><div class="note"  style="background: red; color: red;"><span>{{trans('inputting.MH')}}</span></div>
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" id="TV_hide" checked="checked">
                            <i></i><div class="note"  style="color: green; margin-left: -10px;"><img src="/front-end/img/flash.ico" alt="TV"><span style="position: absolute; top: 5px;">{{trans('inputting.TV')}}</span></div>
                        </label>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">

	var stage = new createjs.Stage();
	var stageZoom = new createjs.Stage();

	Vue.component("streching", {
		template:'#streching',
		data: function() {
			return {
				drag: null,
				resize: null,
				widthCanvas:'',
				boundaryLeft: '',
				boundaryRight: '',
				colors: '',
				type: ['RMD', 'MH', 'TV'],
				directionText: { en: ['L', 'R'], vn: ['T', 'P'] },
				noLane: '',
				heightLane: 5,
				leftMidPoint: 5,
				rightMidPoint: 10 ,
				midHeightLane: '',
				widthResize: 0,
				limitLeftResize: '',
				limitRightResize: ''

			}
		},
		created: function() {
			this.colors = this.$store.getters.getColor;
		},
		mounted: function() {
			var vm = this;
			stage.canvas = this.$refs.streching_area;

			this.midHeightLane = this.$refs.streching_area.clientHeight / 2;
			this.widthCanvas = this.$refs.containerCanvas.clientWidth - 13;
			this.$store.commit('setWidthCanvas', {widthCanvas: this.widthCanvas});

			$('#streching_area').attr('width', this.widthCanvas);
			$('#zoom_zone').attr('width', this.widthCanvas);
			
			var optionDrag = {
				axis: "x",
		        containment: "#streching_area",
		        cursor: "crosshair",
		        drag: function( event, ui ) {
		            
		        },
		        stop: function( event, ui ) {
		        	var limitLeftPx = ui.position.left - 1;
		           	var limitRightPx = (vm.widthResize) ? limitLeftPx + vm.widthResize 
		           										: limitLeftPx + 100;
		           	var params = {
		           		limitLeft: vm.convertPixelToMeter(limitLeftPx),
		           		limitRight: vm.convertPixelToMeter(limitRightPx)
		           	}
		           	vm.$root.$emit('drag', params);
		        }
			}
			var optionResize = {
				handles: "e, w",
		        minWidth: 10,
		        containment: "#streching_area",
		        resize: function( event, ui ) {
		           
		        },
		        stop: function(e, ui) {
		            vm.widthResize = ui.size.width;
		            var params = {
		           		limitLeft: vm.convertPixelToMeter(ui.position.left),
		           		limitRight: vm.convertPixelToMeter(ui.size.width + ui.position.left)
		           	}
		           	vm.$root.$emit('drag', params);
		        }
			}

			this.drag = $('#zoom').draggable(optionDrag);
			this.resize = $('#zoom').resizable(optionResize);

			this.$root.$on('drawCanvas', function(data) {
				vm.$store.commit('setSegmentId', data);
				vm.getDataSegment(data.segmentId);
			});
		
		},
		methods: {
			getDataSegment: function(segmentId) {
				var vm = this;
				var url = '/ajax/frontend/data_segment';
				var data = {segment_id: segmentId}
				
				axios.get(url, { 
					params: data 
				})
				.then(function(res) {
					vm.changeBoundaryInfo(
							res.data.info.km_from,
							res.data.info.m_from, 
		                    res.data.info.km_to,
		                    res.data.info.m_to
						);
					vm.mainDraw(res.data);
				})
				.catch(function(err) {
					console.log(err);
				})
			},

			mainDraw: function(data) {
				var vm = this;
				stage.removeAllChildren();
				this.noLane = data.info.no_lane;

				this.type.forEach(function(type) {
					var dataType = data[type];
					if (type === 'TV') {
						if (dataType.length === 0) { return; }

						dataType.forEach(function(data) {
							var x = vm.convertMeterToPixel(vm.convertToMeter(data.km_station, data.m_station));
							var y = vm.leftMidPoint - (vm.noLane / 2) * vm.heightLane + 30;
							
							vm.drawTV(x - 15, y);
						});
						// draw MH and RMD
					} else { 
						if (dataType.length === 0) { return; }

						dataType.forEach(function(data) {
							vm.drawMhAndRmd(data, vm.colors[type]);
						});
					}
				})
				stage.update();
			},

			drawMhAndRmd: function(data, colors) {
				var startPoint = this.convertMeterToPixel(this.convertToMeter(data.km_from, data.m_from));
				var endPoint = this.convertMeterToPixel(this.convertToMeter(data.km_to, data.m_to));
				
				for (var posNumber = 0; posNumber <= this.noLane / 2; posNumber ++) { 
					if (data.lane_pos_number !== posNumber) { continue; }
					if (data.direction == 1) { //left
						var y = this.leftMidPoint - posNumber - (posNumber - 1) * this.heightLane - 10;
						this.drawRectangle(startPoint, y, endPoint - startPoint, this.heightLane, colors); 

					} else if (data.direction == 2) { //right
						var y = this.rightMidPoint + posNumber + (posNumber - 1) * this.heightLane + 5;
						this.drawRectangle(startPoint, y, endPoint - startPoint, this.heightLane, colors);

					} else if(data.direction == 3 && data.lane_pos_number === 0)  {// singer
						var y = this.leftMidPoint;
						this.drawRectangle(startPoint, y, endPoint - startPoint, this.heightLane, colors);
					}
				}	
			},

			drawTV: function(x, y) {
				var img = new Image();
		        img.src = "/front-end/img/flash.ico";
		        r_mc = new createjs.Bitmap(img);
		        r_mc.y = y;
		        r_mc.x = x;
		        r_mc.scaleX = 0.1; 
		        r_mc.scaleY = 0.1; 		      
		        stage.addChild(r_mc);
			},

			drawRectangle: function(x, y, w, h, colors) {
				var rect = new createjs.Shape();
				rect.graphics.setStrokeStyle(0.5)
		                    .beginStroke('black')
		                    .beginFill(colors)
							.drawRect(x, y, w, h);
				rect.y = this.midHeightLane;
				stage.addChild(rect);
			},

			convertToMeter: function(km, m) {
				return +km*1000 + (+m);
			},

			convertPixelToMeter(px) {
		        var m =  ((px*(this.boundaryRight - this.boundaryLeft)) / this.widthCanvas) + this.boundaryLeft;
		        return Math.round(m);
		    },

			convertMeterToPixel: function(m) {
				var value = ((this.widthCanvas - 2) * (m - this.boundaryLeft)) / (this.boundaryRight - this.boundaryLeft);
        		return +Math.round(value);
			},

			changeBoundaryInfo: function(km_from, m_from, km_to, m_to) {
		        this.boundaryLeft = this.convertToMeter(km_from, m_from);
		        this.boundaryRight = this.convertToMeter(km_to, m_to);
		    }

		}
	})

	Vue.component("zoomZone", {
		template: '#zoomZoneCmp',
		data: function() {
			return {
				widthLane: 40,
				widthY: 55,
				offsetFirst: '',
				offsetSecond: '',
				spaceLeft: 140,
				spaceRight: '',
				widthZoomZone: '',
				directionText : {
					en: ['L', 'R'], 
					vn: ['T', 'P']
				},
				widthAxisY: '',
				laneNo: '',
				colors: ''
			}
		},

		created: function() {
			this.colors = this.$store.getters.getColor;
		},

		mounted: function() {
			var vm = this;
			stageZoom.canvas = this.$refs.zoom_zone;


			this.widthZoomZone = this.$store.getters.getWidthCanvas;
			
			this.$root.$on('drag', function(data) {
				vm.getDataZoomZone(data.limitLeftPx, data.limitRightPx);
			});
		},
		methods: {
			getDataZoomZone: function(limitLeft, limitRight) {
				var vm = this;
				var url = "ajax/frontend/getDataZoomZone";
				var data = {
					segment_id: this.$store.getters.getSegmentId,
	                limit_left: limitLeft,
	                limit_right: limitRight,
				};

				axios.get(url, {params: data})
				.then(function(res) { 
					return res.data;
				})
				.then(function(res) {

					$('#zoom_zone').attr('height', (vm.laneNo + 1) * 50 + 20);

					vm.laneNo = res.segment_info.lane_no;
					
					vm.spaceLeft  = $('#zoom_zone').height() / 2 - vm.widthLane / 2;
		            vm.spaceRight = vm.spaceLeft + vm.widthLane;
		           
		            vm.offsetFirst  = ($('#zoom_zone').height() / 2) - 5 * vm.widthLane / 2 + 7;
		            vm.offsetSecond = vm.offsetFirst + vm.widthLane;
		            
		            vm.widthAxisY = (vm.laneNo + 1) * vm.widthY;


		            for ( var lane_post = 1; lane_post <= vm.laneNo; lane_post++ ) {
		                //draw axist
		                if ( vm.laneNo > 1 && lane_post <= vm.laneNo / 2  ) {
		                    vm.drawAxisX(lane_post, vm.laneNo);

		                    vm.drawZoomType(res, 'RMD', lane_post, vm.laneNo);

		                    // if (RMD_show == true) {
		                    //     drawZoomType(res, 'RMD', lane_post, laneNo);
		                    // }
		                    // if (MH_show == true) {
		                    //     drawZoomType(res, 'MH', lane_post, laneNo);
		                    // }
		                    // if (TV_show == true) {
		                    //     drawZoomType(res, 'TV', lane_post, laneNo);
		                    // }
		                } else if (vm.laneNo == 1) {

		                	vm.drawZoomType(res, 'RMD', 1, 1);

		                    // if (RMD_show == true) {
		                    //     drawZoomType(res, 'RMD', 1, 1);
		                    // }
		                    // if (MH_show == true) {
		                    //     drawZoomType(res, 'MH', 1, 1);
		                    // }
		                    // if (TV_show == true) {
		                    //     drawZoomType(res, 'TV', 1, 1);
		                    // }
		                }
		            }

		            vm.drawAxisY(vm.widthAxisY); // fixed axist Y
		            vm.drawSingerLane();// draw lane singer
		            stageZoom.update();
		            
				})
				.catch(function(err) {
					console.log("err", err);
				});
			},

			drawZoomType: function(dataType, type, lanePost, laneNum) {
				console.log("dataType", dataType[type]);

				for (var k in dataType[type]) {
		            
					var data = dataType[type][k];

		            var x = this.convertMeterToPixel(data.from) + 62 ; // start from
		            var y = ((this.widthLane - 5) - this.convertWidthLane(data.lane_width)) / 2;

		            var startPoint = this.convertMeterToPixel(data.from);
		            var endPoint = this.convertMeterToPixel(data.to);
		            
		            if (type !== 'TV') { // draw MH and RMD
		            	if (data.lane_pos_number === 0 && data.direction === 3) {
		            		var yRect = this.offsetFirst  + y;
		                    
		                    if (type === "MH") { // set distance MH
		                        if (data.direction_running === 0) {
		                            yRect -= this.convertWidthLane(data.distance);
		                       
		                        } else if (data.direction_running === 1) {
		                            yRect += this.convertWidthLane(data.distance);
		                        }    
		                    }
		                    this.drawRectangle(
		                        x,
		                        yRect, 
		                        endPoint - startPoint,
		                        this.convertWidthLane(data.lane_width) ,
		                        this.colors[type], 
		                    )

		            	} else if (data.lane_pos_number === lanePost) {
		                    if (data.direction === 1) { // left
		                        var yRect = this.offsetFirst - lanePost * this.widthLane + y;
		                       
		                        if (type === 'MH') { // set distance MH
		                            if (data.direction_running === 0) {
		                                yRect -= this.convertWidthLane(data.distance);
		                            
		                            } else if (data.direction_running === 1) {
		                                yRect += this.convertWidthLane(data.distance);
		                            }    
		                        }
		                        this.drawRectangle(
		                            x,
		                            yRect, 
		                            endPoint - startPoint,
		                            this.convertWidthLane(data.lane_width) ,
		                            this.colors[type], 
		                        )

		                    } else if (data.direction === 2) {  //right
		                        var yRect = this.offsetSecond + lanePost * this.widthLane - this.widthLane + y;
		                        
		                        if (type === 'MH') { // set distance MH
		                            if (data.direction_running === 0) { 
		                                yRect -= this.convertWidthLane(data.distance);
		                            
		                            } else if (data.direction_running === 1) {
		                                yRect += this.convertWidthLane(data.distance);
		                            }    
		                        }
		                        this.drawRectangle(
		                            x,
		                            yRect, 
		                            endPoint - startPoint,
		                            this.convertWidthLane(data.lane_width) ,
		                            this.colors[type], 
		                        )
		                    }

		                }

		            } else { // draw TV
		                this.drawTV(
		                    this.convertMeterToPixel(data.station) + this.widthLane + 10, 
		                    this.offsetFirst - (laneNum / 2) * this.widthLane + 60,
		                );
		            }
		        };
		    },

		    convertWidthLane: function(m) {
        		return +Math.round((35 / 5) * m); //5m = 40px;
		    },

			convertMeterToPixel: function(m) {
		        var value = (this.widthZoomZone - 100) * (m - this.limitLeftChange) / 
		        			(this.limitRightChange - this.limitLeftChange);
		        return +Math.round(value);
		    },
		   
		    drawAxisY: function() {
		    	this.lineY(  // start
			    	60, 
			    	this.spaceRight - this.widthAxisY / 2, 
			    	this.spaceLeft + this.widthAxisY / 2, 
			    	'white', 
			    	5, 
			    	2
		    	);
        		this.lineY(  // end
        			this.widthZoomZone - 35, 
        			this.spaceRight - this.widthAxisY / 2, 
        			this.spaceLeft + this.widthAxisY / 2, 
        			'white', 
        			5, 
        			2
        		);
		    },

		    drawAxisX: function(lanePost) {
		        var locale = "<?php echo App::isLocale('en') ? 'en' : 'vn'; ?>";

		        this.lineX( // left
		        	5, 
		        	this.widthZoomZone, 
		        	this.spaceLeft - lanePost * this.widthLane, 
		        	'white', 
		        	5, 
		        	2 
		        );
		        this.text( 
		        	this.directionText[locale][0]+ ' ' + lanePost, 
		        	20, 
		        	this.spaceLeft - lanePost * this.widthLane + 10,
		        	'white'
		        );
		        this.lineX( // right
		        	5, 
		        	this.widthZoomZone, 
		        	this.spaceRight + lanePost * this.widthLane, 
		        	'white', 
		        	5, 
		        	2
		        );
		        this.text( 
		        	this.directionText[locale][1]+ ' ' + lanePost, 
		        	20, 
		        	this.spaceRight + lanePost * this.widthLane - 20,
		        	'white'
		        );
		    },

		    drawSingerLane: function() {
		        this.lineX(
		        	5, 
		        	this.widthZoomZone, 
		        	this.spaceLeft, 
		        	'yellow', 
		        	10, 
		        	10
		        );
		        this.text(
		        	'0', 
		        	20, 
		        	this.spaceLeft + 10, 
		        	'white'
		        );
		        this.lineX(
		        	5, 
		        	this.widthZoomZone, 
		        	this.spaceRight, 
		        	'yellow', 
		        	10, 
		        	10
		        );
		    },

		    lineX: function(x1, x2, y, colors, w, h) {
		        var line = new createjs.Shape();
		        line.graphics
		                .setStrokeStyle(1)
		                .beginStroke(colors)
		                .setStrokeDash([w, h], 0)
		                .moveTo(x1, y)
		                .lineTo(x2, y)
		                .endStroke()
		        stageZoom.addChild(line);
		    },

		    lineY: function(x, y1, y2, colors, w, h) {
		        var line = new createjs.Shape();
		        line.graphics
		                .setStrokeStyle(1)
		                .beginStroke(colors)
		                .setStrokeDash([w, h], 0)
		                .moveTo(x, y1)
		                .lineTo(x, y2)
		                .endStroke()
		        stageZoom.addChild(line);
		    },

		    text: function(txt, x, y, colors) {
		        var label = new createjs.Text(txt, 'solid 5px Arial', colors);
		        label.x = x;
		        label.y = y;
		        stageZoom.addChild(label);
		    },

		    drawRectangle: function(x, y, w, h, colors) {
				var rect = new createjs.Shape();
				rect.graphics.setStrokeStyle(0.5)
		                    .beginStroke('black')
		                    .beginFill(colors)
							.drawRect(x, y, w, h);
				rect.y = this.midHeightLane;
				stage.addChild(rect);
			},

			drawTV: function(x, y) {
				var img = new Image();
		        img.src = "/front-end/img/flash.ico";
		        r_mc = new createjs.Bitmap(img);
		        r_mc.y = y;
		        r_mc.x = x;
		        r_mc.scaleX = 0.1; 
		        r_mc.scaleY = 0.1; 		      
		        stage.addChild(r_mc);
			},
		}
	})

</script>

@include('front-end.m13.inputting_system.refactor_script.history_type')