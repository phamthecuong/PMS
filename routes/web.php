<?php


//
// Route::get('find_case/{s}', 'HomeController@findCase');
// Route::get('find_case/{s}', 'HomeController@findCase');
// Route::get('wp', ['uses' => 'TestController@exportWP']);

/**
 * Front-end not login
 */
Route::get('', ['as' => 'home', 'uses' => 'FrontEnd\HomeController@index']);
Route::get('user/login', ['as' => 'user.login', 'uses' => 'Auth\FrontEnd\LoginController@login']);
Route::post('user/do_login', ['as' => 'user.dologin', 'uses' => 'Auth\FrontEnd\LoginController@do_login']);
Route::get('register', ['as' => 'user.register', 'uses' => 'Auth\FrontEnd\RegisterController@register']);
Route::post('do_register', ['as' => 'user.do_register', 'uses' => 'Auth\FrontEnd\RegisterController@create']);
Route::get('/language/{lang}', array('as' => 'language.switcher', 'uses' => 'Languages\LanguagesController@lang'));

/**
 * Front-end logged in
 */

Route::group(['middleware' => 'auth', 'prefix' => 'user'], function () {
    Route::get('/home', ['as'=>'user.home', 'uses' => 'FrontEnd\HomeController@home']);
    Route::get('/logout', array('as' => 'user.logout', 'uses' => 'Auth\FrontEnd\LoginController@logout'));
    /**
     * check user role
     */
    Route::get('/check_user_role', ['as'=>'check.user.role', 'uses' => 'FrontEnd\HomeController@checkUserRole']);
    /**
     * Budget_simulation
     */

    /**
     * Change password
     */
    Route::resource('/change_password', 'Auth\FrontEnd\ChangePasswordController');

    Route::group(['prefix' => 'budget_simulation', 'namespace' => 'FrontEnd\BudgetSimulation'], function() {
        Route::get('init', array(
            'as' => 'user.budget.init', 
            'uses' => 'DatasetController@index'
        ));
        Route::get('dataset_import/{session_id}', array(
            'as' => 'user.budget.dataset_import', 
            'uses' => 'DatasetController@getDatasetImport'
        ));
        Route::get('history', array(
            'as' => 'user.budget.history', 
            'uses' => 'DatasetController@getHistory'
        ));
        Route::get('history/{session_id}', array(
            'as' => 'user.budget.history.detail', 
            'uses' => 'DatasetController@getHistoryDetail'
        ));
        Route::delete('history/{session_id}', array(
            'as' => 'user.budget.history.delete', 
            'uses' => 'DatasetController@deleteHistory'
        ));
        Route::get('repair_method/default', array(
            'as' => 'user.budget.get.repair.method.default', 
            'uses' => 'DatasetController@getListRepairMethodDefault'
        ));
        Route::get('default_repair_matrix/{session_id}', array(
            'as' => 'user.budget.get.repairmethod', 
            'uses' => 'DatasetController@getListRepairMethod'
        )); 
        Route::get('repair_matrix/{session_id}', array(
            'as' => 'user.budget.get.repairmatrix', 
            'uses' => 'DatasetController@getRepairMatrix'
        ));
        Route::get('repair_condition/{session_id}', array(
            'as' => 'user.budget.get.repaircondition', 
            'uses' => 'DatasetController@getRepairCondition'
        ));
        Route::post('repair_condition/update', array(
            'as' => 'user.budget.repaircondition.update', 
            'uses' => 'DatasetController@updateBudgetSimulationRepairCondition'
        ));
        Route::post('scenario_tab/{session_id}', array(
            'as' => 'user.budget.get.scenario_tab.process', 
            'uses' => 'DatasetController@postScenarioProcess'
        ));
        Route::get('scenario_tab/{session_id}', array(
            'as' => 'user.budget.get.scenario_tab',
            'uses' => 'DatasetController@getScenarioTab'
        ));
        Route::get('get_chart_data', array(
            'uses' => 'DatasetController@getChartData'
        ));
        Route::post('remove_customization/{session_id}', array(
            'as' => 'user.budget.remove.customization',
            'uses' => 'DatasetController@postRemoveCustomization'
        ));
        Route::get('export_file/{session_id}/{case}', array(
            'as' => 'user.budget.export',
            'uses' => 'DatasetController@getExport'
        ));
    });
    
    /**
     * Deterioration Init
     */
    Route::get('/deterioration/init', array(
        'as' => 'deterioration.init', 
        'uses' => 'FrontEnd\Deterioration\DatasetController@index'
    ));
    Route::post('/deterioration/init', array(
        'as' => 'deterioration.get.data.init', 
        'uses' => 'FrontEnd\Deterioration\DatasetController@postInit'
    ));

    /**
     * Deterioration dataset import
     */
    Route::get('/deterioration/dataset_import/{session_id}', array('as' => 'data.summary', 'uses' => 'FrontEnd\Deterioration\DatasetController@dataSummary'));
    Route::post('/deterioration/data/summary/{session_id}', array('as' => 'get.data.summary', 'uses' => 'FrontEnd\Deterioration\DatasetController@postDataSummary'));
    Route::post('/deterioration/data/back/{session_id}', array('as' => 'get.back', 'uses' => 'FrontEnd\Deterioration\DatasetController@postBack'));

    /**
     * Deterioration history
     */
    Route::get('/deterioration/history', array('as' => 'deterioration.history', 'uses' => 'FrontEnd\Deterioration\HistoryController@index'));
    Route::get('/deterioration/history/data', array('as' => 'deterioration.history.data', 'uses' => 'FrontEnd\Deterioration\HistoryController@data'));
    Route::get('/deterioration/history/column', array('as' => 'deterioration.history.column', 'uses' => 'FrontEnd\Deterioration\HistoryController@column'));
    Route::delete('/deterioration/history/delete/{id}', array('as' => 'deterioration.history.delete', 'uses' => 'FrontEnd\Deterioration\HistoryController@destroy'));
    Route::get('/deterioration/history/view/{id}', array('as' => 'deterioration.history.view', 'uses' => 'FrontEnd\Deterioration\HistoryController@show'));
    
    /**
     *  export excel
     */
    Route::get('benmarking/{id?}/{option?}','FrontEnd\Deterioration\BenchMarkingController@fileExport');
    Route::get('pavement_type/{id?}/{option?}','FrontEnd\Deterioration\PavementController@fileExport');
    Route::get('section/{id?}/{option?}','FrontEnd\Deterioration\SectionController@fileExport');
    Route::get('route/{id?}/{option?}','FrontEnd\Deterioration\RouteController@fileExport');
    
    /**
     * Deterioration dataset import
     */
    Route::get('/deterioration/benchmarking/{session_id}', array('as' => 'deterioration.benchmarking', 'uses' => 'FrontEnd\Deterioration\BenchMarkingController@index'));
    Route::get('/deterioration/pavement_type/{session_id}', array('as' => 'deterioration.pavement_type', 'uses' => 'FrontEnd\Deterioration\PavementController@index'));
    Route::get('/deterioration/route/{session_id}', array('as' => 'deterioration.route', 'uses' => 'FrontEnd\Deterioration\RouteController@index'));
    Route::get('/deterioration/section/{session_id}', array('as' => 'deterioration.section', 'uses' => 'FrontEnd\Deterioration\SectionController@index'));


    Route::get('/get_data_chart_deterioration', array('as' => 'get.data.chart.deterioration', 'uses' => 'FrontEnd\Deterioration\RouteController@getDataChart'));
    Route::get('/get_data_table_section', array(
        'as' => 'get.data.table.section', 
        'uses' => 'FrontEnd\Deterioration\RouteController@getDataTableSection'
    ));
    Route::get('/get_data_chart_deterioration_with_distress', array('as' => 'get.data.chart.deterioration.with.distress', 'uses' => 'FrontEnd\Deterioration\RouteController@getDataChartWithDistress'));
    // pavement type
    Route::get('/get_data_pavement_performance', array(
        'as' => 'get.data.pavement.performance', 
        'uses' => 'FrontEnd\Deterioration\PavementController@getDataPaymentPerformance'
    ));
    Route::get('/get_all_curves', array(
        'as' => 'get.data.pavement.performance.all', 
        'uses' => 'FrontEnd\Deterioration\PavementController@getAllPavementTypeCurve'
    ));
    Route::get('/get_data_pavement_probabilities', array(
        'as' => 'get.data.pavement.performance', 
        'uses' => 'FrontEnd\Deterioration\PavementController@getDataPaymentProbabilities'
    ));
    Route::get('/get_data_pavement_matrix', array(
        'as' => 'get.data.pavement.matrix', 
        'uses' => 'FrontEnd\Deterioration\PavementController@getDataPavementMatrix'
    ));
    Route::get('/get_data_pavement_disstress', array(
        'as' => 'get.data.pavement.with.disstress', 
        'uses' => 'FrontEnd\Deterioration\PavementController@getDataPaymentDisstress'
    ));
    // benchmarking
    Route::get('/get_data_benchmarking_hazard', array('as' => 'get.data.benchmarking.hazard', 'uses' => 'FrontEnd\Deterioration\BenchMarkingController@getDataBenchmarkingHazard'));
    Route::get('/get_data_benchmarking_probabilities', array('as' => 'get.data.pavement.performance', 'uses' => 'FrontEnd\Deterioration\BenchMarkingController@getDataBenchmarkingProbabilities'));
    Route::get('/get_data_benchmarking_matrix', array('as' => 'get.data.pavement.matrix', 'uses' => 'FrontEnd\Deterioration\BenchMarkingController@getDataBenchmarkingMatrix'));
    // check flg in database
    Route::get('/check_flg', array('as' => 'check.flg', 'uses' => 'FrontEnd\HomeController@CheckFLG'));
    Route::get('budget_simulation/check_flg', array(
        'as' => 'budget.check.flg', 
        'uses' => 'FrontEnd\BudgetSimulation\DatasetController@checkFlg'
    ));
    Route::get('work_planning/check_flg', array(
        'as' => 'wp.check.flg', 
        'uses' => 'FrontEnd\WorkPlanning\IndexController@checkFlg'
    ));
    
    // notification
    Route::get('/load_ajax_notification', array(
        'as' => 'load.ajax.notification', 
        'uses' => 'FrontEnd\HomeController@LoadAjaxNotification'
    ));
    Route::get('/change_data_notification', array(
        'as' => 'change.data.notification', 
        'uses' => 'FrontEnd\HomeController@ChangeDataNotification'
    ));
    //Export to admin DB
    Route::get('/export_to_admin_db', array(
        'as' => 'export.to.admin.db', 
        'uses' => 'FrontEnd\Deterioration\SectionController@ExportAdminDB'
    ));
    Route::get('/deterioration/core_dataset', array(
        'as' => 'core.dataset', 
        'uses' => 'FrontEnd\Deterioration\HistoryController@getCoreDataset'
    ));
    Route::get('/deterioration/get_core_dataset', array(
        'as' => 'ajax.core.dataset', 
        'uses' => 'Ajax\Deterioration\IndexController@getCoreDataset'
    ));
    //Manage Route
    //***
    // TEST HERE
    //***
    // Route::get('/tget.ajax.table.dataest', array('as' => 'user.test', 'uses' => 'FrontEnd\BudgetSimulation\DatasetController@test'));
    //-------------------

    // cuong.pt 
    /**
     * work planning
     */
    Route::group(['prefix' => 'work_planning', 'namespace' => 'FrontEnd\WorkPlanning'], function() {
        Route::get('init', array(
            'as' => 'work.planning.init', 
            'uses' => 'IndexController@index'
        ));
        Route::post('init', array(
            'as' => 'workplanning.get.data.init', 
            'uses' => 'IndexController@postSelectProcess'
        ));
        Route::get('dataset_import/{session_id}', array(
            'as' => 'get.display.data', 
            'uses' => 'IndexController@getDisplayData'
        ));
        Route::get('get_customize_repair', array(
            'as'=> 'get.customize.repair', 
            'uses' => 'IndexController@getCustomizeRepair'
        ));
        Route::get('default_repair_matrix/{session_id}', array(
            'as'=> 'get.repair.matrix', 
            'uses' => 'IndexController@getListRepairMethod'
        ));
        Route::get('getExportData', array(
            'as'=> 'get.export.data', 
            'uses' => 'IndexController@getExportData'
        ));
        Route::get('repair_matrix/{session_id}', array(
            'as' => 'user.work.get.repairmatrix', 
            'uses' => 'IndexController@getRepairMatrix'
        ));
        Route::get('repair_condition/{session_id}', array(
            'as' => 'user.work.get.repaircondition', 
            'uses' => 'IndexController@getRepairCondition'
        ));
        Route::get('base_planning/{session_id}', array(
            'as' => 'user.work.base.planning', 
            'uses' => 'IndexController@getBasePlanning'
        ));
        Route::get('forecast_index/{session_id}', array(
            'as' => 'user.work.forecast.index',
            'uses' => 'IndexController@getForecastIndex'
        ));
        Route::post('remove_customization/{session_id}', array(
            'as' => 'user.wp.remove.customization',
            'uses' => 'IndexController@postRemoveCustomization'
        ));
        Route::get('formulate_annual_year/{session_id}', array(
            'as' => 'user.wp.formulate.annual.year',
            'uses' => 'IndexController@getFormulateAnnualYear'
        ));
        Route::get('result/{session_id}', array(
            'as' => 'user.wp.result',
            'uses' => 'IndexController@getResult'
        ));
        Route::get('export_file/{session_id}/{year}/{list}', array(
            'as' => 'user.wp.export',
            'uses' => 'IndexController@postExport'
        ));
        Route::post('move_section/{session_id}/{year}/{list}/{type}', array(
            'as' => 'user.wp.move.section',
            'uses' => 'IndexController@postMoveSection'
        ));
        Route::get('export/{session_id}/{list}', array(
            'as' => 'user.wp.result_export',
            'uses' => 'IndexController@getResultExport'
        ));
        Route::get('generate/{session_id}/{list}', array (
            'as' => 'user.wp.generate_export',
            'uses' => 'IndexController@getGenerateResult'
        ));
        Route::get('history', array(
            'as' => 'user.wp.history',
            'uses' => 'IndexController@getHistory'
        ));
        Route::delete('delete/{session_id}', array(
            'as' => 'user.wp.delete',
            'uses' => 'IndexController@deleteHistory'
        ));
        Route::get('history/view/{session_id}', array(
            'as' => 'user.wp.history.view',
            'uses' => 'IndexController@getViewHistory'
        ));
        Route::get('proposal/{session_id}', array( 
            'as' => 'user.wp.proposal',
            'uses' => 'IndexController@getProposal'
        ));
        Route::get('planned/{session_id}', array (
            'as' => 'user.wp.planned',
            'uses' => 'IndexController@getPlanned'
        ));
        Route::group(['prefix' => 'planned_section'], function () {
            Route::get('/', 'PlannedSectionController@index');
            Route::get('import_data', 'PlannedSectionController@getImportData');
            Route::post('/', 'PlannedSectionController@postDeletePlannedSection');
            Route::get('import/{file_name}', 'PlannedSectionController@getImport');
            Route::get('{file_name}/import/{id}', 'PlannedSectionController@getSinglePlannedData');
            Route::put('{file_name}/import/{id}', 'PlannedSectionController@editSinglePlannedData');
            Route::get('{file_name}/check', 'PlannedSectionController@getCheck');
            Route::post('validate', 'PlannedSectionController@postValidate');
            Route::get('import/{file_name}/error', 'PlannedSectionController@getImportErrorData');
            Route::get('import/{file_name}/success', 'PlannedSectionController@getImportSuccessData');
            Route::post('{file_name}/import', 'PlannedSectionController@postImport');
            Route::get('download_en', 'PlannedSectionController@getDownloadEn');
            Route::get('download_vi', 'PlannedSectionController@getDownloadVi');  
        });
    });

    //test create file input
    // Route::get('/work_planning/create_input/{session_id}',['uses' => 'FrontEnd\WorkPlanning\IndexController@creatInputFile']);
    
    Route::group(array('prefix' => 'pms_dataset', 'namespace' => '\FrontEnd\PMSDataset'), function(){
        
        Route::post('export/{index}/{year}/{type}','IndexController@exportDataReport');
        Route::get('report/{year}', 'IndexController@getReport');
        Route::resource('', 'IndexController');
        Route::get('pc_import', 'IndexController@getPCImport');
    });
  
});

