<div class="row" ng-controller="TimeSeriesPC" ng-init="init()">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                {!! Form::open(["url" => "/ajax/export_TS", "method" => "GET", "id" => "pc-summaryPS"]) !!}
                <div class="row">
                    <div class="col-lg-3">
                        {!! 
                            Form::lbSelect(
                                'rmb', 
                                '',
                                \App\Models\tblOrganization::getListRmb(),
                                trans('back_end.Road Management Bureau'),
                                [
                                    'ng-model' => 'das_rmb',
                                    'ng-change' => 'rmbChange()'
                                ])
                        !!}
                    </div>
                    <div class="col-lg-3">
                        {!! 
                            Form::lbSelect(
                                "first_year", 
                                '', 
                                [],
                                trans("das.first_year"),
                                [
                                    'ng-model' => 'first_year',
                                    'ng-options' => 'item.value as item.name for item in year',
                                ]) 
                        !!}
                    </div>
                    <div class="col-lg-3">
                        {!! 
                            Form::lbSelect(
                                "second_year", 
                                '', 
                                [],
                                trans("das.second_year"),
                                [
                                    'ng-model' => 'second_year',
                                    'ng-options' => 'item.value as item.name for item in year',
                                ]) 
                        !!}
                    </div>
                    <div class="col-lg-3">
                        {!! Form::lbSelect(
                            "route_name", '', 
                            [
                                ['name'=> trans('das.all'), 'value' => '']
                            ], 
                            trans("back_end.route_name"),
                            [
                                'ng-model' => 'das_route',
                                'ng-options' => 'item.id as item.name for item in route_data',
                            ]) 
                        !!}
                    </div>
                    <div class="col-lg-offset-9 col-xs-3">
                        {!! Form::lbSelect("distress_type", '', [['name'=> trans('das.cracking'), 'value' => '1'],
                        ['name'=> trans('das.rutting_depth_max'), 'value' => '2'],['name'=> trans('das.rutting_depth_ave'), 'value' => '3'],
                        ['name'=> trans('das.IRI'), 'value' => '4'],['name'=> trans('das.MCI'), 'value' => '5']],
                         trans("back_end.distress_type"),[
                            'ng-model' => 'das_distress_type',
                            'ng-init' => "das_distress_type='1'"
                        ]) !!}
                    </div>

                </div>
                 <input type="hidden" id="download_token_value_summary_pc" name="downloadTokenValuePC"/>
                <div class="widget-footer">
                    <a ng-click="submitForm()" class="btn btn-md btn-primary">{!! trans('das.submit') !!}</a>
                    <button type="submit" class="btn btn-md btn-warning">{!! trans('das.export') !!}</button>
                </div>
                {!! Form::close() !!} 
            </div>
        </div>
        @box_close
    </article>

    <!-- chart 1-->    
    <article class="col-lg-12">
        <div>
            <div class="widget-body">
                <!-- <div class="row">
                    <!-- <header style="text-align: center">
                        <h1 id="title_time_series" style="font-weight: bold"></h1>
                    </header> -->
                    <!-- <div style="width: 85%; float: left;">
                        <canvas id="first_chart"></canvas>
                    </div>
                    <div style="width: 15%; float: left;">
                        <div id='legend_time_series' class="legend_time_series"></div>
                    </div>
                </div> --> 
            
                <div class="row">
                    <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-6">
                                <!-- <header style="text-align: center">
                                    <h1 id="title_time_series1" style="font-weight: bold">aaa</h1>
                                </header> -->
                                <canvas id="first_chart" height="250"></canvas>
                            </div>
                            <div class="col-xs-6">
                               <!--  <header style="text-align: center">
                                    <h1 id="title_time_series2" style="font-weight: bold">bb</h1>
                                </header> -->
                                <canvas id="second_chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-2">
                        <div id='legend_time_series' class="legend_time_series"></div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- table-->    
    <article class="col-lg-12" style="padding-top: 40px;display: none;" id="ts-table">
        <div>
            <div class="widget-body">
                <div class="row" style="padding-right: 40px !important;">
                   <table class="table table-bordered table-hover" style="text-align: center !important;">
                        <thead>
                          <tr>
                            <th>@{{title_play}}</th>
                            <th ng-repeat="x in labels">@{{x}}</th>
                            <th>{{trans('das.total')}}</th>
                            <th>{{trans('das.average')}}</th>
                          </tr>  
                        </thead>
                        <tbody>
                          <tr ng-repeat="(key, value) in data_res">
                              <td>@{{key}}</td>
                              <td ng-repeat="(k,v) in value" ng-if="k != 'date_y' && k!= 'total_rank' && k!= 'average'">
                                    @{{v}}
                              </td>
                              <td>@{{value.total_rank}}</td>
                              <td>@{{value.average ||1 |number}} </td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </article>
