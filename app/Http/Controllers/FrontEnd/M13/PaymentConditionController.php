<?php

namespace App\Http\Controllers\FrontEnd\M13;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSegment;
use App\Models\tblSectiondataRMD;
use App\Models\tblRMDHistory;
use App\Models\tblSectionLayer;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblBranch;
use App\Models\tblDesignSpeed;
use App\Models\tblOrganization;
use App\Models\tblSectionPCHistory;
use Carbon\Carbon;
use Excel, DB;
use App\Models\tblDeterioration;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
class PaymentConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        return view('front-end.m13.payment_condition.index', [
            'tree_data' => $tree_data,
            'branch_data' => $branch_data,
            'sb_data' => $sb_data
        ]);
    }

    public function getExport()
    {
        return view('front-end.m13.payment_condition.export');
    }

    public function postExport(Request $request)
    {   
        include_once __DIR__ . "/../../../../../lib/eiseXLSX/eiseXLSX.php";
        //dd(__DIR__);
        $rmb = @$request->rmb;
        $sb = @$request->sb;
        $route = @$request->route;
        $km_from = @$request->km_from;
        $m_from = @$request->m_from;
        $km_to = @$request->km_to;
        $m_to = @$request->m_to;
        $date_y = @$request->year;
        $dataset = [];
        $records = DB::table('tblSection_PC_history as h') ->select('O.sb_name_en', 'O.sb_name_vn', 'O.rmb_name_en', 'O.rmb_name_vn','O.rmb_id', 'tblBranch.road_category' ,'tblBranch.road_number','tblBranch.road_number_supplement','tblBranch.name_en as route_name_en ','tblBranch.branch_number','tblBranch.name_vn as route_name_name_vn','h.id','h.section_code','h.SB_id', 'h.branch_id' , 'h.direction' ,'h.geographical_area', 'h.km_from', 'h.m_from', 'h.km_to', 'h.m_to', 'h.section_length', 'h.analysis_area', 'h.structure', 'h.intersection', 'h.overlapping', 'h.number_of_lane_U', 'h.number_of_lane_D', 'h.lane_position_no', 'h.surface_type', 'h.date_y', 'h.date_m', 'h.cracking_ratio_cracking', 'h.cracking_ratio_patching', 'h.cracking_ratio_pothole', 'h.cracking_ratio_total', 'h.rutting_depth_max', 'h.rutting_depth_ave', 'h.IRI', 'h.MCI', 'h.note')
            ->join(DB::raw('(select sb.id as id,sb.name_en as sb_name_en, sb.name_vn as sb_name_vn, rmb.id as rmb_id ,rmb.name_en as rmb_name_en, rmb.name_vn as rmb_name_vn from tblOrganization as sb, tblOrganization as rmb where sb.parent_id = rmb.id) as O'), 'O.id','=', 'h.SB_id' )
            ->join('tblBranch', 'tblBranch.id', '=', 'h.branch_id');
       // $records = tblSectionPCHistory::with('tblOrganization', 'tblOrganization.rmb', 'tblBranch');
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
            $records = $records->where('O.rmb_id', $rmb);
        }
        if (!empty($sb) && $sb != -1)
        {
            $records = $records->where('h.SB_id', $sb);
        }
        
        if (!empty($route) && $route != -1)
        {
            $records = $records->where('branch_id', $route);
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
        $records = $records->where('h.date_y', $date_y);
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $tblOrganization = tblOrganization::where('level', 2)->get();
        $rmb_data = [];
        foreach ($tblOrganization as $d) 
        {
            $rmb_data[$d->id] = $d->name_en;
        }

        $records = $records->chunk(1000, function($recs) use(&$dataset, $lang) {
                foreach ($recs as $rec) 
                {   
                    $dataset[] = [
                        $rec->section_code,
                        $rec->geographical_area,
                        $rec->rmb_name_en,
                        $rec->sb_name_en,
                        $rec->road_category,
                        $rec->road_number,
                        $rec->road_number_supplement,
                        $rec->branch_number,
                        $rec->route_name_en,
                        $rec->km_from,
                        $rec->m_from,
                        $rec->km_to,
                        $rec->m_to,
                        $rec->section_length,
                        $rec->analysis_area,
                        $rec->structure,
                        $rec->intersection,
                        $rec->overlapping,
                        $rec->number_of_lane_U,
                        $rec->number_of_lane_D,
                        $rec->direction,
                        $rec->lane_position_no,//survey_lane,
                        $rec->surface_type,
                        $rec->date_y,//survey_year,
                        $rec->date_m,//survey_month
                        $rec->cracking_ratio_cracking,
                        $rec->cracking_ratio_patching,
                        $rec->cracking_ratio_pothole,
                        $rec->cracking_ratio_total,
                        $rec->rutting_depth_max,
                        $rec->rutting_depth_ave,
                        $rec->IRI,
                        $rec->MCI,
                        $rec->note,
                    ];
                }
            });

        $template = __DIR__ . '/../../../../../public/excel_templates/pavement_condition/PCfile_template.xlsx';
        $existingFilePath = $template;
        $newFilePath = __DIR__ . "/../../../../../public/tmp/".$rmb_data[$rmb].date('y-m-d').".xlsx";

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);

        $style = (new StyleBuilder())->setShouldWrapText(false)->build();
        // let's read the entire spreadsheet
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) 
        {
            if ($sheetIndex !== 1) 
            {
                $writer->addNewSheetAndMakeItCurrent();
            }
            if ($sheet->getIndex() === 0) 
            { 
                foreach ($sheet->getRowIterator() as $row) 
                {
                    $writer->addRow($row);
                    break;
                }
                foreach ($dataset as $key => $v) 
                {
                    $writer->addRowWithStyle($v, $style);    
                }
            }
            else
            {
                foreach ($sheet->getRowIterator() as $row) 
                {
                    $writer->addRow($row);
                }
            }
            $cur_sheet = $writer->getCurrentSheet();
            $cur_sheet->setName($sheet->getName());
        }

        $reader->close();
        $writer->close();

        $xlsx = new \eiseXLSX($newFilePath);
        $xlsx->Output($rmb_data[$rmb].date('y-m-d').".xlsx", "D");
  
        return view('front-end.m13.payment_condition.export');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
}
