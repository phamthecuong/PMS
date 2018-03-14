<?php 
    $error_1 = (in_array(1, $err_id)) ? 'class="error"' : '';
    $error_2 = (in_array(2, $err_id)) ? 'class="error"' : '';
    $error_3 = (in_array(3, $err_id)) ? 'class="error"' : '';
    $error_4 = (in_array(4, $err_id)) ? 'class="error"' : '';
    $error_5 = (in_array(5, $err_id)) ? 'class="error"' : '';
    $direction_running = ($mh['direction_running'] == 0) ? trans('back_end.left') : trans('back_end.right');
?>

<div class="ScrollStyle">
    <table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
        <tbody>
            <tr>
                <th>{{ trans('back_end.date_collection') }}:</th>
                <td>{!! @$mh['survey_time'] !!}</td>

                <th <?php echo $error_2; ?> >{{ trans('back_end.completion_date') }}:</th>
                <td <?php echo $error_2; ?> >{!! @$mh['completion_date'] !!}</td>

                <th <?php echo $error_3; ?> >{{ trans('back_end.repair_duration') }}:</th>
                <td <?php echo $error_3; ?> >{!! @$mh['repair_duration'] !!}</td>
            </tr>
            <tr>
                <th>{{ trans('back_end.repair_method') }}:</th>
                <td>{!! @$mh['repair_method']['name'] !!}</td>

                <th>{{ trans('back_end.repair_structtype') }}:</th>
                <td>{!! @$mh['repair_struct_type']['name'] !!}</td>

                <th <?php echo $error_1; ?> >{{ trans('back_end.actual_length') }}:</th>
                <td <?php echo $error_1; ?> >{!! @$mh['actual_length']. ' m' !!}</td>
            </tr>
            <tr>
                <th>{{ trans('back_end.repair_classification') }}:</th>
                <td>{!! @$mh['repair_classification']['name'] !!}</td>

                <th>{{ trans("back_end.repair_category") }}:</th>
                <td>{!! @$mh['repair_category']['name'] !!}</td>

                <th></th>
                <td></td>
            </tr>
            <tr>
                <th>{{ trans('back_end.direction_running') }}:</th>
                <td>{!! $direction_running !!}</td>

                <th <?php echo $error_5; ?> >{{ trans('back_end.distance_to_center') }}:</th>
                <td <?php echo $error_5; ?> >{!! @$mh['distance'] !!}</td>

                <th></th>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>