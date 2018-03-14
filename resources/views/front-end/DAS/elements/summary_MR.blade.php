<div class="row" ng-controller="SummaryMR" ng-init="init()">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                  {!! Form::open(["url" => "/ajax/export_MR", "method" => "GET", 'id' => 'pt-form']) !!}
                <div class="row">
                    <div class="col-lg-4">
                        {!! Form::lbSelect(
                            'rmb', 
                            '',
                            \App\Models\tblOrganization::getListRmb(),
                            trans('back_end.Road Management Bureau'),
                            [  
                                'ng-model' => 'das_rmb',
                                'ng-change' => 'loadSb()',
                            ])
                        !!}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::lbSelect(
                            "sb",
                            '-1', 
                            [
                                ['name'=> trans('das.all'), 
                                'value' => '', ]
                            ], 
                            trans("back_end.sub_bureau"),
                            [
                                'ng-model' => 'das_sb',
                                'ng-change' => 'loadRouteName()',
                                'ng-options' => 'item.id as item.organization_name for item in data_sb',

                            ]) 
                        !!}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::lbSelect(
                            "route_name", '', 
                            [
                                ['name'=> trans('das.all'), 'value' => '']
                            ], 
                            trans("back_end.route_name"),
                            [
                                'ng-model' => 'das_route',
                                'ng-options' => 'item.id as item.name for item in route_data'
                            ]) 
                        !!}
                    </div>
                </div>
                <div class="row">
                        <?php 
                            if (App::islocale('en'))
                            {
                                $first_year = "5 years";
                                $second_year = "10 years";
                            }
                            else
                            {
                                $first_year = "5 năm";
                                $second_year = "10 năm";
                            }
                        ?>
                   
                    <div class="col-xs-4">
                        {!! Form::lbSelect(
                            "repair_method",
                            '', 
                            \App\Models\mstRepairMethod::allToOptionTwo(false, false, true),
                            trans("back_end.repair_method"),
                            [
                                'ng-model' => 'repair_method',
                                'ng-init' => 'repair_method = "-1"'
                            ]) 
                        !!}
                    </div>
                    <div class="col-xs-4">
                        {!! Form::lbSelect(
                            "repair_category",
                            '', 
                            \App\Models\tblRCategory::allToOption(false, false, true),
                            trans("back_end.repair_category"),
                            [
                                'ng-model' => 'repair_category',
                                'ng-init' => 'repair_category = "-1"'
                            ]) 
                        !!}
                    </div>
                     <div class="col-xs-4">
                        {!! Form::lbSelect(
                            "year",
                            '', 
                            [
                                ['name'=> $first_year, 'value' => '5'],
                                ['name'=> $second_year, 'value' => '10']
                            ], 
                            trans("back_end.year"),
                            [
                                'ng-model' => 'year',
                                'ng-init' => 'year = "5"'
                            ]) 
                        !!}
                    </div>
                </div>
                <div class="widget-footer">
                    <a ng-click="LoadDataChart()" class="btn btn-md btn-primary">{!! trans('das.submit') !!}</a>
                    <button type="submit"  class="btn btn-md btn-warning">{!! trans('das.export') !!}</button> 
