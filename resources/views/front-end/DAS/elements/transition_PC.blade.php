<div class="row" ng-controller="TransitionPC">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                {!! Form::open(["url" => "/das/export_transition_pc", "method" => "get", "id" => "pc-transition-form"]) !!}
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::lbSelect('rmb', '',\App\Models\tblOrganization::getListRmb(true),trans('back_end.Road Management Bureau')
                        ,['ng-model' => 'das_rmb',
                            'change-option' => 'rmbChange',
                            'ng-init' => "das_rmb='-1'"
                        ])!!}
                    </div>
                    <div class="col-lg-6">
                        {!! Form::lbSelect("distress_type", '', [['name'=> trans('das.cracking'), 'value' => '1'],
                        ['name'=> trans('das.rutting_depth_max'), 'value' => '2'],['name'=> trans('das.rutting_depth_ave'), 'value' => '3'],
                        ['name'=> trans('das.IRI'), 'value' => '4'],['name'=> trans('das.MCI'), 'value' => '5']],
                         trans("back_end.distress_type"),[
                            'ng-model' => 'das_distress_type',
                            'ng-init' => "das_distress_type='1'"
                        ]) !!}
                    </div>
                    <input type="hidden" id="download_token_value_pc" name="downloadTokenValue"/>
                </div>
                <div class="widget-footer">
                    <a ng-click="submitForm()" class="btn btn-md btn-primary">{!! trans('das.submit') !!}</a>
                    <button type="submit" class="btn btn-md btn-warning">{!! trans('das.export') !!}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @box_close
    </article>
    <article class="col-lg-12" id="contentTPC">
        <div>
            <div class="widget-body">
                <div class="row">
                    <header style="text-align: center">
                        <h1 id="big_title" style="font-weight: bold"></h1>
                    </header>
                    <div style="text-align: center;">
                        <div style="width: 5%; float: left">
                            <p id="sub_labelTPC" style="transform: rotate(-90deg); color: darkgrey; font-size: 14px; margin: 90px 0px 0px -50px; width: 150px;"></p>
                        </div>
                        <div style="display: inline-block; width: 70%;float: left">
                            <canvas height="100" id="transitionPC"></canvas>
                        </div>
                        <div style="width: 25%; float: left; margin-top: 50px" id='TPCcrack_legend' class="crack_legend"></div>

                    </div>
                </div>
            </div>
        </div>
    </article>
    <article class="col-lg-12" ng-show="availableYears.length > 0">
        <div>
            <div class="widget-body">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h3>{!! trans('das.survey_time') !!}</h3>
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-bordered table-hover" style="text-align: center !important; margin-top: 30px;">
                            <thead>
                            <tr>
                                <th></th>
                                <th ng-repeat="x in availableYears">@{{x}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(key, value) in availableRMBs">
                                <td>@{{ value.label }}</td>
                                <td ng-repeat="y in value.data track by $index">@{{ y }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>
@push('script')
<script>
    app.controller("TransitionPC", function($scope, $http) {
        $scope.transitionPC = null;
        $scope.availableYears = [];
        $scope.availableRMBs = [];

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
            $scope.drawTransition();
        };

        $scope.drawTransition = function () {
            var title;
            if ($scope.das_distress_type == 1){
                title = '{!! trans('das.cracking_average') !!}';
            }
            if ($scope.das_distress_type == 2){
                title = '{!! trans('das.rutting_depth_max_average') !!}';
            }
            if ($scope.das_distress_type == 3){
                title = '{!! trans('das.rutting_depth_ave_average') !!}';
            }
            if ($scope.das_distress_type == 4){
                title = '{!! trans('das.IRI_average') !!}';
            }
            if ($scope.das_distress_type == 5){
                title = '{!! trans('das.MCI_average') !!}';
            }
            $scope.drawChart(title);
            $('#contentTPC').show();
        };

        $scope.getChartData = function (data) {
            function onlyUnique(value, index, self) { 
                return self.indexOf(value) === index;
            }
            if (data.type == 'all') {
                var labels = [], 
                    datasets = [];
                for (var i in data.all_data) {
                    for (var j in data.all_data[i].year) {
                        labels.push(data.all_data[i].year[j]);
                    }
                }

                labels = labels.filter(onlyUnique).sort();
                var colors = randomColor(data.all_data.length);
                for (var i in data.all_data) {
                    var items = [];
                    for (var j in labels) {
                        var key_chk = data.all_data[i].year.indexOf(labels[j]);
                        if (key_chk >= 0) {
                            items.push(data.all_data[i].total[key_chk]);
                        } else {
                            items.push(null);
                        }
                    }
                    datasets.push({
                        data: items,
                        label: data.all_data[i].name,
                        borderColor: colors[i],
                        fill: false,
                        lineTension: 0,
                        spanGaps: true
                    });
                }
                
                return {
                    labels: labels,
                    datasets: datasets
                }
            } else {
                return {
                    labels: data.year,
                    datasets: [{
                        data: data.total,
                        label: data.name,
                        borderColor: "#3e95cd",
                        fill: false,
                    }]
                }
            }
        };

        $scope.drawChart = function (title) {
            showLoading();
            var ctx = $('#transitionPC');
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/transition_pc')}}',
                params: {
                    rmb: $scope.das_rmb,
                    distress: $scope.das_distress_type,
                }
            }).then(function (response) {
                var data = $scope.getChartData(response.data);
                $scope.availableYears = data.labels;
                $scope.availableRMBs = data.datasets;
                if ($scope.transitionPC) {
                    $scope.transitionPC.destroy();
                }
                $scope.transitionPC = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets,
                    },
                    options: {
                        title: {
                            display: true,
                            text: title
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    padding: 20
                                },
                                stacked: false,
                            }],
                            xAxes: [{
                                stacked: false
                            }]
                        },
                        legend: {
                            display: false,
                        },
                        legendCallback: function(chart) {
                            var html = '<ul class="1-legend" >';
                            var legend_data = chart.legend.legendItems;
                            var i = 0;
                            for (var i in legend_data) {
                                html+= '<li>' +
                                        '<span style="width: 40px;height: 3px; margin-top:7px; background-color:' + legend_data[i].strokeStyle + '" onclick="updateDataset(event, ' + '\'' + chart.legend.legendItems[i].datasetIndex + '\'' + ', \'rcnr\')">' +
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
                document.getElementById('TPCcrack_legend').innerHTML = $scope.transitionPC.generateLegend();
                var sub_label;
                if ($scope.das_distress_type == 1){
                    sub_label = '{!! trans('das.sub_label_crack') !!}';
                }
                if ($scope.das_distress_type == 2){
                    sub_label = '{!! trans('das.sub_label_rutting_depth_max') !!}';
                }
                if ($scope.das_distress_type == 3){
                    sub_label = '{!! trans('das.sub_label_rutting_depth_ave') !!}';
                }
                if ($scope.das_distress_type == 4){
                    sub_label = '{!! trans('das.sub_label_IRI') !!}';
                }
                if ($scope.das_distress_type == 5){
                    sub_label = '{!! trans('das.sub_label_MCI') !!}';
                }
                document.getElementById('sub_labelTPC').textContent = sub_label;
                hideLoading();
            }, function (xhr) {
            });

        };
    });
</script>
<script type="text/javascript" src="{{ asset('js/jquery.cookie.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#pc-transition-form').submit(function () {
            showLoading();
            blockUIForDownload();
        });
    });
    var fileDownloadCheckTimer;
    function blockUIForDownload() {
        var token = new Date().getTime(); //use the current timestamp as the token value
        $('#download_token_value_pc').val(token);
        fileDownloadCheckTimer = window.setInterval(function () {
            var cookieValue = $.cookie('fileDownloadToken');
            if (cookieValue == token)
                finishDownload();
            }, 1000);
    }

    function finishDownload() {
        window.clearInterval(fileDownloadCheckTimer);
        $.removeCookie('fileDownloadToken', {path: '/das'}); //clears this cookie value
        hideLoading();
    }
</script>
@endpush

