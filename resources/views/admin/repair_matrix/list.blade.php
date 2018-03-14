@extends('admin.layouts.app_datatable')

@section('sidebar_repair_matrix')
    active
@endsection

@section('repair_matrix')
    active open
@endsection

@section('contentheader_title')
    {{trans('back_end.repair_matrix')}}
@endsection

@section('contentheader_link')
    <li><a href="/repiar_matrix"><i class="fa fa-dashboard"></i> {{ trans('back_end.repair_matrix') }}</a></li>
@endsection

@section('main-content')
<div class="theme-showcase">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{ trans('back_end.repair_matrix') }}</h3>
        </div>
        <div class="box-body">
            @include('admin.templates.datatable')
        </div>
        <div class="box-footer">
            <div class="col-md-2" style="padding-left: 0px;">
            	<button class="btn btn-block btn-primary" data-toggle="modal" data-target="#myModal">{{trans('back_end.add_new')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center"> {{trans('repair_matrix.info')}} </h4>
			</div>
			<div class="modal-body">
	            <!-- <form id="form"> -->
	            {{ Form::open(array('url' => 'repair_matrix/create', 'id' => 'form', 'method' => 'POST')) }}
	              	<div class="box-body">
	                	<div class="form-group">
	                  		<label>{{trans('repair_matrix.name_repiar_matrix')}} :</label>
                    		<input type="text" class="form-control" placeholder="{{trans('repair_matrix.name_repiar_matrix')}}" id="name_repair_matrix" name="name_repair_matrix">
	                	</div>
	              <!-- /.box-body -->
	              	</div>
	            <!-- </form> -->
	            {{ Form::close() }}
          	</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('menu.close')}}</button>
                <button type="button" class="btn btn-primary show_hide" onclick="addRepairMatrix()">{{trans('menu.save')}}</button>
          </div>
		</div>
	</div>
</div>
<!-- @push('script')
<script src="{{ asset('/js/formValidation/formValidation.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/formValidation/validateFormBootstrap.js') }}" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		console.log(0111);
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
							message : "{{trans('validation.name_vi_required')}}"
						},
					}
				},
			}
		});
	});
	
	function test() {
		var fv = $('#form').data('formValidation'),
        $container = $('#form');
	
		fv.validateContainer($container);
		var isValid = fv.isValidContainer($container);
			
		if (isValid !== false && isValid !== null) {
				
		}
	}
</script>
@endpush -->

@endsection
