<div class="row" ng-controller="SummaryPT" ng-init="init()">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                {!! Form::open(["url" => "/das/export_PT", "method" => "get", 'id' => 'pt-form']) !!}
                <div class="row">
                    <div class="col-lg-4">
                        {!! Form::lbSelect('rmb', '',\App\Models\tblOrganization::getListRmb(),trans('back_end.Road Management Bureau')
                        ,[  'ng-model' => 'das_rmb',
                            'ng-change' => 'loadSb()',
                            'id' => 'rmb'
                        ])!!}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::lbSelect("sb", '-1', [['name'=> trans('das.all'), 'value' => '', ]], trans("back_end.sub_bureau"),[
                            'ng-model' => 'das_sb',
                            'ng-change' => 'loadRouteName()',
                            'ng-options' => 'item.id as item.organization_name for item in data_sb',

                        ]) !!}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::lbSelect("route_name", '', 
                        [['name'=> trans('das.all'), 'value' => '']], trans("back_end.route_name"),[
                            'ng-model' => 'das_route',
                            'ng-options' => 'item.id as item.name for item in route_data',
                            
                        ]) !!}
                    </div>
                </div>
                <div class="widget-footer">
                    <a ng-click="PressSubmit()" class="btn btn-md btn-primary">{!! trans('das.submit') !!}</a>
                    <button type="submit" class="btn btn-md btn-warning">{!! trans('das.export') !!}</button>
                </div>
                {!! Form::close() !!} 
            </div>
        </div>
        @box_close
    </article>

    <article class="col-lg-12">
        <div>
            <div class="widget-body">
                <div class="row">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </article>

    <article class="col-lg-12">
        <div>
            <div class="widget-body">
                <div class="row">
                    <table class="table table-bordered table-hover" style="text-align: center !important; margin-top: 30px;">
                        <thead>
                        <tr>
                            <th>{{trans('das.RMB')}}</th>
                            <th>{{trans('das.SB')}}</th>
                            <th>{{trans('das.Route Name')}}</th>
                            <th>{{trans('das.Elapsed time')}}</th>
                            <th>{{trans('das.Year')}}</th>
                            <th>{{trans('das.Section Length')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat='x in data_table'>
                                <td>@{{x.RMB}}</td>
                                <td>@{{x.SB}}</td>
                                <td>@{{x.route_name}}</td>
                                <td>@{{x.elapsed_time}}</td>
                                <td>@{{x.year}}</td>
                                <td>@{{x.section_length}}</td>
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
    var ctx = document.getElementById("myChart");
    var organization_id = "<?php echo $organization_id; ?>";
    var myChart;
    app.controller('SummaryPT', function($scope, $http) {
        $scope.init = function() {
            $scope.das_rmb = organization_id;
            $scope.das_sb = -1;
            $scope.das_route = -1;
            $scope.loadSb();
            $scope.LoadDataChart(-1, -1);
            $scope.getDataTablePT(-1, -1);
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
            }, function (xhr) {

            });
        };
        $scope.PressSubmit = function() {
            var sb_id = $scope.das_sb == null ? '-1' : $scope.das_sb;
            var branch_id = $scope.das_route == null ? '-1' : $scope.das_route;
            $scope.LoadDataChart(branch_id, sb_id);
            $scope.getDataTablePT(branch_id, sb_id);
        }
        $scope.LoadDataChart = function(branch_id, sb_id) {
            return $http({
                method: 'GET',
                url: '/ajax/getDataChart',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': branch_id,
                }
            }).then(function (response) {
                var labels = [];
                var chart_data = [];
                for (var i in response.data) {
                    var data = response.data[i];
                    labels.push(data.survey_time);
                    chart_data.push(data.total_length);
                }
                if (myChart) {
                    myChart.destroy();
                }  
                DrawDataChart(labels, chart_data); 
                
            }, function (xhr) {

            });
        };

        $scope.getDataTablePT = function(branch_id, sb_id)
        {
            return $http({
                method: 'GET',
                url: '/ajax/getDataTablePT',
                params: {
                    'rmb_id': $scope.das_rmb,
                    'sb_id': sb_id,
                    'branch_id': branch_id,
                }
            }).then(function (response) {
                $scope.data_table  = response.data;
            }, function (xhr) {

            });
        }
    });

    function DrawDataChart(labels, chart_data)
    {
        myChart = new Chart(ctx, {
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
                            labelString: '<?php echo trans("das.Accumulated repaired length (m)"); ?>'
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
                            labelString: '<?php echo trans("das.elapsed time from lastest repair (year)"); ?>'
                       }
                    }]
                },
                legend: {
                    display: false,
                } 
            }
        });

    }
</script>
@endpush