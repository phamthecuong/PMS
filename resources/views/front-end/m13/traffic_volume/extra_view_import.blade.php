<?php ?>
<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
    
    <tr>
        <th>{{trans('back_end.total_traffic_volume_down') }}:</th>
        <td></td>
        <td>{{@$show['traffic_volume_total']}}</td>
        <th></th>
        <td></td>
    </tr>
    <tr>
        <th>{{trans('back_end.Total heavy traffic volume')}}:</th>
        <td></td>
        <td>{{@$show['heavy_traffic_total']}}</td>
        <th></th>
        <td></td>
    </tr>
    <tr>
        <th>{{trans('back_end.total') }}:</th>
        <td></td>
        <td>{{@$show['grand_total']}}</td>
        <th></th>
        <td></td>
    </tr>
</table>