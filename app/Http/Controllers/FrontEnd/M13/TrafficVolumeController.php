<?php

namespace App\Http\Controllers\FrontEnd\M13;

use App\Models\tblTVHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblBranch;
use Carbon\Carbon;
use App\Models\tblSectiondataTV;
use App\Models\tblTVVehicleDetails;
use App\Models\tblSegment;
use App\Models\tblOrganization;
use Excel;

class TrafficVolumeController extends Controller
{
    function __construct()
    {
        // $this->middleware('dppermission:RMD.View');
    }
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
            $records = tblBranch::orderBy('road_number_supplement', 'desc')->get();
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
            $src = 'pc_test/rmb4/TV4.xlsx';
            $workbook = SpreadsheetParser::open($src);

            $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
            foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) 
            {
                if ($rowIndex < 13) continue;
                $route_name = $values[1];
                $branch_number = $values[2];
                $name = $values[5];
                $km = $values[6];
                $m = $values[7];
                $survey_time = $values[10];
                $total_traffic_volume_up = $values[11];
                $total_traffic_volume_down = $values[12];
                $heavy_traffic_up = $values[14];
                $heavy_traffic_down = $values[15];

                $vehicle_detail = [
                    1 => [
                        'up' => $values[17],
                        'down' => $values[18]
                    ],
                    2 => [
                        'up' => $values[20],
                        'down' => $values[21]
                    ],
                    3 => [
                        'up' => $values[23],
                        'down' => $values[24]
                    ],
                    4 => [
                        'up' => $values[26],
                        'down' => $values[27]
                    ],
                    5 => [
                        'up' => $values[29],
                        'down' => $values[30]
                    ],
                    6 => [
                        'up' => $values[32],
                        'down' => $values[33]
                    ],
                    7 => [
                        'up' => $values[35],
                        'down' => $values[36]
                    ],
                    8 => [
                        'up' => $values[38],
                        'down' => $values[39]
                    ],
                    9 => [
                        'up' => $values[41],
                        'down' => $values[42]
                    ],
                    10 => [
                        'up' => $values[44],
                        'down' => $values[45]
                    ]    
                ];

                if (isset($roads[$route_name]))
                {
                    $road_info = $roads[$route_name];    
                }
                else if (isset($roads[str_replace(' ', '', $route_name)]))
                {
                    $road_info = $roads[str_replace(' ', '', $route_name)];
                }
                else
                {
                    echo 'road not found';
                    print_r($values);
                    continue;
                }

                if (!isset($branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])]))
                {
                    echo 'branch not found';
                    print_r($values);
                    continue;
                }
                $branch_id = $branches[intval($road_info[0])][intval($road_info[1])][intval($road_info[2])];

                
                $segment = tblSegment::where('branch_id', $branch_id)
                    ->whereRaw("10000*km_from+m_from <= " . (10000*$km+$m))
                    ->whereRaw("10000*km_to+m_to > " . (10000*$km+$m))
                    ->first();
                if (!$segment)
                {
                    $segment = tblSegment::where('branch_id', $branch_id)
                        ->whereRaw("10000*km_from+m_from <= " . (10000*$km+$m))
                        ->whereRaw("10000*km_to+m_to >= " . (10000*$km+$m))
                        ->first();
                    if (!$segment)
                    {
                        echo 'segment not found';
                        print_r($values);
                        continue;
                        echo $segment = tblSegment::where('branch_id', $branch_id)
                        ->whereRaw("10000*km_from+m_from <= " . (10000*$km+$m))
                        ->whereRaw("10000*km_to+m_to > " . (10000*$km+$m))
                        ->toSql();
                        continue;
                    }
                }

                $record = new tblSectiondataTV();
                $record->segment_id = $segment->id;
                $record->name_en = $name;
                $record->name_vn = $name;
                $record->km_station = $km;
                $record->m_station = $m;
                $record->survey_time = Carbon::createFromFormat('Y-m', $survey_time)->format('Y-m-d');
                $record->remark = '';
                $record->total_traffic_volume_up = $total_traffic_volume_up;
                $record->total_traffic_volume_down = $total_traffic_volume_down;
                $record->heavy_traffic_up = $heavy_traffic_up;
                $record->heavy_traffic_down = $heavy_traffic_down;
                $record->save();

                foreach ($vehicle_detail as $vehicle_id => $info) 
                {
                    $l = new tblTVVehicleDetails();
                    $l->up = $info['up'];
                    $l->down = $info['down'];
                    $l->vehicle_type_id = $vehicle_id;
                    $l->section()->associate($record);
                    $l->save();
                }
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

    function index()
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

        return view('front-end.m13.traffic_volume.index', [
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
        return view('front-end.m13.traffic_volume.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $data = tblSectiondataTV::findOrFail($id);
        $rmb_id = $data->segment->tblOrganization->parent_id;
        $sb_id = $data->segment->tblOrganization->id;
        $branch_id = $data->segment->branch_id;
        $segment_id = $data->segment->id;
       
        return view('front-end.m13.traffic_volume.add', [
            'data' => $data,
            'breadcrumb_txt' =>  trans('back_end.edit_current_record'),
            'rmb_id' => $rmb_id,
            'sb_id' => $sb_id,
            'branch_id' => $branch_id,
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
        return view('front-end.m13.traffic_volume.export');
    }

    function postExport(Request $request)
    {
        $rmb = @$request->rmb;
        $sb = @$request->sb;
        $route = @$request->route;
        $segment = @$request->segment;
        $year_up_to = @$request->year_up_to;
        
        $dataset = [];
        $records = tblSectiondataTV::filterByUser();
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
        if (empty($year_up_to))
        {
            $records = $records->with(
                'segment', 
                'segment.tblBranch', 
                'vehicleInfos'
            );
        }
        else
        {
            $records = $records->where(function($q) use($year_up_to) {
                $q->whereRaw('YEAR(survey_time) <= ' . $year_up_to)->with(
                    'segment', 
                    'segment.tblBranch' 
                );
            })->with('vehicleInfos');
            
        }

        $lang = \App::isLocale('en') ? 'en' : 'vn';
        
        $rmb_data = tblOrganization::where('level', 2)->get();
        $rmb = [];
        foreach ($rmb_data as $d) 
        {
            $rmb[$d->id] = $d->{"name_$lang"};
        }

        $records = $records->chunk(1000, function($recs) use(&$dataset, $lang, $rmb) {
                foreach ($recs as $rec) 
                {   
                    $rmb_id =  $rec->segment->tblOrganization->parent_id;
                    $data = [
                        $rec->id,
                        $rec->segment->tblBranch->{"name_{$lang}"},
                        $rec->segment->tblBranch->branch_number,
                        @$rmb[$rmb_id],
                        $rec->segment->tblOrganization->{"name_{$lang}"},
                        $rec->{"name_{$lang}"},
                        intval($rec->km_station),
                        floatval($rec->m_station),
                        $rec->lat_station,
                        $rec->lng_station,
                        Carbon::createFromFormat('Y-m-d', $rec->survey_time)->format('Y'),
                        floatval($rec->total_traffic_volume_up),
                        floatval($rec->total_traffic_volume_down),
                        floatval($rec->total_traffic_volume_up)+floatval($rec->total_traffic_volume_down),
                        floatval($rec->heavy_traffic_up),
                        floatval($rec->heavy_traffic_down),
                        floatval($rec->heavy_traffic_up)+floatval($rec->heavy_traffic_down)
                    ];

                    foreach ($rec->vehicleInfos as $r) 
                    {
                        $data = array_merge($data, [
                                floatval($r->up),
                                floatval($r->down),
                                floatval($r->up)+floatval($r->down)
                            ]);                 
                    }

                    $data = array_merge($data, [
                        floatval($rec->total_traffic_volume_up)+floatval($rec->total_traffic_volume_down)+floatval($rec->heavy_traffic_up)+floatval($rec->heavy_traffic_down),
                    ]);
                    $dataset[] = $data;
                }
            });

        $tpl_file = public_path('excel_templates/M13/Tpl_TV_' . strtoupper($lang) . '.xlsx');
        $excelFile = Excel::load($tpl_file,  function ($reader) use($dataset) {
            $reader->sheet(0, function($sheet) use ($dataset) {
                $sheet->fromArray($dataset, NULL, 'B13', null, false);
            });
        })->download('xlsx');       
    }
}
