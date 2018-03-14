@extends('front-end.m24.layout.master')
@section('body.content')
	<div id="map" class="map-block"></div>
	<div id="dialog_simple" title="" style="overflow-x: hidden; ">
		<div class="row">
			<div class="col-lg-7">
				<div id="table_1_lane" class="" style="border: 1px solid #CCCCCC;">
					<table class="table table-striped table-bordered table-hover pc-table table dataTable no-footer" style="border: none;margin-bottom: 0px;font-size: 11px;" id="table-pc" >
						<thead >
							<tr>
								<th rowspan="2" style="background: rgba(255, 0, 129, 0.27);">{{ trans('general.route_name') }}</th>
								<th colspan="2" style="background: rgba(255, 0, 129, 0.27);">{{trans('general.milestone_from')}}</th>
								<th colspan="2" style="background: rgba(255, 0, 129, 0.27);">{{trans('general.milestone_to')}}</th>
								<th rowspan="2">{{ trans('general.survey_date') }}</th>
								<th colspan="4">{{trans('general.cracking_ratio')}}</th>
								<th colspan="2">{{trans('general.rutting_depth')}}</th>
								
								<th rowspan="2">{{ trans('general.iri') }}</th>
								<th rowspan="2">{{ trans('general.mci') }}</th>
								<th rowspan="2">{{ trans('general.direction') }}</th>
								<th rowspan="2">{{ trans('general.survey_lane') }}</th>
								<th rowspan="2">{{ trans('general.road_length') }}</th>
								<th rowspan="2">{{ trans('general.pavement_type') }}</th>
								<th rowspan="2">{{ trans('general.management_agency') }}</th>
								<th rowspan="2">{{ trans('general.structure') }}</th>
								<th rowspan="2">{{ trans('general.intersection') }}</th>
								<th rowspan="2">{{ trans('general.overlapping') }}</th>
								<th rowspan="2">{{ trans('general.number_of_lane_up') }}</th>
								<th rowspan="2">{{ trans('general.number_of_lane_down') }}</th>
							</tr>
							<tr >
								<th style="background: rgba(255, 0, 129, 0.27);">{{ trans('general.km') }}</th>
								<th style="background: rgba(255, 0, 129, 0.27);">{{ trans('general.m') }}</th>
								<th style="background: rgba(255, 0, 129, 0.27);">{{ trans('general.km') }}</th>
								<th style="background: rgba(255, 0, 129, 0.27);">{{ trans('general.m') }}</th>
								<th>{{ trans('general.cracking') }}</th>
								<th>{{ trans('general.patching') }}</th>
								<th>{{ trans('general.pothole') }}</th>
								<th>{{ trans('general.total') }}</th>
								<th>{{ trans('general.max') }}</th>
								<th>{{ trans('general.ave') }}</th>
							</tr>
						</thead>
						<tbody id="body_pc" style="overflow-y: auto"></tbody>			
	 				</table>
				</div>
				<div class="layer_index"></div>
			</div>
			<div class="col-lg-5" style="position: relative;">
				<div id="chainage_info" style=" position: absolute;top: 0px; opacity: 0.5;
				background: white; color: black;left: 14px; width: 478px;">
				</div>
				<div id="preview_image" >
					<img width="100%" src="" />
				</div>
				<div class="brief-chainage-info">
					<div id="item1" class="col-md-4"></div>
					<div id="item2" class="col-md-4" style="text-align: center"></div>
					<div id="item3" class="col-md-4" style="text-align: right"></div>
				</div>
				<div >
					<input type="text" class="span2" value="" data-slider-min="1" data-slider-step="1" data-slider-value="0" data-slider-id="BC" id="slide" data-slider-tooltip="hide" data-slider-handle="triangle" />
				</div>
				
				<div style="margin-top:-30px;">
				<div class="center uppercase" style="border: 1px solid #CCCCCC; border-bottom: none;">{{trans('map.left')}}</div>
				<div id="horizontal_view" style="overflow: auto; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; padding-bottom: 14px;position: relative">

				</div>
				<div class="center uppercase" style="border: 1px solid #CCCCCC; border-top: none;">
					<div class="col-xs-4" id="item1_Routine" style="text-align: left; padding-left: 0px;";></div>
					<div class="col-xs-4" id="item_" style="text-align: center;">{{trans('map.right')}}</div>
					<div class="col-xs-4" id="item2_Routine" style="text-align: right; padding-right: 0px;"></div>

				</div>
				</div>
				<div class="smart-form" style="margin-top: 10px">
					<section>
						<div class="inline-group" id="popup-checkbox">
							<label class="checkbox">
								<input type="checkbox" name="popup-crack" checked="checked">
								<i></i>{{ trans('general.cracking_ratio') }}
							</label>
							<label class="checkbox">
								<input type="checkbox" name="popup-rut">
								<i></i>{{ trans('general.rutting_depth') }}
							</label>
							<label class="checkbox">
								<input type="checkbox" name="popup-iri">
								<i></i>{{ trans('general.iri') }}
							</label>
							<label class="checkbox">
								<input type="checkbox" name="popup-mci">
								<i></i>{{ trans('general.mci') }}
							</label>
						</div>
					</section>
				</div>
				<div class="info_checkbox">
					<div class="text_info">
						<img src="/front-end/image/good.png">
						<span  class="number1">0 - 10(%) ({{trans('map.good')}})</span>
						<span style="display: none;" class="number2">0 - (mm) ({{trans('map.good')}})</span>
						<span style="display: none;" class="number3">0 - 4(mm/m) ({{trans('map.good')}})</span>
						<span style="display: none;" class="number4">5 - ({{trans('map.good')}})</span>
					</div>
					<div class="text_info">
						<img src="/front-end/image/fair.png">
						<span   class="number1">10 - 20 (%) ( {{trans('map.Land')}} )</span>
						<span  style="display: none" class="number2">20 - 30(mm) ({{trans('map.Land')}})</span>	
						<span  style="display: none" class="number3">4 - 6(mm/m) ({{trans('map.Land')}})</span>
						<span  style="display: none" class="number4">5 - 4 ({{trans('map.Land')}})</span>
					</div>
					<div class="text_info">
						<img src="/front-end/image/poor.png">
						<span  class="number1">20 - 40 (%) ( {{trans('map.Bad')}} )</span>
						<span  style="display: none" class="number2">30 - 50(mm) ({{trans('map.Bad')}})</span>
						<span  style="display: none" class="number3">6 - 10(mm/m) ({{trans('map.Bad')}})</span>
						<span  style="display: none" class="number4">4 - 3 ({{trans('map.Bad')}})</span>
					</div>
					<div class="text_info">
						<img src="/front-end/image/bad.png">
						<span  class="number1">40 - (%) ( {{trans('map.Extremely_bad')}})</span>
						<span  style="display: none" class="number2">50 - (mm) ({{trans('map.Extremely_bad')}})</span>
						<span  style="display: none" class="number3">10 - (mm/m) ({{trans('map.Extremely_bad')}})</span>
						<span  style="display: none" class="number4">3 - 0 ({{trans('map.Extremely_bad')}})</span>
					</div>
				</div>
				<div><button class="btn btn-info" id="btn-close" style="margin-left: 412px;">{{trans('map.Close')}}</button></div>
			</div>
		</div>
	</div>
@endsection
		
