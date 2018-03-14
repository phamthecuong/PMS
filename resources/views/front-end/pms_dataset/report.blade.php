@extends('front-end.layouts.app')

@section('side_menu_pms_dataset')
active
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>{{trans('menu.home')}}</li>
        <li>{{trans('menu.pms_dataset_title')}}</li>
    </ol>
@endsection

@section('content')

@include('front-end.layouts.partials.heading', [
    'icon' => 'fa-database',
    'text1' => trans('menu.pms_dataset_title'),
    'text2' => trans('menu.pms_process_report')
])

<section id="widget-grid">	
    
	<div id="accordion">	
    <?php 
        for ($index = 1; $index <= 4 ; $index ++)
        {
    ?>
        <h1>{{ trans('pms_dataset.data_report_rmb'. $index) }}</h1>
        <div>
            <div class="row">            
                <article class="col-lg-6 col-md-12 col-xs-12">
                    @box_open(trans("pms_dataset.summary_result"))  
                    <div>   
                        <div class="widget-body">
                            <legend><b>{!! trans('pms_dataset.summary_result') !!} {!! $year !!}</b></legend>
                            <div class="table-responsive">
                                <table id="MyDataTable" class="table table-bordered">
                                    <tr>
                                        <td rowspan="2" class="title_data">{!! trans('pms_dataset.pc_data') !!}</td>
                                        <td>{!! trans('pms_dataset.total_length') !!}</td>
                                        <td>{{ $pc_total_length[$index] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! trans('pms_dataset.total_100m_pc_section') !!}</td>
                                        <td>{{ $pc_total[$index] }}</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="title_data">{!! trans('pms_dataset.ri_data') !!}</td>
                                        <td>{!! trans('pms_dataset.total_length') !!}</td>
                                        <td>{{ $ri_total_length[$index] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! trans('pms_dataset.total_100m_ri_section') !!}</td>
                                        <td>{{ $ri_total[$index] }}</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="title_data">{!! trans('pms_dataset.mh_data') !!}</td>
                                        <td>{!! trans('pms_dataset.total_length') !!}</td>
                                        <td>{{ $mh_total_length[$index] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! trans('pms_dataset.total_100m_mh_section') !!}</td>
                                        <td>{{ $mh_total[$index] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>  
                    </div>      
                    @box_close      
                </article>
                <article class="col-lg-6 col-md-12 col-xs-12">
                    @box_open(trans("pms_dataset.percentage_bar"))  
                    <div>   
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <span>{!! trans('pms_dataset.pc_match_ri/total_pc') !!}</span>

                                    <span class="pull-right">{!! (!empty($pc_total_length[$index])) ? intval($pc_match_ri_total_length[$index]/$pc_total_length[$index] * 100 ) : 0 !!}%</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{!! (!empty($pc_total_length[$index])) ? intval($pc_match_ri_total_length[$index]/$pc_total_length[$index] * 100) : 0 !!}%">
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>     
                            <div class="row">
                                <div class="col-xs-12">
                                    <span>{!! trans('pms_dataset.pc_match_ri/total_ri') !!}</span>
                                    <span class="pull-right">{!! (!empty($ri_total_length[$index])) ? intval($pc_match_ri_total_length[$index]/$ri_total_length[$index] * 100) : 0 !!}%</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{!! (!empty($ri_total_length[$index])) ? intval($pc_match_ri_total_length[$index]/$ri_total_length[$index] * 100) : 0!!}%">
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-footer" style="overflow:hidden;">
                                <div class="pull-right">
                                    <form id="export_pc_not_match_ri_{!! $index !!}" action="{{ url('user/pms_dataset/export/'. $index. '/'. $year. '/0') }}" method="POST" style="margin-right: 5px; float: left;">
                                        {{ csrf_field() }}
                                        <fieldset>
                                            <input type="hidden" id="download_token_value_pc_{!! $index !!}" name="downloadTokenValue"/>
                                            <input class="btn bg-color-blueLight txt-color-white" type="submit" value="{!!trans('pms_dataset.export_pc_not_match_ri')!!}"/>
                                        </fieldset> 
                                    </form>
                                    <form id="export_ri_not_match_pc_{!! $index !!}" action="{{ url('user/pms_dataset/export/'. $index. '/'. $year. '/1') }}" method="POST" style="float:left;">
                                        {{ csrf_field() }}
                                        <fieldset>
                                            <input type="hidden" id="download_token_value_ri_{!! $index !!}" name="downloadTokenValue"/>
                                            <input class="btn bg-color-blueLight txt-color-white" type="submit" value="{!!trans('pms_dataset.export_ri_not_match_pc')!!}"/>
                                        </fieldset> 
                                    </form>
                                </div>
                            </div>
                        </div>  
                    </div>      
                    @box_close      
                </article>

            </div>
            <div class="row">
                <article class="col-lg-6 col-md-12 col-xs-12">
                    @box_open(trans("pms_dataset.km_section_data"))  
                    <div>   
                        <div class="widget-body">
                            <legend><b>{!! trans('pms_dataset.km_section_data_by_data_type_and_their_availability') !!}</b></legend>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="title_data text-center">{!! trans('pms_dataset.ri') !!}</th>
                                            <th rowspan="2" class="title_data text-center">{!! trans('pms_dataset.mh') !!}</th>
                                            <th rowspan="2" class="title_data text-center">{!! trans('pms_dataset.pc') !!}</th>
                                            <th rowspan="2" class="title_data text-center">{!! trans('pms_dataset.total') !!}</th>
                                            <th colspan="2" class="title_data text-center">{!! trans('pms_dataset.eligible_data') !!}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">{!! trans('pms_dataset.repair_work_planning') !!}</th>
                                            <th class="text-center">{!! trans('pms_dataset.budget_simulation') !!}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                    @foreach ($data[$index] as $key => $value)
                                    <?php $item_key = explode('-', $key);?>
                                        <tr>
                                            <td>{!! ($item_key[0] == 0)? '<i class="fa fa-close" aria-hidden="true"></i>': '<i class="fa fa-check" aria-hidden="true"></i>'; !!}</td>
                                            <td>{!! ($item_key[2] == 0)? '<i class="fa fa-close" aria-hidden="true"></i>': '<i class="fa fa-check" aria-hidden="true"></i>'; !!}</td>
                                            <td>{!! ($item_key[1] == 0)? '<i class="fa fa-close" aria-hidden="true"></i>': '<i class="fa fa-check" aria-hidden="true"></i>'; !!}</td>
                                            <td>{!! ($value['total'] != 0) ? $value['total'] : "" !!}</td>
                                            <td>{!! ($value['wp'] != 0)? $value['wp'] : "" !!}</td>
                                            <td>{!! ($value['budget'] != 0)? $value['budget'] : "" !!}</td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <th colspan="3" class="text-center">{!! trans('pms_dataset.total_km') !!}</th>
                                            <th class="text-center">{!! $total[$index] !!}</th>
                                            <th class="text-center">{!! $total_wp[$index] !!}</th>
                                            <th class="text-center">{!! $total_budget[$index] !!}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <p><i class="fa fa-check" aria-hidden="true"></i> : {!! trans('pms_dataset.check_meaning') !!}</p>
                                <p><i class="fa fa-close" aria-hidden="true"></i> : {!! trans('pms_dataset.x_meaning') !!}</p>
                            </div>
                        </div>  
                    </div>      
                    @box_close      
                </article>
            </div>  
        </div>
    <?php 
        };
    ?>
    </div>

    {{-- <div class="row" style="margin-top: 20px;">
        <article class="col-lg-6 col-md-12 col-xs-12">
            @box_open(trans("pms_dataset.mark_description"))  
            <div>   
                <div class="widget-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th class="text-center">{!! trans('pms_dataset.mark') !!}</th>
                                <th class="text-center">{!! trans('pms_dataset.description') !!}</th>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td><i class="fa fa-check" arria-hidden="true"></i></td>
                                    <td>{!! trans("pms_dataset.have_data") !!}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-close" arria-hidden="true"></i></td>
                                    <td>{!! trans("pms_dataset.not_have_data") !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>      
            @box_close      
        </article>  

    </div> --}}
</section>					
@endsection

@push("css")
<style type="text/css">
    .title_data {
        vertical-align: middle !important;
        font-weight: bold;
    }
    .progress {
        border-radius: 5px!important;
    }
    .ui-accordion-header-icon.ui-icon-circle-minus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_a90329_256x240.png') !important;
    }

    .ui-accordion-header-icon.ui-icon-circle-plus {
        background-image: url('http://download.jqueryui.com/themeroller/images/ui-icons_739e73_256x240.png') !important;
    }
</style>
@endpush

@push("script")
<script type="text/javascript" src="{{ asset('js/jquery.cookie.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function () {
        <?php
            for($index = 1; $index <=4; $index ++)
            {

        ?>
            $('#export_pc_not_match_ri_' + {{ $index }}).submit(function () {
                showLoading();
                blockUIForDownload({{ $index }});
            });
            $('#export_ri_not_match_pc_' + {{ $index }}).submit(function () {
                showLoading();
                blockUIForDownload({{ $index }});
            });
        <?php 
            }

        ?>
    });
 
    var fileDownloadCheckTimer;
    function blockUIForDownload(index) {
        var token = new Date().getTime(); //use the current timestamp as the token value
        $('#download_token_value_pc_' + index).val(token);
        $('#download_token_value_ri_' + index).val(token);
        fileDownloadCheckTimer = window.setInterval(function () {
        var cookieValue = $.cookie('fileDownloadToken');

        if (cookieValue == token)
            finishDownload();
        }, 1000);
    }

    function finishDownload() {
        window.clearInterval(fileDownloadCheckTimer);
        $.removeCookie('fileDownloadToken', {path: '/', domain: '{{ $_SERVER["HTTP_HOST"] }}'}); //clears this cookie value
        hideLoading();
    }
</script>
<script>
    $( function() {
        var icons = {
            header: "ui-icon-circle-plus",
            activeHeader: "ui-icon-circle-minus"
        };
        $("#accordion" ).accordion({
            icons: icons,
            collapsible: true
        });
    });
</script>
@endpush