Route::group(array('middleware' => 'auth', 'prefix' => 'ajax', 'namespace' => 'Ajax'), function() {
    Route::group(array('prefix' => 'budget', 'namespace' => 'BudgetSimulation'), function() {
        Route::get('/init/region', array(
            'as' => 'ajax.budget.get.region', 
            'uses' => 'DatasetController@getListRegion'
        ));
        Route::get('/init/road', array(
            'as' => 'ajax.budget.get.road', 
            'uses' => 'DatasetController@getListRoad'
        ));
        Route::post('/init/create', array(
            'as' => 'ajax.budget.create.init', 
            'uses' => 'DatasetController@createInit'
        ));
        Route::put('/defualt_repair_matrix/{session_id}', array(
            'as' => 'ajax.budget.update.default.repair.matrix', 
            'uses' => 'DatasetController@updateBudgetSimulationRepairMethod'
        ));     
        Route::post('/repair_matrix/create_csv', array(
            'as' => 'ajax.budget.repair.matrix.create.file.csv', 
            'uses' => 'DatasetController@createRepairMatrixCSV'
        ));
        Route::post('/repair_matrix/create', array(
            'as' => 'ajax.budget.create.repair.matrix', 
            'uses' => 'DatasetController@createRepairMatrix'
        ));
        Route::delete('/repair_matrix/delete', array(
            'as' => 'ajax.budget.repair.matrix.remove', 
            'uses' => 'DatasetController@deleteRepairMatrixValue'
        ));
        Route::post('/repair_condition/create', array(
            'as' => 'ajax.budget.create.repair.condition', 
            'uses' => 'DatasetController@createRepairCondition'
        ));
        Route::get('history', array(
            'as' => 'ajax.budget.history.data', 
            'uses' => 'DatasetController@getHistory'
        ));
    });

    Route::group(array('prefix' => 'work', 'namespace' => 'WorkPlanning'), function() {
        Route::get('/init/region', array(
            'as' => 'ajax.work.get.region', 
            'uses' => 'DatasetController@getListRegion'
        ));
        Route::post('/init/create', array(
            'as' => 'ajax.work.create.init', 
            'uses' => 'DatasetController@createInit'
        ));
        Route::get('/dataTable/create/{session_id}/{type?}/{list?}', array(
            'as' => 'ajax.work.create.dataTable', 
            'uses' =>'DatasetController@getAjaxDataTable'
        ));
        Route::put('update/default.repair/matrix/{session_id}', array(
            'as' => 'ajax.work_planning.update.base_planning_year', 
            'uses' => 'DatasetController@updateWorkPlanningBasePlanningYear'
        ));
        Route::get('/repair_matrix/road_category', array(
            'as' => 'ajax.work.get.road.category', 
            'uses' => 'DatasetController@getListRoadCategory'
        ));
        Route::get('/repair_matrix/road_class', array(
            'as' => 'ajax.work.get.road.class', 
            'uses' => 'DatasetController@getListRoadClass'
        ));
        Route::post('/repair_matrix/create', array(
            'as' => 'ajax.work.create.repair.matrix', 
            'uses' => 'DatasetController@createRepairMatrix'
        ));
        Route::get('/repair_matrix/condition_rank', array(
            'as' => 'ajax.work.condition.rank', 
            'uses' => 'DatasetController@getConditionRank'
        ));
        Route::post('/repair_matrix/create_csv', array(
            'as' => 'ajax.work.repair.matrix.create.file.csv', 
            'uses' => 'DatasetController@createRepairMatrixCSV'
        ));
        Route::post('formulate_annual_year', array(
            'as' => 'ajax.wp.formulate.annual.year',
            'uses' => 'DatasetController@postFormulateAnnualYear'
        ));
        Route::get('total_cost/{session_id}/{list}', array(
            'as' => 'ajax.wp.total.cost', 
            'uses' => 'DatasetController@getTotalCost'
        ));
        Route::get('formulate_annual_year/criteria', array(
            'as' => 'ajax.wp.formulate.annual.year.criteria',
            'uses' => 'DatasetController@findCriteria'
        ));
        Route::post('generate', array(
            'as' => 'ajax.wp.generate.excel',
            'uses' => 'DatasetController@postGenerate'
        ));
        Route::get('history', array(
            'as' => 'ajax.wp.history',
            'uses' => 'DatasetController@getHistory'
        ));
        Route::post('proposal', array( 
            'as' => 'ajax.wp.proposal',
            'uses' => 'DatasetController@postProposal'
        ));
        Route::post('planned', array(
            'as' => 'ajax.wp.planned',
            'uses' => 'DatasetController@postPlanned'
        ));
        Route::post('planned/save', array(
            'as' => 'ajax.wp.save.plan',
            'uses' => 'DatasetController@postSavePlan'
        ));
        Route::get('planned_section', 'PlannedSectionController@index');
        
    });

    Route::resource('pms_dataset', 'PMSDataset\IndexController');
});

