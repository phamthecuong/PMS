<?php

namespace App\Http\Controllers\FrontEnd\M13;

use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackendRequest\MaintenanceHistoryRequest;
use App\Models\tblSectiondataMH;
use App\Models\tblMHHistory;
use App\Models\tblSectionLayer;
use App\Models\tblSegment;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblBranch;
use App\Models\tblOrganization;
use Carbon\Carbon;
use Excel;

class MaintenanceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index1()
    {
        \DB::beginTransaction();
        try
        {
            $roads = [];
            $records = tblBranch::orderBy('road_number_supplement', 'desc')->get();
            foreach ($records as $r) 
            {
                $roads[$r->name_en] = [
                    $r->road_number,
                    $r->road_number_supplement,
                    $r->branch_number
                ];
                $roads[$r->name_vn] = [
                    $r->road_number,
                    $r->road_number_supplement,
                    $r->branch_number
                ];
            }

            $branches = [];
            $records = tblBranch::get();
            foreach ($records as $r) 
            {
                $branches[intval($r->road_number)][intval($r->road_number_supplement)][intval($r->branch_number)] = $r->id;
            }      

            $repair_categories = [];
            $records = \App\Models\tblRCategory::get();
            foreach ($records as $r) 
            {
                $repair_categories[$r->code] = $r->id;
            }   

            $repair_classifications = [];
            $records = \App\Models\tblRClassification::get();
            foreach ($records as $r) 
            {
                $repair_classifications[strtolower($r->name_en)] = $r->id;
                $repair_classifications[strtolower($r->name_vn)] = $r->id;
            }

            $directions = [
                'left' => 1,
                'trái' => 1,
                'ngược' => 1,
                'right' => 2,
                'phải' => 2,
                'xuôi' => 2,
                'single' => 3,
                'làn đơn chung' => 3,
                'cả hai' => 3
            ];

            $src = 'pc_test/rmb4/MH4.xlsx';
            
            $workbook = SpreadsheetParser::open($src);

            $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
            foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) {
                if ($rowIndex < 13) continue;
                $route_name = $values[2];
                $segment_name = $values[3];
                $branch_number = $values[4];
                
                $rmb = $values[6];
                $sb = $values[7];
                $km_from = $values[8];
                $m_from = round($values[9]);
                $km_to = $values[10];
                $m_to = $values[11];
                $survey_time = $values[21];
                $direction = $values[22];
                $actual_length = $values[24];
                $construct_year = $values[24];
                $lane_pos_number = $values[25];
                $completion_date = $values[26];
                $repair_category = $values[27];
                $repair_classification = $values[28];

                $road_info = @$roads[$route_name];

                if (!isset($branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])]))
                {
                    echo 'branch not found';
                    print_r($values);
                    continue;
                }
                $branch_id = $branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])];

                
                $segment = tblSegment::where('branch_id', $branch_id)
                    ->whereRaw("10000*km_from+m_from <= " . (10000*$km_from+$m_from))
                    ->whereRaw("10000*km_to+m_to >= " . (10000*$km_to+$m_to))
                    ->first();
                if (!$segment)
                {
                    echo 'segment not found';
                    print_r($values);
                    continue;
                    dd($segment = tblSegment::where('branch_id', $branch_id)
                    ->whereRaw("10000*km_from+m_from <= " . (10000*$km_from+$m_from))
                    ->whereRaw("10000*km_to+m_to >= " . (10000*$km_to+$m_to))
                    ->toSql());
                }
                $record = new tblSectiondataMH();
                $record->segment_id = $segment->id;
                $record->km_from = $km_from;
                $record->m_from = $m_from;
                $record->km_to = $km_to;
                $record->m_to = $m_to;
                $record->survey_time = Carbon::createFromFormat('Y/m/d', $survey_time)->format('Y-m-d');
                $record->completion_date = Carbon::createFromFormat('Y/m/d', $completion_date)->format('Y-m-d');
                $record->direction = $directions[strtolower($direction)];
                $record->actual_length = $actual_length;
                $record->lane_pos_number = $lane_pos_number;
                $record->r_classification_id = intval(@$repair_classifications[strtolower($repair_classification)]);
                $record->r_category_id = intval(@$repair_categories[$repair_category]);
                $record->repair_duration = 0;
                $record->total_width_repair_lane = 0;
                $record->r_structType_id = 0;
                $record->distance = 0;
                $record->direction_running = 1;
                $record->remark = '';
                $record->save();

                $record->createHistory();
            }
            \DB::commit();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    public function index()
    {
        $orgs = tblOrganization::whereIn('level', [3])->whereNotNull('parent_id')->get();
        $tree_data = [];
        $branch_data = [];
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

        $branches = tblBranch::with('segments')->get();
        foreach ($branches as $branch) 
        {
            foreach ($branch->segments as $segment) 
            {
                $branch_data[$branch->id][] = [
                    'id' => $segment->id,
                    'text' => $segment->{"segname_$lang"}
                ];
            }
        }

        return view('front-end.m13.maintenance_history.index', [
            'tree_data' => $tree_data,
            'branch_data' => $branch_data,
            'sb_data' => $sb_data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('front-end.m13.maintenance_history.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaintenanceHistoryRequest $request)
    {
        \DB::beginTransaction();
        try
        {
            $validation = $this->_doValidate($request);
            if ($validation !== true)
            {
                return $validation;
            }

            $record = new tblSectiondataMH();
            $record->segment_id = $request->segment;
            $record->from_lat = $request->latitude_from;
            $record->from_lng = $request->longitude_from;
            $record->to_lat = $request->latitude_to;
            $record->to_lng = $request->longitude_to;
            $record->km_from = $request->km_from;
            $record->m_from = $request->m_from;
            $record->km_to = $request->km_to;
            $record->m_to = $request->m_to;
            $record->survey_time = $request->date_collection;
            $record->completion_date = $request->completion_date;
            $record->repair_duration = $request->repair_duration;
            $record->direction = $request->direction;
            $record->actual_length = $request->actual_length;
            $record->lane_pos_number = $request->lane_no;
            $record->total_width_repair_lane = $request->repair_width;
            $record->r_classification_id = $request->repair_classification;
            $record->r_structType_id = $request->repair_structtype;
            $record->r_category_id = $request->repair_category;
            $record->distance = $request->distance_to_center;
            $record->direction_running = $request->direction_running;
            $record->remark = $request->remark;
            $record->save();

            foreach ($request->data as $layer_id => $layer) 
            {
                if (!empty($layer['thickness']))
                {
                    $l = new tblSectionLayer();
                    $l->thickness = $layer['thickness'];
                    $l->description = $layer['desc'];
                    $l->type = 2;
                    $l->material_type_id = $layer['material_type'];
                    $l->layer_id = $layer_id;
                    $l->mhSection()->associate($record);
                    $l->save();
                }
            }
            $record->createHistory();


            \DB::commit();
            return redirect()->back()->with('success', trans('back_end.section_created'))->withInput();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    private function _doValidate($request)
    {
        $adjust = 100000;
        if ($adjust*$request->km_to + $request->m_to - $adjust*$request->km_from - $request->m_from <= 0)
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.invalid_chainage'),
                    'm_from' => ' ',
                    'km_to' => ' ',
                    'm_to' => ' '
                ])->withInput();
        }

        // segment check
        $segment = tblSegment::find($request->segment);

        if (
            ($adjust*$request->km_from + $request->m_from < $adjust*$segment->km_from + $segment->m_from) ||
            ($adjust*$request->km_to + $request->m_to > $adjust*$segment->km_to + $request->m_to)
        )
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.chainage_need_fit_segment'),
                    'm_from' => ' ',
                    'km_to' => ' ',
                    'm_to' => ' '
                ])->withInput();
        }
        // check overlap
        // $count_check = tblSectiondataRMD::where('lane_pos_number', $request->lane_no)
        //     ->where('direction', $request->direction) 
        //     ->where('segment_id', $request->segment) 
        //     ->whereRaw("$adjust*km_to + m_to > $adjust*{$request->km_from} + {$request->m_from}")
        //     ->whereRaw("$adjust*km_from + m_from < $adjust*{$request->km_to} + {$request->m_to}")
        //     ->whereRaw("YEAR(survey_time) = " . date('Y', strtotime($request->date_collection)))
        //     ->count();
        
        // if ($count_check > 0)
        // {
        //     return redirect()->back()->withErrors([
        //             'km_from' => trans('back_end.overlap_with_existing_section'),
        //             'm_from' => ' ',
        //             'km_to' => ' ',
        //             'm_to' => ' '
        //         ])->withInput();
        // }

        return true;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = tblSectiondataMH::findOrFail($id);
        $rmb_id = $data->segment->tblOrganization->parent_id;
        $sb_id = $data->segment->tblOrganization->id;
        $branch_id = $data->segment->branch_id;
        $segment_id = $data->segment->id;
        $edit_flg = 1;
        return view('front-end.m13.maintenance_history.add', [
            'data' => $data,
            'breadcrumb_txt' => trans('back_end.edit_current_record'),
            'rmb_id' => $rmb_id,
            'sb_id' => $sb_id,
            'branch_id' => $branch_id,
            'edit_flg' => $edit_flg,
            'segment_id' => $segment_id// in case of edit old data, segment name display only, because this segment might be deleted
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function getExport()
    {
        return view('front-end.m13.maintenance_history.export');
    }

    function postExport(Request $request)
    {
        $rmb = @$request->rmb;
        $sb = @$request->sb;
        $route = @$request->route;
        $segment = @$request->segment;
        $km_from = @$request->km_from;
        $m_from = @$request->m_from;
        $km_to = @$request->km_to;
        $m_to = @$request->m_to;
        
        $dataset = [];
        $records = tblSectiondataMH::filterByUser()
            ->with('repairCategory', 'repairClassification', 'wardFrom', 'wardTo','layers', 'layers.materialType','repairMethod');
            
        if (!empty($km_from) && !empty($km_to) && ($km_from >= $km_to)) 
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.km_form_less_km_to'),
                ])->withInput();
        }
        if (!empty($km_from) && floatval($km_from)!= intval($km_from))
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.km_from_not_float'),
                ])->withInput();
        }
        if (!empty($km_to) && floatval($km_to)!= intval($km_to))
        {
            return redirect()->back()->withErrors([
                    'km_to' => trans('back_end.km_to_not_float'),
                ])->withInput();
        }
        if (!empty($m_to) && floatval($m_to)!= intval($m_to))
        {
            return redirect()->back()->withErrors([
                    'm_to' => trans('back_end.m_to_not_float'),
                ])->withInput();
        }
        if (!empty($m_from) && floatval($m_from)!= intval($m_from))
        {
            return redirect()->back()->withErrors([
                    'm_from' => trans('back_end.m_from_not_float'),
                ])->withInput();
        }
        if (!empty($m_from) && !empty($m_to) && ($m_from > $m_to)) 
        {
            return redirect()->back()->withErrors([
                    'm_from' => trans('back_end.m_form_less_m_to'),
                ])->withInput();
        }
        if (!empty($rmb) && $rmb != -1)
        {
            $records = $records->whereHas('segment.tblOrganization', function($q) use($rmb) {
                    $q->where('parent_id', $rmb);
                });
        }
        if (!empty($sb) && $sb != -1)
        {
            $records = $records->whereHas('segment', function($q) use($sb) {
                    $q->where('SB_id', $sb);
                });
        }
        if (!empty($segment) && $segment != -1)
        {
            $records = $records->where('segment_id', $segment);
        }
        if (!empty($route) && $route != -1)
        {
            $records = $records->whereHas('segment', function($q) use($route) {
                    $q->where('branch_id', $route);
                });
        }

        if (strlen($km_from) != 0)
        {
            $records = $records->where('km_from', '>=', intval($km_from));
        }
        if (strlen($m_from) != 0)
        {
            if (strlen($km_from) != 0)
            {
                $records = $records->whereRaw('10000*km_from+m_from >= ' . intval(10000*$km_from+$m_from));
            }
            else
            {
                $records = $records->where('m_from', '>=', intval($m_from));
            }
        }
        if (strlen($km_to) != 0)
        {
            $records = $records->where('km_to', '<=', intval($km_to));
        }
        if (strlen($m_to) != 0)
        {
            if (strlen($km_to) != 0)
            {
                $records = $records->whereRaw('10000*km_to+m_to <= ' . intval(10000*$km_to+$m_to));
            }
            else
            {
                $records = $records->where('m_to', '<=', intval($m_to));
            }
        }

        $lang = \App::isLocale('en') ? 'en' : 'vn';
        
        $rmb_data = tblOrganization::where('level', 2)->get();
        $rmb = [];
        foreach ($rmb_data as $d) 
        {
            $rmb[$d->id] = $d->{"name_$lang"};
        }

        $direction_type = [
            1 => strtolower(trans('back_end.left_direction')),
            2 => strtolower(trans('back_end.right_direction')),
            3 => strtolower(trans('back_end.single_direction'))
        ];
        $direction_running = [
            0 => strtolower(trans('back_end.left_direction')),
            1 => strtolower(trans('back_end.right_direction'))
        ];

        $records = $records->chunk(1000, function($recs) use(&$dataset, $lang,  $direction_type, $direction_running, $rmb){
                foreach ($recs as $rec) 
                {   
                    $binder_course = $rec->layers()->where('layer_id', 6)->orWhere('layer_id', 7)->sum('thickness');
                    $wearing_course = $rec->layers()->where('layer_id', 7)->orWhere('layer_id', 8)->sum('thickness');
                    $rmb_id =  $rec->segment->tblOrganization->parent_id;
                    $dataset[] = [
                        $rec->id,
                        $rec->segment->tblBranch->{"name_{$lang}"},
                        $rec->segment->{"segname_{$lang}"},
                        $rec->segment->tblBranch->branch_number,
                        ' ',
                        @$rmb[$rmb_id],
                        $rec->segment->tblOrganization->{"name_{$lang}"},
                        intval($rec->km_from),
                        floatval($rec->m_from),
                        intval($rec->km_to),
                        floatval($rec->m_to),
                        (!empty($rec->from_lat)) ? $rec->from_lat : 0,
                        (!empty($rec->from_lng)) ? $rec->from_lng : 0,
                        (!empty($rec->to_lat)) ? $rec->to_lat: 0,
                        (!empty($rec->to_lng)) ? $rec->to_lng: 0,
                        @$rec->wardFrom->district->province->{"name_{$lang}"},
                        @$rec->wardFrom->district->{"name_{$lang}"},
                        @$rec->wardFrom->{"name_{$lang}"},
                        @$rec->wardTo->district->province{"name_{$lang}"},
                        @$rec->wardTo->district{"name_{$lang}"},
                        @$rec->wardTo->{"name_{$lang}"},
                        ' ',
                        Carbon::createFromFormat('Y-m-d', $rec->survey_time)->format('Y/m/d'),
                        $direction_type[$rec->direction],
                        floatval(1000*($rec->km_to) + $rec->m_to - 1000*($rec->km_from) - $rec->m_from),
                        floatval($rec->actual_length),
                        intval($rec->lane_pos_number),
                        Carbon::createFromFormat('Y-m-d', $rec->completion_date)->format('Y/m/d'),
                        //Carbon::createFromFormat('Y-m-d', $rec->completion_date)->format('m'),
                        intval($rec->repair_duration),
                        @$rec->repairCategory->{"name_{$lang}"},
                        @$rec->repairMethod->{"name_{$lang}"},
                        @$rec->repairClassification->{"name_$lang"},
                        @$rec->layers()->where('layer_id', 6)->first()->materialType->code,
                        $binder_course,
                        $wearing_course,
                        $binder_course + $wearing_course,
                        @$rec->layers()->where('layer_id', 6)->first()->thickness,
                        floatval($rec->total_width_repair_lane),
                        @$direction_running[$rec->direction_running],
                        floatval($rec->distance)
                    ];
                }
            });

        $tpl_file = public_path('excel_templates/M13/Tpl_MH_' . strtoupper($lang) . '.xlsx');
        $excelFile = Excel::load($tpl_file,  function ($reader) use($dataset) {
            $reader->sheet(0, function($sheet) use ($dataset) {
                $sheet->fromArray($dataset, null, 'B13', null, false);
            });
        })->download('xlsx');       
    }

    public function getDataReviewTool()
    {
        $orgs = tblOrganization::with('segments')->whereIn('level', [3])->whereNotNull('parent_id')->get();
        $tree_data = [];
        $branch_data = [];
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

        $branches = tblBranch::with('segments')->get();
        foreach ($branches as $branch) 
        {
            foreach ($branch->segments as $segment) 
            {
                $branch_data[$branch->id][] = [
                    'id' => $segment->id,
                    'text' => $segment->{"segname_$lang"}
                ];
            }
        }

        return view('front-end.m13.maintenance_history.review_tool', [
            'tree_data' => $tree_data,
            'branch_data' => $branch_data,
            'sb_data' => $sb_data
        ]);
    }

    public function postDataReviewTool(Request $request)
    {
        $chainage_three = @$request->chainage_three;
        $time_data_one = $request->time_data_one;
        $time_data_two = $request->time_data_two;
        if (@$request->width_min != "") $width_min = $request->width_min;
        if (@$request->width_max != "") $width_max = $request->width_max;
        if (@$request->distance_min != "") $distance_min = $request->distance_min;
        if (@$request->distance_max != "") $distance_max = $request->distance_max;
        $errors = [];

        if (!isset($chainage_three) && !isset($time_data_one) && !isset($time_data_two) && !isset($width_min) && !isset($width_max) && !isset($temperature_min) && !isset($temperature_max) && !isset($precipitation_min) && !isset($precipitation_max))
        {
            $mh = [];
        }
        else
        {
            $mh = tblSectiondataMH::filterByUser()->with('segment.tblBranch', 'segment.tblOrganization.rmb', 'wardFrom.district.province', 'wardTo.district.province', 'layers.materialType', 'repairCategory', 'repairClassification', 'repairMethod')
                ->get()->toArray();
        }

        foreach ($mh as $record) 
        {
            $err_name = [];
            $err_id = [];

            //Check actual length in chainage
            if (isset($chainage_three))
            {
                if ($record['actual_length'] < 0)
                {
                    $err_name[] = trans('review_tool.invalid_actual_length');
                    $err_id[] = 1;
                }
            }

            //Check completion date
            if (isset($time_data_one))
            {
                if (empty($record['completion_date']))
                {
                    $err_name[] = trans('review_tool.invalid_completion_date');
                    $err_id[] = 2;
                }
            }

            //Check repair duration
            if (isset($time_data_two))
            {
                if ($record['repair_duration'] <= 0 || $record['repair_duration'] === '')
                {
                    $err_name[] = trans('review_tool.invalid_repair_duration');
                    $err_id[] = 3;
                }
            }

            //Check total width repair lane
            if (isset($width_min) && !isset($width_max))
            {
                if ($record['total_width_repair_lane'] < $width_min)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }
            elseif (!isset($width_min) && isset($width_max)) 
            {
                if ($record['total_width_repair_lane'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }
            elseif (isset($width_min) && isset($width_max))
            {
                if ($record['total_width_repair_lane'] < $width_min || $record['total_width_repair_lane'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }

            //Check distance
            if (isset($distance_min) && !isset($distance_max))
            {
                if ($record['distance'] < $distance_min)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }
            elseif (!isset($distance_min) && isset($distance_max)) 
            {
                if ($record['distance'] > $distance_max)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }
            elseif (isset($distance_min) && isset($distance_max))
            {
                if ($record['distance'] < $distance_min || $record['distance'] > $distance_max)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }

            //Add error
            if (count($err_id) > 0)
            {
                $record['err_name'] = implode(', ', $err_name);
                $record['err_id'] = $err_id;
                $errors[] = $record;
            }
        }

        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $data = [];
        foreach ($errors as $d)
        {
            $pavement_type = '';
            $binder_course = 0;
            $wearing_course = 0;
            foreach ($d['layers'] as $layer) 
            {
                if ($layer['layer_id'] == 6)
                {
                    $pavement_type = $layer['material_type']['name_'.$lang];
                    $wearing_course += $layer['thickness'];
                }

                if ($layer['layer_id'] == 8 || $layer['layer_id'] == 9)
                {
                    $binder_course += $layer['thickness'];
                }

                if ($layer['layer_id'] == 7)
                {
                    $wearing_course += $layer['thickness'];
                }
            }
            $result =[
                'section_id' => $d['id'],
                'road' => $d['segment']['tbl_branch']['name'],
                'segment' => $d['segment']['name'],
                'branch_no' => $d['segment']['tbl_branch']['branch_number'],
                'road_class' => '',
                'rmb' => $d['segment']['tbl_organization']['rmb']['name_'.$lang],
                'sb' => $d['segment']['tbl_organization']['name_'.$lang],
                'km_from' => $d['km_from'],
                'm_from' => $d['m_from'],
                'km_to' => $d['km_to'],
                'm_to' => $d['m_to'],
                'from_lat' => $d['from_lat'],
                'from_lng' => $d['from_lng'],
                'to_lat' => $d['to_lat'],
                'to_lng' => $d['to_lng'],
                'province_from' => @$d['ward_from']['district']['province']['name'],
                'district_from' => @$d['ward_from']['district']['name'],
                'ward_from' => @$d['ward_from']['name'],
                'province_to' => @$d['ward_to']['district']['province']['name'],
                'district_to' => @$d['ward_to']['district']['name'],
                'ward_to' => @$d['ward_to']['name'],
                'kilopost_adjustment_date' => '',
                'survey_time' => $d['survey_time'],
                'direction' => $this->getDirection($d['direction']),
                'length_as_per_chainage' => $d['km_to']*1000 + $d['m_to'] - $d['km_from']*1000 - $d['m_from'],
                'actual_length' => $d['actual_length'],
                'lane_pos_number' => $d['lane_pos_number'],
                'completion_date' => $d['completion_date'],
                'repair_duration' => $d['repair_duration'],
                'r_category' => $d['repair_category']['name'],
                'repair_method' => $d['repair_method']['name'],
                'r_classification_id' => $d['repair_classification']['name'],
                'pavement_type' => $pavement_type,
                'binder_course' => $binder_course,
                'wearing_course' => $wearing_course,
                'total' => $binder_course + $wearing_course,
                'total_pavement_thickness' => '',
                'total_width_repair_lane' => $d['total_width_repair_lane'],
                'direction_running' => $this->getDirectionRunning($d['direction_running']),
                'distance' => $d['distance'],
                'error' => $d['err_name']
            ];
            $data[] = $result;
        }
        $lang = \App::getLocale();
        $tpl_file = public_path('excel_templates/M13/Tpl_MH_'.strtoupper($lang) . '.xlsx');
        Excel::load($tpl_file,  function ($reader) use($data) {
            $reader->sheet(0, function($sheet) use ($data) {
                $sheet->fromArray($data, NULL, 'B13', null, false);
            });
        })
            ->setFilename('Template_MH_'.strtoupper($lang).'_Invalid')
            ->download('xlsx');
    }

    function getDirection($direction_id)
    {
        switch ($direction_id)
        {
            case 1:
                return trans('back_end.left_direction');
            case 2:
                return trans('back_end.right_direction');
            case 3:
                return trans('back_end.single_direction');
            default:
                return '';
        }
    }

    function getDirectionRunning($direction_id)
    {
        switch ($direction_id)
        {
            case 0:
                return trans('back_end.left');
            case 1:
                return trans('back_end.right');
            default:
                return '';
        }
    }

}
