<?php

namespace App\Jobs;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
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
// use App\Models\tblTVHistory;
use App\Models\tblSectiondataTV;
use App\Models\tblOrganization;

class formulate_dataset implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $segmentation;

    protected $pms_dataset_id;

    protected $branches = [];

    protected $repair_categories = [];

    protected $repair_classification = [];

    protected $alpha;

    protected $jurisdiction_code = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($segmentation, $pms_dataset_id)
    {
        $this->segmentation = $segmentation;
        $this->pms_dataset_id = $pms_dataset_id;

        $records = tblBranch::orderBy('road_number_supplement', 'desc')->get();
        foreach ($records as $r) 
        {
            $this->branches[$r->id] = [
                'branch_number' => $r->branch_number,
                'road_category' => $r->road_category,
                'road_number' => $r->road_number,
                'road_number_supplement' => $r->road_number_supplement
            ];
        }

        $records = tblRCategory::get();
        foreach ($records as $r) 
        {
            $this->repair_categories[$r->id] = $r->code;
        }

        $records = tblRClassification::get();
        foreach ($records as $r) 
        {
            $this->repair_classification[$r->id] = $r->code;
        }

        $this->alpha = mstSetting::where('code', 2)->first()->value;

        $rmbs = tblOrganization::where('level', 2)->get();
        $rmb_code = [];
        foreach ($rmbs as $r) 
        {
            $rmb_code[$r->id] = $r->code_id;
        }

        $sbs = tblOrganization::where('level', 3)->get();
        foreach ($sbs as $s) 
        {
            $this->jurisdiction_code[$s->id] = @$rmb_code[$s->parent_id];
        }
    }

    private function _countItemByKeyAndValue($data, $key, $value)
    {
        return intval(@array_count_values($data->pluck($key)->toArray())[$value]);
    }

    private function _filterHistoryByTypeId($data, $type_id)
    {
        $dataset = [];
        foreach ($data as $d) 
        {
            if ($d->type_id == $type_id)
            {
                $dataset[] = $d;
            }
        }
        return $dataset;
    }

    private function _getTVData($pms_dataset, $segmentation)
    {
        $target_branches = tblPMSSectioning::whereIn('id', $segmentation)
            ->groupBy('branch_id')
            ->get()
            ->pluck('branch_id')
            ->toArray();
        $date = "{$pms_dataset->year}-12-31";
        
        $records = tblSectiondataTV::with('latestSegment')
            ->whereHas('latestSegment', function($q) use($date, $target_branches) {
                $q->whereRaw('(updated_at is null or updated_at <= "' . $date . '")')
                    ->whereIn('branch_id', $target_branches); 
            })
            ->where('survey_time' , '<=', $date)
            ->orderBy('km_station')
            ->orderBy('m_station')
            ->orderBy('survey_time')
            ->get();
        $data = [];
        foreach ($records as $r) 
        {
            if (!isset($data[$r->latestSegment->branch_id]))
            {
                $data[$r->latestSegment->branch_id] = [];   
            }
            $data[$r->latestSegment->branch_id][$r->sectiondata_id] = [
                'milestone' => 10000*$r->km_station+$r->m_station,
                'total_traffic_volume1' => $r->total_traffic_volume_up,
                'total_traffic_volume2' => $r->total_traffic_volume_down,
                'total_traffic_volume3' => $r->total_traffic_volume_up + $r->total_traffic_volume_down,
                'heavy_traffic1' => $r->heavy_traffic_up,
                'heavy_traffic2' => $r->heavy_traffic_down,
                'heavy_traffic3' => $r->heavy_traffic_up + $r->heavy_traffic_down,
                'survey_time' => $r->survey_time
            ];
        }
        return $data;
    }

    /**
     * @response []
     * [year, month, data]
     */
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

        $cch_ri_year = 0;
        $cch_mh_year = 0;
        $cch_pc_year = 0;

        foreach ($pms_sectioning_info as $rec) 
        {
            if ($rec->type_id == 1 && $found_ri <= 1)
            {
                $ri_info = $rec->ris;
                if ($ri_info)
                {
                    if ($cch_ri_year == 0)
                    {
                        $cch_ri_year = $rec->condition_year;
                    }
                    if ($cch_ri_year != $rec->condition_year)
                    {
                        $cch_ri_year = $rec->condition_year;
                        $found_ri++;
                    }
                    if ($found_ri > 1) continue;

                    foreach ($ri_info as $r) 
                    {
                        $dataset['ri'][] = [
                            'year' => empty($ri_info->service_start_year) ? intval($r->construct_year) : intval($ri_info->service_start_year),
                            'data' => $r,
                            'month' => empty($ri_info->service_start_year) ? intval($r->construct_month) : intval($ri_info->service_start_month)
                        ];
                    }
                }
            }
            else if ($rec->type_id == 2 && $found_mh <= 1)
            {
                $mh_info = $rec->mhs;
                if ($mh_info)
                {
                    if ($cch_mh_year == 0)
                    {
                        $cch_mh_year = $rec->condition_year;
                    }
                    if ($cch_mh_year != $rec->condition_year)
                    {
                        $cch_mh_year = $rec->condition_year;
                        $found_mh++;
                    }
                    if ($found_mh > 1) continue;

                    foreach ($mh_info as $m) 
                    {
                        $dataset['mh'][] = [
                            'year' => intval($m->completion_year),
                            'data' => $m,
                            'month' => intval($m->completion_month)
                        ]; 
                    }
                }   
            }
            else if ($rec->type_id == 3 && $found_pc <= 2)
            {
                $pc_info = $rec->pcs->unique(function ($item) {
                    return $item->from_m . '-' . $item->to_m;
                });
                if ($pc_info)
                {
                    if ($cch_pc_year == 0)
                    {
                        $cch_pc_year = $rec->condition_year;
                    }
                    if ($cch_pc_year != $rec->condition_year)
                    {
                        $cch_pc_year = $rec->condition_year;
                        $found_pc++;
                    }
                    if ($found_pc > 2) continue;

                    if (!isset($dataset['pc'][$cch_pc_year]))
                    {
                        $dataset['pc'][$cch_pc_year] = [];
                    }

                    foreach ($pc_info as $p) 
                    {
                        $dataset['pc'][$cch_pc_year][] = [
                            'year' => $rec->condition_year,
                            'data' => $p,
                            'month' => $rec->condition_month
                        ];
                    }
                }   
            }
        }

        // convert to needed data
        // ri:
        //   year
        //   month
        //   data
        // mh:
        //   year
        //   month
        //   data
        // pc: []
        //   year
        //   month
        //   data 
        //
        $final_data = [
            'ri' => null,
            'mh' => null,
            'pc' => []
        ];
        // ri
        $ri_data = $dataset['ri'];
        $ri_total_length = 0;
        $ri_max_length = -1;
        $ri_max_index = -1;
        $ri_m_start = 999999;

        foreach ($ri_data as $key => $rec) 
        {
            $r = $rec['data'];
            $ri_total_length+= $r->actual_length;
            if ($r->actual_length >= $ri_max_length && $r->m_start < $ri_m_start)
            {
                $ri_max_length = $r->actual_length;
                $ri_max_index = $key;
                $ri_m_start = $r->m_start;
            }
        }
        if ($ri_max_index > -1 && $ri_total_length >= 50)
        {
            $final_data['ri'] = [
                'year' => $ri_data[$ri_max_index]['year'],
                'month' => $ri_data[$ri_max_index]['month'],
                'data' => $ri_data[$ri_max_index]['data']
            ];
        }
        // mh
        $mh_data = $dataset['mh'];
        $mh_total_length = 0;
        $mh_max_length = -1;
        $mh_max_index = -1;
        $mh_m_start = 999999;
        foreach ($mh_data as $key => $rec) 
        {
            $r = $rec['data'];
            $mh_total_length+= $r->actual_length;
            if ($r->actual_length >= $mh_max_length && $r->m_start < $mh_m_start)
            {
                $mh_max_length = $r->actual_length;
                $mh_max_index = $key;
                $mh_m_start = $r->m_start;
            }
        }
        if ($mh_max_index > -1 && $mh_total_length >= 50)
        {
            $final_data['mh'] = [
                'year' => $mh_data[$mh_max_index]['year'],
                'month' => $mh_data[$mh_max_index]['month'],
                'data' => $mh_data[$mh_max_index]['data']
            ];
        }
        // pc
        $pc_year_data = $dataset['pc'];
        foreach ($pc_year_data as $year => $pc_data) 
        {
            $pc_total_length = 0;
            $pc_max_length = -1;
            $pc_max_index = -1;
            $pc_m_start = 999999;
            $pc_section_data = [];
            foreach ($pc_data as $key => $rec)
            {
                $r = $rec['data'];
                $pc_section_data[] = $r;
                $pc_total_length+= $r->section_length;
                if ($r->section_length >= $pc_max_length && $r->from_m < $pc_m_start)
                {
                    $pc_max_length = $r->section_length;
                    $pc_max_index = $key;
                    $pc_m_start = $r->from_m;
                }
            }

            $ac_length = 0;
            $cc_length = 0;
            $bst_length = 0;
            $this->_calculatePavementTypeLength($pc_section_data, $ac_length, $bst_length, $cc_length);

            if ($pc_max_index > -1 && $pc_total_length >= 50)
            {
                $main_data = $pc_data[$pc_max_index]['data'];
                $tmp = [
                    'section_length' => $pc_total_length,
                    'geographical_area' => $main_data->geographical_area,
                    'crossing_type_code' => $main_data->crossing_type_code,
                    'structure_type_code' => $main_data->structure_type_code,
                    'analysis_area' => $main_data->analysis_area,
                    'number_of_lane' => $main_data->number_of_lane,
                    'sb_id' => $main_data->SB_id
                ];

                if (($ac_length + $bst_length) >= 0.5 * ($ac_length + $bst_length + $cc_length) && $pc_section_data[0]->pavement_type != 'CC')
                {
                    $tmp['cracking'] = $this->_calculateIndexes($pc_section_data, 'cracking', 1, 1);
                    $tmp['patching'] = $this->_calculateIndexes($pc_section_data, 'patching', 1, 1);
                    $tmp['pothole'] = $this->_calculateIndexes($pc_section_data, 'pothole', 1, 1);
                    $tmp['cracking_ratio'] = $this->_calculateIndexes($pc_section_data, 'cracking_ratio', 1, 1);
                    if ($ac_length >= $bst_length)
                    {
                        // pavement type is AC
                        $tmp['pavement_type'] = 'AC';
                        $tmp['pavement_type_code'] = 1;
                    }
                    else
                    {
                        // pavement type is BST
                        $tmp['pavement_type'] = 'BST';
                        $tmp['pavement_type_code'] = 2;
                    }
                    $tmp['rutting_max'] = $this->_calculateIndexes($pc_section_data, 'rutting_max', 2);
                    $tmp['rutting_ave'] = $this->_calculateIndexes($pc_section_data, 'rutting_ave', 2);
                    $tmp['IRI'] = $this->_calculateIndexes($pc_section_data, 'IRI', 2);
                    $tmp['MCI'] = Helper::getMCI($tmp['cracking_ratio'], $tmp['rutting_ave'], $tmp['IRI'], 'AC');
                }
                else if ($cc_length >= 0.5 * ($ac_length + $bst_length + $cc_length))
                {
                    $tmp['cracking'] = $this->_calculateIndexes($pc_section_data, 'cracking', 1, 2);
                    $tmp['patching'] = $this->_calculateIndexes($pc_section_data, 'patching', 1, 2);
                    $tmp['pothole'] = $this->_calculateIndexes($pc_section_data, 'pothole', 1, 2);
                    $tmp['cracking_ratio'] = $this->_calculateIndexes($pc_section_data, 'cracking_ratio', 1, 2);
                    $tmp['pavement_type'] = 'CC';
                    $tmp['pavement_type_code'] = 3;
                    $tmp['rutting_max'] = $this->_calculateIndexes($pc_section_data, 'rutting_max', 2);
                    $tmp['rutting_ave'] = $this->_calculateIndexes($pc_section_data, 'rutting_ave', 2);
                    $tmp['IRI'] = $this->_calculateIndexes($pc_section_data, 'IRI', 2);
                    $tmp['MCI'] = Helper::getMCI($tmp['cracking_ratio'], $tmp['rutting_ave'], $tmp['IRI'], 'CC');
                }
                else
                {
                    $tmp['cracking'] = $main_data->cracking;
                    $tmp['patching'] = $main_data->patching;
                    $tmp['pothole'] = $main_data->pothole;
                    $tmp['cracking_ratio'] = $main_data->cracking_ratio;
                    $tmp['rutting_max'] = $main_data->rutting_max;
                    $tmp['rutting_ave'] = $main_data->rutting_ave;
                    $tmp['IRI'] = $main_data->IRI;
                    $tmp['MCI'] = $main_data->MCI;
                    $tmp['pavement_type'] = $main_data->pavement_type;
                    $tmp['pavement_type_code'] = $main_data->pavement_type_code;
                }

                $final_data['pc'][] = [
                    'year' => $pc_data[$pc_max_index]['year'],
                    'month' => $pc_data[$pc_max_index]['month'],
                    'data' => $tmp
                ];
            }
        }
        
        return $final_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();
    	try
        {
            $pms_dataset = tblPMSDataset::find($this->pms_dataset_id);
            $year_dataset = $pms_dataset->year;
            $segmentation = $this->segmentation;
            $tv_data = $this->_getTVData($pms_dataset, $segmentation);

            $pms_sectionings = tblPMSSectioning::with([
                    'infos' => function($q) use($year_dataset) {
                        $q->with([
                            'ris', 
                            'mhs', 
                            'pcs'
                            ])
                            ->where('condition_year', '<=', $year_dataset)
                            ->orderBy('condition_year', 'desc')
                            ->orderBy('condition_month', 'desc');
                    }
                ])
                ->whereIn('id', $segmentation)
                ->get();
            
            foreach ($pms_sectionings as $pms_sectioning)
            {
                // $timeStart = 1000 * microtime(true);
                $jurisdiction_code;
                $case = -1;
                $pms_dataset_info = new tblPMSDatasetInfo();
                $pms_dataset_info->year_of_dataset = $pms_dataset->year;
                $pms_dataset_info->PMS_Dataset_id = $pms_sectioning->id;
                // }

                // get time series for this section
                $pms_sectioning_info = $pms_sectioning->infos;
                $time_series_data = $this->_getTimeSeriesData($pms_sectioning_info);
                
                // check if exist info for MH(SMT) and PC
                if (isset($time_series_data['mh']))
                {
                    $pms_dataset_info->mh_flg = 1;
                }
                if (count($time_series_data['pc']) > 0)
                {
                    $pms_dataset_info->pc_flg = 1;
                }
                if (isset($time_series_data['ri']))
                {
                    $pms_dataset_info->ri_flg = 1;
                }

                if (isset($time_series_data['mh']) || count($time_series_data['pc']) > 0)
                {
                    // check if mh is latest data;
                    if (
                        (isset($time_series_data['mh']) && count($time_series_data['pc']) == 0) ||
                        (isset($time_series_data['mh']) && count($time_series_data['pc']) > 0 && 
                            12 * ($time_series_data['mh']['year'] - $time_series_data['pc'][0]['year']) + ($time_series_data['mh']['month'] - $time_series_data['pc'][0]['month']) >= 0
                            )
                        ) // case 5
                    {
                        $case = 5;
                        $mh_data = $time_series_data['mh']['data'];
                        $pms_dataset_info->latest_condition_year = $time_series_data['mh']['year'];
                        $pms_dataset_info->latest_condition_month = $time_series_data['mh']['month'];
                        $pms_dataset_info->section_length = 100;

                        $pms_dataset_info->completion_year = $mh_data->completion_year;
                        $pms_dataset_info->completion_month = $mh_data->completion_month;
                        $pms_dataset_info->r_category_code = @$this->repair_categories[$mh_data->r_category_id];
                        $pms_dataset_info->r_classification_code = @$this->repair_classification[$mh_data->r_classification_id];

                        if (count($time_series_data['pc']) > 0)
                        {
                            $pc_data = $time_series_data['pc'][0]['data'];
                            $pms_dataset_info->latest_cracking = $pc_data['cracking'];
                            $pms_dataset_info->latest_patching = $pc_data['patching'];
                            $pms_dataset_info->latest_pothole = $pc_data['pothole'];
                            $pms_dataset_info->latest_cracking_ratio = $pc_data['cracking_ratio'];
                            $pms_dataset_info->latest_rutting_max = $pc_data['rutting_max'];
                            $pms_dataset_info->latest_rutting_ave = $pc_data['rutting_ave'];
                            $pms_dataset_info->latest_IRI = $pc_data['IRI'];
                            $pms_dataset_info->latest_MCI = $pc_data['MCI'];
                        }
                        else
                        {
                            $pms_dataset_info->latest_cracking = 0;
                            $pms_dataset_info->latest_patching = 0;
                            $pms_dataset_info->latest_pothole = 0;
                            $pms_dataset_info->latest_cracking_ratio = 0;
                            $pms_dataset_info->latest_rutting_max = 0;
                            $pms_dataset_info->latest_rutting_ave = 0;
                            $pms_dataset_info->latest_IRI = 0;
                            $pms_dataset_info->latest_MCI = 10;
                        }

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

                        if (isset($time_series_data['ri']))
                        {
                            $ri_data = $time_series_data['ri']['data'];
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
                            $pms_dataset_info->sb_id = $pc_data['sb_id'];
                        }
                    }
                    else
                    {
                        if (isset($time_series_data['mh']))
                        {
                            $mh_data = $time_series_data['mh']['data'];
                            $pms_dataset_info->completion_year = $mh_data->completion_year;
                            $pms_dataset_info->completion_month = $mh_data->completion_month;
                            $pms_dataset_info->r_category_code = @$this->repair_categories[$mh_data->r_category_id];
                            $pms_dataset_info->r_classification_code = @$this->repair_classification[$mh_data->r_classification_id];
                        }

                        if (
                            isset($time_series_data['mh']) &&
                            count($time_series_data['pc']) > 1 && 
                            12 * ($time_series_data['mh']['year'] - $time_series_data['pc'][1]['year']) + ($time_series_data['mh']['month'] - $time_series_data['pc'][1]['month']) >= 0
                            ) // case 2
                        {
                            $case = 2;
                            $pc_data = $time_series_data['pc'][0]['data'];
                            if (!$pc_data) continue;
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

                            $mh_data = $time_series_data['mh']['data'];
                            $pms_dataset_info->latest2_condition_year = $time_series_data['mh']['year'];
                            $pms_dataset_info->latest2_condition_month = $time_series_data['mh']['month'];
                            
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
                                $latest = $time_series_data['pc'][0]['data'];
                                if (!$latest) continue;
                                $second_latest = $time_series_data['pc'][1]['data'];

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
                                $pc_data = $time_series_data['pc'][0]['data'];
                                
                                if (!$pc_data) continue;
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
                                if (isset($time_series_data['ri']))
                                {
                                    $ri_data = $time_series_data['ri']['data'];
                                    if ($pms_dataset->year - $ri_data->service_start_year <= $this->alpha)
                                    {
                                        // case 3
                                        $case = 3;
                                        $pms_dataset_info->latest2_condition_year = $time_series_data['ri']['year'];
                                        $pms_dataset_info->latest2_condition_month = $time_series_data['ri']['month'];
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
                                else
                                {
                                    $pms_dataset_info->sb_id = $pc_data['sb_id'];
                                }
                            }
                        }

                        if (isset($time_series_data['ri']))
                        {
                            $ri_data = $time_series_data['ri']['data'];
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
                            if (count($time_series_data['pc']) > 1)
                            {
                                $pc_data = $time_series_data['pc'][0]['data'];
                                $pms_dataset_info->sb_id = $pc_data['sb_id'];
                            }
                            $case = 7;
                        }
                    }
                }
                else
                {
                    if (isset($time_series_data['ri']))
                    {
                        $ri_data = $time_series_data['ri']['data'];
                        if ($pms_dataset->year - $ri_data->service_start_year > $this->alpha)
                        {
                            continue;
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
                        continue;
                    }
                }

                $tv = $this->_calculateTV($pms_sectioning, $tv_data);
                $pms_dataset_info->total_traffic_volume = $tv[0];
                $pms_dataset_info->heavy_traffic = $tv[1];
                $pms_dataset_info->traffic_survey_year = $tv[2];

                $pms_dataset_info->case = $case;
                $pms_dataset_info->branch_number = (string)@$this->branches[$pms_sectioning->branch_id]['branch_number'];

                $section_id = [];
                $section_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_category'];
                $section_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number'];
                $section_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number_supplement'];
                $section_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['branch_number'];
                $section_id[] = $pms_sectioning->direction;
                $section_id[] = $pms_sectioning->lane_pos_no;
                $section_id[] = '_';
                $section_id[] = str_pad($pms_sectioning->km_from, 4, 0, STR_PAD_LEFT);
                $section_id[] = str_pad($pms_sectioning->m_from, 5, 0, STR_PAD_LEFT);
                $pms_dataset_info->section_id = implode('', $section_id);

                if (isset($jurisdiction_code))
                {
                    $section_id2 = [];
                    $section_id2[] = $jurisdiction_code;
                    $section_id2[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_category'];
                    $section_id2[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number'];
                    $section_id2[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number_supplement'];
                    $section_id2[] = (string)@$this->branches[$pms_sectioning->branch_id]['branch_number'];
                    $section_id2[] = $pms_sectioning->direction;
                    $section_id2[] = '_';
                    $section_id2[] = str_pad($pms_sectioning->km_from, 4, 0, STR_PAD_LEFT);
                    $pms_dataset_info->section_id2 = implode('', $section_id2);
                    $route_id = [];
                    $route_id[] = $jurisdiction_code;
                    $route_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_category'];
                    $route_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number'];
                    $route_id[] = (string)@$this->branches[$pms_sectioning->branch_id]['road_number_supplement'];
                    $pms_dataset_info->route_id = implode('', $route_id);
                }

                $pms_dataset_info->save();
                
            }

            $pms_dataset = tblPMSDataset::find($this->pms_dataset_id);
            $pms_dataset->completed_segment+= 1;
            $pms_dataset->save();
            \DB::commit();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            \Log::info('formulate_dataset');
            \Log::info($e->getMessage());
            echo $e->getMessage(), ':line:', $e->getLine();
            $pms_dataset = tblPMSDataset::find($this->pms_dataset_id);
            $pms_dataset->completed_segment+= 1;
            $pms_dataset->save();
        }
    }
    /**
     * @response
     * [
     *  total traffic volume
     *  heavy traffic volume
     *  traffic survey year
     * ]
     */
    private function _calculateTV($pms_sectioning, $tv_data)
    {
        $tv_data = @$tv_data[$pms_sectioning->branch_id];
        if (!$tv_data) return [0, 0, null];
        $tv_data = array_values($tv_data);
        if (count($tv_data) == 0)
        {
            return [0, 0, null];
        }
        else if (count($tv_data) == 1)
        {
            $rec = current($tv_data);
            return [
                $rec['total_traffic_volume' . $pms_sectioning->direction], 
                $rec['heavy_traffic' . $pms_sectioning->direction], 
                substr($rec['survey_time'], 0, 4)
            ];
        }
        else
        {
            $key1 = 0;
            $key2 = 0;
            $b1 = 0;
            $b3 = 0;
            foreach ($tv_data as $index => $t) 
            {
                if (10000*$pms_sectioning->km_from+$pms_sectioning->m_from > $t['milestone'])
                {
                    $key1 = $index+1;
                    $b1 = 10000*$pms_sectioning->km_from+$pms_sectioning->m_from - $t['milestone'];
                }
                if (10000*$pms_sectioning->km_to+$pms_sectioning->m_to > $t['milestone'])
                {
                    $key2 = $index+1;
                    $b3 = intval(@$tv_data[$index+1]['milestone']) - 10000*$pms_sectioning->km_to+$pms_sectioning->m_to;
                }
            }
            if ($key1 == $key2)
            {
                if ($key1 == 0)
                {
                    $rec = array_values($tv_data)[0];
                    return [
                        $rec['total_traffic_volume' . $pms_sectioning->direction], 
                        $rec['heavy_traffic' . $pms_sectioning->direction], 
                        substr($rec['survey_time'], 0, 4)
                    ];
                }
                else if ($key1 == count($tv_data))
                {
                    $rec = array_values($tv_data)[$key1 - 1];
                    return [
                        $rec['total_traffic_volume' . $pms_sectioning->direction], 
                        $rec['heavy_traffic' . $pms_sectioning->direction], 
                        substr($rec['survey_time'], 0, 4)
                    ];
                }
                else
                {
                    $b2 = 100;
                    $Vs = array_values($tv_data)[$key1 - 1]['total_traffic_volume' . $pms_sectioning->direction];
                    $Ve = array_values($tv_data)[$key1]['total_traffic_volume' . $pms_sectioning->direction];
                    $area = ((0.5*$b2* abs($Vs - $Ve) * ( 2*$b1 + $b2 )) / ($b1+$b2+$b3) ) + ($Vs*$b2);
                    $total_traffic_volume = 0.01 * $area;

                    $Vs = array_values($tv_data)[$key1 - 1]['heavy_traffic' . $pms_sectioning->direction];
                    $Ve = array_values($tv_data)[$key1]['heavy_traffic' . $pms_sectioning->direction];    
                    $area = ((0.5*$b2* abs($Vs - $Ve) * ( 2*$b1 + $b2 )) / ($b1+$b2+$b3) ) + ($Vs*$b2);
                    $heavy_traffic = 0.01 * $area;

                    return [
                        $total_traffic_volume,
                        $heavy_traffic,
                        max([
                            substr(array_values($tv_data)[$key1 - 1]['survey_time'], 0, 4),
                            substr(array_values($tv_data)[$key1]['survey_time'], 0, 4)
                        ])
                    ];
                }
            }
            else
            {
                $rec = array_values($tv_data)[$key1];
                return [
                    $rec['total_traffic_volume' . $pms_sectioning->direction], 
                    $rec['heavy_traffic' . $pms_sectioning->direction], 
                    substr($rec['survey_time'], 0, 4)
                ];
            }
        }
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
            if ($d->{$index_key} < 0) continue;
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

    // private function _recalculatePCValues($data)
    // {
    //     $dataset = [];
    //     if ($data->count() == 1)
    //     {
    //         $dataset['cracking'] = $data->first()->cracking;
    //         $dataset['patching'] = $data->first()->patching;
    //         $dataset['pothole'] = $data->first()->pothole;
    //         $dataset['cracking_ratio'] = $data->first()->cracking_ratio;
    //         $dataset['rutting_max'] = $data->first()->rutting_max;
    //         $dataset['rutting_ave'] = $data->first()->rutting_ave;
    //         $dataset['IRI'] = $data->first()->IRI;
    //         $dataset['MCI'] = $data->first()->MCI;
    //         $dataset['section_length'] = $data->first()->section_length;
    //         $dataset['geographical_area'] = $data->first()->geographical_area;
    //         $dataset['crossing_type_code'] = $data->first()->intersection;
    //         $dataset['structure_type_code'] = $data->first()->structure;
    //         $dataset['pavement_type'] = $data->first()->pavement_type;
    //         $dataset['pavement_type_code'] = $data->first()->pavement_type_code;
    //         $dataset['analysis_area'] = $data->first()->analysis_area;
    //         $dataset['number_of_lane'] = $data->first()->number_of_lane;
    //     }
    //     else
    //     {
    //         $ac_length = 0;
    //         $cc_length = 0;
    //         $bst_length = 0;
    //         $this->_calculatePavementTypeLength($data, $ac_length, $bst_length, $cc_length);

    //         $dataset['section_length'] = $ac_length + $bst_length + $cc_length;
    //         $dataset['geographical_area'] = $data->first()->geographical_area;
    //         $dataset['crossing_type_code'] = $data->first()->intersection;
    //         $dataset['structure_type_code'] = $data->first()->structure;
    //         $dataset['number_of_lane'] = $data->first()->number_of_lane;
    //         $dataset['analysis_area'] = array_sum($data->pluck('analysis_area')->toArray());

    //         if ($ac_length + $bst_length + $cc_length == 0)
    //         {
    //             return null;
    //         }

    //         if (($ac_length + $bst_length)/($ac_length + $bst_length + $cc_length) >= 0.5 && $data->first()->pavement_type != 'CC')
    //         {
    //             // pavement type for 100m is AC or BST
    //             $dataset['cracking'] = $this->_calculateIndexes($data, 'cracking', 1, 1);
    //             $dataset['patching'] = $this->_calculateIndexes($data, 'patching', 1, 1);
    //             $dataset['pothole'] = $this->_calculateIndexes($data, 'pothole', 1, 1);
    //             $dataset['cracking_ratio'] = $this->_calculateIndexes($data, 'cracking_ratio', 1, 1);
    //             $dataset['rutting_max'] = $this->_calculateIndexes($data, 'rutting_max', 2);
    //             $dataset['rutting_ave'] = $this->_calculateIndexes($data, 'rutting_ave', 2);
    //             $dataset['IRI'] = $this->_calculateIndexes($data, 'IRI', 2);
    //             $dataset['MCI'] = Helper::getMCI($dataset['cracking_ratio'], $dataset['rutting_ave'], $dataset['IRI'], 'AC');
    //             if ($ac_length >= $bst_length)
    //             {
    //                 // pavement type is AC
    //                 $dataset['pavement_type'] = 'AC';
    //                 $dataset['pavement_type_code'] = 1;
    //             }
    //             else
    //             {
    //                 // pavement type is BST
    //                 $dataset['pavement_type'] = 'BST';
    //                 $dataset['pavement_type_code'] = 2;
    //             }
    //         }
    //         else if ($cc_length/($ac_length + $bst_length + $cc_length) >= 0.5)
    //         {
    //             // pavement type for 100 is CC
    //             $dataset['cracking'] = $this->_calculateIndexes($data, 'cracking', 1, 2);
    //             $dataset['patching'] = $this->_calculateIndexes($data, 'patching', 1, 2);
    //             $dataset['pothole'] = $this->_calculateIndexes($data, 'pothole', 1, 2);
    //             $dataset['cracking_ratio'] = $this->_calculateIndexes($data, 'cracking_ratio', 1, 2);
    //             $dataset['rutting_max'] = $this->_calculateIndexes($data, 'rutting_max', 2);
    //             $dataset['rutting_ave'] = $this->_calculateIndexes($data, 'rutting_ave', 2);
    //             $dataset['IRI'] = $this->_calculateIndexes($data, 'IRI', 2);
    //             $dataset['MCI'] = Helper::getMCI($dataset['cracking_ratio'], $dataset['rutting_ave'], $dataset['IRI'], 'CC');
    //             $dataset['pavement_type'] = 'CC';
    //             $dataset['pavement_type_code'] = 3;
    //         }
    //         else
    //         {
    //             $dataset = null;
    //         }
    //     }
    //     return $dataset;
    // }

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
