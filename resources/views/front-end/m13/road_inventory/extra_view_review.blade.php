<?php 
    $error_1 = (in_array(1, $err_id)) ? 'class="error"' : '';
    $error_2 = (in_array(2, $err_id)) ? 'class="error"' : '';
    $error_3 = (in_array(3, $err_id)) ? 'class="error"' : '';
    $error_4 = (in_array(4, $err_id)) ? 'class="error"' : '';
    $error_5 = (in_array(5, $err_id)) ? 'class="error"' : '';
    $error_6 = (in_array(6, $err_id)) ? 'class="error"' : '';
?>

<div class="ScrollStyle">
    <table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
        <tbody>
            <tr>
                <th>{{ trans('back_end.date_collection') }}:</th>
                <td>{!! @$rmd['survey_time'] !!}</td>
                <th <?php echo $error_2; ?> >{{ trans('back_end.construct_year') }}:</th>
                <td <?php echo $error_2; ?> >{!! substr((string) @$rmd['construct_year'], 0, 4). "/" . substr((string) @$rmd['construct_year'], 4, 2) !!}</td>
                <th <?php echo $error_6; ?> >{{ trans('back_end.annual_precipitation') }}:</th>
                <td <?php echo $error_6; ?> >{!! @$rmd['annual_precipitation'].' mm' !!}</td>
            </tr>
            <tr>
                <th>{{ trans('back_end.terrain_type') }}:</th>
                <td>{!! @$rmd['terrian_type']['name'] !!}</td>
                <th <?php echo $error_3; ?> >{{ trans('back_end.service_start_year') }}:</th>
                <td <?php echo $error_3; ?> >{!! substr((string) @$rmd['service_start_year'], 0, 4). "/" .substr((string) @$rmd['service_start_year'], 4, 2) !!}</td>
            
                <th <?php echo $error_1; ?> >{{ trans('back_end.actual_length') }}:</th>
                <td <?php echo $error_1; ?> >{!! @$rmd['actual_length']. ' m' !!}</td>
                
            </tr>
            <tr>
                <th>{{ trans('back_end.road_class') }}:</th>
                <td>{!! @$rmd['route_class']['name'] !!}</td>
                <th <?php echo $error_5; ?> >{{ trans('back_end.Temperature') }}:</th>
                <td <?php echo $error_5; ?> >{!! @$rmd['temperature'] !!}</td>
                <th>{{ trans('back_end.no_of_lane') }}:</th>
                <td>{!! @$rmd['no_lane'] !!}</td>
            </tr>
        </tbody>
    </table>
</div>