/**
 * Admin login
 */
Route::group(['middleware' => ['auth']], function () {
    Route::get('admin/logout', '\App\Http\Controllers\Auth\Admin\LoginController@logout');
    // service return segments list belongs to a branch
    Route::get('/segment/service/list', array('as' => 'segment.in.branch', 'uses' => 'Admin\SegmentController@getSegmentsInBranch'));
    // Route::resource('admin_manager', 'Admin\AdminController');

    Route::get('get_ajax_datatable/{name}', array('as' => 'get.ajax.table.data', 'uses' => 'Controller@GetTableAjaxData'));
    Route::get('get_config_table', array('as' => 'get.config.table', 'uses' => 'Controller@GetConfigTable'));
    
    Route::resource('branch', 'Admin\BranchController');

    
    Route::get('/check_segment_exist', array('as' => 'check.segment.exits', 'uses' => 'Admin\SegmentController@CheckSegmentExits'));
    Route::get('/get_data_header_sb_segment', array('as' => 'get.data.header.branch.segment', 'uses' => 'Admin\SegmentController@get_header_SB'));
    Route::get('/get_data_bureau_segment/{id}', array('as' => 'get.data.bureau.segment', 'uses' => 'Admin\SegmentController@getBureau'));
    Route::get('/get_data_route_segment/{id}', array('as' => 'get.data.route.segment', 'uses' => 'Admin\SegmentController@getRoute'));
    Route::get('/get_data_branch_segment/{id}', array('as' => 'get.data.branch.segment', 'uses' => 'Admin\SegmentController@getBranch'));
    Route::get('/check_segment_neutral', array('as' => 'check.segment.neutral', 'uses' => 'Admin\SegmentController@postNeutral'));
    Route::get('/check_segment_component', array('as' => 'check.segment.component', 'uses' => 'Admin\SegmentController@component'));
    Route::post('/component', array('as' => 'component', 'uses' => 'Admin\SegmentController@proceed'));
    // manage RMB
    Route::resource('organization', 'Admin\OrganizationController');
    // manage SB
    Route::get('SB/{id}',array('as' => 'listSB' , 'uses' => 'Admin\OrganizationController@showlistSB' ));

    Route::get('SBcreate/{id}',array('as' => 'sbcreate' , 'uses' => 'Admin\OrganizationController@sbcreate' ));
    Route::post('SBstore', array('as' => 'sbstore', 'uses' => 'Admin\OrganizationController@sbstore'));

    Route::get('editSB/{id}',array('as' => 'editSB' , 'uses' => 'Admin\OrganizationController@editSB' ));
    Route::post('updateSB/{id}', array('as' => 'updateSB', 'uses' => 'Admin\OrganizationController@updateSB'));

    Route::post('destroySB/{id}', array('as' => 'destroySB', 'uses' => 'Admin\OrganizationController@destroySB'));

    Route::get('MergeSB/{id}/{idSB}',array('as' => 'list.sb' , 'uses' => 'Admin\OrganizationController@Merge'));
    Route::post('SB/meger',array('as' => 'SB.Merge' , 'uses' => 'Admin\OrganizationController@MergeSB'));
    // MergeRMB
    Route::get('/MergeRMB/{id}',array('as' => 'list.RMB' , 'uses' => 'Admin\OrganizationController@MergeRMB'));
    Route::post('/MegerRMB',array('as' => 'MergeRMB' , 'uses' => 'Admin\OrganizationController@PostMergeRMB'));
    
    Route::get('/segment/{action}/{segment_id}',array('as' => 'list.segment' , 'uses' => 'Admin\SegmentController@getListSegment'));

    Route::post('/segment/merge', array('as' => 'segment.merge', 'uses' => 'Admin\SegmentController@postMergeSegment'));
    Route::post('/segment/split', array('as' => 'segment.split', 'uses' => 'Admin\SegmentController@postSplitSegment'));

    Route::resource('condition_rank', 'Admin\ConditionRankController');
    Route::get('/condition_rank_table', array('as' => 'rank.table', 'uses' => 'Admin\ConditionRankController@table'));
    Route::POST('/condition_rank_update', array('as' => 'rank.update', 'uses' => 'Admin\ConditionRankController@update_condition'));
});

