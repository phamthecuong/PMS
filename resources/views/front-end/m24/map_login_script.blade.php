
<script type="text/javascript">
	var map, // contain object for map
		alpha = 0.000005, // increase boundary
		loaded_sections = [], // contain loaded sections
		polylines_container = {}, // contain info to convert polyline id back to section code
		data_sections = {}, // contain detail data for sections. indexes, points, chainage info
		loading = false, // flg to check if a process is running
		hang_count = 0, // count in case loading flg = true, if after loading is done, hang_count > 0
		// do one more load
		lane_data = {},// contain data for each lane, use for detail popup
		selected_direction,
		selected_lane,
		markers_container = [],
		selected_section_code,
		stop_load_decrease = false,
		stop_load_increase = false,
		$slider,
		flash = 0,
		prefix = 'http://pms.drvn.gov.vn/pavementconditionimg/';
	

	var branch_info = <?php echo json_encode(\App\Models\tblBranch::allOptionToAjax()) ?>;
	var sb_info = <?php echo json_encode(\App\Models\tblOrganization::getListSB()) ?>;
	var lang = "<?php echo (App::getLocale() == 'en') ? 'en' : 'vn' ?>";
	var direction_obj = {
		en: {L: 'L', R: 'R', U: 'L', D: 'R'},
		vn: {L: 'T', R: 'P', U: 'T', D: 'P'}
	}; 
	vietbando.event.addDomListener(window, 'load', loadMap);
	var table = $('#table-pc').DataTable({
			showNEntries: false,
   			searching: false,
   			scrollY: "450px",
   			scrollX: "100%",
   			paging: false,
   			ordering: false,
	        scrollCollapse: true,
	        bInfo : false
   		});
   	new $.fn.dataTable.FixedColumns(table, {
   		   "iLeftColumns" : 5,
   		});
	function loadMap() {
		var mapProp = {
	      	center: new vietbando.LatLng(21.051181240269386, 105.8148193359375),
	      	zoom: 6
	  	};

	  	map = new vietbando.Map(document.getElementById("map"), mapProp);
	  	loadRoadData();
	  	vietbando.event.addListener(map, 'boundchange', loadRoadData);
	}

	/**
	 * ajax to get road section data
	 */
	function loadRoadData() {
		if (loading) {
			hang_count++;
			return;
		}

		loading = true;

		var zoom_level = map.getZoom();
	    var bound = map.getBounds();
	    var min_lat = (1 - alpha) * bound.sw.Latitude;
	    var min_lng = (1 - alpha) * bound.sw.Longitude;
	    var max_lat = (1 + alpha) * bound.ne.Latitude;
	    var max_lng = (1 + alpha) * bound.ne.Longitude;
	    var kilopost_from = $('input[name="kilopost_from"]').val();
		var	kilopost_to = $('input[name="kilopost_to"]').val();
		var rmb_id = +rmb_select.val();
		var sb_id = +sb_select.val();
		var route_id = +route_select.val();
		var year = year_select.val();
		if (year == null) {
			year = 'latest';
		}

		$.post("data_map", {
			_token: "{{ csrf_token() }}",
			sb_id: 0,
			survey_date: "latest",
			min_lat: min_lat,
			min_lng: min_lng,
			max_lat: max_lat,
			max_lng: max_lng,
			zoom_level: zoom_level,
			rmb_id: rmb_id,
			sb_id: sb_id,
			branch_id: route_id,
			date_y: year,
			kilopost_from: kilopost_from,
			kilopost_to: kilopost_to
		}, function(res){
			console.log('res-data-map:' + res);
			showLoading();
	  		createPoly(res);
	  		if (flash == 0) {
	  			map.zoomFit();	
	  		}
	  		flash ++;
	  		loading = false;
	  		if (hang_count > 0) {
	  			hang_count = 0;
	  			loadRoadData();
	  		}
	  		hideLoading();
	  	});
	}

	/**
	 * draw lines on map 
	 * @param {Object} data
	 */
	function createPoly(data) {
	  	for (var x in data) {
	  		if (typeof loaded_sections[data[x].section_code] !== 'undefined') {
	  			continue;
	  		}

	  		drawLineAndCache(data[x]);
	  	}
	}

	function drawLineAndCache(data) {
		var result = [];
  		var dataset = JSON.parse(data.points);
  		var color = convertColor(data, true);
  		var section_code = data.section_code;

  		for (var y in dataset) {
  			result.push(new vietbando.LatLng(dataset[y].latitude, dataset[y].longitude));
	  	}
	  	
	  	var polyline = new vietbando.Polyline({path: result, strokeOpacity: 1, strokeWidth: 5, strokeColor: color});
	  	polyline.setMap(map);

	  	vietbando.event.addListener(polyline, 'click', function(obj){
	  		handleClick(obj.Me.id, 0);
	  	});

	  	polylines_container[polyline.id] = {
	  		section_code: section_code,
	  		polyline: polyline
	  	};
	  	loaded_sections.push(section_code);
	  	data_sections[section_code] = data;
	}

	/**
	 * load lane data
	 * mode: 0: all
	 *       1: increase 
	 *       2: decrease
	 */
	function loadLaneData(section_code, mode) {
		if (typeof mode == 'undefined') mode = 0;

		$.get("get_lane_data", {
			section_code: section_code,
			mode: mode,
			year: year_select.val(),
			
		}, function(res) {
			//console.log(res);
			var need_to_add = 0;
			for (var i in res) {
				need_to_add+= archiveLaneData(res[i], mode);
			}
			//console.log(lane_data);
			if (mode == 1 && need_to_add == 0) {
				stop_load_increase = true;
			} else if (mode == 2 && need_to_add == 0) {
				stop_load_decrease = true;
			}
			if (mode == 0) {
				loadHorizontalSection();	
			}
			loadPCTable();
			showHidePCTable();
			selectSection(selected_section_code);
	  	});
	}

	function archiveLaneData(data, mode) {
		if (typeof loaded_sections[data.section_code] == 'undefined') {
  			drawLineAndCache(data);
  		}
		var key = convertDirectionToKey(data.direction) + '-' + data.lane_position_no;
		if (typeof lane_data[key] == 'undefined') {
			lane_data[key] = [];
		}
		var points = JSON.parse(data.points);
		// if (convertDirectionToKey(data.direction) == 1) {
		// 	points.reverse();
		// }
		var current_pos = getDefaultPosition(convertDirectionToKey(data.direction), points);
		if (mode == 0) {
			lane_data[key].push({
				data: data,
				points: points,
				current_pos: current_pos
			});
		} if (mode == 1) {
			if ($('[data-id="r-' + data.section_code + '"]').length > 0) {
				return 0;
			}
			lane_data[key].push({
				data: data,
				points: points,
				current_pos: current_pos
			});
			appendHorizontalSection($('#r-' + key), data, 0, 0);
		} else if (mode == 2) {
			if ($('[data-id="r-' + data.section_code + '"]').length > 0) {
				return 0;
			}
			lane_data[key].unshift({
				data: data,
				points: points,
				current_pos: current_pos
			});
			appendHorizontalSection($('#r-' + key), data, 1, 0);
		}
		return 1;
	}

	function convertDirectionToKey(direction) {
		if (direction == 'U' || direction == 'L') {
			return 1;
		} else if (direction == 'D' || direction == 'R') {
			return 2;
		} else {
			return 0;
		}
	}

	function preloadImg() {
		var key = selected_direction + '-' + selected_lane;
		var index = findPosInLane();
		var points = lane_data[key][index].points;
		for (var i in points) {
			$('<img/>')[0].src = getFullImageLink(points[i].image_path);
		}
		if (typeof lane_data[key][index-1] != 'undefined') {
			var points = lane_data[key][index-1].points;
			for (var i in points) {
				$('<img/>')[0].src = getFullImageLink(points[i].image_path);
			}
		}
		if (typeof lane_data[key][+index+1] != 'undefined') {
			var points = lane_data[key][+index+1].points;
			for (var i in points) {
				$('<img/>')[0].src = getFullImageLink(points[i].image_path);
			}
		}
	}

	function findMaxMinSection(lane_data) {
  		var 
  			k_max_from = 0;
  			k_max = 0;
  			m_max = 0;
  			k_min = 0;
  			m_min = 0;
		for (var j in lane_data) {
			var data = lane_data[j];	
			for (var i in data) {
				var item = data[i].data;
				if (item['km_from'] >= k_max_from) {
					m_max = 0;
					k_max_from = item['km_from'];
					k_max = item['km_to'];
					if (item['m_to'] > m_max) {
						m_max = item['m_to'];
					}
				}
		   	}
	    }

	    var item_min = k_max_from;
  		for (var j in lane_data) {
			var data = lane_data[j];	
			for (var i in data) {
				var item = data[i].data;
				if (item['km_from'] <item_min) {
					item_min = item['km_from'];
					k_min = item['km_from'];
					m_min = item['m_from'];
				}
		   	}
	    }
   		var obj = {k_max: k_max, m_max: m_max, k_min: k_min, m_min: m_min};
   		return obj;
	}

	function showBriefInfo() {
		section_data = data_sections[selected_section_code];
		
		$('#chainage_info')
			.html('')
			.append($('<strong/>', {
				text: '{{ trans('general.route_name') }}: '
			}))
			.append($('<span/>', {
				text: getBranchName(section_data.branch_id) + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.km') }}: '
			}))
			.append($('<span/>', {
				text: section_data.km_from + '+' + section_data.m_from + ' - ' + section_data.km_to + '+' + section_data.m_to + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.road_length') }}: '
			}))
			.append($('<span/>', {
				text: section_data.section_length + 'm' + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.analysis_area') }}: '
			}))
			.append($('<span/>', {
				text: section_data.analysis_area + 'm2' + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.direction') }}: '
			}))
			.append($('<span/>', {
				text: direction_obj[lang][section_data.direction] + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.survey_lane') }}: '
			}))
			.append($('<span/>', {
				text: section_data.lane_position_no + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.branch_number') }}: '
			}))
			.append($('<span/>', {
				text: section_data.section_code.substring(3, 5) + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.pavement_type') }}: '
			}))
			.append($('<span/>', {
				text: section_data.surface_type + ', '
			}))
			.append($('<strong/>', {
				text: '{{ trans('general.survey_date') }}: '
			}))
			.append($('<span/>', {
				text: section_data.date_m + '/' + section_data.date_y,
			}));
		$('.brief-chainage-info #item1').html('KM' + section_data.km_from + '+' + section_data.m_from);
		$('.brief-chainage-info #item2').html(direction_obj[lang][section_data.direction]);
		$('.brief-chainage-info #item3').html('KM' + section_data.km_to + '+' + section_data.m_to);
		

	}

	/**
	 * 
	 * select a section and view it
	 */
	function selectSection(section_code) {
		selected_direction = section_code.substring(9, 10);
		selected_lane = section_code.substring(10, 11);
		selected_section_code = section_code;
		var key = selected_direction + '-' + selected_lane;

		$('#body_pc tr').hide();
		$('#body_pc tr.r-' + key).show();

		$('.selectedSection').removeClass('selectedSection');
		$('.quadrat').removeClass('quadrat');
		$('[data-id="r-' + section_code + '"]').addClass('selectedSection');
		$('#h-' + section_code).addClass('quadrat');

		$('[data-id="r-' + section_code + '"]').scrollintoview();
		$('#h-' + section_code).scrollintoview();

		preloadImg();

		showImage();
		moveSlide();
		showBriefInfo();
		var result_obj = findMaxMinSection(lane_data);
		$('#item1_Routine').html('Km' + result_obj.k_min +'+'+ result_obj.m_min);
		$('#item2_Routine').html('Km' + result_obj.k_max +'+'+ result_obj.m_max);
		placeMarkerAtCenter(data_sections[section_code]);
		// load more
		var load_flg = 0;
		for (var i in lane_data) {
			if (lane_data[i][0].data.section_code == section_code && !stop_load_decrease) {
				load_flg = 2;
				break;
			} else if (lane_data[i][lane_data[i].length - 1].data.section_code == section_code && !stop_load_increase) {
				load_flg = 1;
				break;
			}
		}
		if (load_flg != 0) {
			loadLaneData(section_code, load_flg);
		}
	}

	function placeMarkerAtCenter(section_data) {
		placeMarker((+section_data.min_lat + (+section_data.max_lat))/2, (+section_data.min_lng + (+section_data.max_lng))/2, section_data.section_code);
	}

	function loadPCTable() {
		// $('#body_pc').html('');
		table.clear();
		for (var j in lane_data) {
			var data = lane_data[j];	
			for (var i in data) {
				var section_data = data[i].data;
				appendRowToPCTable(section_data, 0);
	       	}
	    }
	    table.draw();

	}

	function getBranchName(branch_id) {
		return (typeof branch_info[branch_id] != 'undefined') ? branch_info[branch_id].name : '';
	}

	function getSbName(sb_id) {
		return (typeof sb_info[sb_id] != 'undefined') ? sb_info[sb_id].name : '';
	}

	function appendRowToPCTable(section_data, direction) {
		var key = section_data.section_code.substring(9, 10) + '-' + section_data.section_code.substring(10, 11);
		// console.log(section_data);
		// var row_data = $('<tr/>', {
		// 	id: 'r-' + section_data.section_code,
		// 	class: 'r-' + key,
		// 	onclick: "selectSection('" + section_data.section_code + "')"
		// });

  //       row_data.append($('<td/>', {
  //           	text: getBranchName(section_data.branch_id),
  //           	title: '{{ trans('general.route_name') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.km_from,
  //           	title: '{{ trans('general.km_from') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.m_from,
  //           	title: '{{ trans('general.m_from') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.km_to,
  //           	title: '{{ trans('general.km_to') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.m_to,
  //           	title: '{{ trans('general.m_to') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.date_m + '/' + section_data.date_y,
  //           	title: '{{ trans('general.survey_date') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.cracking_ratio_cracking,
  //           	title: '{{ trans('general.cracking') }}',
  //           	class: 'col-crack'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.cracking_ratio_patching,
  //           	title: '{{ trans('general.patching') }}',
  //           	class: 'col-crack'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.cracking_ratio_pothole,
  //           	title: '{{ trans('general.pothole') }}',
  //           	class: 'col-crack'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.cracking_ratio_total,
  //           	title: '{{ trans('general.cracking_ratio') }}',
  //           	class: 'col-crack'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.rutting_depth_max,
  //           	title: '{{ trans('general.rut_max') }}',
  //           	class: 'col-rut'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.rutting_depth_ave,
  //           	title: '{{ trans('general.rut_ave') }}',
  //           	class: 'col-rut'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.IRI,
  //           	title: '{{ trans('general.iri') }}',
  //           	class: 'col-iri'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.MCI,
  //           	title: '{{ trans('general.mci') }}',
  //           	class: 'col-mci'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.direction,
  //           	title: '{{ trans('general.direction') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.lane_position_no,
  //           	title: '{{ trans('general.survey_lane') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.section_length,
  //           	title: '{{ trans('general.road_length') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.analysis_area,
  //           	title: '{{ trans('general.analysis_area') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.structure,
  //           	title: '{{ trans('general.structure') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.intersection,
  //           	title: '{{ trans('general.intersection') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.overlapping,
  //           	title: '{{ trans('general.overlapping') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.number_of_lane_U,
  //           	title: '{{ trans('general.number_of_lane_up') }}'
  //           }))
  //           .append($('<td/>', {
  //           	text: section_data.number_of_lane_D,
  //           	title: '{{ trans('general.number_of_lane_down') }}'
  //           }));
          	
          	// if (direction == 0) {
          	// 	table.row.add(row).draw( false );
          	// } else {
          	// 	table.fnAddData(row, false);
          	// }
          	
          	// table.row.add(row_data).order([ 1, 'desc' ]).draw(false);
 		// console.log('branh_name' + section_data.branch_id);
        row_data = [

        	getBranchName(section_data.branch_id),
        	section_data.km_from,
            section_data.m_from,
            section_data.km_to,
            section_data.m_to,
            section_data.date_m + '/' + section_data.date_y,
            parseFloat(section_data.cracking_ratio_cracking),
            parseFloat(section_data.cracking_ratio_patching),
            parseFloat(section_data.cracking_ratio_pothole),
            parseFloat(section_data.cracking_ratio_total),
            parseFloat(section_data.rutting_depth_max),
            parseFloat(section_data.rutting_depth_ave),
            parseFloat(section_data.IRI),
            parseFloat(section_data.MCI),
           	direction_obj[lang][section_data.direction],
            section_data.lane_position_no,
            section_data.section_length,
            section_data.surface_type,
            getSbName(section_data.SB_id),
	    	section_data.structure,
	    	section_data.intersection,
	    	section_data.overlapping,
	    	section_data.number_of_lane_U,
	    	section_data.number_of_lane_D
        ];

        row_node = table.row.add(row_data).node();
        $(row_node)
        	.attr('data-id', 'r-' + section_data.section_code)
        	.attr('class', 'r-' + key)
        	.attr('onclick', "selectSection('" + section_data.section_code + "')");
	}

	function loadHorizontalSection() {
		$('#horizontal_view').html('');
		var up = [];
		var down = [];
		for (var i in lane_data) {
			var key = +i.substring(2, 3) - 1;
			if (i.substring(0, 1) == 1) {
				if (typeof up[key] == 'undefined') {
					up[key] = [];
				}
				for (var j in lane_data[i]) {
					up[key].push(lane_data[i][j].data);
				}
			} else if (i.substring(0, 1) == 2) {
				if (typeof down[key] == 'undefined') {
					down[key] = [];
				}
				for (var j in lane_data[i]) {
					down[key].push(lane_data[i][j].data);	
				}
			}
		}

		// UP
		// $('<div/>', {
		// 	class:"col-xs-offset-6",text:"{{trans('map.left')}}"
		// }).appendTo('#horizontal_view');
		up.reverse();
		var adjust_point = null;
		if (up.length > 1) {
			adjust_point = +up[up.length - 1][0].km_from * 1000 + (+up[up.length - 1][0].m_from);
		}
		
		for (var i in up) {
			var data = up[i];
			drawHorizontalLine(data, adjust_point);
		}
		$('<div>', {
			style: 'position:relative;', 
			class: 'block-h'
		}).appendTo('#horizontal_view');
		$('<img>',{src:'/front-end/image/arrow.png', style:'width: 60px; height: 30px;position: fixed;margin-top: -22px; z-index:2'  }).appendTo('.block-h');
		$('<hr/>', {
			style: 'position: fixed; border-color: black; border-style: dashed; margin-top: -8px; width: ' + $('#horizontal_view').width() + 'px;',
		}).appendTo('.block-h');

		// DOWN
		adjust_point = null;
		if (down.length > 1) {
			adjust_point = +down[0][0].km_from * 1000 + (+down[0][0].m_from);
		}

		for (var i in down) {
			var data = down[i];
			drawHorizontalLine(data, adjust_point);
		}
		// $('<div/>', {
		// 	class:"col-xs-offset-6",text:"{{trans('map.right')}}"
		// }).appendTo('#horizontal_view');
	}

	function drawHorizontalLine(data, adjust_point) {
		var key = data[0].section_code.substring(9, 10) + '-' + data[0].section_code.substring(10, 11);
		var div_lane = $('<div/>', {
			id: 'r-' + key,
			style: 'white-space: nowrap;display: inline-block;' + (key == '1-1' ? 'padding-bottom: 16px' : '')
		}).appendTo('#horizontal_view');
		
		for (var j in data) {
			var margin_left = 0;

			if (adjust_point != null && j == 0) {
				margin_left = +data[j].km_from * 1000 + (+data[j].m_from) - adjust_point;
			}
			appendHorizontalSection(div_lane, data[j], 0, margin_left);
		}
	}

	function appendHorizontalSection(div_lane, data, direction, margin_left) {
		
		var option = {
        	style: "display: inline-block;width: " + data.section_length + "px;color: #FFFFFF;height: 20px;background: " + convertColor(data) + ";font-size: 12px;vertical-align: top;text-align: center;border: 1px solid #cccccc; margin-left: " + margin_left + "px",
        	onclick: "selectSection('" + data.section_code + "')",
        	id: 'h-' + data.section_code,
        	text: data.structure
        };
        if (direction == 0) {
        	div_lane.append($('</div/>',{class:"class-col-xs-6",text:"left"}))
        	div_lane.append($('<div/>', option));
        	div_lane.append($('</div/>',{class:"class-col-xs-6",text:"left"}))
        } else {
        	div_lane.prepend($('<div/>', option));
        }
		
	}

	/**
	 * type: 0: item_id is polyline_id, 1: item_id is section_id
	 */
	function handleClick(item_id, type) {
		lane_data = {}; // reset lane data
		stop_load_increase = false;
		stop_load_decrease = false;

		var section_code;
		if (type == 0) {
			section_code = polylines_container[item_id].section_code;	
		} else {
			section_code = item_id
		}
		
		selected_direction = section_code.substring(9, 10);
		selected_lane = section_code.substring(10, 11);
		selected_section_code = section_code;
		loadLaneData(section_code);
		
		placeMarkerAtCenter(data_sections[section_code]);
		$('#dialog_simple').dialog('open');
		return;
	}

	function resetLineColor() {
		resetMap();
		loadRoadData();
	}

	function resetMap() {
		for (var i in markers_container) {
			markers_container[i].setMap(null);
		}
		for (var i in polylines_container) {
			polylines_container[i].polyline.setMap(null);
		}
		
		markers_container = [];
		polylines_container = {};
		loaded_sections = [];
		data_sections = {}; 
		lane_data = {};
	}

	
	function convertColor(data, source) {
		var result = 0;
		var type = '';
		if (typeof source != 'undefined') {
			if ($('[name="option-crack"]').is(':checked')) {
	    		type = 'cracking_ratio';
	    		//alert(1);
	    	}

	    	if ($('[name="option-rut"]').is(':checked')) {
	    		type = 'rutting_depth';
	    	}
	    	if ($('[name="option-iri"]').is(':checked')) {
	    		type = 'iri';	
	    	}
	    	if ($('[name="option-mci"]').is(':checked')) {
	    		type = 'mci';
	    	}	
		} else {
			if ($('[name="popup-crack"]').is(':checked')) {
				type = 'cracking_ratio';
	    	}

	    	if ($('[name="popup-rut"]').is(':checked')) {
	    		if (!type) {
	    			type = 'rutting_depth';
	    		} else {
	    			type = 'empty';
		    	}
	    	}
	    	if ($('[name="popup-iri"]').is(':checked')) {
	    		if (!type) {
	    			type = 'iri';	
	    		} else {
	    			type = 'empty';
		    	}	
	    	}
	    	if ($('[name="popup-mci"]').is(':checked')) {
	    		if (!type) {
	    			type = 'mci';	
	    		} else {
	    			type = 'empty';
		    	}
	    	}
		}
		var crack_data = {
			1: 0,
			2: 10,
			3: 20,
			4: 40
		};
		var rut_data = {
			1: 0,
			2: 20,
			3: 30,
			4: 50
		};
		var iri_data = {
			1: 0,
			2: 4,
			3: 6,
			4: 10
		};
		var mci_data = {
			4: 0,
			3: 3,
			2: 4,
			1: 5
		};
		
		switch (type)
		{
			case 'cracking_ratio':
				var value = data.cracking_ratio_total;
				result = vlookup(value, crack_data);
				break;
			case 'rutting_depth':
				var value = data.rutting_depth_ave;
				result = vlookup(value, rut_data);
				break;
			case 'iri' :
				var value = data.IRI;
				result = vlookup(value, iri_data);
				break;
			case 'mci':
				var value = data.MCI;
				result = vlookup(value, mci_data, false, true);
				break;
			default :
				break;
		}
		switch (result) {
			case '1':
				return "#434AC8";
			case '2':
				return "#68B054";
			case '3':
				return "#E37E33";
			case '4':
				return "#CB2228";
			default :
				return "#6D6D6D";
		}
	}

	function vlookup(lookupValue, array, equal, break_when_found) {
        result = null;

        for (var key in array) {
        	value = array[key];
            if (equal) {
                if (lookupValue == value) {
                    result = key;
                    if (break_when_found) break;
                }
            } else {
                if (lookupValue >= value) {
                    result = key;
                    if (break_when_found) break;
                }
            }
        }
        return result;
    }

    /**
	 * remove all current markers and place new one
	 */
	function placeMarker(latitude, longitude, section_id) {
		for (var i in markers_container)
		{
			markers_container[i].setMap(null);
		}
		resetMarker();
		
		var latlng = new vietbando.LatLng(latitude, longitude);
		marker = new vietbando.Marker({
			position: latlng,
			title: section_id
		});
		marker.setMap(map);
		vietbando.event.addListener(marker, 'click', function (obj) {
	      	handleClick(obj.Me.getTitle(), 1);
	   	});
		markers_container.push(marker);	
		// map.setCenter(latlng);
	}

	function resetMarker() {
		for (var i in markers_container)
		{
			markers_container[i].setMap(null);
		}	
		markers_container = [];
	}

	function showImage() {
		var key = selected_direction + '-' + selected_lane;
		var index = findPosInLane();
		var current_pos = lane_data[key][index].current_pos;
		var points = lane_data[key][index].points;
		
		$('#preview_image img').attr('src', getFullImageLink(points[current_pos].image_path));
	}

	function getFullImageLink(image_path) {
		return prefix + image_path.replace('1.RMB II/', 'RMB II/').replace('1.RMB III/', 'RMB III/');
	}

	function findPosInLane() {
		var key = selected_direction + '-' + selected_lane;
		for (var i in lane_data[key]) {
			if (lane_data[key][i].data.section_code == selected_section_code) {
				return i;
				break;
			}
		}
		return false;
	}

	function getDefaultPosition(direction, points) {
		// return 0;
		return (direction == 1) ? (points.length - 1) : 0
	}

	function moveSlide() {
		if (selected_direction == 1) {
			$('#BC .triangle').css({
				transform: 'rotate(180deg)'
			});	
		} else {
			$('#BC .triangle').css({
				transform: 'rotate(0deg)'
			});	
		}
		var key = selected_direction + '-' + selected_lane;
		var index = findPosInLane();
		var current_pos = lane_data[key][index].current_pos;
		var points = lane_data[key][index].points;

		// Get current value
		$slider.data('slider').max = points.length;
		$slider.slider('setValue', +current_pos + 1);
	}

    $(window).load(function() {
    	$('#dialog_simple').dialog({
			autoOpen : false,
			width : 1200,
			resizable : false,
			modal : true,
			title : '{{ trans("general.front_view_window") }}',
			position: ['center', 20],

		}).dialog("widget").draggable({ containment: "none", scroll: false });

    	$('#btn-close').click(function(){
    		$('#dialog_simple').dialog("close");
    	})
		$("body").keydown(function(e) {
			keyDownHandle(e);
		});	
		// init slide
		$slider = $('#slide').slider({
  			min: 1,
  			max: 20,
  			value: 1      			
  		});
  		// catch event when popup checkbox is clicked
  		$('#popup-checkbox input[type="checkbox"]').change(function() {
  			$(' #popup-checkbox input[name!="' + $(this).attr('name') + '"]').prop("checked", false);
        	$(' #popup-checkbox input[name!="' + $(this).attr('name') + '"]').prop("disabled", false);
        	$(this).prop("disabled", true);
  			showHidePCTable();
  			loadHorizontalSection();
  			selectSection(selected_section_code);
  		})
  		//
  		$.get('get_center',function(data) {
  			map.setCenter(new vietbando.LatLng(data.lat , data.lng));
	  		return false;
	  	},'json');

    	$(window).resize(function() {
		   	setTimeout(function(){
			    map.resize();
			}, 1000);
		});
    })

    function showHidePCTable() {
    	$('.col-crack, .col-rut, .col-iri, .col-mci').show();
    	if ($('[name="popup-crack"]').is(':checked')) {
	        $(".number1").show();
            $(".number2").hide();
            $(".number3").hide();
            $(".number4").hide();
    	} else if ($('[name="popup-rut"]').is(':checked')) {
	        $(".number2").show();
            $(".number1").hide();
            $(".number3").hide();
            $(".number4").hide();
    	} else if ($('[name="popup-iri"]').is(':checked')) {
	        $(".number3").show();
            $(".number1").hide();
            $(".number2").hide();
            $(".number4").hide();
    	} else if ($('[name="popup-mci"]').is(':checked')) {
	        $(".number4").show();
            $(".number1").hide();
            $(".number2").hide();
            $(".number3").hide();
    	}
    }

    function keyDownHandle(e) {
    	if (!selected_section_code) return;
	  	if (e.keyCode == 37) { // left
	  		var key = selected_direction + '-' + selected_lane;
			var index = findPosInLane();
			var current_pos = lane_data[key][index].current_pos;
			var points = lane_data[key][index].points;

			if (current_pos == 0) {
				if (typeof lane_data[key][index - 1] == 'undefined') {
					return;
				}
				lane_data[key][index].current_pos = getDefaultPosition(selected_direction, points);
				if (selected_direction == 2) {
					lane_data[key][index - 1].current_pos = lane_data[key][index - 1].points.length - 1;	
				}
				selectSection(lane_data[key][index - 1].data.section_code);
			} else {
				lane_data[key][index].current_pos--;
				showImage();
				moveSlide();
			}
	  	} else if (e.keyCode == 38) { // up
	  		
	  	} else if (e.keyCode == 39) { // right
	    	var key = selected_direction + '-' + selected_lane;
			var index = findPosInLane();
			var current_pos = lane_data[key][index].current_pos;
			var points = lane_data[key][index].points;
			
			if (current_pos == points.length - 1) {
				if (typeof lane_data[key][+index + 1] == 'undefined') {
					return;
				}
				lane_data[key][index].current_pos = getDefaultPosition(selected_direction, points);
				if (selected_direction == 1) {
					lane_data[key][+index + 1].current_pos = 0;	
				}
				selectSection(lane_data[key][+index + 1].data.section_code);
			} else {
				lane_data[key][index].current_pos++;
				showImage();
				moveSlide();
			}
	  	} else if(e.keyCode == 40) { // down
	    	
	  	}

	  	

    }
</script>