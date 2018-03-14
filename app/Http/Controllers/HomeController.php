<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblPMSDataset;
use App\Models\tblPMSDatasetInfo;
use App\Models\tblPMSSectioning;
use App\Models\tblPMSSectioningInfo;
use App\Models\tblPMSPCInfo;
use App\Models\tblPMSRIInfo;
use App\Models\tblPMSMHInfo;
use App\Models\tblBranch;
use App\Models\mstSetting;
use App\Classes\Helper;
use App\Models\tblSegmentHistory;
use App\Models\tblRCategory;
use App\Models\tblRClassification;
use App\Models\tblTVHistory;
use App\Models\tblOrganization;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', -1);
        return view('home');
        \DB::beginTransaction();
        try
        {
            $branches = [];
            $records = \App\Models\tblBranch::get();
            foreach ($records as $r) 
            {
                $branches[intval($r->road_number)][intval($r->road_number_supplement)][intval($r->branch_number)] = $r->id;
            }      

            $sbs = [];
            $records = \App\Models\tblOrganization::where('level', 3)->get();
            foreach ($records as $r) 
            {
                $sbs[$r->code_id] = $r->id;
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
                $repair_classifications[strtolower($r->name_en)] = $r->code;
                $repair_classifications[strtolower($r->name_vn)] = $r->code;
            }

            $surfaces = [
                0 => '',
                1 => 'AC',
                2 => 'BST',
                3 => 'CC',
            ];

            $src = 'pc_test/PMSDS_RMBII.xlsx';
            
            $workbook = SpreadsheetParser::open($src);

            $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
            foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) {
                if ($rowIndex <= 1) dd(-3);//continue;

                $branch_id = $branches[intval($values[4])][intval($values[5])][intval($values[6])];
                $sectioning = new \App\Models\tblPMSSectioning();
                $sectioning->km_from = $values[20];
                $sectioning->m_from = $values[21];
                $sectioning->km_to = $values[22];
                $sectioning->m_to = $values[23];
                $sectioning->direction = $values[27];
                $sectioning->branch_id = $branch_id;
                $sectioning->lane_pos_no = $values[28];
                $sectioning->save();
                
                $segment = \App\Models\tblSegment::where('branch_id', $branch_id)
                    ->whereRaw("10000*km_from+m_from <= " . (10000*$values[20]+$values[21]))
                    ->whereRaw("10000*km_to+m_to >= " . (10000*$values[22]+$values[23]))
                    ->first();
                if (!$segment)
                {
                    dd($segment = tblSegment::where('branch_id', $branch_id)
                    ->whereRaw("10000*km_from+m_from <= " . (10000*$km_from+$m_from))
                    ->whereRaw("10000*km_to+m_to >= " . (10000*$km_to+$m_to))
                    ->toSql());
                }
                $record = new \App\Models\tblPMSDatasetInfo();
                $record->PMS_Dataset_id = $sectioning->id;
                $record->branch_number = $values[6];
                $record->latest_condition_year = $values[37];
                $record->latest_condition_month = $values[38];
                $record->latest_pavement_type = $surfaces[intval($values[39])];
                $record->latest2_condition_year = $values[48];
                $record->latest2_condition_month = $values[49];
                $record->latest2_pavement_type = $surfaces[intval($values[50])];
                $record->pavement_type_code = $values[29];
                $record->pavement_thickness = $values[32];
                $record->pavement_width = $values[31];
                $record->segment_id = $segment->id;
                $record->segment_en = $segment->segname_en;
                $record->segment_vn = $segment->segname_vn;
                $record->service_start_year = $values[16];
                $record->service_start_month = $values[17];
                $record->construct_year = $values[18];
                $record->construct_month = $values[19];
                $record->annual_precipitation = $values[33];
                $record->temperature = $values[34];
                $record->terrain_type_id = $values[35];
                $record->road_class_id = $values[36];
                $record->structure_type_code = $values[13];
                $record->crossing_type_code = $values[14];
                $record->geographical_area = $values[15];
                $record->analysis_area = $values[25];
                $record->number_of_lane = $values[26];
                $record->latest_cracking = $values[40];
                $record->latest_patching = $values[41];
                $record->latest_pothole = $values[42];
                $record->latest_cracking_ratio = $values[43];
                $record->latest_rutting_max = $values[44];
                $record->latest_rutting_ave = $values[45];
                $record->latest_IRI = $values[46];
                $record->latest_MCI = $values[47];
                $record->latest2_cracking = $values[51];
                $record->latest2_patching = $values[52];
                $record->latest2_pothole = $values[53];
                $record->latest2_cracking_ratio = $values[54];
                $record->latest2_rutting_max = $values[55];
                $record->latest2_rutting_ave = $values[56];
                $record->latest2_IRI = $values[57];
                $record->latest2_MCI = $values[58];
                $record->section_length = $values[24];
                $record->completion_year = $values[59];
                $record->completion_month = $values[60];
                $record->r_category_code = $values[61];
                $record->r_classification_code = @$repair_classifications[strtolower($values[62])];
                $record->total_traffic_volume = $values[67];
                $record->heavy_traffic = $values[68];
                $record->year_of_dataset = 2017;
                $record->section_id = $values[0];
                $record->section_id2 = $values[1];
                $record->route_id = $values[2];
                $record->traffic_survey_year = $values[69];
                $record->sb_id = $sbs[$values[11]];
                $case = -1;
                if ($record->latest_MCI == 10 && empty($record->latest2_condition_year))
                {
                    $case = 6;
                }
                else if (!empty($record->latest_condition_year) && !empty($record->latest2_condition_year))
                {
                    $case = 3;
                }
                else if (!empty($record->latest_condition_year) && empty($record->latest2_condition_year) && $record->service_start_year + 30 < 2017)
                {
                    $case = 4;
                }
                $record->case = $case;
                $record->save();
            }
            \DB::commit();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    function importKP()
    {
        ini_set('max_execution_time', -1);
        \DB::beginTransaction();
        try
        {
            $branches = [];
            $records = \App\Models\tblBranch::get();
            foreach ($records as $r) 
            {
                $branches[intval($r->road_number)][intval($r->road_number_supplement)][intval($r->branch_number)] = $r->id;
            }

            $direction = [
                'left' => 1,
                'right' => 2
            ];

            $src = 'pc_test/irregular_kp.xlsx';
            
            $workbook = SpreadsheetParser::open($src);

            $myWorksheetIndex = $workbook->getWorksheetIndex(0);
            
            $not_found = 0;
            foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values) {
                if (!isset($branches[intval($values[0])][intval($values[1])][intval($values[2])]))
                {
                    $not_found++;
                    continue;
                }
                $branch_id = $branches[intval($values[0])][intval($values[1])][intval($values[2])];
                $sectioning = new \App\Models\mstIrregularKp();
                $sectioning->kp = $values[5];
                $sectioning->direction = $direction[strtolower($values[4])];
                $sectioning->branch_id = $branch_id;
                $sectioning->section_length = $values[6];
                $sectioning->note = (string)@$values[7];
                $sectioning->save();
            }
            \DB::commit();
            echo $not_found;
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    private function _getTimeSeriesData($pms_sectioning_info)
    {
        $dataset = [
            'ri' => [],
            'mh' => [],
            'pc' => []
        ];
        $found_ri = 0;
        $found_mh = 0;
        $found_pc = 0;

        foreach ($pms_sectioning_info as $rec) 
        {
            if ($rec->type_id == 1 && $found_ri != 1)
            {
                $ri_info = $rec->ri()->first();
                if ($ri_info)
                {
                    $dataset['ri'][] = [
                        'year' => intval($ri_info->service_start_year),
                        'month' => intval($ri_info->service_start_month),
                        'data' => $ri_info
                    ];
                    $found_ri++;    
                }
            }
            else if ($rec->type_id == 2 && $found_mh != 1)
            {
                $mh_info = $rec->mh()->first();
                if ($mh_info && $mh_info->r_category_id != 4)
                {
                    $dataset['mh'][] = [
                        'year' => intval($mh_info->completion_year),
                        'month' => intval($mh_info->completion_month),
                        'data' => $mh_info
                    ];
                    $found_mh++;    
                }   
            }
            else if ($rec->type_id == 3 && $found_pc != 2)
            {
                $pc_info = $rec->pcs()->get();
                if ($pc_info)
                {
                    $dataset['pc'][] = [
                        'year' => $rec->condition_year,
                        'month' => $rec->condition_month,
                        'data' => $pc_info
                    ];
                    $found_pc++;    
                }   
            }
        }
        return $dataset;
    }

    function findCase($s)
    {
        $jurisdiction_code;
        $case = -1;
        $pms_sectioning = tblPMSSectioning::find($s);

        $pms_dataset_info = new tblPMSDatasetInfo();    

        // get time series for this section
        $pms_sectioning_info = tblPMSSectioningInfo::where('PMS_section_id', $pms_sectioning->id)
            ->where('condition_year', '<=', 2017)
            ->orderBy('condition_year', 'desc')
            ->orderBy('condition_month', 'desc')
            ->get();

        $time_series_data = $this->_getTimeSeriesData($pms_sectioning_info);
        // check if exist info for MH(SMT) and PC
        if (count($time_series_data['mh']) + count($time_series_data['pc']) > 0)
        {
            // check if mh is latest data;
            if (
                (count($time_series_data['mh']) > 0 && count($time_series_data['pc']) == 0) ||
                (count($time_series_data['mh']) > 0 && count($time_series_data['pc']) > 0 && 
                    12 * ($time_series_data['mh'][0]['year'] - $time_series_data['pc'][0]['year']) + ($time_series_data['mh'][0]['month'] - $time_series_data['pc'][0]['month']) >= 0
                    )
                ) // case 5
            {
                $case = 5;
                $mh_data = $time_series_data['mh'][0]['data'];
                $pms_dataset_info->latest_condition_year = $time_series_data['mh'][0]['year'];
                $pms_dataset_info->latest_condition_month = $time_series_data['mh'][0]['month'];
                $pms_dataset_info->section_length = 100;

                $pms_dataset_info->completion_year = $mh_data->completion_year;
                $pms_dataset_info->completion_month = $mh_data->completion_month;
                $pms_dataset_info->r_category_code = @$this->repair_categories[$mh_data->r_category_id];
                $pms_dataset_info->r_classification_code = @$this->repair_classification[$mh_data->r_classification_id];

                $pms_dataset_info->latest_cracking = 0;
                $pms_dataset_info->latest_patching = 0;
                $pms_dataset_info->latest_pothole = 0;
                $pms_dataset_info->latest_cracking_ratio = 0;
                $pms_dataset_info->latest_rutting_max = 0;
                $pms_dataset_info->latest_rutting_ave = 0;
                $pms_dataset_info->latest_IRI = 0;
                $pms_dataset_info->latest_MCI = 10;

                $pms_dataset_info->latest2_condition_year = null;
                $pms_dataset_info->latest2_condition_month = null;
                $pms_dataset_info->latest2_pavement_type = null;
                $pms_dataset_info->latest2_cracking = null;
                $pms_dataset_info->latest2_patching = null;
                $pms_dataset_info->latest2_pothole = null;
                $pms_dataset_info->latest2_cracking_ratio = null;
                $pms_dataset_info->latest2_rutting_max = null;
                $pms_dataset_info->latest2_rutting_ave = null;
                $pms_dataset_info->latest2_IRI = null;
                $pms_dataset_info->latest2_MCI = null;

                if (count($time_series_data['ri']) > 0)
                {
                    $ri_data = $time_series_data['ri'][0]['data'];
                    $pms_dataset_info->latest_pavement_type = $ri_data->pavement_type;
                    $pms_dataset_info->pavement_type_code = $ri_data->pavement_type_code;
                    $pms_dataset_info->pavement_thickness = $ri_data->pavement_thickness;
                    $pms_dataset_info->pavement_width = $ri_data->lane_width;
                    $pms_dataset_info->segment_id = $ri_data->segment_id;

                    $segment = tblSegmentHistory::where('segment_id', $ri_data->segment_id)->orderBy('id', 'desc')->first();
                    $jurisdiction_code = @$this->jurisdiction_code[@$segment->SB_id];
                    $pms_dataset_info->sb_id = @$segment->SB_id;
                    $pms_dataset_info->segment_en = @$segment->segname_en;
                    $pms_dataset_info->segment_vn = @$segment->segname_vn;
                    $pms_dataset_info->service_start_year = $ri_data->service_start_year;
                    $pms_dataset_info->service_start_month = $ri_data->service_start_month;
                    $pms_dataset_info->construct_year = $ri_data->construct_year;
                    $pms_dataset_info->construct_month = $ri_data->construct_month;
                    $pms_dataset_info->annual_precipitation = $ri_data->annual_precipitation;
                    $pms_dataset_info->temperature = $ri_data->temperature;
                    $pms_dataset_info->terrain_type_id = $ri_data->terrain_type_id;
                    $pms_dataset_info->road_class_id = $ri_data->road_class_id;
                }
                else
                {
                    $case = 7;
                }
            }
            else
            {
                if (count($time_series_data['mh']) > 0)
                {
                    $mh_data = $time_series_data['mh'][0]['data'];
                    $pms_dataset_info->completion_year = $mh_data->completion_year;
                    $pms_dataset_info->completion_month = $mh_data->completion_month;
                    $pms_dataset_info->r_category_code = @$this->repair_categories[$mh_data->r_category_id];
                    $pms_dataset_info->r_classification_code = @$this->repair_classification[$mh_data->r_classification_id];
                }

                if (
                    count($time_series_data['mh']) > 0 &&
                    count($time_series_data['pc']) > 1 && 
                    12 * ($time_series_data['mh'][0]['year'] - $time_series_data['pc'][1]['year']) + ($time_series_data['mh'][0]['month'] - $time_series_data['pc'][1]['month']) >= 0
                    ) // case 2
                {
                    $case = 2;
                    $pc_data = $this->_recalculatePCValues($time_series_data['pc'][0]['data']);
                    if (!$pc_data) dd(-5);//continue;
                    $pms_dataset_info->latest_condition_year = $time_series_data['pc'][0]['year'];
                    $pms_dataset_info->latest_condition_month = $time_series_data['pc'][0]['month'];
                    $pms_dataset_info->latest_pavement_type = $pc_data['pavement_type'];
                    $pms_dataset_info->pavement_type_code = $pc_data['pavement_type_code'];
                    $pms_dataset_info->latest_cracking = $pc_data['cracking'];
                    $pms_dataset_info->latest_patching = $pc_data['patching'];
                    $pms_dataset_info->latest_pothole = $pc_data['pothole'];
                    $pms_dataset_info->latest_cracking_ratio = $pc_data['cracking_ratio'];
                    $pms_dataset_info->latest_rutting_max = $pc_data['rutting_max'];
                    $pms_dataset_info->latest_rutting_ave = $pc_data['rutting_ave'];
                    $pms_dataset_info->latest_IRI = $pc_data['IRI'];
                    $pms_dataset_info->latest_MCI = $pc_data['MCI']; 
                    $pms_dataset_info->structure_type_code = $pc_data['structure_type_code']; 
                    $pms_dataset_info->crossing_type_code = $pc_data['crossing_type_code']; 
                    $pms_dataset_info->geographical_area = $pc_data['geographical_area']; 
                    $pms_dataset_info->analysis_area = $pc_data['analysis_area']; 
                    $pms_dataset_info->number_of_lane = $pc_data['number_of_lane']; 
                    $pms_dataset_info->section_length = $pc_data['section_length'];

                    $mh_data = $time_series_data['mh'][0]['data'];
                    $pms_dataset_info->latest2_condition_year = $time_series_data['mh'][0]['year'];
                    $pms_dataset_info->latest2_condition_month = $time_series_data['mh'][0]['month'];
                    
                    $pms_dataset_info->completion_year = $mh_data->completion_year;
                    $pms_dataset_info->completion_month = $mh_data->completion_month;
                    $pms_dataset_info->r_category_code = @$this->repair_categories[$mh_data->r_category_id];
                    $pms_dataset_info->r_classification_code = @$this->repair_classification[$mh_data->r_classification_id];

                    $pms_dataset_info->latest2_cracking = 0;
                    $pms_dataset_info->latest2_patching = 0;
                    $pms_dataset_info->latest2_pothole = 0;
                    $pms_dataset_info->latest2_cracking_ratio = 0;
                    $pms_dataset_info->latest2_rutting_max = 0;
                    $pms_dataset_info->latest2_rutting_ave = 0;
                    $pms_dataset_info->latest2_IRI = 0;
                    $pms_dataset_info->latest2_MCI = 10;      
                    $pms_dataset_info->latest2_pavement_type = null;                          
                }
                else
                {
                    if (count($time_series_data['pc']) > 1) // case 0, 1
                    {
                        $case = 0;
                        $latest = $this->_recalculatePCValues($time_series_data['pc'][0]['data']);
                        if (!$latest) dd(-6);//continue;
                        $second_latest = $this->_recalculatePCValues($time_series_data['pc'][1]['data']);

                        $pms_dataset_info->latest_condition_year = $time_series_data['pc'][0]['year'];
                        $pms_dataset_info->latest_condition_month = $time_series_data['pc'][0]['month'];
                        $pms_dataset_info->latest_pavement_type = $latest['pavement_type'];
                        $pms_dataset_info->pavement_type_code = $latest['pavement_type_code'];
                        $pms_dataset_info->latest_cracking = $latest['cracking'];
                        $pms_dataset_info->latest_patching = $latest['patching'];
                        $pms_dataset_info->latest_pothole = $latest['pothole'];
                        $pms_dataset_info->latest_cracking_ratio = $latest['cracking_ratio'];
                        $pms_dataset_info->latest_rutting_max = $latest['rutting_max'];
                        $pms_dataset_info->latest_rutting_ave = $latest['rutting_ave'];
                        $pms_dataset_info->latest_IRI = $latest['IRI'];
                        $pms_dataset_info->latest_MCI = $latest['MCI']; 
                        $pms_dataset_info->structure_type_code = $latest['structure_type_code']; 
                        $pms_dataset_info->crossing_type_code = $latest['crossing_type_code']; 
                        $pms_dataset_info->geographical_area = $latest['geographical_area']; 
                        $pms_dataset_info->analysis_area = $latest['analysis_area']; 
                        $pms_dataset_info->number_of_lane = $latest['number_of_lane']; 
                        $pms_dataset_info->section_length = $latest['section_length'];

                        if (isset($latest['MCI']) && isset($second_latest['MCI']) && $latest['MCI'] <= $second_latest['MCI'])
                        {
                            // case 1
                            $case = 1;
                            $pms_dataset_info->latest2_condition_year = $time_series_data['pc'][1]['year'];
                            $pms_dataset_info->latest2_condition_month = $time_series_data['pc'][1]['month'];
                            $pms_dataset_info->latest2_cracking = $second_latest['cracking'];
                            $pms_dataset_info->latest2_patching = $second_latest['patching'];
                            $pms_dataset_info->latest2_pavement_type = $second_latest['pavement_type'];
                            $pms_dataset_info->latest2_pothole = $second_latest['pothole'];
                            $pms_dataset_info->latest2_cracking_ratio = $second_latest['cracking_ratio'];
                            $pms_dataset_info->latest2_rutting_max = $second_latest['rutting_max'];
                            $pms_dataset_info->latest2_rutting_ave = $second_latest['rutting_ave'];
                            $pms_dataset_info->latest2_IRI = $second_latest['IRI'];
                            $pms_dataset_info->latest2_MCI = $second_latest['MCI'];
                        }
                        else
                        {
                            $pms_dataset_info->latest2_condition_year = null;
                            $pms_dataset_info->latest2_condition_month = null;
                            $pms_dataset_info->latest2_cracking = null;
                            $pms_dataset_info->latest2_patching = null;
                            $pms_dataset_info->latest2_pavement_type = null;
                            $pms_dataset_info->latest2_pothole = null;
                            $pms_dataset_info->latest2_cracking_ratio = null;
                            $pms_dataset_info->latest2_rutting_max = null;
                            $pms_dataset_info->latest2_rutting_ave = null;
                            $pms_dataset_info->latest2_IRI = null;
                            $pms_dataset_info->latest2_MCI = null;
                        }
                    }
                    else
                    {
                        $case = 4;
                        $pc_data = $this->_recalculatePCValues($time_series_data['pc'][0]['data']);
                        if (!$pc_data) dd(-7);//continue;
                        $pms_dataset_info->latest_condition_year = $time_series_data['pc'][0]['year'];
                        $pms_dataset_info->latest_condition_month = $time_series_data['pc'][0]['month'];
                        $pms_dataset_info->latest_pavement_type = $pc_data['pavement_type'];
                        $pms_dataset_info->pavement_type_code = $pc_data['pavement_type_code'];
                        $pms_dataset_info->latest_cracking = $pc_data['cracking'];
                        $pms_dataset_info->latest_patching = $pc_data['patching'];
                        $pms_dataset_info->latest_pothole = $pc_data['pothole'];
                        $pms_dataset_info->latest_cracking_ratio = $pc_data['cracking_ratio'];
                        $pms_dataset_info->latest_rutting_max = $pc_data['rutting_max'];
                        $pms_dataset_info->latest_rutting_ave = $pc_data['rutting_ave'];
                        $pms_dataset_info->latest_IRI = $pc_data['IRI'];
                        $pms_dataset_info->latest_MCI = $pc_data['MCI']; 
                        $pms_dataset_info->structure_type_code = $pc_data['structure_type_code']; 
                        $pms_dataset_info->crossing_type_code = $pc_data['crossing_type_code']; 
                        $pms_dataset_info->geographical_area = $pc_data['geographical_area']; 
                        $pms_dataset_info->analysis_area = $pc_data['analysis_area']; 
                        $pms_dataset_info->number_of_lane = $pc_data['number_of_lane']; 
                        $pms_dataset_info->section_length = $pc_data['section_length'];

                        $pms_dataset_info->latest2_condition_year = null;
                        $pms_dataset_info->latest2_condition_month = null;
                        $pms_dataset_info->latest2_pavement_type = null;
                        $pms_dataset_info->latest2_cracking = null;
                        $pms_dataset_info->latest2_patching = null;
                        $pms_dataset_info->latest2_pothole = null;
                        $pms_dataset_info->latest2_cracking_ratio = null;
                        $pms_dataset_info->latest2_rutting_max = null;
                        $pms_dataset_info->latest2_rutting_ave = null;
                        $pms_dataset_info->latest2_IRI = null;
                        $pms_dataset_info->latest2_MCI = null;
                        if (count($time_series_data['ri']) > 0)
                        {
                            $ri_data = $time_series_data['ri'][0]['data'];
                            if (2017 - $ri_data->service_start_year <= 30)
                            {
                                // case 3
                                $case = 3;
                                $pms_dataset_info->latest2_condition_year = $time_series_data['ri'][0]['year'];
                                $pms_dataset_info->latest2_condition_month = $time_series_data['ri'][0]['month'];
                                $pms_dataset_info->latest2_cracking = 0;
                                $pms_dataset_info->latest2_patching = 0;
                                $pms_dataset_info->latest2_pothole = 0;
                                $pms_dataset_info->latest2_cracking_ratio = 0;
                                $pms_dataset_info->latest2_rutting_max = 0;
                                $pms_dataset_info->latest2_rutting_ave = 0;
                                $pms_dataset_info->latest2_IRI = 0;
                                $pms_dataset_info->latest2_MCI = 10;
                            }   
                        }
                    }
                }

                if (count($time_series_data['ri']) > 0)
                {
                    $ri_data = $time_series_data['ri'][0]['data'];
                    $pms_dataset_info->pavement_thickness = $ri_data->pavement_thickness;
                    $pms_dataset_info->pavement_width = $ri_data->lane_width;
                    $pms_dataset_info->segment_id = $ri_data->segment_id;

                    $segment = tblSegmentHistory::where('segment_id', $ri_data->segment_id)->orderBy('id', 'desc')->first();
                    $jurisdiction_code = @$this->jurisdiction_code[@$segment->SB_id];
                    $pms_dataset_info->sb_id = @$segment->SB_id;
                    $pms_dataset_info->segment_en = @$segment->segname_en;
                    $pms_dataset_info->segment_vn = @$segment->segname_vn;
                    $pms_dataset_info->service_start_year = $ri_data->service_start_year;
                    $pms_dataset_info->service_start_month = $ri_data->service_start_month;
                    $pms_dataset_info->construct_year = $ri_data->construct_year;
                    $pms_dataset_info->construct_month = $ri_data->construct_month;
                    $pms_dataset_info->annual_precipitation = $ri_data->annual_precipitation;
                    $pms_dataset_info->temperature = $ri_data->temperature;
                    $pms_dataset_info->terrain_type_id = $ri_data->terrain_type_id;
                    $pms_dataset_info->road_class_id = $ri_data->road_class_id;
                }
                else
                {
                    $case = 7;
                }
            }
        }
        else
        {
            if (count($time_series_data['ri']) > 0)
            {
                $ri_data = $time_series_data['ri'][0]['data'];
                if (2017 - $ri_data->service_start_year > 30)
                {
                    dd(-1);//continue;
                }
                else // case 6
                {
                    $case = 6;
                    $pms_dataset_info->latest_condition_year = $ri_data->service_start_year;
                    $pms_dataset_info->latest_condition_month = $ri_data->service_start_month;
                    $pms_dataset_info->section_length = 100;
                    $pms_dataset_info->latest_pavement_type = $ri_data->pavement_type;
                    $pms_dataset_info->pavement_type_code = $ri_data->pavement_type_code;
                    $pms_dataset_info->pavement_thickness = $ri_data->pavement_thickness;
                    $pms_dataset_info->pavement_width = $ri_data->lane_width;
                    $pms_dataset_info->segment_id = $ri_data->segment_id;

                    $segment = tblSegmentHistory::where('segment_id', $ri_data->segment_id)->orderBy('id', 'desc')->first();
                    $jurisdiction_code = @$this->jurisdiction_code[@$segment->SB_id];
                    $pms_dataset_info->sb_id = @$segment->SB_id;
                    $pms_dataset_info->segment_en = @$segment->segname_en;
                    $pms_dataset_info->segment_vn = @$segment->segname_vn;
                    $pms_dataset_info->service_start_year = $ri_data->service_start_year;
                    $pms_dataset_info->service_start_month = $ri_data->service_start_month;
                    $pms_dataset_info->construct_year = $ri_data->construct_year;
                    $pms_dataset_info->construct_month = $ri_data->construct_month;
                    $pms_dataset_info->annual_precipitation = $ri_data->annual_precipitation;
                    $pms_dataset_info->temperature = $ri_data->temperature;
                    $pms_dataset_info->terrain_type_id = $ri_data->terrain_type_id;
                    $pms_dataset_info->road_class_id = $ri_data->road_class_id;

                    $pms_dataset_info->latest_cracking = 0;
                    $pms_dataset_info->latest_patching = 0;
                    $pms_dataset_info->latest_pothole = 0;
                    $pms_dataset_info->latest_cracking_ratio = 0;
                    $pms_dataset_info->latest_rutting_max = 0;
                    $pms_dataset_info->latest_rutting_ave = 0;
                    $pms_dataset_info->latest_IRI = 0;
                    $pms_dataset_info->latest_MCI = 10; 

                    $pms_dataset_info->latest2_condition_year = null;
                    $pms_dataset_info->latest2_condition_month = null;
                    $pms_dataset_info->latest2_pavement_type = null;
                    $pms_dataset_info->latest2_cracking = null;
                    $pms_dataset_info->latest2_patching = null;
                    $pms_dataset_info->latest2_pothole = null;
                    $pms_dataset_info->latest2_cracking_ratio = null;
                    $pms_dataset_info->latest2_rutting_max = null;
                    $pms_dataset_info->latest2_rutting_ave = null;
                    $pms_dataset_info->latest2_IRI = null;
                    $pms_dataset_info->latest2_MCI = null;
                }
            }
            else
            {
                // continue;
                dd(-2);
            }
        }
        echo $case;
    }

    /**
     * index_type: 1: cracking ratio
     * index_type: 2: rutting and IRI
     */
    private function _calculateIndexes($data, $index_key, $index_type = 1, $case = false)
    {
        $length = 0;
        $value = 0;
        foreach ($data as $d) 
        {
            if ($index_type == 1)
            {
                if ($case == 1)
                {
                    if (in_array($d->pavement_type, ['AC', 'BST']))
                    {
                        $value+= $d->{$index_key} * $d->section_length;
                        $length+= $d->section_length;
                    }
                    else if ($d->pavement_type == 'CC')
                    {
                        $value+= $d->{$index_key} * $d->section_length / 3.33;
                        $length+= $d->section_length;
                    }
                }
                else
                {
                    if (in_array($d->pavement_type, ['AC', 'BST']))
                    {
                        $value+= $d->{$index_key} * $d->section_length * 3.33;
                        $length+= $d->section_length;
                    }
                    else if ($d->pavement_type == 'CC')
                    {
                        $value+= $d->{$index_key} * $d->section_length;
                        $length+= $d->section_length;
                    }
                }
            }
            else
            {
                $value+= $d->{$index_key} * $d->section_length;
                $length+= $d->section_length;
            }
        }
        return ($length == 0) ? 0 : round($value/$length, 1);
    }

    private function _recalculatePCValues($data)
    {
        $dataset = [];
        if ($data->count() == 1)
        {
            $dataset['cracking'] = $data->first()->cracking;
            $dataset['patching'] = $data->first()->patching;
            $dataset['pothole'] = $data->first()->pothole;
            $dataset['cracking_ratio'] = $data->first()->cracking_ratio;
            $dataset['rutting_max'] = $data->first()->rutting_max;
            $dataset['rutting_ave'] = $data->first()->rutting_ave;
            $dataset['IRI'] = $data->first()->IRI;
            $dataset['MCI'] = $data->first()->MCI;
            $dataset['section_length'] = $data->first()->section_length;
            $dataset['geographical_area'] = $data->first()->geographical_area;
            $dataset['crossing_type_code'] = $data->first()->intersection;
            $dataset['structure_type_code'] = $data->first()->structure;
            $dataset['pavement_type'] = $data->first()->pavement_type;
            $dataset['pavement_type_code'] = $data->first()->pavement_type_code;
            $dataset['analysis_area'] = $data->first()->analysis_area;
            $dataset['number_of_lane'] = $data->first()->number_of_lane;
        }
        else
        {
            $ac_length = 0;
            $cc_length = 0;
            $bst_length = 0;
            $this->_calculatePavementTypeLength($data, $ac_length, $bst_length, $cc_length);

            $dataset['section_length'] = $ac_length + $bst_length + $cc_length;
            $dataset['geographical_area'] = $data->first()->geographical_area;
            $dataset['crossing_type_code'] = $data->first()->intersection;
            $dataset['structure_type_code'] = $data->first()->structure;
            $dataset['number_of_lane'] = $data->first()->number_of_lane;
            $dataset['analysis_area'] = array_sum($data->pluck('analysis_area')->toArray());
            if ($ac_length + $bst_length + $cc_length == 0)
            {
                dd(-9);
                //continue;
            }
            if (($ac_length + $bst_length)/($ac_length + $bst_length + $cc_length) >= 0.5 && $data->first()->pavement_type != 'CC')
            {
                // pavement type for 100m is AC or BST
                $dataset['cracking'] = $this->_calculateIndexes($data, 'cracking', 1, 1);
                $dataset['patching'] = $this->_calculateIndexes($data, 'patching', 1, 1);
                $dataset['pothole'] = $this->_calculateIndexes($data, 'pothole', 1, 1);
                $dataset['cracking_ratio'] = $this->_calculateIndexes($data, 'cracking_ratio', 1, 1);
                $dataset['rutting_max'] = $this->_calculateIndexes($data, 'rutting_max', 2);
                $dataset['rutting_ave'] = $this->_calculateIndexes($data, 'rutting_ave', 2);
                $dataset['IRI'] = $this->_calculateIndexes($data, 'IRI', 2);
                $dataset['MCI'] = Helper::getMCI($dataset['cracking_ratio'], $dataset['rutting_ave'], $dataset['IRI'], 'AC');
                if ($ac_length >= $bst_length)
                {
                    // pavement type is AC
                    $dataset['pavement_type'] = 'AC';
                    $dataset['pavement_type_code'] = 1;
                }
                else
                {
                    // pavement type is BST
                    $dataset['pavement_type'] = 'BST';
                    $dataset['pavement_type_code'] = 2;
                }
            }
            else if ($cc_length/($ac_length + $bst_length + $cc_length) >= 0.5)
            {
                // pavement type for 100 is CC
                $dataset['cracking'] = $this->_calculateIndexes($data, 'cracking', 1, 2);
                $dataset['patching'] = $this->_calculateIndexes($data, 'patching', 1, 2);
                $dataset['pothole'] = $this->_calculateIndexes($data, 'pothole', 1, 2);
                $dataset['cracking_ratio'] = $this->_calculateIndexes($data, 'cracking_ratio', 1, 2);
                $dataset['rutting_max'] = $this->_calculateIndexes($data, 'rutting_max', 2);
                $dataset['rutting_ave'] = $this->_calculateIndexes($data, 'rutting_ave', 2);
                $dataset['IRI'] = $this->_calculateIndexes($data, 'IRI', 2);
                $dataset['MCI'] = Helper::getMCI($dataset['cracking_ratio'], $dataset['rutting_ave'], $dataset['IRI'], 'CC');
                $dataset['pavement_type'] = 'CC';
                $dataset['pavement_type_code'] = 3;
            }
            else
            {
                $dataset = null;
            }
        }
        return $dataset;
    }

    private function _calculatePavementTypeLength($data, &$ac_length, &$bst_length, &$cc_length)
    {
        foreach ($data as $d) 
        {
            if ($d->pavement_type == 'AC')
            {
                $ac_length+= $d->section_length;
            }
            else if ($d->pavement_type == 'BST')
            {
                $bst_length+= $d->section_length;
            }
            else if ($d->pavement_type == 'CC')
            {
                $cc_length+= $d->section_length;
            }
        }
    }
}
