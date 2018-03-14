@if(isset($edit))
	<a href="/{{$edit['link']}}" class="btn btn-info btn-flat" title="{!! $edit['edit'] !!}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
	background-color: #3c8dbc;"> <i class="fa fa-pencil" aria-hidden="true"></i> </a>
@endif

@if(isset($delete))
{!! Form::open(array( 'method' => 'DELETE', 'style' => 'float:left; margin-right: 3px;','id' => $delete['id'], 'route' => array($delete['action'],$delete['id']))) !!}
<?php
    if (App::getLocale() == 'en')
    {
        $yes = 'Yes'; $no = 'No';
    }
    else
    {
        $yes = 'Có'; $no = 'Không';
    }
 ?>
    <button type="button" id = "{{$delete['title'].$delete['id']}}"  onclick="remove(
    {{$delete['id']. ","."'".$delete['title']."'".","."'".$yes."'".","."'".$no."'"}});" class="btn btn-info btn-flat" title="{{$delete['delete']}}" style="border-radius: 5px;width: 34px;background-color:#dd4b39; border:1px solid #dd4b39; padding-right: 22px;">
        <i class="fa fa-trash-o" aria-hidden="true"></i>
    </button>
    <style type="text/css">
        .btnsubmit{
            pointer-events:none; 
            cursor:not-allowed;
        }
    </style>
{!! Form::close() !!}
<script src="{{ asset('/admin/js/delete_datatable.js') }}" type="text/javascript"></script>
@endif

@if(isset($deleteSB))
{!! Form::open(array( 'method' => 'POST', 'style' => 'float:left; margin-right: 3px;','id' => $deleteSB['id'], 'route' => array($deleteSB['action'],$deleteSB['id']))) !!}
    <button type="button" id = "{{$deleteSB['title'].$deleteSB['id']}}"  onclick="remove({{$deleteSB['id'].","."'". $deleteSB['title']."'"}});" class="btn btn-info btn-flat" title="{{$deleteSB['delete']}}" style="border-radius: 5px;width: 34px;background-color:#dd4b39; border:1px solid #dd4b39; padding-right: 22px;">
        <i class="fa fa-trash-o" aria-hidden="true"></i>
    </button>
    <style type="text/css">
        .btnsubmit{
            pointer-events:none; 
            cursor:not-allowed;
        }
    </style>
{!! Form::close() !!}
<script src="{{ asset('/admin/js/delete_datatable.js') }}" type="text/javascript"></script>
@endif

@if(isset($change_password))
<a href="/{{$change_password['link']}}" class="btn btn-info btn-flat" title="{{$change_password['change_password']}}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
background-color: #3c8dbc;"> <i class="fa fa-key" aria-hidden="true"></i> </a>
@endif

@if(isset($active))
    @if($active['check'] == 0)
        <a href="{{$active['link']}}/active" class="btn btn-info btn-flat" title="{!! $active['active'] !!}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
        background-color: #3c8dbc;"> <i class="fa fa-check" aria-hidden="true"></i> </a>
    @else
        <a href="{{$active['link']}}/notactive" class="btn btn-info btn-flat" title="{!! $active['not_active'] !!}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
        background-color: #3c8dbc;"> <i class="fa fa-times" aria-hidden="true"></i> </a>
    @endif
@endif

@if(isset($SB))
    <a href="/{{$SB['link']}}" class="btn btn-info btn-flat" title="{!! $SB['title'] !!}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
    background-color: #3c8dbc;"> <i class="fa fa-user" aria-hidden="true"></i> </a>
@endif

@if(isset($Merge))
    <a href="/{{$Merge['link']}}" class="btn btn-info btn-flat" title="{!! $Merge['title'] !!}" style="margin-right: 3px;padding-right:22px;width: 34px;border-radius: 5px;
    background-color: #3c8dbc;"> <i class="fa fa-wrench" aria-hidden="true"></i> </a>
@endif

