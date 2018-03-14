@include('admin.layouts.partials.scripts')
<table class="table table-bordered" id="users-table">
    <thead>
        <tr>
            @foreach(App\Http\Controllers\Controller::GetConfigTable($case) as $h=>$value)
                @foreach($value as $v=>$va)
                    @if($v == 'header')
                        
                        <th>{{$va}}</th>
                    @endif
                @endforeach
            @endforeach
        </tr>
    </thead>
</table>
<script>
    $(function() {
        var x = {!! json_encode(App\Http\Controllers\Controller::GetConfigTable($case)) !!};
        var json = "[";
        x.forEach(function(object) {
            json = json + "{data: " + "'" + object.data + "'" + 
                ", name: " + "'" + object.name + "'" + 
                ",orderable: " + '"' + object.orderable + "'" +
                ",searchable: " + '"' + object.searchable + "'" +
                "},";
        });
        
        json = json + "]";
        $('#users-table').DataTable({
            type: "GET",
            processing: true,
            serverSide: true,
            ajax: '{!! route('get.ajax.table.data', array("name" => $case)) !!}?{{@$custom}}',
            columns: x

        });
    });
</script>
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		$('#form').formValidation({
			excluded : ':disabled',
			framework : 'bootstrap',
			icon : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
			fields : {
				name_repair_matrix : {
					validators : {
						notEmpty : {
							message : "{{trans('validation.name_repair_matrix_required')}}"
						},
					}
				},
			}
		});
	});
	
	function addRepairMatrix() {
		var fv = $('#form').data('formValidation'),
        $container = $('#form');
	
		fv.validateContainer($container);
		var isValid = fv.isValidContainer($container);
		
		if (isValid !== false && isValid !== null) {
			$('.modal-footer button.show_hide').addClass('disabled');
			// $('form#form').submit();
			$.post("/repair_matrix",
			{
				"name": $('input[name=name_repair_matrix]').val(),
				_token : '{!! csrf_token() !!}'
			},
			function(data) {
				if (data.code == 200) {
					location.href = '/repair_matrix/' + data.id;
				} else {
					location.reload();
				}
			}
			,"json" );
		}
	}
</script>