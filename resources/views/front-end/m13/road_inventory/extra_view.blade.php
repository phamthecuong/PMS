<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
	
	<tr>
		<td>{{trans('back_end.survey_time')}}:</td>
		<td>
			<strong>{{@$road_inventory->survey_time}}</strong>
			
			
			
		</td>
	</tr>
	<tr>
		<td>{{trans('back_end.action')}}:</td>
		<td>
			<a class="btn btn-xs btn-success" href="/admin/road_inventories/{{$road_inventory->id}}/edit">
				{{trans('back_end.edit_record')}}
			</a> 
		</td>
	</tr>
</table>