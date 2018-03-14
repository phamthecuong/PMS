@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_repair_matrix')
    active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            {{trans('menu.back_end')}}
        </li>
        <li>
            {{trans('menu.repair_matrix')}}
        </li>
        <li>{{ trans('menu.matrix_setting') }}</li>
    </ol>
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-table',
        'text1' => trans('menu.repair_matrix'),
        'text2' => $repair_matrix_name
    ])

    <section id="widget-grid">
    	<div class="well" style="text-align: right">		
			<a href="javascript:void(0);" class="btn btn-labeled btn-success" onclick="saveMatrix()"> 
				<span class="btn-label"><i class="glyphicon glyphicon-thumbs-up"></i></span>
				{{trans('back_end.save_your_setting') }}
			</a>
		</div>		

        <div class="row">
        	<article class="col-lg-3 col-md-12 col-xs-12">
	            @box_open(trans('back_end.matrix_parameters'))
	                <div>
	                    <div class="widget-body">
			                @include('custom.form_select', [
			                    'title' => trans('back_end.road_category'),
			                    'items' => \App\Models\mstRoadCategory::allOptionToAjax(trans('back_end.select_an_item'), true),
			                    'name' => 'road_category',
			                    'hint' => '',
			                    'value' => ''
			                ])
			                @include('custom.form_select', [
			                    'title' => trans('back_end.road_class'),
			                    'items' => \App\Models\mstRoadClass::allOptionToAjax(trans('back_end.select_an_item'), 0),
			                    'name' => 'road_class[0]',
			                    'hint' => '',
			                    'value' => ''
			                ])
			                @include('custom.form_select', [
			                    'title' => trans('back_end.road_class'),
			                    'items' => \App\Models\mstRoadClass::allOptionToAjax(trans('back_end.select_an_item'), 1),
			                    'name' => 'road_class[1]',
			                    'hint' => '',
			                    'value' => ''
			                ])
			                @include('custom.form_select', [
			                    'title' => trans('back_end.pavement_type'),
			                    'items' => \App\Models\mstSurface::getData(),
			                    'name' => 'pavement_type',
			                    'hint' => '',
			                    'value' => ''
			                ])
		                </div>
		            </div>
		        @box_close
            </article>
            <article class="col-lg-9 col-md-12 col-xs-12">
                @box_open(trans('back_end.matrix_parameters'))
	                <div>
	                    <div class="widget-body">
	                    	<div class="matrix-setup">
	                    	@foreach ($road_category as $rc)
	                    		@foreach ($road_class as $rclass)
	                    			@foreach ($pavement_type as $pt)
	                    				<table class="table table-bordered no-border center" style="table-layout: fixed; white-space: nowrap; font-size: 11px;position: relative;" id="matrix-{{$rc->code_id}}-{{$rclass->code_id}}-{{$pt->code_id}}">
											@if ($pt->code_id != 3)
												<tr>
													<td class="no-border"></td>
													@foreach ($rut_ranks as $rindex => $r)
														<td class="no-border">
															{{\Helper::convertConditionInforToText($r->from, $r->to, 'Rut')}}
														</td>
													@endforeach
												</tr>
												@foreach ($crack_ranks as $cindex => $c)
													<tr>
														<td class="no-border">
															{{\Helper::convertConditionInforToText($c->from, $c->to, 'C')}}
														</td>
														@foreach ($rut_ranks as $rindex => $r)
															<td class="@if ($cindex == count($crack_ranks)-1) border-bottom @endif @if ($rindex == 0) border-left @endif @if ($rindex == count($rut_ranks)-1) border-right @endif">
																{!!\Helper::generateMatrixCell($rc, $rclass, $pt, $cindex, $rindex, $zones, $matrix, @$saved_zone)!!}
															</td>
														@endforeach
													</tr>
												@endforeach
											@else
												<tr>
													<td class="no-border"></td>
													<td class="no-border"></td>
												</tr>
												@foreach ($crack_ranks as $cindex => $c)
													<tr>
														<td class="no-border">
															{{\Helper::convertConditionInforToText($c->from, $c->to, 'C')}}
														</td>
														
														<td class="@if ($cindex == count($crack_ranks)-1) border-bottom @endif border-left border-right">
															{!!\Helper::generateMatrixCell($rc, $rclass, $pt, $cindex, 0, $zones, $matrix, @$saved_zone)!!}
														</td>
													</tr>
												@endforeach
											@endif
										</table>
									@endforeach
								@endforeach
							@endforeach
							</div>
							<div class="row">
								@foreach ($zones as $z)
								<div class="col-lg-4">
									<table class="table no-border">
										<tr>
											<td><span style="background: {{$z['color']}};width: 30px;display: block;height: 20px"></span></td>
											<td>{{$z['name']}}</td>
										</tr>
									</table>
								</div>
								@endforeach
							</div>

	                    </div>
		            </div>
		        @box_close
            </article>
        </div>
    </section>

