// var search = $('#check_search').val();
// var sb_id = $('#sb_id').val();
// var road_name = $('#road_name').val();
// var survey_date = $('#survey_date').val();
var map, markers_container = [], polylines_container = {}, active_sections_data = {}, current_section_code;
var alpha = 0.0;
vietbando.event.addDomListener(window, 'load', loadMap);	
function loadMap() {
	var mapProp = {
      	center: new vietbando.LatLng(21.051181240269386, 105.8148193359375),
      	zoom: 8
  	};
  	map = new vietbando.Map(document.getElementById("map"), mapProp);
  	// loadRoadData();
  	vietbando.event.addListener(map, 'boundchange', loadRoadData);

  	$.post("/data_map/getCenterPosition", {
  		sb_id: $('input[name="sb_id_seek"]').val(),
  		survey_date: $('input[name="survey_date_seek"]').val(),
  	}, function(res) {
  		var result = JSON.parse(res);
  		map.setCenter(new vietbando.LatLng(result.latitude , result.longitude));
  	});
}

function setCenterMap()
{
	var set_center = true;
	if (set_center){
		$.post("/data_map/getCenterPosition", {
	  		sb_id: $('input[name="sb_id_seek"]').val(),
	  		survey_date: $('input[name="survey_date_seek"]').val(),
	  	}, function(res) {
	  		var result = JSON.parse(res);
	  		map.setCenter(new vietbando.LatLng(result.latitude , result.longitude));
	  	});
	  	set_center = false;
	}
}

/**
 * ajax to get road section data
 */
function loadRoadData()
{
	var zoomLevel = map.getZoom();
    var bound = map.getBounds();
    var min_lat = bound.sw.Latitude;
    var min_lng = bound.sw.Longitude;
    var max_lat = bound.ne.Latitude;
    var max_lng = bound.ne.Longitude;
    min_lat-= alpha*bound.sw.Latitude;
    min_lng-= alpha*bound.sw.Longitude;
    max_lat+= alpha*bound.ne.Latitude;
    max_lng+= alpha*bound.ne.Longitude;
	$.post("/data_map/getSectionRoadData", {
		sb_id: $('input[name="sb_id_seek"]').val(),
		survey_date: $('input[name="survey_date_seek"]').val(),
		road_name: $('input[name="road_name"]').val(),
		kilopost_from: $('input[name="kilopost_from"]').val(),
		kilopost_to: $('input[name="kilopost_to"]').val(),
		min_lat: min_lat,
		min_lng: min_lng,
		max_lat: max_lat,
		max_lng: max_lng,
		zoomLvl: zoomLevel,
	}, function(res){
		resetMap();
  		createPoly(JSON.parse(res));
  		$('#dim_wrapper').hide();
  	});
}
function resetLineColor()
{
	resetMap();
	loadRoadData();
}
function resetMap()
{
	for (var i in polylines_container)
	{
		polylines_container[i].polyline.setMap(null);
	}
	
	polylines_container = [];
}

function resetMarker()
{
	for (var i in markers_container)
	{
		markers_container[i].setMap(null);
	}	
	markers_container = [];
}

function createPoly(data){
	var dataset =[];
  	var result =[];
  	var section_code;
  	for(var x in data)
  	{
  		result = [];
  		dataset = data[x].data;
  		section_code = data[x].section_code;
  		for(var y in dataset)
  		{
	  		result.push(new vietbando.LatLng(dataset[y].latitude, dataset[y].longitude));
	  	}
	  	// handle wheren there's one point only
	  	if (result.length == 1)
	  	{
	  		result.push(result[0]);
	  	}
	  	// get color
	  	var layer_tree = getLayerTreeValue();
		var color;
		switch(layer_tree) {
		    case 1:
		    case 2:
		    	
		        color = data[x].cracking_ratio;
		        break;
		    
		    case 3:
		    case 4:
				color = data[x].rutting_depth;
		        break;
		        
		    case 5:
		    case 6:
				color = data[x].iri;
		        break;
		        
		    case 7:
		    case 8:
				color = data[x].mci;
		        break;    
		    default:
		    	color = '#FFFFFF';
		    	break;   
		}
	  	var polyline = new vietbando.Polyline({path: result, strokeOpacity: 1, strokeWidth: 5, strokeColor: color});
	  	polyline.setMap(map);
	  	vietbando.event.addListener(polyline, 'click', function(obj){
	  		handleClick(obj);
	  	});
	  	polylines_container[polyline.id] = {
	  		section_code: section_code,
	  		data: dataset,
	  		polyline: polyline
	  	};
  	}
}

