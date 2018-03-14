<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
	<tr>
		<th></th>
		<th>{{trans('back_end.survey_time')}}</th>
		<th>{{trans('back_end.total_traffic_volume')}}</th>
		<th>{{trans('back_end.heavy_traffic')}}</th>
		<th></th>
	</tr>
	<tr>
		<td>{{trans('back_end.info')}}:</td>
		<td>
			{{@$traffic_volume->survey_time}}
		</td>
		<td>
			{{@$traffic_volume->total_traffic_volume_up}} ({!! trans('back_end.up') !!}) <br/>
			<small class="text-muted">
				<i>
				{{@$traffic_volume->total_traffic_volume_down}} ({!! trans('back_end.down') !!})
				</i>
			</small>
		</td>
		<td>
			{{@$traffic_volume->heavy_traffic_up}} ({!! trans('back_end.up') !!}) <br/>
			<small class="text-muted">
				<i>
				{{@$traffic_volume->heavy_traffic_down}} ({!! trans('back_end.down') !!})
				</i>
			</small>
		</td>
		<td>
			<!-- <a class="btn btn-xs btn-danger pull-right" style="margin-left:5px" href="#" onclick="return confirm('{{trans('back_end.are_you_sure')}}')">
				{{trans('back_end.delete_record')}}
			</a> -->
		</td>
	</tr>
	<tr>
		<td>{{trans('back_end.action')}}:</td>
		<td>
			<a class="btn btn-xs btn-success" href="/admin/traffic_volume/{{@$traffic_volume->id}}/edit">
				{{trans('back_end.edit_record')}}
			</a>
		</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>