/**
 * New back-end
 */
// Route::get('logout', array('uses' => 'Auth\FrontEnd\LoginController@logout'));
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function() {
    // admin manager
    Route::get('admin_manager/{id}/change_password', 'Admin\AdminController@changePassword');
    Route::post('admin_manager/{id}/change_password', [
        'as' => 'admin.change.password',
        'uses' => 'Admin\AdminController@postChange']);
    Route::resource('admin_manager', 'Admin\AdminController');
    // admin manager - end 

    // user manager
    Route::get('user_manager/{id}/change_password', 'Admin\UserController@changePassword');
    Route::post('user_manager/{id}/change_password', [
        'as' => 'user.change.password',
        'uses' => 'Admin\UserController@postChange']);
    Route::resource('user_manager', 'Admin\UserController');
    // user manager - end

    // repair method manager
    Route::post('/repair_methods/{id}/cost', 'Admin\RepairMethodController@postCostSetting');
    Route::get('/repair_methods/{id}/cost', 'Admin\RepairMethodController@getCostSetting');
    Route::resource('/repair_methods', 'Admin\RepairMethodController');
    // repair method manager - end

    Route::resource('/routes', 'Admin\RouteController');
    Route::resource('/road_class', 'Admin\RoadClassController',['names'=> ['destroy' => 'admin.road_class.destroy']]);
    Route::resource('/pavement_types', 'Admin\PavementTypeController');
    Route::resource('repair_matrix', 'Admin\RepairMatrixController');

    // road inventory 
    Route::get('road_inventory/{history_id}/delete', 'FrontEnd\M13\RoadInventoryController@delete');
    Route::get('road_inventory/{history_id}/newsurvey', 'FrontEnd\M13\RoadInventoryController@newsurvey');
    Route::get('/road_inventories/export', 'FrontEnd\M13\RoadInventoryController@getExport');
    Route::post('/road_inventories/export', 'FrontEnd\M13\RoadInventoryController@postExport');
    Route::resource('/road_inventories', 'FrontEnd\M13\RoadInventoryController');
    // road inventory - end

    // maintenance history
    Route::get('/maintenance_history/export', 'FrontEnd\M13\MaintenanceHistoryController@getExport');
    Route::post('/maintenance_history/export', 'FrontEnd\M13\MaintenanceHistoryController@postExport');
    Route::resource('/maintenance_history', 'FrontEnd\M13\MaintenanceHistoryController');
    // maintenance history - end

    // traffic volume
    Route::get('/traffic_volume/export', 'FrontEnd\M13\TrafficVolumeController@getExport');
    Route::post('/traffic_volume/export', 'FrontEnd\M13\TrafficVolumeController@postExport');
    Route::resource('/traffic_volume', 'FrontEnd\M13\TrafficVolumeController');
    // traffic volume - end

    // segment manager
    Route::resource('manager_segment', 'Admin\SegmentController');
    Route::get('manager_segment/{id}/delete', array(
        'as' => 'manager.delete', 
        'uses' => 'Admin\SegmentController@delete'
    ));

    // pc backend
    Route::get('migrate_pc', [
            'as' => 'migrate_pc',
            'uses' => 'Admin\PCController@migratepc'
        ]);
    Route::get('migrate_pc/create', [
            'as' => 'migrate_pc.create',
            'uses' => 'Admin\PCController@migratepcCreate'
        ]);
    Route::post('migrate_pc/create', [
            'as' => 'migrate_pc.store',
            'uses' => 'Admin\PCController@migratepcStore'
        ]);
    // pc backend - end

});

