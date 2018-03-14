<?php

namespace App\Http\Controllers\FrontEnd\M13;

use App\Classes\Helper;
use App\Http\Requests\BackendRequest\ImportRequest;
use App\Models\mstPavementType;
use App\Models\mstRoadClass;
use App\Models\mstSurface;
use App\Models\tblRCategory;
use App\Models\tblRClassification;
use App\Models\tblTerrainType;
use Illuminate\Http\Request;
use App\Models\tblRMDHistory;
use App\Models\tblSegment;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Http\Controllers\Controller;
use File;
use Excel;
use Illuminate\Support\Facades\Storage;
use URL;
use Carbon\Carbon;
use DB;

use Yajra\Datatables\Facades\Datatables;
use Webpatser\Uuid\Uuid;

class BaseImportController extends Controller
{
    protected $models;
    protected $prefix_url;
    protected $custom_column_datatable;
    protected $except;
    protected $start_row;
    protected $active;
    protected $ribbon;
    protected $view;
    protected $check;
    function __construct($models, $prefix_url, $custom_column_datatable, $view = null, $check = null, 
        $except = null, $active = NULL, $ribbon = NULL, $start_row = 2)
    {
        array_unshift($custom_column_datatable, 'id');
        $this->models = $models;
        $this->prefix_url = $prefix_url;
        $this->custom_column_datatable = $custom_column_datatable;
        $this->except = $except;
        $this->start_row = $start_row;
        $this->active = $active;
        $this->ribbon = $ribbon;
        $this->view = $view;
        $this->check = $check;
    }

    /**
     * Config for road_asset.
     *
     * @return []
     */
    function allConfig($validate = FALSE)
    {
        $configs = [];
        $model = $this->models;
        if (empty($this->except))
        {
            $configs[] = $model::config();
        }
        else
        {
            $configs[] = $model::config($this->except);
        }
        if (!$validate)
        {
            $arr = [];
            foreach ($configs as $config)
            {
                foreach ($config as $key => $value)
                {
                    $arr[$key] = $value;
                }
            }
            return $arr;
        }
        else
        {
            $arr = [];
            foreach ($configs as $config) 
            {
                $arr[] = $config;
            }
            return $arr;
        }
    }

    function getConfig()
    {
        $configs = [];
        $config = [];
        $model = $this->models;
        if (empty($this->except))
        {
            $configs[] = $model::config();
        }
        else
        {
            $configs[] = $model::config($this->except);
        }
        $arr = [];
        foreach ($configs as $config) 
        {
            $arr[] = $config;
        }
        return $arr;
    }
    
    public function getImportData()
    {
        return view('front-end.m13.import.import', [
            'prefix_url' => $this->prefix_url,
            'ribbon' => $this->ribbon
        ]);
    }

