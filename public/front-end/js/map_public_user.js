var map, current_section_code,markers_container = [], polylines_container = {}, active_sections_data = {}, alpha = 0.0000001;

var type = 'cracking_ratio';
	$('document').ready(function(){
		$('#layer_tree input[type=checkbox]').click(function(){
			if ($(this).val() == 1) {
				type = 'cracking_ratio';
			}
			else if ($(this).val() == 3){
				type = 'rutting_depth';
			}
			else if ($(this).val() == 5){
				type = 'iri';
			}
			else
			{
				type = 'mci';
			}

		});
	});

	vietbando.event.addDomListener(window, 'load', loadMap);	
	function loadMap() {
		var mapProp = {
	      	center: new vietbando.LatLng(21.051181240269386, 105.8148193359375),
	      	zoom: 10
	  	};
	  	map = new vietbando.Map(document.getElementById("map"), mapProp);
	  	loadRoadData();
	  	vietbando.event.addListener(map, 'boundchange', loadRoadData);
	}

	/**
	 * ajax to get road section data
	 */
	function loadRoadData()
	{
		var zoom_level = map.getZoom();
	    var bound = map.getBounds();
	    var min_lat = bound.sw.Latitude;
	    var min_lng = bound.sw.Longitude;
	    var max_lat = bound.ne.Latitude;
	    var max_lng = bound.ne.Longitude;
	    min_lat-= alpha*bound.sw.Latitude;
	    min_lng-= alpha*bound.sw.Longitude;
	    max_lat+= alpha*bound.ne.Latitude;
	    max_lng+= alpha*bound.ne.Longitude;
		$.get("/data_map", {
			sb_id: 0,
			survey_date: "latest",
			min_lat: min_lat,
			min_lng: min_lng,
			max_lat: max_lat,
			max_lng: max_lng,
			zoom_level: zoom_level
		}, function(res){
			resetMap();
	  		createPoly(res);
	  	});
	}

	/**
	 * draw lines on map 
	 * @param {Object} data
	 */
	function createPoly(data) {

		var zoom_level = map.getZoom();
		var dataset =[];
	  	var result =[];
	  	var section_code;
	  	for (var x in data) {
	  		result = [];
	  		dataset = JSON.parse(data[x].points);

	  		section_code = data[x].section_code;

	  		for (var y in dataset) {
	  			if (y != 0 && (+ y + 1) != dataset.length && zoom_level <= 16) {
	  				continue;	
	  			}
	  			result.push(new vietbando.LatLng(dataset[y].latitude, dataset[y].longitude));
		  	}
		  	color = convertColor(data[x]);
		  	var polyline = new vietbando.Polyline({path: result, strokeOpacity: 1, strokeWidth: 5, strokeColor: color});
		  	polyline.setMap(map);
		  	polylines_container[polyline.id] = {
		  		section_code: section_code,
		  		data: dataset,
		  		polyline: polyline
		  	};
	  	}
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

	function convertColor(data) {
		var result = 0;
		var crack_data = {
			0:  1,
			10: 2,
			20: 3,
			40: 4
		};
		var rut_data = {
			0:  1,
			20: 2,
			30: 3,
			50: 4
		};
		var iri_data = {
			0: 1,
			4: 2,
			6: 3,
			10: 4
		};
		var mci_data = {
			0: 4,
			3: 3,
			4: 2,
			5: 1
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
				result = vlookup(value, mci_data);
				console.log(value);
				console.log(result);
				console.log('-----');
				break;
			default :
				break;
		}
		switch (result) {
			case 1 :
				return "#434AC8";
			case 2 :
				return "#68B054";
			case 3 :
				return "#E37E33";
			case 4 :
				return "#CB2228";
			default :
				return "#FFFFFF";
		}
	}

	function vlookup(lookupValue, array, equal = false)
    {

        result = null;
        for (var key in array)
        {
        	value = array[key];
            if (equal)
            {
                if (lookupValue == key)
                {
                    result = value;
                }
            }
            else
            {
                if (lookupValue >= key)
                {
                    result = value;
                }
            }
        }
        return result;
    }