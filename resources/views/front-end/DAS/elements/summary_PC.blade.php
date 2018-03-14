<div class="row" ng-controller="SummaryPC" ng-init="loadYear(); loadRoad()">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                {!! Form::open(["url" => "/ajax/export_summaryPC", "method" => "get", "id" => "pc-summary"]) !!}
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::lbSelect('rmb', '',\App\Models\tblOrganization::getListRmb(),trans('das.Road Management Bureau')
                        ,['ng-model' => 'das_rmb',
                            'change-option' => 'rmbChange',
                            'ng-init' => "das_rmb='$organization_id'"
                        ])!!}
                    </div>
                    <div class="col-lg-6">
                        {!! Form::lbSelect("year", '', [], trans("das.year"),[
                            'ng-model' => 'das_year',
                            'ng-options' => 'item.value as item.text for item in year',
                        ]) !!}
                    </div>
                    <div class="col-lg-6">
                        {!! Form::lbSelect("road", '', [], trans("das.route_branch"),[
                            'ng-model' => 'das_road',
                            'ng-options' => 'item.id as item.name for item in road',
                        ]) !!}
                    </div>
                    <div class="col-lg-6">
                        {!! Form::lbSelect("distress_type", '', [['name'=> trans('das.cracking'), 'value' => '1'],
                        ['name'=> trans('das.rutting_depth_max'), 'value' => '2'],['name'=> trans('das.rutting_depth_ave'), 'value' => '3'],
                        ['name'=> trans('das.IRI'), 'value' => '4'],['name'=> trans('das.MCI'), 'value' => '5']],
                         trans("das.distress_type"),[
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
    <article class="col-lg-12" id="contentPC">
        <div>
            <div class="widget-body">
                <div class="row">
                    <header style="text-align: center">
                        <h1 id="big_title" style="font-weight: bold"></h1>
                    </header>
                    <div style="width: 85%; float: left">
                        <canvas id="firstChartPC"></canvas>
                    </div>
                        <div style="width: 15%; float: left; margin-bottom: 0" id='crack_legend' class="crack_legend"></div>
                </div>
                <div class="row" style="margin-top: 20px">
                    <div style="width: 55%; float: left; text-align: center">
                        <header style="text-align: center">
                            <h3 id="third_title" style="font-weight: bold"></h3>
                            <div class="row">
                                <div class="col-lg-7 pull-right">
                                    <p style="width: 15px;height: 15px; color: red; float: left; font-weight: bold">_____</p>
                                    <span style="float: left; margin: 6px 0px 0px 30px;" id="glosbe"></span>
                                </div>
                            </div>
                        </header>
                        <div>
                            <div style="width: 5%; height: 100%; float: left">
                                <p id="sub_label" style="transform: rotate(-90deg); color: darkgrey; font-size: 10px; margin: 70px 0px 0px -50px; width: 150px;"></p>
                            </div>
                            <div style="width: 95%; float: left">
                                <canvas id="thirdChartPC"></canvas>
                            </div>
                        </div>

                    </div>
                    <div style="width: 45%; float: left; text-align: center">
                        <header style="text-align: center">
                            <h3 id="second_title" style="font-weight: bold"></h3>
                        </header>
                        <div style="width: 70%; float: left; text-align: center">
                            <canvas id="secondChartPC"></canvas>
                            <input style="width: 60%; text-align: center; margin-top: 15px" type="text" name="length" disabled>
                        </div>
                        <div style="width: 30%; float: left; margin-bottom: 0" id='second_crack_legend' class="crack_legend"></div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px; height: 50%" id="fourth">
                    <header style="text-align: center">
                        <h1 id="fourth_title" style="font-weight: bold"></h1>
                    </header>
                    <div style="width: 85%; float: left">
                        <canvas id="fourthChartPC"></canvas>
                    </div>
                    <div style="width: 15%; float: left; margin-bottom: 0" id='fourth_legend' class="crack_legend"></div>
                </div>
            </div>
        </div>
    </article>

    <article class="col-lg-12" id="tablePC" style="margin-top: 20px">
        <div>
            <div class="widget-body">
                <div class="row" style="padding-right: 40px !important;">
                    <table class="table table-bordered table-hover" style="text-align: center !important;">
                        <thead>
                        <tr>
                            <th></th>
                            <th ng-repeat="x in condition">@{{x}}</th>
                            <th>{{trans('das.total')}}</th>
                            <th>{{trans('das.line')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="(key, value) in data_table">
                            <td ng-if="lang == 'en'">@{{value.name_en}}</td>
                            <td ng-if="lang == 'vi'">@{{value.name_vi}}</td>
                            <td ng-repeat="(k,v) in value" ng-if="k != 'name_en' && k != 'name_vi' && k != 'total' && k != 'branch_total'&& k != 'average'">
                                @{{v}}
                            </td>
                            <td >@{{value.total}}</td>
                            <td >@{{value.average | number:2 }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">@{{ organization }} {!! trans('das.total') !!}</td>
                            <td style="font-weight: bold" ng-repeat="(k,v) in total_data" ng-if="k != 'total' && k != 'branch_total'&& k != 'average'">
                                @{{v}}
                            </td>
                            <td style="font-weight: bold">@{{ total_data.total }}</td>
                            <td style="font-weight: bold">@{{ total_data.average | number:2 }}</td>
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
    $(document).ready(function () {
        $('#pc-summary').submit(function () {
            showLoading();
            /*window.setTimeout(function () {
                hideLoading();
            }, 8000);*/
            blockUIForDownloadPC();
        });
    });
    var fileDownloadCheckTimerPC;
    function blockUIForDownloadPC() {
        var token = new Date().getTime(); //use the current timestamp as the token value
        $('#download_token_value_summary_pc').val(token);
        fileDownloadCheckTimerPC = window.setInterval(function () {
            var cookieValue = $.cookie("fileDownloadTokenPC");
            if (cookieValue == token)
                finishDownloadPC();
        }, 1000);
    }

    function finishDownloadPC() {
        window.clearInterval(fileDownloadCheckTimerPC);
        $.removeCookie('fileDownloadTokenPC', {path: '/das'}); //clears this cookie value
        hideLoading();
    }
</script>
<script>
    app.controller('SummaryPC', function($scope, $http) {
        var firstChartPC;
        var thirdChartPC;
        var secondChartPC;
        var fourthChartPC;
        $('#contentPC').hide();
        $('#tablePC').hide();
        $scope.rmbChange = function(id) {
            $scope.getConditionYear(id);
            $scope.getRoad(id);
        };
        $scope.getConditionYear = function (id) {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/')}}/' + id + '/year'
            }).then(function (response) {
                $scope.year = response.data;
                $scope.das_year = 'latest';
            }, function (xhr) {
            });
        };
        $scope.getRoad = function (id) {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/')}}/' + id + '/road'
            }).then(function (response) {
                $scope.road = response.data;
                $scope.das_road = 'all';
            }, function (xhr) {
            });
        };
        $scope.loadYear = function(id) {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/')}}/' + '{!! $organization_id !!}' + '/year'
            }).then(function (response) {
                $scope.year = response.data;
                $scope.das_year = 'latest';
            }, function (xhr) {
            });
        };
        $scope.loadRoad = function(id) {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/')}}/' + '{!! $organization_id !!}' + '/road'
            }).then(function (response) {
                $scope.road = response.data;
                $scope.das_road = 'all';
            }, function (xhr) {
            });
        };
        $scope.submitForm = function () {
            showLoading();
            $scope.drawPC();
        };

        $scope.drawPC = function () {
            var year = $scope.das_year == 'latest' ? '{!! trans('das.latest') !!}' : $scope.das_year;
            $scope.drawFirst(year);
            $scope.drawSeconds(year);
            $scope.drawThird(year);
            $scope.drawFourth();
            $scope.loadDataTable();
            $('#contentPC').show();
            $('#tablePC').show();
        };

        $scope.drawFirst = function (year) {
            var ctx = angular.element("#firstChartPC");
            return $http({
                method: 'POST',
                url: '{{asset('ajax/das/first_chartPC')}}',
                data: {'rmb' : $scope.das_rmb, 'road' : $scope.das_road, 'year' : $scope.das_year, 'distress' : $scope.das_distress_type}
            }).then(function (response) {
                var name = "name_{!! \App::getLocale() !!}";
                var i = Object.keys(response.data[0][0]).length;
                var rank = [];
                var labels = [];
                var cl = randomColor(i - 3);
               if ($scope.das_distress_type != '4'){
                    var colors = cl.reverse();
                }
                for (var j = 1; j <= i-4; j++){
                    rank['rank'+j] = [];
                }
                response.data[0].forEach(function(item) {
                    for (var j = 1; j <= i-4; j++) {
                        if ($scope.das_distress_type != 5){
                            rank['rank' + j].push(((parseInt(item['rank' + j]) / parseInt(item.total)) * 100).toFixed(2));
                        }else {
                            rank['rank' + j].push(((parseInt(item['rank' + j]) / parseInt(item.total)) * 100).toFixed(1));
                        }
                    }
                    labels.push(item[name]+': '+Number(item.branch_total)+' (km)')
                });
                var chartData = [];
                for (var j = 1; j <= i- 3 - 1; j++) {
                    chartData.push({
                        label : response.data[1][j-1],
                        type: 'horizontalBar',
                        data: rank['rank'+j],
                        backgroundColor: colors[j],
                        borderWidth: 0
                    });
                }
                if (firstChartPC) {
                    firstChartPC.destroy();
                }
                firstChartPC = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: {
                        labels: labels,
                        datasets:chartData
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                stacked: true
                            }],
                            xAxes: [{
                                ticks: {
                                    max : 100,
                                    callback: function(value) {
                                        return Number((value).toFixed(1)) + "%"
                                    }
                                },
                                stacked: true
                            }]
                        },
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                        legendCallback: function(chart) {
                            var html = '<ul class="1-legend" >';
                            var legend_data = chart.legend.legendItems;
                            for (var num = 0 in legend_data) {
                                html+= '<li>' +
                                        '<span style="width: 15px;height: 15px;background-color:' + legend_data[num].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[num].datasetIndex + '\'' + ', \'rcnr\')">' +
                                        '</span>' +
                                        '<div class="number_chart" id="rcnr-' + chart.legend.legendItems[num].datasetIndex + '">'
                                        + legend_data[num].text + '' +
                                        '</div>' +
                                        '</li>';
                                num++;
                            }
                            return html;
                        }
                    }
                });
                document.getElementById('crack_legend').innerHTML = firstChartPC.generateLegend();
                if ($scope.das_distress_type == 1){
                    title = '{!! trans('das.cracking_first_chart') !!}'
                }
                if ($scope.das_distress_type == 2){
                    title = '{!! trans('das.rutting_depth_max') !!}'
                }
                if ($scope.das_distress_type == 3){
                    title = '{!! trans('das.rutting_depth_ave') !!}'
                }
                if ($scope.das_distress_type == 4){
                    title = '{!! trans('das.IRI') !!}'
                }
                if ($scope.das_distress_type == 5){
                    title = '{!! trans('das.MCI') !!}'
                }
                document.getElementById('big_title').textContent = title+' ('+year+')';
            }, function (xhr) {
            });
        };

        $scope.drawSeconds = function (year) {
            var ctx = angular.element("#secondChartPC");
            return $http({
                method: 'POST',
                url: '{{asset('ajax/das/second_chartPC')}}',
                data: {'rmb' : $scope.das_rmb, 'road' : $scope.das_road, 'year' : $scope.das_year, 'distress' : $scope.das_distress_type}
            }).then(function (response) {
                var i = Object.keys(response.data[1][0]).length;
                var cl = randomColor(i - 2);
                if ($scope.das_distress_type != '4'){
                    var colors = cl.reverse();
                }
                var condition = [];
                response.data[1].forEach(function(item) {
                    for (var j = 0; j <= i-2; j++){
                        if ($scope.das_distress_type != 5) {
                            condition[j] = ((parseInt(item['rank'+j]) / parseInt(item.total)) * 100).toFixed(2);
                        }else {
                            condition[j] = ((parseInt(item['rank'+j]) / parseInt(item.total)) * 100).toFixed(1);
                        }
                }});
                if (secondChartPC || response.data == 'errors') {
                    secondChartPC.destroy();
                }
                secondChartPC = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: response.data[0],
                        datasets: [{
                            backgroundColor: colors,
                            borderColor: colors,
                            data: condition,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                        legendCallback: function(chart) {
                            var html = '<ul class="1-legend" >';
                            var legend_data = chart.legend.legendItems;
                            var i = 0;
                            for (var i in legend_data) {
                                html+= '<li>' +
                                        '<span style="width: 15px;height: 15px;background-color:' + legend_data[i].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[i].datasetIndex + '\'' + ', \'rcnr\')">' +
                                        '</span>' +
                                        '<div class="number_chart" id="rcnr-' + chart.legend.legendItems[i].datasetIndex + '">'
                                        + legend_data[i].text + '' +
                                        '</div>' +
                                        '</li>';
                                i++;
                            }
                            return html;
                        }
                    }
                });
                document.getElementById('second_crack_legend').innerHTML = secondChartPC.generateLegend();
                if ($scope.das_distress_type == 1){
                    title = '{!! trans('das.cracking_first_chart') !!}';
                }
                if ($scope.das_distress_type == 2){
                    title = '{!! trans('das.rutting_depth_max') !!}'
                }
                if ($scope.das_distress_type == 3){
                    title = '{!! trans('das.rutting_depth_ave') !!}'
                }
                if ($scope.das_distress_type == 4){
                    title = '{!! trans('das.IRI') !!}'
                }
                if ($scope.das_distress_type == 5){
                    title = '{!! trans('das.MCI') !!}'
                }
                var sub = $scope.das_road == 'all' ? response.data[2] + ' {!! trans('das.total') !!}' : response.data[2];
                document.getElementById('second_title').textContent = title+' ('+year+') ' + sub;
                angular.element("input[name=length]").val('{!! trans('das.total_road') !!}: '+ Number(response.data[1][0].branch_total)+' (km)');
            }, function (xhr) {
            });
        };

        $scope.drawThird = function (year) {
            var thirdChartData = [];
            var ctx = angular.element("#thirdChartPC");
            var label = [];
            return $http({
                method: 'POST',
                url: '{{asset('ajax/das/third_chartPC')}}',
                data: {'rmb' : $scope.das_rmb, 'road' : $scope.das_road, 'year' : $scope.das_year, 'distress' : $scope.das_distress_type}
            }).then(function (response) {
                var average  = [];
                response.data[0].forEach(function(item) {
                    if ($scope.das_distress_type != 5) {
                        average.push(Number(item.total).toFixed(2));
                    }else {
                        average.push(Number(item.total).toFixed(2));
                    }
                    label.push(item['name_{!! \App::getLocale() !!}'])
                });
                thirdChartData.push({
                    label: '{!! trans("das.average") !!}',
                    type: 'bar',
                    data: average,
                    backgroundColor: '#01DFD7',
                    borderWidth: 0
                });
                if (thirdChartPC) {
                    thirdChartPC.destroy();
                }
                Chart.pluginService.register({
                    afterDraw: function(chart) {
                        if (typeof chart.config.options.lineAt != 'undefined') {
                            var lineAt = chart.config.options.lineAt;
                            var ctxPlugin = chart.chart.ctx;
                            var xAxe = chart.scales[chart.config.options.scales.xAxes[0].id];
                            var yAxe = chart.scales[chart.config.options.scales.yAxes[0].id];

                            // I'm not good at maths
                            // So I couldn't find a way to make it work ...
                            // ... without having the `min` property set to 0
                            if(yAxe.min != 0) return;

                            ctxPlugin.strokeStyle = "red";
                            ctxPlugin.beginPath();
                            lineAt = (lineAt - yAxe.min) * (100 / yAxe.max);
                            lineAt = (100 - lineAt) / 100 * (yAxe.height) + yAxe.top;
                            ctxPlugin.moveTo(xAxe.left, lineAt);
                            ctxPlugin.lineTo(xAxe.right, lineAt);
                            ctxPlugin.stroke();
                        }
                    }
                });
                thirdChartPC = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: label,
                        datasets:thirdChartData
                    },
                    options: {
                        lineAt: response.data[1],
                        scales: {
                            yAxes: [{
                                afterFit: function(scaleInstance) {
                                    scaleInstance.width = 30;
                                },
                                ticks: {
                                    beginAtZero: true,
                                    min: 0
                                },
                                stacked: true
                            }],
                            xAxes: [{
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 90,
                                    minRotation: 90
                                },
                                stacked: true
                            }]
                        },
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                    }
                });
                var sub_label;
                if ($scope.das_distress_type == 1){
                    title = '{!! trans('das.cracking_first_chart') !!}';
                    sub_label = '{!! trans('das.sub_label_crack') !!}';
                }
                if ($scope.das_distress_type == 2){
                    title = '{!! trans('das.rutting_depth_max') !!}'
                    sub_label = '{!! trans('das.sub_label_rutting_depth_max') !!}';
                }
                if ($scope.das_distress_type == 3){
                    title = '{!! trans('das.rutting_depth_ave') !!}'
                    sub_label = '{!! trans('das.sub_label_rutting_depth_ave') !!}';
                }
                if ($scope.das_distress_type == 4){
                    title = '{!! trans('das.IRI') !!}'
                    sub_label = '{!! trans('das.sub_label_IRI') !!}';
                }
                if ($scope.das_distress_type == 5){
                    title = '{!! trans('das.MCI') !!}'
                    sub_label = '{!! trans('das.sub_label_MCI') !!}';
                }
                document.getElementById('third_title').textContent = title+'('+year+' {!! trans('das.survey') !!})';
                document.getElementById('glosbe').textContent = ': {!! trans('das.line') !!} '+ response.data[2]+ ' (' + Number(response.data[3]) + ' km)';
                document.getElementById('sub_label').textContent = sub_label;
                hideLoading();
            }, function (xhr) {
            });
        };

        $scope.drawFourth = function () {
            var ctx = angular.element("#fourthChartPC");
            return $http({
                method: 'POST',
                url: '{{asset('ajax/das/fourth_chartPC')}}',
                data: {'rmb' : $scope.das_rmb, 'road' : $scope.das_road, 'year' : $scope.das_year, 'distress' : $scope.das_distress_type}
            }).then(function (response) {
                if (response.data == 'error'){
                    angular.element('#fourth').hide();
                }
                else {
                    var i = Object.keys(response.data[1][0]).length;
                    var cl = randomColor(i - 3);
                   if ($scope.das_distress_type != '4'){
                        var colors = cl.reverse();
                    }
                    var rank = [];
                    for (var j = 1; j <= i-3; j++){
                        rank['rank'+j] = [];
                    }
                    var labels = [];
                    response.data[1].forEach(function(item) {
                        for (var j = 1; j <= i-3; j++) {
                            if ($scope.das_distress_type != 5) {
                                rank['rank' + j].push(((parseInt(item['rank' + j]) / item.total) * 100).toFixed(2));
                            }else {
                                rank['rank' + j].push(((parseInt(item['rank' + j]) / item.total) * 100).toFixed(1));
                            }
                        }
                        if(item.new_direction == 1){
                            labels.push('L'+item.lane_position_no+ ' '+ (item.total / 1000)+ 'km');
                        } else if(item.new_direction == 2) {
                            labels.push('R'+item.lane_position_no+ ' '+ (item.total / 1000)+ 'km');
                        }else {
                            labels.push('{!! trans('das.total') !!} '+ (item.total / 1000)+ 'km');
                        }
                    });
                    var chartData = [];
                    for (var j = 1; j <= i - 3; j++) {
                        chartData.push({
                            label : response.data[0][j-1],
                            type: 'horizontalBar',
                            data: rank['rank'+j],
                            backgroundColor: colors[j-1],
                            borderWidth: 0
                           });
                    }
                    if (fourthChartPC) {
                        fourthChartPC.destroy();
                    }
                    fourthChartPC = new Chart(ctx, {
                        type: 'horizontalBar',
                        data: {
                            labels: labels,
                            datasets:chartData
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    },
                                    stacked: true
                                }],
                                xAxes: [{
                                    ticks: {
                                        max : 100,
                                        callback: function(value) {
                                            return Number((value).toFixed(1)) + "%"
                                        }
                                    },
                                    stacked: true
                                }]
                            },
                            legend: {
                                display: false,
                                position: 'bottom'
                            },legendCallback: function(chart) {
                                var html = '<ul class="1-legend" >';
                                var legend_data = chart.legend.legendItems;
                                for (var num = 0 in legend_data) {
                                    html+= '<li>' +
                                            '<span style="width: 15px;height: 15px;background-color:' + legend_data[num].fillStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[num].datasetIndex + '\'' + ', \'rcnr\')">' +
                                            '</span>' +
                                            '<div class="number_chart" id="rcnr-' + chart.legend.legendItems[num].datasetIndex + '">'
                                            + legend_data[num].text + '' +
                                            '</div>' +
                                            '</li>';
                                    num++;
                                }
                                return html;
                            }
                        }
                    });
                    document.getElementById('fourth_legend').innerHTML = fourthChartPC.generateLegend();
                    if ($scope.das_distress_type == 1){
                        title = '{!! trans('das.cracking_first_chart') !!}'
                    }
                    if ($scope.das_distress_type == 2){
                        title = '{!! trans('das.rutting_depth_max') !!}'
                    }
                    if ($scope.das_distress_type == 3){
                        title = '{!! trans('das.rutting_depth_ave') !!}'
                    }
                    if ($scope.das_distress_type == 4){
                        title = '{!! trans('das.IRI') !!}'
                    }
                    if ($scope.das_distress_type == 5){
                        title = '{!! trans('das.MCI') !!}'
                    }
                    document.getElementById('fourth_title').textContent = response.data[2]+' - '+ response.data[3]+' - '+title;
                    angular.element('#fourth').show();
                }
            }, function (xhr) {
            });
        };

        $scope.loadDataTable = function() {
            return $http({
                method: 'GET',
                url: '/ajax/das/getDataTable_PC',
                params: {
                    'rmb': $scope.das_rmb,
                    'road': $scope.das_road,
                    'year': $scope.das_year,
                    'distress': $scope.das_distress_type
                }
            }).then(function (response) {
                $scope.data_table = response.data[0];
                $scope.condition = response.data[1];
                $scope.total_data = response.data[2];
                $scope.organization = response.data[3];
                $scope.lang = '{!! \App::getLocale() !!}';
            }, function (xhr) {

            });
        }
    });
</script>
@endpush
@push('css')
<style type="text/css">
    .crack_legend ul,
    .crack_legend ul {
        list-style: none;
        text-align: left;
        margin-top: 7px;
        padding-left: 0px;
    }
    .crack_legend li,
    .RL_legend li {
        width: 100%;
        display: inline-block;
        padding: 0px 4px;
    }
    .crack_legend li span,
    .RL_legend li span {
        float: left;
    }
    .crack_legend li div.number_chart,
    .crack_legend li div.number_chart {
        display: inline-block;
        float: left;
        font-size: 12px;
        margin-left: 4px;
    }
    .line-through {
        text-decoration: line-through;
    }
</style>
@endpush