//Data review tool
Route::group(['middleware' => 'auth', "namespace" => "FrontEnd\M13"], function () {
    Route::get('road_inventory/data_review_tool', 'RoadInventoryController@getDataReviewTool');
    Route::post('road_inventory/data_review_tool', 'RoadInventoryController@postDataReviewTool');
    Route::get('maintenance_history/data_review_tool', 'MaintenanceHistoryController@getDataReviewTool');
    Route::post('maintenance_history/data_review_tool', 'MaintenanceHistoryController@postDataReviewTool');
    Route::resource('pavement_conditions', 'PaymentConditionController'); //PC-table
    Route::get('pavement_condition/export', 'PaymentConditionController@getExport');
    Route::post('pavement_conditions/post_export', 'PaymentConditionController@postExport');
});
// import
Route::group(['middleware' => 'auth', "namespace" => "FrontEnd\M13\Import"], function () { //"middleware" => ["auth"],
    $data =  [
        ['name' => 'road_inventories', 'use' => 'RoadInventoryController'],
        ['name' => 'maintenance_history', 'use' => 'MaintenanceHistoryController'],
        ['name' => 'traffic_volume', 'use' => 'TrafficVolumeController']
    ];
    foreach ($data as $value) {
        Route::group(['prefix' => $value['name'] ], function() use ($value) {
            Route::get('download_en', $value['use'].'@downloadEN');
            Route::get('download_vi', $value['use'].'@downloadVI');
            Route::get('import_data', $value['use'].'@getImportData');
            Route::post('validate', $value['use'].'@postValidate');
            Route::group(['prefix' => '{file_name}'], function($file_name) use ($value) {
                Route::get('new', $value['use'].'@ajax_new');
                Route::get('ajax', $value['use'].'@ajax');
                Route::get('ajax_success', $value['use'].'@ajax_success');
                Route::get('ajax_update', $value['use'].'@ajax_update');
                Route::get('ajax_ignore', $value['use'].'@ajax_ignore');
                Route::get('ignore/{id}', $value['use'].'@ignore');
                Route::get('restore/{id}', $value['use'].'@restore');
                Route::get('export_invalid', $value['use'].'@exportInvalid');
                Route::resource('import', $value['use']);
                Route::get('check/{id}', $value['use'].'@getCheck');
            });
        });
    }
});
//import - end

