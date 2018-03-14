<?php

namespace App\Http\Controllers\FrontEnd\WorkPlanning;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackendRequest\ImportRequest;
use Helper, App, Validator, DB, Auth;
use App\Models\tblPlannedSection;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Models\mstRepairMethod;
use App\Models\tblSegment;
use App\Models\tblRClassification;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;
use Yajra\Datatables\Facades\Datatables;
use Excel;

class PlannedSectionController extends Controller
{
    function __construct()
    {
        $this->middleware("dppermission:work_planning.work_planning");
    }

    public function index()
	{
        $orgs = App\Models\tblOrganization::whereIn('level', [3])->whereNotNull('parent_id')->get();
        $tree_data = [];
        $sb_data = [];
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        foreach ($orgs as $org)
        {
            $tree_data[$org->parent_id][] = [
                'id' => $org->id,
                'text' => $org->{"name_$lang"}
            ];

            foreach ($org->segments as $segment)
            {
                $sb_data[$org->id][] = [
                    'id' => $segment->id,
                    'text' => $segment->{"segname_$lang"}
                ];
            }
        }
		return view('front-end.work_planning.planned_section', [
            'tree_data' => $tree_data,
        ]);
	}

	public function getImportData()
	{
		return view('front-end.work_planning.planned_section_import');
	}

    public function getImport($file_name)
    {
        $arr = $this->allConfig();
        $import_error = $this->dataError($file_name);
        $data_success = $this->dataSuccess($file_name);
        return view('front-end.work_planning.ps_import_validate', [
            'config' => $arr,
            'import_error' => $import_error,
            'count_success' => 0,
            'count_all' => count($import_error) + count($data_success),
            'file_name' => $file_name
        ]);
    }