function handleClick(obj)
{
	var polyline = obj.Me;
	var survey_date = $('input[name="survey_date_seek"]').val();
	var section_code = polylines_container[polyline.id].section_code;
	var dataset = polyline.getPath().getArray();
	
	if (map.getZoom() <= 10)
	{
		map.setZoom(15);
	}	
	placeMarker(dataset[Math.floor(dataset.length/2)].Latitude, dataset[Math.floor(dataset.length/2)].Longitude);
	
	$('#pavement_window').modal("show");
	$("#pavement_window .modal-dialog").draggable({
		handle: ".modal-body",
		cursor: "move",
	});
	$.post("/data_map/get_data_popup", {
		survey_date:survey_date,
		section_code: section_code,
		layer_tree_value: getLayerTreeValue()
	}, function(res) {
		var result = JSON.parse(res);
		active_sections_data = result;
		// var imgs = result.imgs.split(",");
		// preload(imgs);
		current_section_code = section_code;
		$('#pavement_window .modal-body').html(active_sections_data[section_code]);
	});
}
/*
  
 * get next section or previous section when click 'next' or 'back'
 * */
function view_section_other (control)
{
	var survey_date = $('input[name="survey_date_seek"]').val();
	var section_code = current_section_code;
	var Km_from = section_code.substring(8, 12);
	$.post("/data_map/get_section", {
  		sb_id: $('input[name="sb_id_seek"]').val(),
  		// Km_from : Km_from,
  		control: control,
  		// H.ANH  15.08.2016  fix issue when click next/back
  		section_code: section_code
  		// end modification
  	}, function(res) { 
  		if (!res) return;
		$.ajax({
		    type: "post", 
		    url: "/data_map/get_data_popup",
		    data: {
		    	survey_date: survey_date,
				section_code: res,
				layer_tree_value: getLayerTreeValue()
		    },
		    dataType: "json",
		    success: function (data, text) {
		    	active_sections_data = data;
		    	current_section_code = res;
		    	console.log(res);
		    	//console.log(active_sections_data[res]);
		    	$('#pavement_window .modal-body').html("");
		    	$('#pavement_window .modal-body').html(active_sections_data[res]);
		        // hide_loading();
				$('#loading').hide();
		    },
		    error: function (request, status, error) {
		        alert(request.responseText);
		    }
		});

  	});
  	
}

/**
 * remove all current markers and place new one
 */
function placeMarker(latitude, longitude)
{
	for (var i in markers_container)
	{
		markers_container[i].setMap(null);
	}
	resetMarker();
	
	var latlng = new vietbando.LatLng(latitude , longitude);
	marker = new vietbando.Marker({
		position: latlng
	});
	marker.setMap(map);
	markers_container.push(marker);	
	map.setCenter(latlng);
}

function view_img(control)
{
	if (!$('#pavement_window_tab_2').hasClass('active'))
	{
		var current_active = $('#pavement_window_tab_1 .list_img img.active');
		var section = $('tr.selectedSection');
		if (control == 'next')
		{			
			
			if (current_active.next().length)
			{
				$("#slide").data('slider').setValue(($("#slide").data('slider').getValue())+1);
				$('#pavement_window_tab_1 .list_img img').removeClass('active').addClass('hidden');
				current_active.next().removeClass('hidden').addClass('active');
			}		
			else
			{
				var next_section_code = "";
				var section_code = current_section_code;
				var precode = section_code.substring(0, 13);
				var m_from = +section_code.substring(13, 17);
				
				
				if (!$('tr.selectedSection').is(':last-child'))
				{
					section.next().click();
				}	
				else
				{
					view_section_other(control);
					return;
				}			
			}
		}
		else if (control == 'back')
		{
			if (current_active.prev().length)
			{	
				$("#slide").data('slider').setValue(($("#slide").data('slider').getValue())-1);
				//console.log($("#slide").data('slider').getValue());
						
				$('#pavement_window_tab_1 .list_img img').removeClass('active').addClass('hidden');
				current_active.prev().removeClass('hidden').addClass('active');	
			}
			else
			{
				var prev_section_code = "";
				var section_code = current_section_code;
				var precode = section_code.substring(0, 13);
				var m_from = +section_code.substring(13, 17);
				if (m_from != 0)
				{							
					section.prev().click();
					$('#pavement_window_tab_1 .list_img img:first').removeClass('active').addClass('hidden');	
					$('#pavement_window_tab_1 .list_img img:last').removeClass('hidden').addClass('active');
					$("#slide").data('slider').setValue($('#pavement_window_tab_1 .list_img img').length);
				}
				else 
				{
					view_section_other(control);
					return;
				}
			}
		}
			
	}
	else
	{
		var current_active = $('#pavement_window .list_img div.active');
		if (control == 'next')
		{
			if (current_active.next().length)
			{
				$("#slide").data('slider').setValue(($("#slide").data('slider').getValue())+1);
				$('#pavement_window_tab_2 .list_img div').removeClass('active').addClass('hidden');
				current_active.next().removeClass('hidden').addClass('active');	
			}
		}
		else if (control == 'back' && current_active.prev().length)
		{
			$("#slide").data('slider').setValue(($("#slide").data('slider').getValue())-1);
			$('#pavement_window_tab_2 .list_img div').removeClass('active').addClass('hidden');
			current_active.prev().removeClass('hidden').addClass('active');
		}
	}
	$('#loading').hide();
}

