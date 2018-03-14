<div class="row" ng-controller="SummaryRNetwork" ng-init="loadSb()">
    <article class="col-lg-12">
        @box_open('')
        <div>
            <div class="widget-body">
                {!! Form::open(["url" => "/das/export_summary_r_network", "method" => "get", "id" => "rn-transition-form"]) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            {!! 
                                Form::lbSelect(
                                    'rmb', 
                                    '',
                                    \App\Models\tblOrganization::getListRmb(),
                                    trans('back_end.Road Management Bureau'), 
                                    [
                                        'ng-model' => 'das_rmb',
                                        'change-option' => 'sbChange',
                                        'ng-init' => "das_rmb='$organization_id'"
                                    ]
                                )
                            !!}
                        </div>
                        <div class="col-lg-6">
                            {!! 
                                Form::lbSelect(
                                    "sb", 
                                    '', 
                                    [['name'=> trans('das.all'), 'value' => '']], 
                                    trans("back_end.sub_bureau"),
                                    [
                                        'ng-model' => 'das_sb',
                                        'ng-options' => 'item.id as item.organization_name for item in sb track by item.id',
                                    ]
                                ) 
                            !!}
                        </div>
                    </div>
                <input type="hidden" id="download_token_value_rn" name="downloadTokenValueRN"/>
                <div class="widget-footer">
                        <a ng-click="submitForm()" class="btn btn-md btn-primary">{!! trans('das.submit') !!}</a>
                        <button type="submit" class="btn btn-md btn-warning">{!! trans('das.export') !!}</button>
                    </div>
                {!! Form::close() !!} 
            </div>
        </div>
        @box_close
    </article>
    <article class="col-lg-12 content">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="2" style="padding-bottom: 3%">{!! trans('das.year') !!}</th>
                    <th colspan="2">{!! trans('das.ac') !!}</th>
                    <th colspan="2">{!! trans('das.bst') !!}</th>
                    <th colspan="2">{!! trans('das.cc') !!}</th>
                    <th colspan="2">{!! trans('das.total') !!}</th>
                </tr>
                <tr>
                    <th>{!! trans('das.ri') !!}</th>
                    <th>{!! trans('das.pc') !!}</th>
                    <th>{!! trans('das.ri') !!}</th>
                    <th>{!! trans('das.pc') !!}</th>
                    <th>{!! trans('das.ri') !!}</th>
                    <th>{!! trans('das.pc') !!}</th>
                    <th>{!! trans('das.ri') !!}</th>
                    <th>{!! trans('das.pc') !!}</th>
                </tr>
                </thead>
                <tbody ng-repeat="x in data">
                    <tr>
                        <td>@{{ x.year }}</td>
                        <td><div ng-controller="RILengthController" ng-init="init(x.year, 1)">@{{ data || 0 | number }}</div></td>
                        <td>@{{ x.pc_ac || 0 | number }}</td>
                        <td><div ng-controller="RILengthController" ng-init="init(x.year, 2)">@{{ data || 0 | number }}</div></td>
                        <td>@{{ x.pc_bst || 0 | number }}</td>
                        <td><div ng-controller="RILengthController" ng-init="init(x.year, 3)">@{{ data || 0 | number }}</div></td>
                        <td>@{{ x.pc_cc || 0 | number }}</td>
                        <td><div ng-controller="RILengthController" ng-init="init(x.year, 'all')">@{{ data || 0 | number }}</div></td>
                        <td>@{{ x.pc_total | number }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</div>
@push('script')
<script type="text/javascript" src="{{ asset('js/jquery.cookie.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#rn-transition-form').submit(function () {
            showLoading();
            blockUIForDownload2();
        });
    });
    var fileDownloadCheckTimerRN;
    function blockUIForDownload2() {
        var token = new Date().getTime(); //use the current timestamp as the token value
        $('#download_token_value_rn').val(token);
        fileDownloadCheckTimerRN = window.setInterval(function () {
            var cookieValue = $.cookie('fileDownloadTokenRN');
            if (cookieValue == token)
                finishDownloadRN();
        }, 1000);
    }

    function finishDownloadRN() {
        window.clearInterval(fileDownloadCheckTimerRN);
        $.removeCookie('fileDownloadTokenRN', {path: '/das'}); //clears this cookie value
        hideLoading();
    }
</script>
<script>
    app.controller('SummaryRNetwork', function($scope, $http) {
        // $('.content').hide();
        // $http({
        //     method: 'GET',
        //     url: '{{asset('ajax/das/summary')}}/all_option',
        //     params: {
        //         rmb: 1,
        //         sb: ''
        //     }
        // }).then(function (response) {
        //     $scope.data = response.data;
        //     // $('.content').show();
        // });

        $scope.sbChange = function(id) {
            // $scope.submitForm();
            return $http({
                method: 'GET',
                url: '{{asset('ajax/rmb/')}}/' + id + '/sb'
            }).then(function (response) {
                $scope.sb = response.data;
            }, function (xhr) {

            });
        };
        $scope.loadSb = function(id) {
            return $http({
                method: 'GET',
                url: '{{asset('ajax/rmb/')}}/' + '{!! $organization_id !!}' + '/sb'
            }).then(function (response) {
                $scope.sb = response.data;
            }, function (xhr) {
            });
        };

        $scope.submitForm = function () {
            showLoading();
            var sb_value = angular.element("select[name=sb]").val();
            return $http({
                method: 'GET',
                url: '{{asset('ajax/das/summary')}}/all_option',
                params: {
                    rmb: $scope.das_rmb,
                    sb: sb_value
                }
            }).then(function (response) {
                $scope.data = response.data;
                hideLoading();
            });
        }
    });

    app.controller('RILengthController', ['$scope','$http', function($scope, $http){
        $scope.init = function(year, pavement_type) {
            var sb_value = angular.element("select[name=sb]").val();
            var rmb_value = angular.element("select[name=rmb]").val();
            $http({
                method: 'GET',
                url: '{!!URL::to("ajax/das/ri_length")!!}',
                params: {
                    rmb: rmb_value,
                    sb: sb_value,
                    year: year,
                    pavement_type: pavement_type
                }
            }).then(function successCallback(response) {
                $scope.data = response.data;
            }, function errorCallback(response) {

            });
        }

        $scope.changeTime = function(time) {
            var myDate = moment(time).toDate();
            return myDate;
        }
    }]);
</script>
@endpush
@push('css')
<style>
    .table-hover thead tr th{
        text-align: center;
    }
</style>
@endpush