    public function getImportErrorData($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $data_success = $this->allData($file_name);
        
        $backup = $this->divideDataToTab($data_success);
        
        foreach ($backup['err'] as  $value) 
        {
            if (is_numeric($value['route_name'])) {
                $road = tblBranch::where('id',$value['route_name'])->first();
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
            if (is_numeric($value['repair_method'])) 
            {
                $repair_method = mstRepairMethod::where('id', $value['repair_method'])->first();
                $value['repair_method'] = $repair_method['name_'. $key];
            }
            if (is_numeric($value['repair_classification'])) 
            {
                $repair_classification = tblRClassification::where('id', $value['repair_classification'])->first();
                $value['repair_classification'] = $repair_classification['name_'. $key];
            }
            $save[] = $value;
        }
        $data  = collect($save);
        
        return Datatables::of($data)
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

    public function getImportSuccessData($file_name)
    {
        $key = \App::getLocale() == 'en' ? $key = "en" : $key = "vn";
        $save = [];
        $data_success = $this->allData($file_name);
        
        $backup = $this->divideDataToTab($data_success);
        foreach ($backup['success'] as  $value) 
        {
            if (is_numeric($value['route_name'])) {
                $road = tblBranch::where('id',$value['route_name'])->first();
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
            if (is_numeric($value['repair_method'])) 
            {
                $repair_method = mstRepairMethod::where('id', $value['repair_method'])->first();
                $value['repair_method'] = $repair_method['name_'. $key];
            }
            if (is_numeric($value['repair_classification'])) 
            {
                $repair_classification = tblRClassification::where('id', $value['repair_classification'])->first();
                $value['repair_classification'] = $repair_classification['name_'. $key];
            }
            $save[] = $value;
        }
        $data = collect($save);
        return Datatables::of($data)
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

	public function postValidate(ImportRequest $request)
	{
		try
        {
            $data = null;
            $configs[] = tblPlannedSection::config();
   
            $arr = [];
            foreach ($configs as $config) 
            {
                $arr[] = $config;
            }

            $success = [];
            $error = [];
            $all = [];
            $file = $request->file;
            $all_request = $request->except('_token');
            if ($request->hasFile("file"))
            {
                $path = $file->store('upload_excel');
                $data = Helper::preProcessing($path, $this->allConfig(TRUE), 5, 1);
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
                // $dataQuery = [];
                foreach ($data as $record)
                {
                    $resValidate = $this->validateData($record, $arr, null);
                    // dd($resValidate);
                    // $dataQuery = $resValidate['query'];
                    // unset($resValidate['query']);
                    $all[] = $resValidate;
                }

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
                $file_name = str_replace('-', '', Uuid::generate()->string);
                $file_path = $this->getFilePath($file_name);

                $this->saveCSV($success, $error, $file_path);
                return redirect('user/work_planning/planned_section/import/'. $file_name);
            }
        }
        catch(\Exception $e)
        {
            dd($e->getMessage());
        }
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

	function allConfig($validate = FALSE)
    {
        $configs[] = tblPlannedSection::config();
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

    function validateData($record, $query = [], $dataQuery = NULL, $data_success = NULL, $id = NULL)
    {
        $config = $query;
        $has_error = false;
        $validate = [];
        $check_csv = true;
        $check_segment = false;
        $rmb;
        $sb;
        foreach ($config as $model1)    
        {
            foreach ($model1 as $key1 => $value1)
            {
                if (isset($record[$key1])) $validate[$key1] = $value1['validate'];
            }
        }

        $validator = Validator::make($record, $validate);

        $record['err'] = $validator->errors();
        
        if ($record['err'] != NULL)
        {
            $has_error = true;
        }
        if (isset($record['km_from']) && isset($record['m_from']) && isset($record['km_to']) && isset($record['m_to']))
        {
            $m_from_convert = $record['km_from'] * 1000000 + $record['m_from'];
            $m_to_convert = $record['km_to'] * 1000000 + $record['m_to'];
            if ($m_from_convert >= $m_to_convert)
            {
                $validator->errors()->add('m_to', trans('validator.juridistion_invalid'));
                $record['err'] = $validator->errors();
            }
            if ($record['m_from'] % 100 != 0)
            {
                $validator->errors()->add('m_from', trans('validator.juridistion_invalid'));
                $record['err'] = $validator->errors();
            }
        }

        foreach ($config as $model)
        {
            foreach ($model as $key => $value)
            {
                if ($value['type'] == 'check_select')
                {
                    $checked = false;
                    if ($record[$key] != null)
                    {
                        $checkbox_value = mb_strtolower($record[$key]);
                        $checkbox_value = trim($checkbox_value);
                        if ($checkbox_value == "trái" || $checkbox_value == "left" || $checkbox_value == 1)
                        {
                            $record[$key] = '1';
                        }
                        else if ($checkbox_value == "phải" || $checkbox_value == "right" || $checkbox_value == 2)
                        {
                            $record[$key] = '2';
                        }
                        else if ($checkbox_value == "làn đơn chung" || $checkbox_value == "single" || $checkbox_value == 3)
                        {
                            $record[$key] = '3';
                        }
                        else
                        {
                            $checked = true;
                        }
                        if ($checked)
                        {
                            $validator->errors()->add($key, $record[$key].' '.trans('validator.import_master_not_exist'));
                            $record['err'] = $validator->errors();
                        }
                    }
                }
                else if ($value['type'] == 'select')
                {
                    if ($key != 'repair_classification')
                    {
                        $found = false;
                        $valueKey = isset($record[$key]) ? trim($record[$key]) : '';
                        if (!empty($value['modelCheck']))
                        {
                            foreach ($value['modelCheck'] as $master_item)
                            {
                                if ($master_item['value'] != '' && $valueKey != '')
                                {
                                    $master_name = mb_strtolower($master_item['name']);
                                    $record_lower = mb_strtolower($valueKey);
                                    if (strpos($master_name, $record_lower) !== false || $master_item['value'] == $valueKey)
                                    {
                                        $found = true;   
                                    }
                                }
                                
                            }
                            if ($found)
                            {
                                if ($key == 'rmb')
                                {
                                    $rmb = tblOrganization::where('name_en', $record[$key])->orWhere('name_vn', $record[$key])->orWhere('id', $record[$key])->first();
                                }
                                else if ($key == 'sb' && isset($rmb) && $rmb != NULL)
                                {
                                    $sb = tblOrganization::where('parent_id', $rmb->id)->where(function ($query) use ($record, $key) {
                                        $query->where('name_en', $record[$key])->orWhere('name_vn', $record[$key])->orWhere('id', $record[$key]);
                                    })->first();           
                                    if ($sb == NULL)
                                    {
                                        $found = false;
                                    }
                                }
                                else if ($key == 'route_name')
                                {
                                    $branch = tblBranch::where('name_en', $record[$key])->orWhere('name_vn', $record[$key])->orWhere('id', $record[$key])->first();
                                    $sb = tblOrganization::where('name_en', $record['sb'])->orWhere('name_vn', $record['sb'])->orWhere('id', $record['sb'])->first();

                                    if ($sb != NULL && $branch != NULL)
                                    {
                                        $segment = tblSegment::where('sb_id', $sb->id)->where('branch_id', $branch->id)->first();

                                        if ($segment == NULL)
                                        {
                                            $found = false;
                                        }
                                        $segment_juridistion = tblSegment::where('sb_id', $sb->id)->where('branch_id', $branch->id)
                                                ->whereRaw('10000 * km_from + m_from <= ?', [10000 * $record['km_from'] + $record['m_from']])
                                                ->whereRaw('10000 * km_to + m_to >= ?', [10000 * $record['km_to'] + $record['m_to']])->first();
                                                  
                                        if ($segment_juridistion == NULL)
                                        {
                                            $listError[] = 'm_to';
                                            $validator->errors()->add('m_to', trans('validator.juridistion_invalid'));
                                            $record['err'] = $validator->errors();
                                        }
                                    }
                                    else
                                    {
                                        $found = false;
                                    }        
                                }
                            }
                        }
                        if (isset($record[$key]) && $valueKey != '')
                        {
                            if (!$found && $value['validate'] == 'required')
                            {
                                $listError[] = $key;
                                $validator->errors()->add($key, trans('validator.import_master_not_exist'));
                                $record['err'] = $validator->errors();

                            }
                        }
                    }
                }
            }
        }
        $exist = false;
        if (is_a($record['planned_year'], 'DateTime'))
        {
            $a = $record['planned_year']->format('d-m-Y');
            $record['planned_year'] = $a;
        }
        if ($found)
        {
            $branch = tblBranch::where('name_en', $record['route_name'])->orWhere('name_vn', $record['route_name'])->orWhere('id', $record['route_name'])->first();

            if ($branch != NULL )
            {
                $road_category = $branch->road_category;
                $road_number = $branch->road_number;
                $road_number_supplement = $branch->road_number_supplement;
                $branch_number = $branch->branch_number;
                if ($record['branch_number'] != $branch_number)
                {
                    $listError[] = 'branch_number';
                    $validator->errors()->add('branch_number', trans('validator.branch_number_invalid'));
                    $record['err'] = $validator->errors();
                }
                $km_from = sprintf("%04d", $record['km_from']); 
                $m_from = sprintf("%05d", $record['m_from']);
                $section_id = $road_category . $road_number . $road_number_supplement . $branch_number . $record['direction'] . $record['lane_pos_no']. '_' .$km_from . $m_from;      
                $planned_section = tblPlannedSection::where('section_id', $section_id)->where('planned_year', $record['planned_year'])->first();
                if ($planned_section != NULL)
                {
                    $exist = true;
                }
            }

            if (isset($data_success) && !empty($data_success))
            {
                foreach ($data_success as $item_success)
                {
                    $check_csv = $this->checkOverlapCsv($record, $item_success);
                    if (!$check_csv)
                    {
                        break;
                    }
                }
            }

            if ($exist || !$check_csv)
            {
                $listError[] = 'planned_year';
                $validator->errors()->add('planned_year', trans('validator.planned_section_exists'));
                $record['err'] = $validator->errors();
            }
        }

        return $record;
    }

    function checkOverlapCsv($record, $item_success)
    {
        if ($record['route_name'] == $item_success['route_name'] && $record['branch_number'] == $item_success['branch_number'] && $record['km_from'] == $item_success['km_from'] && $record['m_from'] == $item_success['m_from'] && $record['lane_pos_no'] == $item_success['lane_pos_no'] && $record['direction'] == $item_success['direction'] && $record['planned_year'] == $item_success['planned_year'])
        {
            return false;
        }
        else return true;
    }

    function getFilePath($file_name)
    {
        $file_path = public_path('upload_csv/'.$file_name.'.csv');
        return $file_path;
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
            foreach ($data as $k => $v)
            {  

                if ($k != 'err')
                {
                    $value = Helper::getID($k, $v, $arr,$d);
                    if (!$value) 
                    {
                        $value = $v;
                    }
                    $d[$k] = $value;
                }
            }
            $d['ignore'] = 0;
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
            $d['ignore'] = 0;
            $e[] = $d;
            $i++;
        }
        $all = array_merge($s, $e);    
        $this->writeCSV($all, $file_path);
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

    function convertConfig($config)
    {
        $arr = [];
        foreach ($config as $key => $value) 
        {
            $arr[] = $key;
        }
        return $arr;
    }

    function divideDataToTab($check)
    {
        $count = count($check);
        $success = [];
        $err = [];
        $section_data = null;
        for ($i = 0; $i < $count; $i++)
        {

            if ($check[$i]['error'] > 0)
            {
                $err[] = $check[$i];
            }
            else
            {
                $success[] = $check[$i];
            }
        }
        return array(
            'success' => $success,
            'err' => $err
        );
    }

    function getSinglePlannedData($file_name, $id)
    {
        $data = $this->getDataCSVByRow($id, $file_name);
        if ($data['error'] == 2)
        {
            $data['excel_overlap'] = true;
        }
        $all = $this->validateData($data, $this->allConfig(TRUE));

        return $this->transform($all);
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

    function transform($data)
    {
        $config = $this->allConfig();
        $arr = [];
        foreach ($data as $key => $value) 
        {
            $value = Helper::getMasterIDByKey($key, $value, $config) ? Helper::getMasterIDByKey($key, $value, $config): $value;
            if ($key == 'km_from' || $key == 'm_from' || $key == 'km_to'
                || $key == 'm_to' ||  $key == 'sb' || $key == 'route_name' || $key == 'route_branch'
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

        }
        return $arr;
    }

    public function editSinglePlannedData(Request $request, $file_name, $id)
    {
        $file_path = $this->getFilePath($file_name);
        $all_request = $request->except(['_token', 'err', 'excel_overlap']);
        $data_success = $this->dataSuccess($file_name);
        $all = $this->validateData($all_request, $this->allConfig(TRUE), null, $data_success);

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

    function sortByConfig($request, $config, $check_ignore)
    {
        print_r($request);
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

    public function getCheck($file_name)
    {
        $arr = $this->allConfig();
        $import_error = $this->dataError($file_name);
        $data_success = $this->dataSuccess($file_name);
        $count_success = $this->divideDataToTab($data_success);
        
        // $new = count($count_success['new']);
        // $update = count($count_success['update']);
        $success = count($count_success['success']);
        $data_all = $this->allData($file_name);
        $count_all = $this->divideDataToTab($data_all);
        $err = count($count_all['err']);
        //$ignore = count($count_all['ignore']);
        return array(
            'success' => $success,
            'err' => $err,
        );
    }

    function postImport(Request $request, $file_name)
    {
        $data = $this->dataSuccess($file_name);
        $data_check = $this->divideDataToTab($data);
        
        $err = $this->allData($file_name);
        $data_err = $this->divideDataToTab($err);
        $count_success = count($data_check['success']);
        $count_err = count($data_err['err']);

        DB::beginTransaction();
        try 
        {
            if (!empty($data_check['success']))
            {
                foreach ($data_check['success'] as $item)
                {
                    $branch = tblBranch::findOrFail($item['route_name']);

                    $road_category = $branch->road_category;
                    $road_number = $branch->road_number;
                    $road_number_supplement = $branch->road_number_supplement;

                    $km_from = sprintf("%04d", $item['km_from']); 
                    $m_from = sprintf("%05d", $item['m_from']);
                    $section_id = $road_category . $road_number . $road_number_supplement . $item['branch_number'] . $item['direction'] . $item['lane_pos_no']. '_' .$km_from . $m_from;        
                    $repair_method = mstRepairMethod::with('classification')->findOrFail($item['repair_method']);

                    $planned_section = new tblPlannedSection;
                    $planned_section->section_id = $section_id;
                    $planned_section->branch_id = $item['route_name'];
                    $planned_section->sb_id = $item['sb'];
                    $planned_section->km_from = $item['km_from'];
                    $planned_section->m_from = $item['m_from'];
                    $planned_section->km_to = $item['km_to'];
                    $planned_section->m_to = $item['m_to'];
                    $planned_section->section_length = $item['length'];
                    $planned_section->direction = $item['direction'];
                    $planned_section->lane_pos_no = $item['lane_pos_no'];
                    $planned_section->planned_year = $item['planned_year'];
                    $planned_section->repair_quantity = $item['repair_quantity'];
                    $planned_section->unit_cost = $item['unit_cost'];
                    $planned_section->repair_cost = $item['repair_amount'];
                    $planned_section->repair_method_en = $repair_method->name_en;
                    $planned_section->repair_method_vn = $repair_method->name_vn;
                    $planned_section->repair_classification_en = $repair_method->classification->name_en;
                    $planned_section->repair_classification_vn = $repair_method->classification->name_vn;
                    $planned_section->remark = $item['remarks'];
                    $planned_section->created_by = Auth::user()->id;
                    $planned_section->import_flg = 1;
                    $planned_section->save();
                }
            }
            
            DB::commit();
            return redirect(url('/user/work_planning/planned_section'))->with([
                'flash_level' => 'success',
                'flash_message' => trans('wp.import_success'),
                'count_success' => $count_success
            ]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function postDeletePlannedSection(Request $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->id as $id)
            {
                $planned_section = tblPlannedSection::findOrFail($id);
                $planned_section->delete();
            }
            DB::commit();
            return redirect('/user/work_planning/planned_section')->with([
                'flash_level' => 'success',
                'flash_message' => trans('wp.delete_planned_section_success'),
            ]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function getDownloadEn()
    {
        $tpl_file = 'excel_templates/planned_section_template_en.xlsx';
        Excel::load($tpl_file,  function ($excel){
        })
            ->download('xlsx');
    }

    public function getDownloadVi()
    {
        $tpl_file = 'excel_templates/planned_section_template_vi.xlsx';
        Excel::load($tpl_file,  function ($excel){
        })
            ->download('xlsx');
    }
}
