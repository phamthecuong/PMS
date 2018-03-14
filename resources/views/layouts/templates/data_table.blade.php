<table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead>
        <tr role="row">
            @foreach($header as $h=>$key)
                <th class="sorting" tabindex="4" aria-controls="example2" rowspan="1" colspan="1" > {{$key}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($value as $h=>$key)
            <tr>
                <?php $c = count($key);?>
                @for($i = 0; $i < count($key)-6; $i++)
                    @if($i == 0)
                    <td><a href="{{$key[$i]}}">{{$key[$i+1]}}</a></td>
                    @else
                    <td>{{$key[$i+1]}}</td>
                    @endif
                @endfor
                <td>
                    <div class="btn-group">
                        <a href="{{@$key[$c-5]}}" class="btn btn-info btn-flat" title="{{@$key[$c-4]}}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
                        background-color: #3c8dbc;">
                            <i class="{{$key[$c-3]}}" aria-hidden="true"></i>
                        </a>
                        {!! Form::open(array( 'method' => 'DELETE', 'style' => 'float:left','id' => $key[$c-1], 'route' => array($key[$c-2],$key[$c-1]))) !!}
                            <button type="button" onclick="remove({{$key[$c-1]}})" class="btn btn-info btn-flat" title="{{trans('back_end.delete')}}" style="border-radius: 5px;width: 34px;background-color:#dd4b39; border:1px solid #dd4b39; padding-right: 22px;">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </button>
                            <input type="hidden" name="id"/>
                        {!! Form::close() !!}
                        
                        
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
   
    function remove()
    {
        // bootbox.alert("This is the default alert!");
        // var x = confirm("Are you sure you want to delete?");
        // var x = confirm({!! trans('back_end.sure_want_delete') !!});
        // if (x) {
            // return true;
        // }
        // else {
            // return false;
        // }
        // bootbox.confirm({
            // message: "This is a confirm with custom button text and color! Do you like it?",
            // buttons: {
                // confirm: {
                    // label: 'Yes',
                    // className: 'btn-success'
                // },
                // cancel: {
                    // label: 'No',
                    // className: 'btn-danger'
                // }
            // },
            // callback: function (result) {
                // console.log('This was logged in the callback: ' + result);
            // }
            // });
    }
</script>