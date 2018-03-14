<?php

namespace App\Http\Controllers\FrontEnd\M13;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackendRequest\RoadInventoryRequest;
use App\Models\tblSegment;
use App\Models\tblSectiondataRMD;
use App\Models\tblRMDHistory;
use App\Models\tblSectionLayer;
use App\Models\mstPavementLayer;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblBranch;
use App\Models\tblDesignSpeed;
use App\Models\tblOrganization;
use Carbon\Carbon;
use Excel;

class RoadInventoryController extends Controller
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
            $src = 'route.xlsx';
            $workbook = SpreadsheetParser::open($src);

            $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
            foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) {
                if ($rowIndex < 2) continue;
                $check = tblBranch::where('road_number', $values[2])
                    ->where('road_number_supplement', $values[3])
                    ->where('branch_number', $values[4])
                    ->where('road_category', $values[1])
                    ->first();
                if (!$check)
                {
                    $record = new tblBranch;
                    $record->road_number = $values[2];
                    $record->road_category = $values[1];
                    $record->name_en = $values[0];
                    $record->name_vn = $values[0];
                    $record->branch_number = $values[4];
                    $record->description_en = $values[0];
                    $record->description_vn = $values[0];
                    $record->road_number_supplement = $values[3];
                    $record->save();
                }
            }
            \DB::commit();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
        // \DB::beginTransaction();
        // try
        // {
        //     $roads = [];
        //     $records = tblBranch::get();
        //     foreach ($records as $r) 
        //     {
        //         $roads[$r->name_en] = [
        //             $r->road_number,
        //             $r->road_number_supplement,
        //             $r->branch_number
        //         ];
        //         $roads[$r->name_vn] = [
        //             $r->road_number,
        //             $r->road_number_supplement,
        //             $r->branch_number
        //         ];
        //     }

        //     $branches = [];
        //     $records = tblBranch::get();
        //     foreach ($records as $r) 
        //     {
        //         $branches[intval($r->road_number)][intval($r->road_number_supplement)][intval($r->branch_number)] = $r->id;
        //     }     

        //     $road_classes = [];
        //     $records = \App\Models\mstRoadClass::get();
        //     foreach ($records as $r) 
        //     {
        //         $road_classes[$r->text_id] = $r->id;
        //         $road_classes[$r->name_en] = $r->id;
        //     }   

        //     $terrain_types = [];
        //     $records = \App\Models\tblTerrainType::get();
        //     foreach ($records as $r) 
        //     {
        //         $terrain_types[$r->name_en] = $r->id;
        //         $terrain_types[$r->name_vn] = $r->id;
        //     } 

        //     $surfaces = [
        //         'AC' => 1,
        //         'BST' => 2,
        //         'CC' => 3
        //     ];

        //     $directions = [
        //         'left' => 1,
        //         'trái' => 1,
        //         'ngược' => 1,
        //         'right' => 2,
        //         'phải' => 2,
        //         'xuôi' => 2,
        //         'single' => 3,
        //         'làn đơn chung' => 3,
        //         'cả hai' => 3
        //     ];

        //     $src = 'pc_test/rmb4/RMD4.xlsx';
        //     $workbook = SpreadsheetParser::open($src);

        //     $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
        //     foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) {
        //         if ($rowIndex < 13) continue;
        //         $route_name = $values[2];
        //         $segment_name = $values[3];
        //         $branch_number = $values[4];
        //         $road_class = $values[5];
        //         $rmb = $values[6];
        //         $sb = $values[7];
        //         $km_from = $values[8];
        //         $m_from = round($values[9]);
        //         $km_to = $values[10];
        //         $m_to = $values[11];
        //         $survey_time = $values[21];
        //         $actual_length = $values[23];
        //         $construct_year = $values[24];
        //         $service_start_year = $values[25];
        //         $temperature = $values[27];
        //         $annual_precipitation = $values[28];
        //         $terrain_type = $values[26];
        //         $direction = $values[33];
        //         $lane_width = $values[39];
        //         $lane_pos_number = $values[38];
        //         $pavement_type = (string)@$values[40];
                
        //         $road_info = $roads[$route_name];

        //         if (!isset($branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])]))
        //         {
        //             // continue;
        //             echo 'branch not found';
        //             print_r($values);
        //             continue;
        //         }
        //         $branch_id = $branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])];

                
        //         $segment = tblSegment::where('branch_id', $branch_id)
        //             ->whereRaw("10000*km_from+m_from <= " . (10000*$km_from+$m_from))
        //             ->whereRaw("10000*km_to+m_to >= " . (10000*$km_to+$m_to))
        //             ->first();
        //         if (!$segment)
        //         {
        //             // continue;
        //             echo 'segment not found';
        //             print_r($values);
        //             continue;
        //             dd($segment = tblSegment::where('branch_id', $branch_id)
        //             ->whereRaw("10000*km_from+m_from <= " . (10000*$km_from+$m_from))
        //             ->whereRaw("10000*km_to+m_to >= " . (10000*$km_to+$m_to))
        //             ->toSql());
        //         }
        //         $record = new tblSectiondataRMD();
        //         $record->segment_id = $segment->id;
        //         $record->terrian_type_id = intval(@$terrain_types[$terrain_type]);
        //         $record->road_class_id = intval(@$road_classes[$road_class]);
                
        //         $record->km_from = $km_from;
        //         $record->m_from = $m_from;
        //         $record->km_to = $km_to;
        //         $record->m_to = $m_to;
        //         $record->survey_time = Carbon::createFromFormat('Y/m/d', $survey_time)->format('Y-m-d');
        //         $record->direction = $directions[strtolower($direction)];
        //         $record->lane_pos_number = intval($lane_pos_number);
        //         $record->lane_width = $lane_width;
        //         $record->construct_year = str_replace('/', '', $construct_year);
        //         $record->service_start_year = str_replace('/', '', $service_start_year);
        //         $record->temperature = $temperature;
        //         $record->annual_precipitation = $annual_precipitation;
        //         $record->actual_length = $actual_length;
        //         $record->no_lane = intval($values[37]);
        //         $record->remark = '';
        //         $record->pavement_type_id = @$surfaces[$pavement_type];
                
        //         $record->save();
        //         $record->createHistory();
        //     }
        //     \DB::commit();
        // }
        // catch (\Exception $e)
        // {
        //     \DB::rollBack();
        //     dd($e);
        // }
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



        return view('front-end.m13.road_inventory.index', [
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
        return view('front-end.m13.road_inventory.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoadInventoryRequest $request)
    {
        \DB::beginTransaction();
        try
        {
            $validation = $this->_doValidate($request);
            if ($validation !== true)
            {
                return $validation;
            }

            $record = new tblSectiondataRMD();
            $record->segment_id = $request->segment;
            $record->terrian_type_id = $request->terrain_type;
            $record->road_class_id = $request->road_class;
            $record->from_lat = $request->latitude_from;
            $record->from_lng = $request->longitude_from;
            $record->to_lat = $request->latitude_to;
            $record->to_lng = $request->longitude_to;
            $record->km_from = $request->km_from;
            $record->m_from = $request->m_from;
            $record->km_to = $request->km_to;
            $record->m_to = $request->m_to;
            $record->survey_time = $request->date_collection;
            $record->direction = $request->direction;
            $record->lane_pos_number = $request->lane_no;
            $record->lane_width = $request->lane_width;
            $record->no_lane = $request->no_of_lane;
            $record->construct_year = str_replace('/', '', $request->construct_year);
            $record->service_start_year = str_replace('/', '', $request->service_start_year);
            $record->temperature = $request->temperature;
            $record->annual_precipitation = $request->annual_precipitation;
            $record->actual_length = $request->actual_length;
            $record->remark = $request->remark;
            $record->pavement_type_id = $request->data[6]['material_type'];
            $record->pavement_thickness = array_sum(array_column($request->data, 'thickness'));
            $record->save();

            foreach ($request->data as $layer_id => $layer)
            {
                if (!empty($layer['thickness']))
                {
                    $l = new tblSectionLayer();
                    $l->thickness = $layer['thickness'];
                    $l->description = $layer['desc'];
                    $l->type = 1;
                    $l->material_type_id = $layer['material_type'];
                    $l->layer_id = $layer_id;
                    $l->rmdSection()->associate($record);
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
        $count_check = tblSectiondataRMD::where('lane_pos_number', $request->lane_no)
            ->where('direction', $request->direction) 
            ->where('segment_id', $request->segment) 
            ->whereRaw("$adjust*km_to + m_to > $adjust*{$request->km_from} + {$request->m_from}")
            ->whereRaw("$adjust*km_from + m_from < $adjust*{$request->km_to} + {$request->m_to}")
            ->whereRaw("YEAR(survey_time) = " . date('Y', strtotime($request->date_collection)))
            ->count();
        
        if ($count_check > 0)
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.overlap_with_existing_section'),
                    'm_from' => ' ',
                    'km_to' => ' ',
                    'm_to' => ' '
                ])->withInput();
        }

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
        $data = tblSectiondataRMD::findOrFail($id);
        $rmb_id = $data->segment->tblOrganization->parent_id;
        $sb_id = $data->segment->tblOrganization->id;
        $branch_id = $data->segment->branch_id;
        $segment_id = $data->segment->id;
        $edit_flg = 1;
        return view('front-end.m13.road_inventory.add', [
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
        return view('front-end.m13.road_inventory.export');
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
        $year_up_to = @$request->year_up_to;
        $direction = @$request->direction;
        $lane_no = @$request->lane_no;
        $dataset = [];
        $records = tblSectiondataRMD::with('segment', 'segment.tblBranch', 'wardFrom', 'wardFrom.district', 'wardFrom.district.province', 'wardTo', 'wardTo.district', 'wardTo.district.province')->filterByUser();
        if (!empty($km_from) && !empty($km_to) && ($km_from >= $km_to)) 
        {
            return redirect()->back()->withErrors([
                    'km_from' => trans('back_end.km_form_less_km_to'),
                ])->withInput();
        }
        if (!empty($m_from) && !empty($m_to) && ($m_from > $m_to)) 
        {
            return redirect()->back()->withErrors([
                    'm_from' => trans('back_end.m_form_less_m_to'),
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
        
        if (!empty($direction))
        {
            $records = $records->where('direction', $direction);
        }
        if (!empty($lane_no))
        {
            $records = $records->where('lane_pos_number', $lane_no);
        }
        if (empty($year_up_to))
        {
            $records = $records->with('segment', 'segment.tblBranch');
        }
        else
        {
            $records = $records->where(function($q) use($year_up_to) {
                $q->whereRaw('YEAR(survey_time) <= ' . $year_up_to)->with('segment')->with('segment.tblBranch');
            });
        }

        $lang = \App::isLocale('en') ? 'en' : 'vn';
        
        $rmb_data = tblOrganization::where('level', 2)->get();
        $rmb = [];
        foreach ($rmb_data as $d) 
        {
            $rmb[$d->id] = $d->{"name_$lang"};
        }

        $speed = tblDesignSpeed::get();
        $data_s = [];
        foreach ($speed as $s)
        {
            $key = $s->terrain_id.$s->road_class_id;
            $data_s[$key] = intval($s->speed);
        }

        $direction_type = [
            1 => trans('back_end.left'),
            2 => trans('back_end.right'),
            3 => trans('back_end.single'),
        ];

        $records = $records->chunk(1000, function($recs) use(&$dataset, $lang, $rmb, $data_s, $direction_type) {
                foreach ($recs as $rec) 
                { 

                    $parent_id =  $rec->segment->tblOrganization->parent_id;
                    $dataset[] = [
                        $rec->id,
                        $rec->segment->tblBranch->{"name_{$lang}"},
                        $rec->segment->{"segname_{$lang}"},
                        $rec->segment->tblBranch->branch_number,
                        $rec->routeClass->{"name_{$lang}"},
                        $rec->segment->tblBranch->road_category,
                        $rmb[$parent_id],
                        $rec->segment->tblOrganization->{"name_{$lang}"},
                        intval($rec->km_from),
                        floatval($rec->m_from),
                        intval($rec->km_to),
                        floatval($rec->m_to),
                        !empty($rec->from_lat) ? $rec->from_lat : 0,
                        !empty($rec->from_lng) ? $rec->from_lng : 0,
                        !empty($rec->to_lat) ? $rec->to_lat : 0,
                        !empty($rec->to_lng) ? $rec->to_lng : 0,
                        @$rec->segment->tblCity_from->{"name_{$lang}"},
                        @$rec->segment->tblDistrict_from->{"name_{$lang}"},
                        @$rec->segment->tblward_from->{"name_{$lang}"},
                        @$rec->segment->tblCity_to->{"name_{$lang}"},
                        @$rec->segment->tblDistrict_to->{"name_{$lang}"},
                        @$rec->segment->tblward_to->{"name_{$lang}"},
                        '',
                        Carbon::createFromFormat('Y-m-d', $rec->survey_time)->format('Y/m/d'),
                        floatval(1000*($rec->km_to) + $rec->m_to - 1000*($rec->km_from) - $rec->m_from),
                        floatval($rec->actual_length),
                        substr($rec->construct_year, 0, 4),
                        substr($rec->construct_year, 4, 2),
                        substr($rec->service_start_year, 0, 4),
                        substr($rec->service_start_year, 4, 2),
                        @$rec->terrianType->{"name_{$lang}"},
                        floatval($rec->temperature),
                        floatval($rec->annual_precipitation),
                        @$data_s[$rec->terrian_type_id.$rec->road_class_id],
                        $direction_type[$rec->direction],
                        ' ',//payment width
                        $rec->pavement_thickness,
                        intval($rec->no_lane),
                        intval($rec->lane_pos_number),
                        floatval($rec->lane_width),
                        @$rec->layers()->where('layer_id', 6)->first()->materialType->code,
                    ];
                }
            });        
        $tpl_file = public_path('excel_templates/M13/Tpl_RMD_' . strtoupper($lang) . '.xlsx');
        $excelFile = Excel::load($tpl_file,  function ($reader) use($dataset) {
            $reader->sheet(0, function($sheet) use ($dataset) {
                $sheet->fromArray($dataset, NULL, 'B13', null, false);
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



        return view('front-end.m13.road_inventory.review_tool', [
            'tree_data' => $tree_data,
            'branch_data' => $branch_data,
            'sb_data' => $sb_data
        ]);
    }

    public function postDataReviewTool(Request $request)
    {
        // dd($request->all());
        $chainage_three = @$request->chainage_three;
        $time_data_one = $request->time_data_one;
        $time_data_two = $request->time_data_two;
        if (@$request->width_min != "") $width_min = $request->width_min;
        if (@$request->width_max != "") $width_max = $request->width_max;
        if (@$request->temperature_min != "") $temperature_min = $request->temperature_min;
        if (@$request->temperature_max != "") $temperature_max = $request->temperature_max;
        if (@$request->precipitation_min != "") $precipitation_min = $request->precipitation_min;
        if (@$request->precipitation_max != "") $precipitation_max = $request->precipitation_max;
        $errors = [];

        if (!isset($chainage_three) && !isset($time_data_one) && !isset($time_data_two) && !isset($width_min) && !isset($width_max) && !isset($temperature_min) && !isset($temperature_max) && !isset($precipitation_min) && !isset($precipitation_max))
        {
            $rmd = [];
        }
        else
        {
            $rmd = tblSectiondataRMD::filterByUser()->with('segment.tblBranch.mstRoadCategory', 'segment.tblOrganization.rmb', 'terrianType', 'routeClass', 'wardFrom.district.province', 'wardTo.district.province', 'layers.materialType')
                ->get()->toArray();
        }

        foreach ($rmd as $record) 
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

            //Check construct year
            if (isset($time_data_one))
            {
                if (strlen($record['construct_year']) < 5)
                {
                    $err_name[] = trans('review_tool.invalid_construct_year');
                    $err_id[] = 2;
                }
            }

            //Check service start year
            if (isset($time_data_two))
            {
                if (strlen($record['service_start_year']) < 5)
                {
                    $err_name[] = trans('review_tool.invalid_service_start_year');
                    $err_id[] = 3;
                }
            }

            //Check lane width
            if (isset($width_min) && !isset($width_max))
            {
                if ($record['lane_width'] < $width_min)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }
            elseif (!isset($width_min) && isset($width_max)) 
            {
                if ($record['lane_width'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }
            elseif (isset($width_min) && isset($width_max))
            {
                if ($record['lane_width'] < $width_min || $record['lane_width'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }

            //Check temperature
            if (isset($temperature_min) && !isset($temperature_max))
            {
                if ($record['temperature'] < $temperature_min)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }
            elseif (!isset($temperature_min) && isset($temperature_max)) 
            {
                if ($record['temperature'] > $temperature_max)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }
            elseif (isset($temperature_min) && isset($temperature_max))
            {
                if ($record['temperature'] < $temperature_min || $record['temperature'] > $temperature_max)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }

            //Check precipitation
            if (isset($precipitation_min) && !isset($precipitation_max))
            {
                if ($record['annual_precipitation'] < $precipitation_min)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
                }
            }
            elseif (!isset($precipitation_min) && isset($precipitation_max)) 
            {
                if ($record['annual_precipitation'] > $precipitation_max)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
                }
            }
            elseif (isset($precipitation_min) && isset($precipitation_max))
            {
                if ($record['annual_precipitation'] < $precipitation_min || $record['annual_precipitation'] > $precipitation_max)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
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

        $design_speed = tblDesignSpeed::get()->toArray();




        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $data = [];
        foreach ($errors as $d)
        {
            $pavement_type = '';
            foreach ($d['layers'] as $layer) 
            {
                if ($layer['layer_id'] == 6)
                {
                    $pavement_type = $layer['material_type']['name_'.$lang];
                    break;
                }
            }
            $result = [
                'section_id' => $d['id'],
                'road' => $d['segment']['tbl_branch']['name'],
                'segment' => $d['segment']['name'],
                'branch_no' => $d['segment']['tbl_branch']['branch_number'],
                'road_class' => $d['route_class']['name'],
                'road_category' => $d['segment']['tbl_branch']['mst_road_category']['code_name'],
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
                'kilometpost' => '',
                'survey_time' => $d['survey_time'],
                'length_as_per_chainage' => $d['km_to']*1000 + $d['m_to'] - $d['km_from']*1000 - $d['m_from'],
                'actual_length' => $d['actual_length'],
                'construct_year_y' => substr($d['construct_year'], 0, 4) ?: '',
                'construct_year_m' => substr($d['construct_year'], 4, 2) ?: '',
                'service_start_year_y' => substr($d['service_start_year'], 0, 4) ?: '',
                'service_start_year_m' => substr($d['service_start_year'], 4, 2) ?: '',
                'terrian_type_id' => $d['terrian_type']['name'],
                'temperature' => $d['temperature'],
                'annual_precipitation' => $d['annual_precipitation'],
                'design_speed' => $this->getDesignSpeed($design_speed, $d['terrian_type_id'], $d['road_class_id']),
                'direction' => $this->getDirection($d['direction']),
                'pavement_width' => '',
                'pavement_thickness' => $d['pavement_thickness'],
                'no_lane' => $d['no_lane'],
                'lane_pos_number' => $d['lane_pos_number'],
                'lane_width' => $d['lane_width'],
                'pavement_type' => $pavement_type,
                'remark' => $d['remark'],
                'error' => $d['err_name'],
            ];
            $data[] = $result;
        }
        $lang = \App::getLocale();
        $tpl_file = public_path('excel_templates/M13/Tpl_RMD_'.strtoupper($lang) . '.xlsx');
        Excel::load($tpl_file,  function ($reader) use($data) {
            $reader->sheet(0, function($sheet) use ($data) {
                $sheet->fromArray($data, NULL, 'B13', null, false);
            });
        })
            ->setFilename('Template_RMD_'.strtoupper($lang).'_Invalid')
            ->download('xlsx');
    }

    function getDesignSpeed($all_data, $terrain_id, $road_class_id)
    {
        $design_speed = "N/A";
        foreach ($all_data as $data) 
        {
            if ($data['terrain_id'] == $terrain_id && $data['road_class_id'] == $road_class_id)
            {
                $design_speed = $data['speed'];
                break;
            }
        }
        return $design_speed;
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
}
