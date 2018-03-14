<script>
	$(function() {
		loadD('fro_to', 'dis_to');
		loadD('fro_pro', 'dis_pro');
	});
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

    function loadD(P_name, D_name) {
        var P_id = +$('#'+P_name).val();
        var D_select = $('#' + D_name);
        var url = '/ajax/frontend/province/' + P_id + '/district';
        if (P_id != 0) {
        	$.ajax({
	            url: url,
	            method: 'GET'
	        })
	        .done(function(response) {
	        	console.log(response);
	            var data = [{value: -1, title: '{{trans("back_end.chose your item")}}'}];
	            for (var i in response) {
	                data.push({
	                    value: response[i]['id'],
	                    title: response[i]['name']
	                });
	            }
	            reloadOptions(D_select, data);
	            if (D_name == 'dis_to') {
	            	var selected_id = "{{ ( @$segment->distto_id > 0) ? @$segment->distto_id : -1 }}";
	            } else {
	            	var selected_id = "{{ (@$segment->distfrom_id > 0)  ? @$segment->distfrom_id : -1 }}";
	            }
	          	D_select.val(selected_id).trigger("change");
	        })
	        .fail(function(jqXHR, textStatus, errorThrown) {
	            //alert(errorThrown);
	            D_select.val(-1).trigger('change');
	        })
        } else {
        	D_select.val(-1).trigger('change');
        }
    }

    function loadW(D_name,W_name) {
        var D_id = +$('#'+ D_name).val();
        var W_select = $('#' + W_name);
        var url =  '/ajax/frontend/district/' + D_id + '/ward';
        if (D_id > 0) {
    		$.ajax({
	            url: url,
	            method: 'GET'
	        })
	        .done(function(response) {
	            var data = [{value: -1, title: '{{trans("back_end.chose your item")}}'}];
	            for (var i in response) {
	                data.push({
	                    value: response[i]['id'],
	                    title: response[i]['name']
	                });
	            }
	            
	            reloadOptions(W_select, data);
	            if (W_name == 'commune_fro') {
	            	var selected_id = "{{ is_numeric(@$segment->commune_from) > 0 ? @$segment->commune_from : -1}}";
	            } else {
	            	var selected_id = "{{ is_numeric(@$segment->commune_to) > 0 ? @$segment->commune_to : -1}}";
	            }
	            	W_select.val(selected_id).trigger("change");
	        })
	        .fail(function(jqXHR, textStatus, errorThrown) {
	            //alert(errorThrown);
	            W_select.val(-1).trigger("change");
	        })
        } else {
        	W_select.empty();
        	W_select.val(-1).trigger("change");
        }

    }

</script>

