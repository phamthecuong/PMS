  <script>
    var rmb_select = $('#rmb_id'),
        sb_select = $('#sb_id'),
        route_select = $('#road_name'),
        segment_select = $('#segment'),
        branch_no_select = $('#branch_no');
     
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
            var data = [{value : -1,title: 'All'}];
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
            var data = [];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['route_name'] +':   '+response[i]['branch_number']
                });
            }
            reloadOptions(route_select, data);
            route_select.trigger("change");
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function loadSegment() {
        var sb_id = +sb_select.val();
        var rmb_id = +rmb_select.val();
        var route_id = +route_select.val();
        var url = '/ajax/route/' + route_id +'/segment?sb_id=' + sb_id + '&rmb_id=' +rmb_id;
        $.ajax({
            url: url,
            method: 'GET',
        })
        .done(function(response) {
            var data = [];
            for (var i in response) {
                data.push({
                    value: response[i]['id'],
                    title: response[i]['segment_info']
                });
            }
            reloadOptions(segment_select, data);
            segment_select.trigger("change");
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    $('document').ready(function(){
        setOnChangeEvent();
        loadSB();
        loadRoute();
    });

    function setOnChangeEvent() {
        sb_select.change(loadRoute);
        rmb_select.change(loadSB);
        route_select.on('change', loadSegment);
    }
</script>

@include('front-end.pmos.script.streching')