Route::group(['middleware'=> 'auth', 'prefix'=> 'ajax'], function (){
    Route::resource('design_speed', 'Ajax\Backend\DesignSpeedController');
    Route::resource('admin_manager', 'Ajax\Backend\AdminController');
    Route::resource('user_manager', 'Ajax\Backend\UserController');
    Route::resource('route.segment', 'Ajax\Backend\RouteSegmentController');
    Route::resource('route.branch_no', 'Ajax\Backend\RouteBranchController');
    Route::resource('sb.route', 'Ajax\Backend\SbRouteController');
    Route::resource('rmb.sb', 'Ajax\Backend\RmbSbController');
    Route::resource('role.organization', 'Ajax\Backend\RoleOrganizationController');
    Route::resource('user_role.organization', 'Ajax\Backend\UserRoleOrganizationController');
    Route::resource('route', 'Ajax\Backend\RouteController');
    Route::resource('road_class', 'Ajax\Backend\RoadClassController');
    Route::resource('repair_method', 'Ajax\Backend\RepairMethodController');
    Route::resource('pavement_type', 'Ajax\Backend\PavementTypeController');
    Route::resource('repair_matrix', 'Ajax\Backend\RepairMatrixController');
    Route::resource('road_inventory', 'Ajax\Backend\RoadInventoryController');
    Route::resource('traffic_column', 'Ajax\Backend\TrafficVolumeController');
    Route::resource('maintenance_history', 'Ajax\Backend\MaintenanceHistoryController');
    Route::get('pc/migrate_process', 'Ajax\Backend\PCController@getProcess');
    Route::get('download/{id}/err', 'FrontEnd\M13\Import\DownloadErrController@getdata');
    Route::resource('payment_condition', 'Ajax\Backend\PaymentConditionController');//PC table C.pt

    Route::get('das/summary/all_option', 'Ajax\DAS\SummaryOfRoadNetworkAndPC@index');//DAS
    Route::get('das/ri_length', 'Ajax\DAS\SummaryOfRoadNetworkAndPC@getRILength');//DAS
    Route::get('das/{rmb}/year', 'Ajax\DAS\SummaryPCController@getYear');//DAS
    Route::get('das/{rmb}/road', 'Ajax\DAS\SummaryPCController@getRoad');//DAS
    Route::post('das/first_chartPC', 'Ajax\DAS\SummaryPCController@firstChart');//summarryPC
    Route::post('das/second_chartPC', 'Ajax\DAS\SummaryPCController@secondChart');//summarryPC
    Route::post('das/third_chartPC', 'Ajax\DAS\SummaryPCController@thirdChartPC');//summarryPC
    Route::post('das/fourth_chartPC', 'Ajax\DAS\SummaryPCController@fourthChartPC');//summarryPC
    Route::get('das/getDataTable_PC', 'Ajax\DAS\SummaryPCController@getDataTable');//summarryPC
    Route::get('/export_summaryPC', 'Ajax\DAS\SummaryPCController@exportData');//summarryPC
    Route::resource('getDataChart/','Ajax\DAS\SummaryPassedTimeController');//summaryPT
    Route::resource('getDataChartMR/','Ajax\DAS\SummaryMRController');//summarryMR
    Route::get('getDataChartRW/','Ajax\DAS\SummaryMRController@repairWork');//summarryMR
    Route::resource('das/transition_pc','Ajax\DAS\TransitionPCController');//transitionPC
    Route::get('getDataTable/','Ajax\DAS\SummaryMRController@getDataTable');//summarryMR
    Route::get('/export_MR/','Ajax\DAS\SummaryMRController@exportSummaryMR');//summarryMR
    //TimeSeriresPC
    Route::get('getYearTimeSerires', 'Ajax\DAS\TimeSeriesPCController@loadYear');
    Route::get('getSecondYearTimeSerires', 'Ajax\DAS\TimeSeriesPCController@loadSecondYear');
    Route::resource('getDataTimeSeriesPC', 'Ajax\DAS\TimeSeriesPCController');
    Route::get('/export_TS', 'Ajax\DAS\TimeSeriesPCController@getExportSummaryTimeSeries');
    Route::resource('getDataTablePT', 'Ajax\DAS\SummaryPassedTimeController@getDataTable');

    Route::group(['prefix'=> 'frontend', 'namespace'=> 'Ajax\Frontend'], function () {
        Route::resource('maintenance_history', 'MaintenanceHistoryController');
        Route::resource('maintenance_history_survey', 'MaintenanceHistorySurveyController');
        Route::resource('road_inventory', 'RoadInventoryController');
        Route::resource('road_inventory_survey', 'RoadInventorySurveyController');
        Route::resource('traffic_volume', 'TrafficVolumeController');
        Route::resource('traffic_volume_survey', 'TrafficVolumeSurveyController');
        Route::resource('data_segment', 'DataSegmentController');
        Route::resource('road_inventory', 'RoadInventoryController');
        Route::resource('province.district', 'ProvinceDistrictController');
        Route::resource('district.ward', 'DistrictWardController');
        Route::resource('ward.info_pd', 'WardInfoPDController');
        Route::resource('getDataZoomZone', 'DataZoomZoneController');
        Route::resource('terrain.road_class', 'TerrainRoadClassController');
        Route::resource('getDataHistory', 'DataHistoryController');
        Route::resource('repair_method.classification', 'RepairMethodClassificationController');
        Route::resource('surface.repair_category', 'SurfaceRepairCategoryController');
        Route::resource('material_type.surface', 'MaterialTypeSurfaceController');
        Route::resource('checksurface.repair_category', 'CheckSurfaceController');
        Route::resource('getsurface.repair_category', 'GetSurfaceController');
        Route::resource('rmb.repair_method.cost', 'RmbRepairMethodCostController');
        Route::get('getDataPmos','DataHistoryController@getData');
        Route::get('getDataPmosHistory','DataHistoryController@getDataPmosHistory');
        Route::get('getData_TV','DataHistoryController@getData_TV');

    });
    
    //Ajax data review tool
    Route::group(['prefix'=> 'backend', 'namespace'=> 'Ajax\Backend'], function () {
        Route::get('road_inventory/review', 'RoadInventoryController@review');
        Route::get('maintenance_history/review', 'MaintenanceHistoryController@review');

    });
});

