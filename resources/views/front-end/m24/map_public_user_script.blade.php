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
		prefix = 'http://pms.drvn.gov.vn/assets/data/';
		// current_section_code,
		//  
		
		
		// active_sections_data = {}, 
	<?php
		$branch_data = str_replace(array("\\n", "\\r", "\\r\\n", "\\n\\r"), '', json_encode(\App\Models\tblBranch::allOptionToAjax()));
	?> 
	var branch_info = JSON.parse('{!! $branch_data !!}');

	vietbando.event.addDomListener(window, 'load', loadMap);

	function loadMap() {
		var mapProp = {
	      	center: new vietbando.LatLng(21.051181240269386, 105.8148193359375),
	      	zoom: 6
	  	};

	  	map = new vietbando.Map(document.getElementById("map"), mapProp);
	  	loadRoadData();
	  	vietbando.event.addListener(map, 'boundchange', loadRoadData);

	  	// $.get('get_center',function(data){
	  	// 	map.setCenter(new vietbando.LatLng(data.lat , data.lng));
	  	// 	return false;
	  	// },'json');
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
	    
		$.post("data_map", {
			_token: "{{ csrf_token() }}",
			sb_id: 0,
			survey_date: "latest",
			min_lat: min_lat,
			min_lng: min_lng,
			max_lat: max_lat,
			max_lng: max_lng,
			zoom_level: zoom_level,
		}, function(res){
			// resetMap();
	  		createPoly(res);
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
	  	
	  	var polyline = new vietbando.Polyline({path: result, strokeOpacity: 1, strokeWidth: 4, strokeColor: color});
	  	polyline.setMap(map);

	  	vietbando.event.addListener(polyline, 'click', function(obj){
	  		// handleClick(obj.Me.id, 0);
	  		webDisplayLogin();
	  	});

	  	// polylines_container[polyline.id] = {
	  	// 	section_code: section_code,
	  	// 	polyline: polyline
	  	// };
	  	loaded_sections.push(section_code);
	  	data_sections[section_code] = data;
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
		polylines_container = [];
	}
	
	function convertColor(data, source) {
		var result = 0;
		var type = '';
		if (typeof source != 'undefined') {
			if ($('[name="option-crack"]').is(':checked')) {
	    		type = 'cracking_ratio';
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

    $(window).load(function() {
    	$(window).resize(function() {
		   	setTimeout(function(){
			    map.resize();
			}, 1000);
		});

    	$.get('get_center',function(data) {
	  		map.setCenter(new vietbando.LatLng(data.lat , data.lng));
	  		return false;
	  	}, 'json');
	  	//map.zoomFit();	
    })
</script>