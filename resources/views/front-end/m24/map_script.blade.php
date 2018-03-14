<script>
	var search_lat, search_lng;

	$(function() {
	  	$('#modal_link').click(function() {
			$('#dialog-message').dialog('open');
			return false;
		});

		$("#dialog-message").dialog({
			autoOpen: false,
			maxWidth: 600,
        	height:'auto',
			modal: true,
			width: "509px",
			title: "{{trans('map.search')}}",
			buttons: [{
				html: "{{trans('map.Cancel')}}",
				class: "btn btn-default",
				click: function() {
					$(this).dialog("close");
				}
			}, {
				html: "<i class='fa fa-check '></i>&nbsp; {{trans('map.search')}}",
				class: "btn btn-primary search_submit",
				click: function() {
					if (loadRoadDataSearch())
					{
						$(this).dialog("close");
					}
				}
			}]
	
		}).dialog("widget").draggable({ containment: "none", scroll: false });		/*
		 * Remove focus from buttons
		 */
		$('.ui-dialog :button').blur();
	});


	// search bar
	$('.image-search img').on('click', function(){
		$('.image-drop').show();
		$('.image-search').hide();
		$('.frm-search').show();
	});
	$('.image-drop img').on('click', function(){
		$('.image-search').show();
		$('.image-drop').hide();
		$('.frm-search').hide();
	});
	
	$("input[type='number']").keypress(function (evt) {
		if (evt.which !== 8 && evt.which !== 0 && (evt.which < 48 || evt.which > 57) && evt.which !== 188) {
			return false;
		}
	});
	
	function loadRoadDataSearch() {
		var kilopost_from = $('input[name="kilopost_from"]').val();
		var	kilopost_to = $('input[name="kilopost_to"]').val();
		if (kilopost_from != '' || kilopost_to != '') {
			if ( +kilopost_from > +kilopost_to) {
				alert("{{trans('map.kilopost_notValid')}}");
				return false;
			}
		} 

		var rmb_id = +rmb_select.val();
		var sb_id = +sb_select.val();
		var route_id = +route_select.val();
		var year = year_select.val();
		if (year == '') {
			year = 'latest';
		}
        showLoading();
		$.post("search_map", {
			_token: "{{ csrf_token() }}",
			rmb_id: rmb_id,
			sb_id: sb_id,
			branch_id: route_id,
			date_y: year,
			kilopost_from: kilopost_from,
			kilopost_to: kilopost_to
		}, function(res) {
			if (res.lat == 0 && res.lng == 0) {
				alert('{{trans("map.no_record_found")}}');
				hideLoading();
				return false;
			}
			resetMap();
			map.setZoom(res.zoom);
           // map.zoomFitEx(Array(LatLng(res.lat, res.lng)));
			map.setCenter(new vietbando.LatLng(res.lat, res.lng));
              
	  	});
	  	return true;
	}
	
	var rmb_select = $('#rmb_id'),
        sb_select = $('#sb_id'),
        route_select = $('#road_name'),
        year_select = $('#year_name');

    function reloadOptions(selector, options) {
        selector.empty();
        var opts = [];
        $.each(options, function (ix, val) {
            var option = $('<option>').text(val.title).val(val.value);
            opts.push(option);
        });
        selector.html(opts);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    function loadSB() {
        var rmb_id = +rmb_select.val();
        var url = '/ajax/rmb/' + rmb_id + '/sb';
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: -1,
                title: '{{ trans("map.all_sb") }}'
            }];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['organization_name']
                });
            }
            reloadOptions(sb_select, data);
            sb_select.trigger("change");
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadRoute() {
        var sb_id = +sb_select.val();
        var rmb_id = +rmb_select.val();
        var url = '/ajax/sb/' + sb_id + '/route?rmb_id=' + rmb_id;
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
            var data = [{
                value: -1,
                title: '{{ trans("back_end.all_route") }}'
            }];
            console.log('--------------------');
            console.log(response);
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['route_name']
                });
            }
            reloadOptions(route_select, data);
            route_select.trigger("change");
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadYear() {
        var route_id = +route_select.val();
        var sb_id = +sb_select.val();
        var rmb_id = +rmb_select.val();
        var url = '/get_year_value?rmb_id='+rmb_id+'&sb_id='+sb_id+'&branch_id='+route_id;
        $.ajax({
            url: url,
            method: 'GET'
        })
        .done(function(response) {
        	console.log('year' + response);
            var data = [];
            for (var i in response) {
            	if (response[i]['text'] == '****') {
            		continue;
            	}
                data.push({
                    value: response[i]['value'],
                    title: response[i]['text']
                });
            }
            reloadOptions(year_select, data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    $('document').ready(function(){
        setOnChangeEvent();
        loadRoute();
    });

    function setOnChangeEvent() {
        sb_select.change(loadRoute);
        rmb_select.change(loadSB);
        route_select.change(loadYear);
    }
</script>	

		

		