// web-display pavement condition
Route::get('web_map','FrontEnd\M24\DataMapController@webMap');
Route::get('get_sb_list', 'Ajax\Map\IndexController@getOrganization');
Route::get('get_route_list', 'Ajax\Map\IndexController@getRouteList');
Route::get('get_year_value', 'Ajax\Map\IndexController@getYear');
Route::get('get_center', 'Ajax\Map\IndexController@getCenter');
Route::post('map_login', 'Ajax\Map\IndexController@mapLogin');
Route::post('search_map', 'Ajax\Map\IndexController@searchMap');
Route::post('data_map', 'Ajax\Map\IndexController@getDataMap');
Route::get('get_lane_data', 'FrontEnd\M24\DataMapController@getLaneData');


// web-display pavement condition - end=======
// web-display pavement condition - end
Route::group(['middleware'=> 'auth'], function () {
    Route::resource('inputting', 'FrontEnd\M13\InputtingController');
    Route::resource('pmos', 'FrontEnd\PMoS\PavementMonitoringSoftwareController');
    Route::resource('condition', 'Admin\ConditionRankController');
    Route::get('getCrack', 'Admin\ConditionRankController@getCrack');
    
});


Route::get('/deterioration/{session_id}','FrontEnd\Deterioration\DatasetController@postDataSummary');

// Route::get('draw_chart_js', function(){
//     return view('chart_js');
// });

// DAS
Route::get('testView', function(){
    return 1;
});
Route::group(['middleware'=> 'auth', 'prefix'=> 'das', 'namespace' => 'FrontEnd\DAS'], function (){

    Route::resource('/', 'DASController');
    Route::get('export_summary_r_network', 'DASController@getExportSummaryRNetwork');
   // Route::get('export_PT', 'DASController@getExportPT');
    Route::get('export_transition_pc', 'DASController@getExportTransitionPC');
    Route::get('export_summary_mr', 'DASController@getExportSummaryMR');
    Route::get('export_PT', 'DASController@getExportSummaryPassedTime');

});

Route::get('Dependency', 'DependencyController@index');
Route::group(['middleware'=> 'auth'], function (){
    Route::get('inputting_test','FrontEnd\M13\InputtingController@index');
});
Route::get('test', function() {
    return 1;
});