    public function postValidate(ImportRequest $request)
    {
        try
        {
            $data_pavement = mstPavementType::where('pavement_layer_id',2)->orderBy('id','ASC')->pluck('id', 'code');
            // $timess = microtime(true);
            $configs = [];
            $model = $this->models;
            if (empty($this->except))
            {
                $configs[] = $model::config();
            }
            else
            {
                $configs[] = $model::config($this->except);
            }
            $arr = [];
            foreach ($configs as $config) 
            {
                $arr[] = $config;
            }
            $data = null;
            $success = [];
            $error = [];
            $all = [];
            $file = $request->file;
            $all_request = $request->except('_token');
            if ($request->hasFile("file"))
            {
                $path = $file->store('upload_excel');
                $data = Helper::preProcessing($path, $this->allConfig(TRUE), $this->start_row);
            }
            else
            {
                $data = $all_request['data'];
            }

            if (empty($data))
            {
                return redirect()->back()->with('empty', trans('validator.empty_record'));
            }
            else
            {
                $dataQuery = [];
                
                // $times = microtime(true);
                foreach ($data as $record)
                {
                    if ($this->prefix_url == 'traffic_volume')
                    {
                        $record['survey_time'] = $record['survey_time'] . '/12';
                    }
                    // $timesval = microtime(true);
                    $resValidate = Helper::validate($record, $arr, $dataQuery, $this->models, null, null, $data_pavement);
                   // echo "validate: ", microtime(true) - $timesval, '<br/>';
                    $dataQuery = $resValidate['query'];
                    //
                    $resValidate['ignore'] = 0;

                    unset($resValidate['query']);
                    // $timecsv = microtime(true);
                    //check overlapping with other record in same find to decide ignoring or not
                    // if ($this->_csvOverlapCheck($resValidate, $all))
                    // {
                    //     $resValidate['ignore'] = 1;
                    // }
                    // echo "csv: ", microtime(true) - $timecsv, '<br/>';
                    // echo "*****************************************<br/>";
                    $all[] = $resValidate;
                }
                //echo "validate all: ", microtime(true) - $times, '<br/>';
                
                foreach ($all as $all_key => $result)
                {
                    if (isset($result['err']))
                    {
                        $err = $result['err']->toArray();
                        !empty($err) ? $error[$all_key] = $result : $success[$all_key] = $result;
                    }
                    else
                    {
                        $success[$all_key] = $result;
                    }
                }
               // dd($error);
                // $time = microtime(true);
                $file_name = str_replace('-', '', Uuid::generate()->string);
                $file_path = $this->getFilePath($file_name);
                $this->saveCSV($success, $error, $file_path);
                // echo "save csv: ", microtime(true) - $time, '<br/>';
                // echo "all: ", microtime(true) - $times, '<br/>';
                // dd('end');
                return redirect($this->prefix_url . '/' . $file_name . '/import');
            }
        }
        catch(\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($file_name)
    {
        $arr = $this->allConfig();
        $import_error = $this->dataError($file_name);
        $data_success = $this->dataSuccess($file_name);
        // $count_success = Helper::checkSurveyTime($data_success, $this->check, $this->prefix_url);
        // $data_all = $this->allData($file_name);
        // $count_all = Helper::checkSurveyTime($data_all, $this->check, $this->prefix_url);
        return view('front-end.m13.import.validate', [
            'config' => $arr,
            'import_error' => $import_error,
            'count_success' => 0,
            'count_all' => count($import_error) + count($data_success),
            'file_name' => $file_name,
            'prefix_url' => $this->prefix_url,
            'custom_column_datatable' => $this->custom_column_datatable
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd($this->allConfig(false));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $file_name)
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($file_name, $id)
    {
        $data_pavement = mstPavementType::where('pavement_layer_id',2)->orderBy('id','ASC')->pluck('id', 'id');
        $configs = [];
        $model = $this->models;
        if (empty($this->except))
        {
            $configs[] = $model::config();
        }
        else
        {
            $configs[] = $model::config($this->except);
        }
        $arr = [];
        foreach ($configs as $config) 
        {
            $arr[] = $config;
        }
        $data = $this->getDataCSVByRow($id, $file_name);
        if ($data['error'] == 2)
        {
            $data['excel_overlap'] = true;
        }
        $all = Helper::validate($data, $arr, null, $this->models, null, null, $data_pavement);
        return $this->transform($all);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($file_name, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $file_name, $id)
    {
        $data_pavement = mstPavementType::where('pavement_layer_id',2)->orderBy('id','ASC')->pluck('id', 'id');
        $file_path = $this->getFilePath($file_name);
        $all_request = $request->except(['_token', 'err', 'excel_overlap']);
        $data_success = $this->dataSuccess($file_name);
        $all = Helper::validate($all_request, $this->allConfig(TRUE), null, $this->models, null, null, $data_pavement);
        if (count(($all['err'])) == 0)
        {
            $check_ignore = true;
            $all_data = $this->allData($file_name);
            foreach ($all_data as $key => $value)
            {
                if ($value['ignore'] == 0)
                {
                   $check_ignore = false;
                }
                if ($value['id'] == $id)
                {
                    $all_data[$key] = $this->sortByConfig($all_request, $this->convertConfig($this->allConfig()), $check_ignore);
                    break;
                }
            }

            $this->writeCSV($all_data, $file_path);
            $error_data = $this->dataError($file_name);
            if (count($error_data) == 0)
            {
                return 2;
            }
            return 1;
        }
        else
        {
            return response($all['err'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCheck($file_name)
    {
        $arr = $this->allConfig();
        $import_error = $this->dataError($file_name);
        $data_success = $this->dataSuccess($file_name);
        $count_success = Helper::checkSurveyTime($data_success,$this->check, $this->prefix_url);
        
        $new = count($count_success['new']);
        $update = count($count_success['update']);
        $data_all = $this->allData($file_name);
        $count_all = Helper::checkSurveyTime($data_all,$this->check, $this->prefix_url);
        $err = count($count_all['err']);
        $ignore = count($count_all['ignore']);
        return array(
            'new' => $new,
            'update' => $update,
            'err' => $err,
            'ignore' => $ignore
        );
    }

    public function ajax($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $data_success = $this->allData($file_name);
        $backup = Helper::checkSurveyTime($data_success,$this->check, $this->prefix_url);
        foreach ($backup['err'] as  $value) {
            if (is_numeric($value['segment_id'])) {
                $segment = tblSegment::where('id',$value['segment_id'])->first();
                $value['segment_id'] = $segment['segname_'.$key];
            }
            if (is_numeric($value['road'])) {
                $road = tblBranch::where('id',$value['road'])->first();
                $value['road'] = $road['name_'.$key];
            }
            if (is_numeric($value['rmb'])) {
                $rmb = tblOrganization::where('id',$value['rmb'])->first();
                $value['rmb'] = $rmb['name_'.$key];
            }
            if (is_numeric($value['sb'])) {
                $sb = tblOrganization::where('id',$value['sb'])->first();
                $value['sb'] = $sb['name_'.$key];
            }
            $save[] = $value;
        }
        $data  = collect($save);
        return Datatables::of($data)
            ->addColumn('extra_view', function($t) use (&$bu) {
                return view($this->view,['show'=>$t]);
            })
            ->addColumn('status', function ($c) use (&$i) {
                    $err = [];
                    $overlap = [];
                    if($c['error'] == 1)
                    {
                        $err[] = '<p style="color: red;">'. trans('back_end.status_err') .'</p>';
                         return implode(' ', $err);
                    }
                    if($c['error'] == 2)
                    {
                        $overlap[] = '<p style="color: red;">'. trans('back_end.overlap') .'</p>';
                         return implode(' ', $overlap);
                    }
                })
            ->addColumn('id', function ($c) use (&$i) {
                    $button = [];
                    
                    $button[] = $c['id'];
                return implode(' ', $button);
            })
            ->editColumn(@'direction', function ($c){
                if(@$c['direction'] == 1)
                {
                    return trans('back_end.left_direction');
                }
                if(@$c['direction'] == 2)
                {
                    return trans('back_end.right_direction');

                }
                if(@$c['direction'] == 3)
                {
                    return trans('back_end.single_direction');
                }
                if(@$c['direction'] == '')
                {
                    return '';
                }

            })
            ->make(true);
    }

    public function ajax_new($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $check = $this->dataSuccess($file_name);
        $backup = Helper::checkSurveyTime($check,$this->check, $this->prefix_url);
        foreach ($backup['new'] as  $value) {
            $segment = tblSegment::where('id',$value['segment_id'])->first();
            $road = tblBranch::where('id',$value['road'])->first();
            $rmb = tblOrganization::where('id',$value['rmb'])->first();
            $sb = tblOrganization::where('id',$value['sb'])->first();
            if ($this->prefix_url == 'road_inventories')
            {
                $value['terrian_type_id'] = @tblTerrainType::where('id', $value['terrian_type_id'])->first()->name;
                $value['road_class_id'] = @mstRoadClass::where('id', $value['road_class_id'])->first()->name;
            }
            if ($this->prefix_url == 'maintenance_history')
            {
                $value['r_classification_id'] = @tblRClassification::where('id', $value['r_classification_id'])->first()->name;
                $value['r_category_id'] = @tblRCategory::where('id', $value['r_category_id'])->first()->name;
            }
            $value['segment_id'] = $segment['segname_'.$key];
            $value['road'] = $road['name_'.$key];
            $value['rmb'] = $rmb['name_'.$key];
            $value['sb'] = $sb['name_'.$key];
            $save[] = $value;
        }
        $data = collect($save);
        return Datatables::of($data)
            ->addColumn('extra_view', function($t) use (&$bus) {
                return view($this->view,['show'=>$t]);
            })
            ->addColumn('id', function ($c) use (&$i) {
                $button = [];
                $button[] = $c['id'];
                return implode(' ', $button);
            })
            ->editColumn('direction', function ($c) {
                $direction = @$c['direction'];
                switch ($direction) {
                    case 1:
                        return trans('back_end.left_direction');
                    case 2:
                        return trans('back_end.right_direction');
                    case 3:
                        return trans('back_end.single_direction');
                    default:
                        return '';
                }
            })
            ->make(true);
    }

    public function ajax_update($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $check = $this->dataSuccess($file_name);
        $backup = Helper::checkSurveyTime($check,$this->check, $this->prefix_url);
        
        foreach ($backup['update'] as  $value) {
            $segment = tblSegment::where('id',$value['segment_id'])->first();
            $road = tblBranch::where('id',$value['road'])->first();
            $rmb = tblOrganization::where('id',$value['rmb'])->first();
            $sb = tblOrganization::where('id',$value['sb'])->first();
            if ($this->prefix_url == 'road_inventories')
            {
                $value['terrian_type_id'] = @tblTerrainType::where('id', $value['terrian_type_id'])->first()->name;
                $value['road_class_id'] = @mstRoadClass::where('id', $value['road_class_id'])->first()->name;
            }
            if ($this->prefix_url == 'maintenance_history')
            {
                $value['r_classification_id'] = @tblRClassification::where('id', $value['r_classification_id'])->first()->name;
                $value['r_category_id'] = @tblRCategory::where('id', $value['r_category_id'])->first()->name;
            }
            $value['segment_id'] = $segment['segname_'.$key];
            $value['road'] = $road['name_'.$key];
            $value['rmb'] = $rmb['name_'.$key];
            $value['sb'] = $sb['name_'.$key];
            $save[] = $value;
        }
        $data  = collect($save);
        return Datatables::of($data)
            ->addColumn('extra_view', function($t) use (&$bus) {
                return view($this->view,['show'=>$t]);
            })
            ->addColumn('id', function ($c) use (&$i) {
                    $button = [];
                    
                    $button[] = $c['id'];
                return implode(' ', $button);

            })
            ->editColumn(@'direction', function ($c){
                if (@$c['direction'] == 1)
                {
                    return trans('back_end.left_direction');
                }
                if (@$c['direction'] == 2)
                {
                    return trans('back_end.right_direction');

                }
                if (@$c['direction'] == 3)
                {
                    return trans('back_end.single_direction');
                }
                if (@$c['direction'] == '')
                {
                    return '';
                }

            })
            ->make(true);
        
    }

    public function ajax_ignore($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $data_success = $this->allData($file_name);
        
        $backup = Helper::checkSurveyTime($data_success,$this->check, $this->prefix_url);
        foreach ($backup['ignore'] as  $value) {
            $road = tblBranch::where('id',$value['road'])->first();
            $rmb = tblOrganization::where('id',$value['rmb'])->first();
            $sb = tblOrganization::where('id',$value['sb'])->first();
            if ($this->prefix_url == 'road_inventories')
            {
                $value['terrian_type_id'] = @tblTerrainType::where('id', $value['terrian_type_id'])->first()->name;
                $value['road_class_id'] = @mstRoadClass::where('id', $value['road_class_id'])->first()->name;
            }
            if ($this->prefix_url == 'maintenance_history')
            {
                $value['r_classification_id'] = @tblRClassification::where('id', $value['r_classification_id'])->first()->name;
                $value['r_category_id'] = @tblRCategory::where('id', $value['r_category_id'])->first()->name;
            }
            if (isset($value['segment_id'])) {
                if (is_numeric($value['segment_id']))
                {
                    $segment = tblSegment::where('id',$value['segment_id'])->first();
                    $value['segment_id'] = $segment['segname_'.$key];
                }
            }
            if (is_numeric($value['road']))
            {
                $value['road'] = $road['name_'.$key];
            }
            if (is_numeric($value['rmb'])) {
                $value['rmb'] = $rmb['name_'.$key];
            }
            if (is_numeric($value['sb'])) {
                $value['sb'] = $sb['name_'.$key];
            }
            $save[] = $value;
        }
        $data  = collect($save);

        // $data_success = $this->allData($file_name);
        // $backup = Helper::checkSurveyTime($data_success,$this->check);
        // $data  = collect($backup['bigger']);
        
        return Datatables::of($data)
            ->addColumn('extra_view', function($t) use (&$bus, $file_name) {
                return view($this->view,['show'=>$t]);
            })
            ->editColumn(@'direction', function ($c){
                if(@$c['direction'] == 1)
                {
                    return trans('back_end.left_direction');
                }
                if(@$c['direction'] == 2)
                {
                    return trans('back_end.right_direction');

                }
                if(@$c['direction'] == 3)
                {
                    return trans('back_end.single_direction');
                }
                if(@$c['direction'] == '')
                {
                    return '';
                }

            })
            ->addColumn('id', function ($c) use (&$i) {
                    $button = [];

                    $button[] = $c['id'];
                return implode(' ', $button);

            })
            ->make(true);  
        
    }

    public function ignore($file_name, $id)
    {
        $file_path = $this->getFilePath($file_name);
        $data = $this->allData($file_name);

        $ignore = [];
        foreach ($data as $all)
        {
            if ($all['id'] == $id)
            {
                $all['ignore'] = 1;
            }
            $ignore[] = $all;
        }
        $this->writeCSV($ignore, $file_path);
        //dd($ignore);
        //$backup = Helper::checkSurveyTime($ignore,$this->check);
        //dd($backup);
    }

    public function restore($file_name, $id)
    {
        $file_path = $this->getFilePath($file_name);
        $data = $this->allData($file_name);

        $ignore = [];
        foreach ($data as $all)
        {
            if ($all['id'] == $id)
            {
                $all['ignore'] = 0;
            }
            $ignore[] = $all;
        }
        $this->writeCSV($ignore, $file_path);
    }

    function dataError($file_name)
    {
        $file_path = $this->getFilePath($file_name);
        $config = $this->convertConfig($this->allConfig());
        array_unshift($config, 'id', 'error');
        array_push($config, 'ignore');
        $row = 1;
        $dl = [];
        if (($handle = fopen($file_path, "r")) !== FALSE)
        {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
            {
                $num = count($data);
                $arr = [];
                $row++;
                for ($c=0; $c < $num; $c++) {
                    foreach ($config as $k => $v)
                    {
                        if ($k == $c)
                        {
                            $arr[$v] = $data[$c];
                        }
                    }

                }
                if ($arr['error'] == 1 || $arr['error'] == 2)
                {
                    $dl[] = $arr;
                }
            }
            fclose($handle);
        }
        return $dl;
    }

    function allData($file_name)
    {
        $file_path = $this->getFilePath($file_name);
        $config = $this->convertConfig($this->allConfig());
        array_unshift($config, 'id', 'error');
        array_push($config, 'ignore');
        $row = 1;
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $dl = []; 
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $num = count($data);
                $arr = [];
                $row++;
                for ($c=0; $c < $num; $c++) {
                    foreach ($config as $k => $v) {
                        if ($k == $c)
                        {
                            $arr[$v] = $data[$c];
                        }
                    }
                    
                }
                $dl[] = $arr;
            }
            fclose($handle);
        }
        return $dl;
    }
    
    function dataSuccess($file_name)
    {
        $file_path = $this->getFilePath($file_name);
        $config = $this->convertConfig($this->allConfig());
        array_unshift($config, 'id', 'error');
        array_push($config, 'ignore');
        $row = 1;
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $dl = []; 
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $num = count($data);
                $arr = [];
                $row++;
                for ($c=0; $c < $num; $c++) {
                    foreach ($config as $k => $v) {
                        if ($k == $c)
                        {
                            $arr[$v] = $data[$c];
                        }
                    }  
                }
                if ($arr['error'] == 0)
                {
                    $dl[] = $arr;
                }
            }
            fclose($handle);
        }
        return $dl;
    }

    function getDataCSVByRow($id, $file_name)
    {
        $file_path = $this->getFilePath($file_name);
        $config = $this->convertConfig($this->allConfig());
        array_unshift($config, 'id', 'error');
        array_push($config, 'ignore');
        $row = 1;
        if (($handle = fopen($file_path, "r")) !== FALSE) 
        {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) 
            {
                if ($data[0]  == $id)
                {
                    $num = count($data);
                    $arr = [];
                    $row++;
                    for ($c=0; $c < $num; $c++) {
                        foreach ($config as $k => $v) {
                            if ($k == $c)
                            {
                                $arr[$v] = $data[$c];
                            }
                        }
                    }
                }
            }
            fclose($handle);
        }
        return $arr;
    }

    function sortByConfig($request, $config, $check_ignore)
    {
        // print_r($request);
        array_unshift($config, 'id', 'error');
        array_push($config, 'ignore');
        $arr = [];
        foreach ($config as $conf)
        {
            $arr[$conf] = isset($request[$conf]) ? $request[$conf] : null;
            if ($conf == 'error')
            {
                $arr['error'] = 0;
            }
            if (isset($request['construct_year']) && isset($request['service_start_year']))
            {
                $arr['construct_year'] = $request['construct_year'];
                $arr['service_start_year'] = $request['service_start_year'];
            }
            if ($conf == 'ignore')
            {
                if ($check_ignore)
                {
                    $arr['ignore'] = 1;
                }
                else
                {
                    $arr['ignore'] = 0;
                }
            }
        }
        return $arr;
    }

    function saveCSV($success, $errors, $file_path)
    {
        $i = 1;
        $arr = $this->allConfig();
        $s = [];
        foreach ($success as $data) 
        {
            $d = [];
            $d['id'] = $i;
            $d['error'] = 0;
            // $time = microtime(true);
            foreach ($data as $k => $v)
            {  
                $times = microtime(true);
                if ($k != 'err')
                {
                    $value = Helper::getID($k, $v, $arr,$d);
                    if (!$value) 
                    {
                        $value = $v;
                    }
                    $d[$k] = $value;
                }
                // echo "*****************************************<br/>";
                // echo "check: ",microtime(true) - $times,'<br/>';
            }
            // echo "all csv: ",microtime(true) - $time,'<br/>';
            // dd('end');
            $s[] = $d;
            $i++;
        }
        $e = [];
        foreach ($errors as $data) 
        {
            $d = [];
            $d['id'] = $i;
            $d['error'] = 1;
            foreach ($data as $k => $v)
            {
                if ($k != 'err' && $k != 'overlap')
                {
                    $d[$k] = $v;
                }
                if ($k == 'overlap')
                {
                    $d['error'] = 2;
                }
            }
            $e[] = $d;
            $i++;
        }
        $all = array_merge($s, $e);
        $this->writeCSV($all, $file_path);
        
    }

    function transform($data)
    {
        $config = $this->allConfig();
        $arr = [];
        foreach ($data as $key => $value) 
        {
            $value = Helper::getMasterIDByKey($key, $value, $config) ? Helper::getMasterIDByKey($key, $value, $config): $value;
            
            if ($key == 'km_from' || $key == 'm_from' || $key == 'km_to'
                || $key == 'm_to' || $key == 'km_station' || $key == 'm_station'
                || $key == 'binder_course' || $key == 'wearing_course'
                || $key == 'm_to' || $key == 'km_station' || $key == 'm_station'
                || $key == 'up1' || $key == 'down1' || $key == 'up2' || $key == 'down2'
                || $key == 'up3' || $key == 'down3' || $key == 'up4' || $key == 'down4'
                || $key == 'up5' || $key == 'down5' || $key == 'up6' || $key == 'down6'
                || $key == 'up7' || $key == 'down7' || $key == 'up8' || $key == 'down8'
                || $key == 'up9' || $key == 'down9' || $key == 'up10' || $key == 'down10'
                ||  $key == 'sb' ||  $key == 'segment_id' || $key == 'road' || $key == 'route_branch'
                ||  $key == 'district_from' ||  $key == 'district_to' ||  $key == 'ward_from' || $key == 'ward_to'
                || $key == 'r_category_id'
            )
            {
                $arr[$key] = (int)$value;
            }
            elseif (is_numeric($value)) {
                $arr[$key] = (string)$value;
            }
            else{
                $arr[$key] = $value;
            }
            
            // if (isset($config[$key]))
            // {
            //     if ($config[$key]['type'] == 'checkbox')
            //     {
            //         $arr[$key] = ($value != 1) ? 0 : 1;
            //     }
            //     else
            //     {
            //         $value = Helper::getMasterIDByKey($key, $value, $config) ? Helper::getMasterIDByKey($key, $value, $config): $value;
            //         $arr[$key] = $value;
            //     }
            // }
            // else
            // {
            //     $arr[$key] = $value;
            // }

        }
        if (isset($arr['district_from']) && $arr['ward_to'])
        {
            if ($arr['province_from'] == 0) {
                $arr['province_from'] = "";
            }
            if ($arr['province_to'] == 0) {
                $arr['province_to'] = "";
            }
            if ($arr['district_from'] == 0) {
                $arr['district_from'] = "";
            }
            if ($arr['district_to'] == 0) {
                $arr['district_to'] = "";
            }
            if ($arr['ward_from'] == 0) {
                $arr['ward_from'] = "";
            }
            if ($arr['ward_to'] == 0) {
                $arr['ward_to'] = "";
            }
        }
        if (isset($arr['design_speed']))
        {
            strpos($arr['design_speed'], 'km/h') !== false ? $arr['design_speed'] : $arr['design_speed'] = $arr['design_speed'].' km/h';
        }
        return $arr;
    }

    static function writeCSV($data, $file)
    {
        $out = fopen($file, 'w+');
        foreach($data as $row)
        {
            fputcsv($out, $row);
        }
        fclose($out);
    }

    function convertConfig($config)
    {
        $arr = [];
        foreach ($config as $key => $value) 
        {
            $arr[] = $key;
        }
        return $arr;
    }

    function getFilePath($file_name)
    {
        $file_path = public_path('upload_csv/   '.$file_name.'.csv');
        return $file_path;
    }

    function convertPavementType($id)
    {
        $rule = [
            'AC' => 'AC',
            'CC' => 'CC',
            'BST' => 'BST',
            'BPM' => 'BST',
            'BM' => 'BST',
            'WBM' => 'UP',
            'GP' => 'UP',
            'SSP' => 'UP',
            'EP' => 'UP',
            'RP' => '*',
            'Others' => '*',
        ];
        $pavement_type = mstPavementType::findOrFail($id)->code;
        $surface_code = $rule[$pavement_type];
        $surface = mstSurface::where('code_name', $surface_code)->first();
        return $surface->id;
    }

    function convertSurface($id)
    {
        $pavement_type = mstPavementType::findOrFail($id)->surface_id;
        $surface = mstSurface::where('id', $pavement_type)->first();
        return $surface->id;
    }
    
    private function _csvOverlapCheck($record, $all)
    {
       // $time = microtime(true);
        if ($all != null) {
            $checkall = collect($all)
            ->filter(function($value) {
                return ($value['ignore'] == 0) ? true : false;
            })
            ->map(function($item) 
            {
                if (isset($item['completion_date'])) {
                    $dd['record_completion_date'] = Carbon::parse($item['completion_date']);
                    $dd['sub_record_completion_date'] = Carbon::parse($item['completion_date'])->copy()->subMonths($item['repair_duration']);
                }
                $dd['survey_time'] = substr($item['survey_time'], 0, 4);
                $dd['m_from_convert'] = $item['km_from'] * 1000000 + $item['m_from'];
                $dd['m_to_convert'] = $item['km_to'] * 1000000 + $item['m_to'];
                $distance_start = 0;
                $distance_end = 0;
                $dd['distance_start'] = $distance_start;
                $dd['distance_end'] = $distance_end;
                $dd['segment_id'] = $item['segment_id'];
                $dd['direction'] = $item['direction'];
                $dd['lane_pos_number'] = $item['lane_pos_number'];
                return $dd;
            });
            $c_chk = 0;
            if ($this->prefix_url == 'traffic_volume')
            {
                $c_chk = $checkall->where('segment_id', $record['segment_id'])
                            ->where('km_station', $record['km_station'])
                            ->where('m_station', $record['m_station'])
                            ->where('survey_time', substr($record['survey_time'], 0, 4))
                            ->where('ignore', '<>', 1)
                            ->count();
            }
            else if ($this->prefix_url == 'road_inventories')
            {
                $m_from_convert = $record['km_from'] * 1000000 + $record['m_from'];
                $m_to_convert = $record['km_to'] * 1000000 + $record['m_to'];
                $c_chk = $checkall->where('segment_id', $record['segment_id'])
                                ->where('direction', $record['direction'])
                                ->where('lane_pos_number', $record['lane_pos_number'])
                                ->where('m_to_convert', '=', $m_to_convert)
                                ->where('m_from_convert', '=', $m_from_convert)
                                ->where('survey_time', substr($record['survey_time'], 0, 4))
                                ->where('ignore', '<>', 1)
                                ->count();
                //$c_chk = 1;
            }
            else
            {
                $distance_start_db = 0;
                $distance_end_db = 0;
                $m_from_convert = $record['km_from'] * 1000000 + $record['m_from'];
                $m_to_convert = $record['km_to'] * 1000000 + $record['m_to'];
                //
                $row_completion_date = Carbon::parse($record['completion_date']);
                $sub_row_completion_date = Carbon::parse($record['completion_date'])->copy()->subMonths($record['repair_duration']);
                if ($record['direction_running'] == "0")
                {
                    $distance_start_db = " round(-(distance) - ((total_width_repair_lane)/2), 2)";
                    $distance_end_db = "round(-(distance) + ((total_width_repair_lane)/2), 2)";
                }
                elseif ($record['direction_running'] == "1") 
                {
                    $distance_start_db = " round((distance) - ((total_width_repair_lane)/2), 2)";
                    $distance_end_db = "round((distance) + ((total_width_repair_lane)/2), 2)";
                }
                $distance_start = 0;
                $distance_end = 0;
                if ($record['direction_running'] == '0')
                {
                    $distance_start -= ((int)$record['distance'] + (int)$record['total_width_repair_lane']/2);
                    $distance_end -= ((int)$record['distance'] - (int)$record['total_width_repair_lane']/2);
                }
                else
                {
                    $distance_start += ((int)$record['distance'] - (int)$record['total_width_repair_lane']/2);
                    $distance_end += ((int)$record['distance'] + (int)$record['total_width_repair_lane']/2);
                }

                if (is_numeric($record['repair_duration']))
                {
                    $check = $checkall->filter(function($value) use ($record,$m_from_convert,$m_to_convert,$sub_row_completion_date,$row_completion_date) {
                        if( ($value['segment_id'] == $record['segment_id']) && 
                            ($value['direction'] == $record['direction']) && 
                            ($value['lane_pos_number'] == $record['lane_pos_number']) && 
                            ($value['m_from_convert'] == $m_from_convert) && 
                            ($value['m_to_convert'] == $m_to_convert)  && 
                            ($value['sub_record_completion_date'] < $row_completion_date || $value['record_completion_date'] >  $sub_row_completion_date)
                        ) 
                        {
                            return true;
                        }
                        else 
                        {
                            return false;
                        }
                    });
                    
                    $c_chk = count($check);
                }

            }          
            return ($c_chk > 0) ? true : false;
        }
        //echo "csv: ", microtime(true)- $time ,'<br/>';
        //dd(1);
    }
}