@endsection

@push('css')
	<style type="text/css">		
		.no-border {
			border: none !important;
		}
		
		.center {
			text-align: center;
		}
		.border-left {
			border-left: 1px solid #ddd !important;
		}
		.border-right {
			border-right: 1px solid #ddd !important;
		}
		.border-bottom {
			border-bottom: 1px solid #ddd !important;
		}
		.full-width {
			width: 100%;
			min-width: 50px;
		}
		[data-color=method-0] {
			background: transparent;
		}
		[data-color=method-0] .caret {
			color: black;
		}
		@foreach ($zones as $z)
		[data-color=method-{{$z['id']}}] {
			background: {{$z['color']}};
		}
		@endforeach
		.matrix-setup > table {
			display: none;
		}
		.matrix-active {
			display: block !important;
		}
		.jarviswidget-ctrls {
			display: none;
		}
		.list-select-method>li>a {
		    white-space: normal !important;
		}
		.list-select-method {
			min-width: 300px;
		}
	</style>
@endpush

@push('script')
	<script type="text/javascript">
		var $road_category = $('select[name="road_category"]');
		var $pavement_type = $('select[name="pavement_type"]');
		var matrix = JSON.parse('{!! json_encode($matrix) !!}');
		var nochange = true;

		function getRoadClassEl() {
			return $('select[name="road_class[' + $road_category.val() + ']"]');
		}

		function selectItem(el, method_id, road_category, road_class, pavement_type, crack_index, rut_index) {
			$(el).parent().parent().prev().attr('data-color', 'method-' + method_id);
			matrix[road_category][road_class][pavement_type][crack_index][rut_index] = method_id;
			nochange = false;
		}

		function changeRoadClass() {
			$('select[name^="road_class"]').parent().hide();
			getRoadClassEl().parent().show();
			showMatrix();
		}

		function showMatrix() {
			var selected_rc = $road_category.val();
			var selected_rclass = getRoadClassEl().val();
			var selected_pt = $pavement_type.val();
			$('.matrix-active').removeClass('matrix-active');
			$('#matrix-' + selected_rc + '-' + selected_rclass + '-' + selected_pt).addClass('matrix-active');
		}

		function saveMatrix()
		{
			showLoading();
			if (nochange) {
				window.location = '/admin/repair_matrix';
			} else {
				var url = '/ajax/repair_matrix';
	            $.ajax({
	                url: url,
	                method: 'POST',
	                data: {
	                    repair_matrix_id: {{$repair_matrix_id}},
	                    matrix: matrix
	                }
	            })
	            .done(function(response) {
	                if (response.code == 200) {
	                	alert('{{trans('back_end.success')}}');
	                	window.location = '/admin/repair_matrix';
	                } else {
	                	alert(response);
	                }
	            })
	            .fail(function(jqXHR, textStatus, errorThrown) {
	                alert(errorThrown);
	            })
			}

		}

		$(document).ready(function(){
			$('select[name="road_class[1]"]').parent().hide();
			$road_category.change(changeRoadClass);
			$('select[name^="road_class"]').change(showMatrix);
			$pavement_type.change(showMatrix);
		});
	</script>
@endpush