<!--                     ng-click="export_MR()"`
 -->                </div>
                 {!! Form::close() !!} 
            </div>
        </div>
        @box_close
    </article>
    <!-- chart 1 -->    
    <article class="col-lg-6">
        <div>
            <div class="widget-body">
                <div class="row">
                    <canvas id="myChartMR"></canvas>
                </div>
            </div>
        </div>
    </article>
    <!-- chart 2-->    
    <article class="col-lg-6">
        <div>
            <div class="widget-body">
                <div class="row">
                    <div class="title_chart">{{trans('das.repair_work')}}({{trans('das.from')}} @{{min_s}} - {{trans('das.to')}} @{{max_s}})</div>
                    <div id='myChartRW_L' class='myChartRW_L'></div>
                    <canvas id="myChartRW"></canvas>
                </div>
            </div>
        </div>
    </article>

     <!-- table-->    
    <article class="col-lg-12">
        <div>
            <div class="widget-body">
                <div class="row" style="padding-right: 40px !important;">
                   <table class="table table-bordered table-hover" style="text-align: center !important;">
                        <thead>
                          <tr>
                            <th></th>
                            <th ng-repeat="x in survey_data">@{{x}}</th>
                            <th>{{trans('das.total')}}</th>
                          </tr>  
                        </thead>
                        <tbody>
                          <tr ng-repeat="(key, value) in data_table">
                              <td >@{{key}}</td>
                              <td ng-repeat="x in value.data">@{{x.sum_length}}</td>
                              <td >@{{value.total}}</td>
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
    var chartMR = document.getElementById("myChartMR");
    var chartRW = document.getElementById("myChartRW");
    var organization_id = "<?php echo $organization_id ?>";
    var myChartMR;
    var myChartRW;
    app.controller('SummaryMR', function($scope, $http) {
        $scope.init = function() {
            $scope.das_rmb = organization_id;
            $scope.das_sb = -1;
            $scope.loadSb();
            $scope.LoadDataChart();
        };
        $scope.loadSb = function() {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/rmb/')}}/' + $scope.das_rmb + '/sb'
            }).then(function (response) {
                $scope.data_sb = response.data;
                $scope.loadRouteName();
            }, function (xhr) {

            });
        };
        $scope.loadRouteName = function() {
            var sb_id = $scope.das_sb == null ? '-1' : $scope.das_sb;
            return $http({
                method: 'GET',
                url: '/ajax/sb/' + sb_id + '/route?rmb_id=' + $scope.das_rmb
            }).then(function (response) {
                $scope.route_data = response.data;
                // $scope.LoadDataChart();
            }, function (xhr) {

            });
        };

        $scope.LoadDataChart = function() {
            showLoading();
            var route_id = $scope.das_route == null ? '-1' : $scope.das_route;
            var sb_id = $scope.das_sb == null ? '-1' : $scope.das_sb;
            var repair_method = $scope.repair_method == null ? '-1' : $scope.repair_method;
            var repair_category = $scope.repair_category == null ? '-1' : $scope.repair_category;
            return $http({
                method: 'GET',
                url: '/ajax/getDataChartMR',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': route_id,
                    'repair_method': repair_method,
                    'repair_category': repair_category 
                }
            }).then(function (response) {
                var labels = [];
                var chart_data = [];
                for (var i in response.data) {
                    var data = response.data[i];
                    labels.push(data.survey_time);
                    chart_data.push(data.total_length);
                }
                if (myChartMR) {
                    myChartMR.destroy();
                }  
                DrawDataChartMR(labels, chart_data);
                $scope.LoadDataChartRW(route_id, sb_id);
                
            }, function (xhr) {

            });
        };

        $scope.LoadDataChartRW = function() {
            var route_id = $scope.das_route == null ? '-1' : $scope.das_route;
            var sb_id = $scope.das_sb == null ? '-1' : $scope.das_sb;
            var year = $scope.year == null ? '-1' : $scope.year;
            var repair_method = $scope.repair_method == null ? '-1' : $scope.repair_method;
            var repair_category = $scope.repair_category == null ? '-1' : $scope.repair_category;
            return $http({
                method: 'GET',
                url: '/ajax/getDataChartRW',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': route_id,
                    'year': year,
                    'repair_method': repair_method,
                    'repair_category': repair_category 
                }
            }).then(function (response) {
                //showLoading();
                var labels = response.data.labels;  
                var right_data = response.data.right_data;
                var left_data = response.data.left_data;
                var survey_time = [];
                var chart_data = [];
                var colors = randomColor(Object.keys(left_data).length + 1);
                var index = 1;
                chart_data.push({
                    type: 'line',
                    yAxisID: "right-y-axis",
                    data: right_data,
                    backgroundColor: colors[0],
                    borderColor: colors[0],
                    borderWidth: '1',
                    showLine: false,
                    fill: false,
                });
                for (var i in left_data) {
                   // survey_time.push(i);
                    //console.log(survey_time);
                    chart_data.push({
                        type: 'bar',
                        yAxisID: "left-y-axis",
                        data: left_data[i],
                        backgroundColor: colors[index],
                        borderColor: colors[index]
                    });
                    index++;
                }
                var min_year =(new Date()).getFullYear() - year + 1;
                for (var i = min_year; i <= (new Date()).getFullYear(); i++) {
                    survey_time.push(i);
                }
                $scope.max_s = Math.max.apply(null, survey_time);
                $scope.min_s = Math.min.apply(null, survey_time);
                if (myChartRW) {
                    myChartRW.destroy();
                }  
                DrawDataChartRW(labels, chart_data, survey_time);
                $scope.loadDataTable(sb_id, route_id, year, repair_method, repair_category);
            }, function (xhr) {

            });
        };

        $scope.export_MR = function() {
            //event.preventDefault();
            var route_id = $scope.das_route == null ? '-1' : $scope.das_route;
            var sb_id = $scope.das_sb == null ? '-1' : $scope.das_sb;
            var year = $scope.year == null ? '-1' : $scope.year;
            var repair_method = $scope.repair_method == null ? '-1' : $scope.repair_method;
            var repair_category = $scope.repair_category == null ? '-1' : $scope.repair_category;
            return $http({
                method: 'GET',
                url: '/ajax/export_MR',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': route_id,
                    'year': year,
                    'repair_method': repair_method,
                    'repair_category': repair_category 
                }
            }).then(function (response) {
               // $scope.data_table = response.data[0];
               // $scope.survey_data = response.data[1];
               // hideLoading();
            }, function (xhr) {

            });
        }

        $scope.loadDataTable = function(sb_id, route_id, year, repair_method, repair_category) {
            return $http({
                method: 'GET',
                url: '/ajax/getDataTable',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': route_id,
                    'year': year,
                    'repair_method': repair_method,
                    'repair_category': repair_category 
                }
            }).then(function (response) {
               $scope.data_table = response.data[0];
               $scope.survey_data = response.data[1];
               hideLoading();
            }, function (xhr) {

            });
        }

    });

    // $scope.exportMR = function() {
        
    // }

    function DrawDataChartMR(labels, chart_data)
    {
        myChartMR = new Chart(chartMR, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: chart_data,
                    backgroundColor: 'blue',
                    borderColor:'blue',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: '<?php echo trans("das.length(m)") ?>'
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        scaleLabel: {
                            display: true,
                            labelString: '<?php echo trans("das.accumulation_term") ?>'
                       }
                    }]
                },
                title: {
                    display: true,
                    text: '<?php echo trans("das.repair_length") ?>',
                    fontSize: 24
                },
                legend: {
                    display: false,
                },
            }
        });
    }

    function DrawDataChartRW(labels, chart_data, survey_time) {
        myChartRW = new Chart(chartRW, {
            type: 'bar',
            data: {
                datasets: chart_data,
                labels: labels
            },
            options: {
                scales: {
                    yAxes: [{
                        id: 'left-y-axis',
                        type: 'linear',
                        position: 'left',
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            beginAtZero: true,
                          
                        },
                        scaleLabel: {
                            display: true,
                            labelString: '<?php echo trans("das.repair_length(km)") ?>'
                        },
                        stacked: true,
                    }, {
                        id: 'right-y-axis',
                        type: 'linear',
                        position: 'right',
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            beginAtZero: true,

                            callback: function(value) {
                                return Number((value).toFixed(1)) + "%"
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 90
                        },
                        stacked: true,
                    }]
                },
                legend: {
                    display: false,
                },
                legendCallback: function(chart) {
                    survey_time.unshift(0);
                    var html = '<ul class="1-legend">';
                    var legend_data = chart.legend.legendItems;
                    for (var i in legend_data) {
                        if (i == 0) continue;
                        html+= '<li><span style="width: 30px;height: 10px;background-color:' + legend_data[i].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[i].datasetIndex + '\'' + ')"></span><div class="number_chart" id="rcnr-' + chart.legend.legendItems[i].datasetIndex + '">' + survey_time[i] + '</div></li>';
                    }
                    html+= '<li><span style="border-radius:50%;margin-top: 1px;width: 10px;height: 10px;background-color:' + legend_data[0].fillStyle + '"></span><div class="number_chart">'+"{{trans('das.repair_rate')}}"+'</div></li>';  

                    return html;
                }
            }
        });
        document.getElementById('myChartRW_L').innerHTML = myChartRW.generateLegend();
    }

    updateDataset = function(e, datasetIndex) {
        var index = datasetIndex;
        var ci = e.view.weightChart;
        var meta = ci.getDatasetMeta(index);

        // See controller.isDatasetVisible comment
        meta.hidden = meta.hidden === null? !ci.data.datasets[index].hidden : null;

        // We hid a dataset ... rerender the chart
        ci.update();
    };
</script>
@endpush
@push('css')
    <style type="text/css">
        .myChartRW_L ul {
            list-style: none;
            text-align: center;
            margin-top: 7px;
            padding-left: 0px;
        }
        .myChartRW_L li {
            display: inline-block;
            padding: 0px 4px;
        }
        .myChartRW_L li span {
            float: left;
        }
        .myChartRW_L li div.number_chart {
            display: inline-block;
            float: left;
            font-size: 12px;
            margin-left: 4px;
        }
        .line-through {
            text-decoration: line-through;
        }
        .title_chart {
            width: auto;
            text-align: center;
            font-size: 13px;
            /* color: yellow; */
            font-size: 22px;
            font-weight: bold;
        }
    </style>
@endpush
