
<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
    
    <tr>
        <th>{{trans('back_end.completion_date') }}:</th>
        <td></td>
        <td>{{@$show['completion_date']}}</td>
        <th>{{trans('back_end.repair_category')}}:</th>
        <td>{{@$show['r_category_id']}}</td>
    </tr>
    <tr>
        <th>{{trans('back_end.repair_duration')}}:</th>
        <td></td>
        <td>{{@$show['repair_duration']}} month</td>
        <th>{{trans('back_end.date_collection')}}:</th>
        <td>{{@$show['survey_time']}}</td>
    </tr>
    <tr>
        <th>{{trans('back_end.actual_length') }}:</th>
        <td></td>
        <td>{{@$show['actual_length']}} m</td>
        <th>{{trans('back_end.repair_classification')}}:</th>
        <td>{{@$show['r_classification_id']}}</td>
    </tr>
    <tr>
        <th>{{trans('back_end.total')}}:</th>
        <td></td>
        <td>{{@$show['total']}} m</td>
        <th></th>
        <td></td>
    </tr>
</table>