</div>
@push('script')
<script>
   // $(document).ready(function () {
   //      $('#pc-summaryPS').submit(function () {
   //          showLoading();
   //          /*window.setTimeout(function () {
   //              hideLoading();
   //          }, 8000);*/
   //          blockUIForDownloadPC();
   //      });
   //  });
   //  var fileDownloadCheckTimerPC;
   //  function blockUIForDownloadPC() {
   //      var token = new Date().getTime(); //use the current timestamp as the token value
   //      $('#download_token_value_summary_pc').val(token);
   //      fileDownloadCheckTimerPC = window.setInterval(function () {
   //          var cookieValue = $.cookie("fileDownloadTokenPC");
   //          if (cookieValue == token)
   //              finishDownloadPC();
   //      }, 1000);
   //  }

   //  function finishDownloadPC() {
   //      window.clearInterval(fileDownloadCheckTimerPC);
   //      $.removeCookie('fileDownloadTokenPC', {path: '/das'}); //clears this cookie value
   //      hideLoading();
   //  }
</script>
<script>
    var first_chart = document.getElementById("first_chart");
    var second_chart = document.getElementById("second_chart");
    var myFirstChart;
    var mySecondChart;
    app.controller('TimeSeriesPC', function($scope, $http,$window) {
        $scope.init = function() {
            $scope.das_rmb = organization_id;
            $scope.das_sb = -1;
            $scope.rmbChange();
        };
        $scope.rmbChange = function() {
            $scope.loadRouteName();
            $scope.loadYear();
        }
        $scope.loadYear = function() {
            return $http({
                method: 'GET',
                url: '/ajax/getYearTimeSerires',
                params: {
                    'rmb_id': $scope.das_rmb
                }
            }).then(function (response) {
                $scope.year = response.data.year;
                $scope.first_year = response.data.year[0].value;//selected option
                $scope.second_year = response.data.year[0].value;//selected option
                //delete cache
                if(myFirstChart) {
                    myFirstChart.destroy();
                }
                if (mySecondChart) {
                    mySecondChart.destroy();
                }
                $('#legend_time_series').empty();
                $scope.labels = [];
                $scope.data_res = [];
                $('#ts-table').css('display','none');
            }, function (xhr) {

            });
        }
        // url: '{{asset('ajax/das/')}}/' + id + '/road'
        $scope.loadRouteName = function() {
            var sb_id = -1;
            return $http({
                method: 'GET',
                url: '/ajax/sb/' + sb_id + '/route?rmb_id=' + $scope.das_rmb+'&&flash=1'
            }).then(function (response) {
                $scope.route_data = response.data;
                //$scope.loadDataChartTS1();
            }, function (xhr) {

            });
        };

        $scope.submitForm = function() {
            var rmb_id = $scope.das_rmb;
            var first_year = $scope.first_year;
            var second_year = $scope.second_year;
            var branch_id = $scope.das_route;
            var type = $scope.das_distress_type
            
            var distress_type = {
                1: 'Cracking ratio total',
                2: 'Rutting depth max',
                3: 'Rutting depth ave',
                4: 'IRI',
                5: 'MCI',
            };
            var distress_type_vn = {
                1: 'Tỉ lệ nứt',
                2: "Hằn lún vệt bánh xe (lớn nhất)",
                3: "Hằn lún vệt bánh xe (trung bình)",
                4: "Gồ ghề (IRI)",
                5: "MCI"
            }
            var lang = "{{App::isLocale('en')? 'en' : 'vn'}}";
            if (lang == 'en') {
                $scope.title_play = distress_type[type];
            }else {
                $scope.title_play = distress_type_vn[type];
            }
            if (first_year <= second_year)
            {
                $window.alert("{{trans('das.second_year_have_to_smaler_first_year')}}");
                //$('#ts-table').css('display','none');
                return false;
            }
            showLoading();
            return $http({
                method: 'GET',
                url: 'ajax/getDataTimeSeriesPC',
                params: {
                    rmb_id: rmb_id,
                    first_year: first_year,
                    second_year: second_year,
                    branch_id: branch_id,
                    type: type
                }

            }).then(function (response) {
                $('#ts-table').css('display','block');
                $scope.labels = response.data[2] // data for label
                $scope.data_res = response.data[0];
                var res = response.data[0];// data chart 1
                var res_chart2 = response.data[1]; // data chart 2

                var data = [];
                var chart_data_ts1 = [];
                var chart_data_ts2 = [];
                var title_note= [];
                var title_year = [];
                var k = 0;
                for (var i in res) {
                    title_note.push(i+' ('+((res[i].total_rank)/1000).toFixed(2)+' km)');
                    title_year.push(i);
                    var key = Object.keys(res[i]).length - 3;
                    if (key >= k) {
                        k = key;
                    }
                }
                var color = randomColor(k);
                var colors = color.reverse();
                //load data chart 1
                for (var j = 1; j <= k; j ++) {
                    var data_tmp = [];
                    for (var i in res) {
                        if (type == 5) {
                            data_tmp.push((((res[i]['rank'+ j])/res[i].total_rank)*100).toFixed(1));
                        }else {
                            data_tmp.push((((res[i]['rank'+ j])/res[i].total_rank)*100).toFixed(2));
                        }
                    }
                    data.push(data_tmp);
                }
                for (var i in data) {
                    chart_data_ts1.push({
                        data: data[i],
                        backgroundColor: colors[i],
                        borderColor: colors[i],
                        borderWidth: '1',
                    })
                }
                if (myFirstChart) {
                    myFirstChart.destroy();
                }
                $scope.loadDataChartTS1(title_note, chart_data_ts1, $scope.labels, $scope.title_play,title_year);
               
                //load data chart 2;
                var data_ts2 = [];
                if (Object.keys(res_chart2).length > 1)
                {
                    for (var i in res_chart2) {
                        var data_tmp = [];
                        for (var j = 1; j <= k; j ++) {
                            if (type == 5) {
                                data_tmp.push((((res_chart2[i]['rank'+ j])/res_chart2[i].total_rank)*100).toFixed(1));
                            }else {
                                data_tmp.push((((res_chart2[i]['rank'+ j])/res_chart2[i].total_rank)*100).toFixed(2));
                            }
                        }
                        data_ts2.push(data_tmp);
                    }
                }

                for (var i in data_ts2) {
                    chart_data_ts2.push({
                        data: data_ts2[i],
                        backgroundColor: colors,
                        borderColor: 'black',
                        borderWidth: '1',
                    })
                }
                if (mySecondChart) {
                    mySecondChart.destroy();
                }
                $scope.loadDataChartTS2(title_note, chart_data_ts2, $scope.labels, $scope.title_play, title_year);
                hideLoading();
            }, function (xhr) {

            });

        }
        $scope.loadDataChartTS1 = function(title_note, chart_data, labels, title_play,title_year)
        {
            myFirstChart = new Chart(first_chart, {
                    type: 'horizontalBar',
                    data: {
                        labels: title_note,
                        datasets: chart_data
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                stacked: true,
                                gridLines: {
                                    display: false
                                },
                            }],
                            xAxes: [{
                                ticks: {
                                    max : 100,
                                    beginAtZero: true,
                                    callback: function(value) {
                                        return value + "%"
                                    }
                                },
                                stacked: true
                            }]
                        },
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: title_play+'('+ Math.max.apply(null, title_year) +' - '+ Math.min.apply(null, title_year)+')',
                            fontSize: 16
                        },
                        legendCallback: function(chart) {
                            var html = '<ul class="1-legend" >';
                            var legend_data = chart.legend.legendItems;
                            for (var num = 0 in legend_data) {
                                html+= '<li>' +
                                        '<span style="width: 15px;height: 15px;background-color:' + legend_data[num].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[num].datasetIndex + '\'' + ', \'rcnr\')">' +
                                        '</span>' +
                                        '<div class="number_chart" id="rcnr-' + chart.legend.legendItems[num].datasetIndex + '">'
                                        + labels[num] + '' +
                                        '</div>' +
                                        '</li>';
                                num++;
                            }
                            return html;
                        }
                    }
                });
            document.getElementById('legend_time_series').innerHTML = myFirstChart.generateLegend();
        }

        $scope.loadDataChartTS2 = function(title_note, chart_data, labels, title_play,title_year)
        {
            mySecondChart = new Chart(second_chart, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: chart_data
                },
                options: {
                    legend: {
                        display: false,
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: title_play+' - '+Math.max.apply(null, title_year)+'(in)'+' - '+Math.min.apply(null, title_year)+'(out)',
                        fontSize: 16
                    },
                    legendCallback: function(chart) {
                        var html = '<ul class="1-legend" >';
                        var legend_data = chart.legend.legendItems;
                        for (var num = 0 in legend_data) {
                            html+= '<li>' +
                                    '<span style="width: 15px;height: 15px;background-color:' + legend_data[num].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[num].datasetIndex + '\'' + ', \'rcnr\')">' +
                                    '</span>' +
                                    '<div class="number_chart" id="rcnr-' + chart.legend.legendItems[num].datasetIndex + '">'
                                    + labels[num] + '' +
                                    '</div>' +
                                    '</li>';
                            num++;
                        }
                        return html;
                    }
                }
            });
        }
    });

</script>
@endpush
@push('css')
    <style type="text/css">
    .legend_time_series2 ul,
    .legend_time_series ul {
        list-style: none;
        text-align: left;
        margin-top: 7px;
        padding-left: 0px;
    }
    .legend_time_series2 li,
    .legend_time_series li {
        width: 100%;
        display: inline-block;
        padding: 0px 4px;
    }
    .legend_time_series2 li span,
    .legend_time_series li span {
        float: left;
    }
    .legend_time_series2 li div.number_chart,
    .legend_time_series li div.number_chart {
        display: inline-block;
        float: left;
        font-size: 12px;
        margin-left: 4px;
    }
    .line-through {
        text-decoration: line-through;
    }
@endpush

