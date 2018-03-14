    <table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed">
   
    <tr>
        <td style="width: 300px">{{trans('back_end.completion_date')}}:</td>
        <td>
            <strong>{{$h->completion_date}}</strong>
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.repair_duration')}}:</td>
        <td>
            {{$h->repair_duration}}<small> {!! trans('back_end.month') !!}</small>
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.actual_length')}}:</td>
        <td>
            {{$h->actual_length}}<small> {!! trans('back_end.m') !!}</small>
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.total_width_repair_lane')}}:</td>
        <td>
            {{$h->total_width_repair_lane}}<small> {!! trans('back_end.m') !!}</small>
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.repair_categories')}}:</td>
        <td>
            {{@$h->repair_category->name}}
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.repair_structtypes')}}:</td>
        <td>
            {{@$h->repair_structtype->name}}
        </td>
    </tr>
    <tr>
        <td>{{trans('back_end.repair_classifications')}}:</td>
        <td>
            {{@$h->repair_classification->name}}
        </td>
    </tr>
<tr>
    <td>{{trans('back_end.action')}}:</td>
    <td>
        <a class="btn btn-xs btn-success" href="/admin/maintenance_history/{{@$h->id}}/edit">
            {{trans('back_end.edit_record')}}
        </a>
    </td>
</tr>
</table>