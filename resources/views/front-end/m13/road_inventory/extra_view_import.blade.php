
<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
    
    <tr>
        <th>{{trans('back_end.terrain_type') }}:</th>
        <td></td>
        <td>{{@$show['terrian_type_id']}}</td>
        <th>{{trans('back_end.road_class')}}:</th>
        <td>{{@$show['road_class_id']}}</td>
    </tr>
    <tr>
        <th>{{trans('back_end.construct_year')}}:</th>
        <td></td>
        <td>{{@$show['construct_year_y'].'/'.@$show['construct_year_m']}}</td>
        <th>{{trans('back_end.date_collection')}}:</th>
        <td>{{@$show['survey_time']}}</td>
    </tr>
    <tr>
        <th>{{trans('back_end.no_of_lane')}}:</th>
        <td></td>
        <td>{{@$show['no_lane']}}</td>
        <th>{{trans('back_end.actual_length')}}:</th>
        <td>{{@$show['actual_length']}} m</td>
    </tr>
    <tr>
        <th>{{trans('back_end.lane_width')}}:</th>
        <td></td>
        <td>{{@$show['lane_width']}} m</td>
        <th></th>
        <td></td>
    </tr>
</table>