/**
 * handle change lane when pressing up/down
 */
function change_lane(control)
{
	var section_code = current_section_code;
	var route_branch = section_code.substring(0, 5);
	var direction = section_code.substring(5, 6);
	var survey_lane = section_code.substring(6, 7);
	var the_rest = section_code.substring(7, section_code.length);
	var new_direction, new_survey_lane;
	
	if (control == 'up')
	{
		if (direction == 2)
		{
			if (survey_lane >= 2)
			{
				new_direction = direction;
				new_survey_lane = +survey_lane - 1;
			}
			else
			{
				new_direction = "1";
				new_survey_lane = "1";
			}
		}
		else
		{
			new_direction = direction;
			new_survey_lane = +survey_lane + 1;
		}		
	}
	else if (control == 'down')
	{
		if (direction == 1)
		{
			if (survey_lane >= 2)
			{
				new_direction = direction;
				new_survey_lane = +survey_lane - 1;
			}
			else
			{
				new_direction = "2";
				new_survey_lane = "1";
			}
		}
		else
		{
			new_direction = direction;
			new_survey_lane = +survey_lane + 1;
		}		
	}
	else
	{
		return;
	}
	var new_section_code = route_branch + new_direction + new_survey_lane + the_rest;
	
	if (typeof active_sections_data[new_section_code] != 'undefined')
	{
		current_section_code = new_section_code;
		$('#pavement_window .modal-body').html(active_sections_data[new_section_code]);
		for (var i in polylines_container)
		{
			if (polylines_container[i].section_code == new_section_code)
			{
				var polyline = polylines_container[i].polyline;
				var dataset = polyline.getPath().getArray();
				placeMarker(dataset[Math.floor(dataset.length/2)].Latitude, dataset[Math.floor(dataset.length/2)].Longitude);
				break;
			}
		}
		
	}
	
	$('#loading').hide();
}

/**
 * selectSection
 * select a section and view it
 */
function selectSection(new_section_code, moveBack)
{
	console.log(new_section_code);
	if (typeof active_sections_data[new_section_code] != 'undefined')
	{
		current_section_code = new_section_code;
		//console.log(new_section_code);
		$('#pavement_window .modal-body').html(active_sections_data[new_section_code]);
		if (moveBack)
		{
			$('#pavement_window_tab_1 .list_img img').removeClass('active').addClass('hidden');
			$('#pavement_window_tab_1 .list_img img').last().removeClass('hidden').addClass('active');
		}
		for (var i in polylines_container)
		{
			if (polylines_container[i].section_code == new_section_code)
			{
				var polyline = polylines_container[i].polyline;
				var dataset = polyline.getPath().getArray();
				placeMarker(dataset[Math.floor(dataset.length/2)].Latitude, dataset[Math.floor(dataset.length/2)].Longitude);
				break;
			}
		}
	}
}

$(document).ready(function(){
	$("body").keydown(function(e) {
	  	if(e.keyCode == 37) { // left
	  		if ($('#pavement_window').hasClass('in'))
	  		{
	  			view_img('back');
	  		}
	  	} else if(e.keyCode == 38) { // up
	  		if ($('#pavement_window').hasClass('in'))
	  		{
	  			e.preventDefault();
	  			change_lane('up');
	  		}
	  	} else if(e.keyCode == 39) { // right
	    	if ($('#pavement_window').hasClass('in'))
	  		{
	  			view_img('next');
	  		}
	  	} else if(e.keyCode == 40) { // down
	    	if ($('#pavement_window').hasClass('in'))
	  		{
	  			e.preventDefault();
	  			change_lane('down');	
	  		}
	  	}
	});	
});

/**
 * prefix 0 to number
 * @param {Object} nr
 * @param {Object} n
 * @param {Object} str
 */
function padLeft(nr, n, str){
    return Array(n-String(nr).length+1).join(str||'0')+nr;
}

function preload(arrayOfImages) {
    $(arrayOfImages).each(function(){
    	if (this)
    	{
    		console.log('here');
			$('<img/>')[0].src = "/assets/data/" + this;    		
    	} 
        // Alternatively you could use:
        // (new Image()).src = this;
    });
}