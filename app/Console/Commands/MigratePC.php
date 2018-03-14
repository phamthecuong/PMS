<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PavementConditionTable;
use App\Models\ImageTable;
use App\Models\tblSectionPC;
use App\Models\tblSectionPCHistory;
use App\Models\tblPCPoint;
use App\Models\tblPCPointHistory;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use DB;

class MigratePC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:migrate_pc {process_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate PC Excel Into DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $sbs = [];
    protected $branches = [];

    public function __construct()
    {
        parent::__construct();
        $records = tblOrganization::where('level', 3)->get();
        foreach ($records as $r) 
        {
            $this->sbs[$r->name_en] = $r->id;
        }

        $records = tblBranch::get();
        foreach ($records as $r) 
        {
            $this->branches[intval($r->road_number)][intval($r->branch_number)][intval($r->road_number_supplement)][intval($r->road_category)] = $r->id;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $per_time = 1000;
            while (1)
            {
                $records = PavementConditionTable::where('process_id', $this->argument('process_id'))
                    ->take($per_time)
                    ->get();

                if ($records->count() > 0)
                {
                    foreach ($records as $rec)
                    {
                        // $timeStart = microtime(true);

                        $rec->section_id = str_replace('"', '', $rec->section_id);

                        $this->_migrateSection($rec);

                        // echo 'total: ', (microtime(true) - $timeStart),'.';
                        // dd('end');
                    }
                }
                else
                {
                    break;
                }
            }
            echo 'end';
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    private function _migrateSection($pc)
    {
        DB::transaction(function () use ($pc) {
            if ($pc->kp_from >= 0 && $pc->m_from >= 0 && $pc->kp_to >= 0 && $pc->m_to >= 0)
            {
                // $timeStart = microtime(true);
                $section = tblSectionPC::where('section_code', $pc->section_id)->first();
                // echo 'find in section pc: ', (microtime(true) - $timeStart),'.';
                if ($section)
                {
                    // $timeStart = microtime(true);
                    // check if history existed
                    $history_rec = tblSectionPCHistory::where('section_id', $section->id)
                        ->where('date_y', $pc->survey_year)
                        ->where('date_m', $pc->survey_month)
                        // ->whereRaw("(date_y > '" . $pc->survey_year . "' or (date_y = '" . $pc->survey_year . "' and date_m > '" . $pc->survey_month . "'))")
                        ->get();
                    // echo 'find in section pc history: ', (microtime(true) - $timeStart),'.';
                    if ($history_rec->count() > 0)
                    {
                        // do update here in the future
                    }
                    else
                    {
                        if ($section->date_y < $pc->survey_year || ($section->date_y == $pc->survey_year && $section->date_m < $pc->survey_month))
                        {
                            // new history record
                            // $timeStart = microtime(true);
                            $this->_updateSectionInformation($section, $pc);
                            // echo 'save in section pc: ', (microtime(true) - $timeStart),'.';

                            // $timeStart = microtime(true);
                            $section = $this->_applyPoints($section, $pc);
                            // echo 'update point: ', (microtime(true) - $timeStart),'.';

                            // $timeStart = microtime(true);
                            $this->_createHistory($section);
                            // echo 'save history: ', (microtime(true) - $timeStart),'.';
                        }
                    }
                }
                else
                {
                    // create new one
                    // $timeStart = microtime(true);
                    $section = $this->_createSectionInformation($pc);
                    // echo 'save in section pc: ', (microtime(true) - $timeStart),'.';

                    // $timeStart = microtime(true);
                    $this->_applyPoints($section, $pc);
                    // echo 'update point: ', (microtime(true) - $timeStart),'.';

                    // $timeStart = microtime(true);
                    $this->_createHistory($section);
                    // echo 'save history: ', (microtime(true) - $timeStart),'.';
                }
            }
            $pc->delete();
        });
    }

    private function _createSectionInformation($pc)
    {
        $section = new tblSectionPC();
        $section->section_code = $pc->section_id;
        $section->km_from = $pc->kp_from;
        $section->m_to = $pc->m_to;
        $section->km_to = $pc->kp_to;
        $section->m_from = $pc->m_from;
        $section->lane_position_no = $pc->survey_lane;
        return $this->_updateSectionInformation($section, $pc);
    }    

    private function _updateSectionInformation($section, $pc)
    {
        $sb_id = $this->_getSBId($pc->sb);
        $branch_id = $this->_getBranchId($pc->route_number, $pc->branch_number, $pc->road_number_supplement, $pc->road_category);
        
        $section->geographical_area = $pc->geographical_area;
        $section->road_category = $pc->road_category;
        $section->section_length = $pc->section_length;
        $section->analysis_area = $pc->analysis_area;
        $section->structure = $pc->structure;
        $section->intersection = $pc->intersection;
        $section->overlapping = $pc->overlapping;
        $section->direction = $pc->direction;
        $section->surface_type = $pc->surface_type;
        $section->date_y = $pc->survey_year;
        $section->date_m = $pc->survey_month;
        $section->cracking_ratio_cracking = $pc->cracking;
        $section->cracking_ratio_patching = $pc->patching;
        $section->cracking_ratio_pothole = $pc->pothole;
        $section->cracking_ratio_total = $pc->cracking_ratio;
        $section->rutting_depth_max = $pc->rutting_max;
        $section->rutting_depth_ave = $pc->rutting_average;
        $section->IRI = $pc->iri;
        $section->MCI = $pc->mci;
        $section->note = $pc->note;
        $section->SB_id = $sb_id;
        $section->branch_id = $branch_id;
        $section->number_of_lane_U = $pc->number_of_lane_u;
        $section->number_of_lane_D = $pc->number_of_lane_d;
        $section->save();
        return $section;
    }

    private function _getSBId($sb)
    {
        if (($tmp = @$this->sbs[$sb]) !== null)
        {
            return $tmp;
        }
        return 0;
    }

    private function _getBranchId($route_number, $branch_number, $road_number_supplement, $road_category)
    {
        if (($tmp = @$this->branches[intval($route_number)][intval($branch_number)][intval($road_number_supplement)][intval($road_category)]) !== null)
        {
            return $tmp;
        }
        return 0;
    }

    private function _applyPoints($section, $pc)
    {
        
        tblPCPoint::where('section_id', $section->id)->delete();

        // $timeStart = microtime(true);
        $images = ImageTable::where('process_id', $pc->process_id)
            ->where('section_id', $pc->section_id)
            ->get();
        // echo 'get images: ', (microtime(true) - $timeStart),'.';

        $min_lat = 1000;
        $max_lat = 0;
        $min_lng = 1000;
        $max_lng = 0;
        $points = [];

        // $timeStart = microtime(true);
        foreach ($images as $i) 
        {
            if ($i->latitude < $min_lat) $min_lat = $i->latitude;
            if ($i->latitude > $max_lat) $max_lat = $i->latitude;
            if ($i->longitude < $min_lng) $min_lng = $i->longitude;
            if ($i->longitude > $max_lng) $max_lng = $i->longitude;
            $points[] = [
                'latitude' => $i->latitude,
                'longitude' => $i->longitude,
                'image_path' => str_replace('"', '', str_replace('\\', '/', $i->image_path)),
            ];

            $rec = new tblPCPoint();
            $rec->name = $i->image_id;
            $rec->lat = $i->latitude;
            $rec->lng = $i->longitude;
            $rec->height = $i->height;
            $rec->order_id = $i->image_id;
            $rec->image_path = str_replace('"', '', str_replace('\\', '/', $i->image_path));
            $rec->section_id = $section->id;
            $rec->save();
        }
        $section->min_lat = $min_lat;
        $section->min_lng = $min_lng;
        $section->max_lat = $max_lat;
        $section->max_lng = $max_lng;
        $section->points = json_encode($points);
        $section->save();

        // echo 'save images: ', (microtime(true) - $timeStart),'.';

        // $timeStart = microtime(true);
        $images = ImageTable::where('process_id', $pc->process_id)
            ->where('section_id', $pc->section_id)
            ->delete();
        // echo 'remove images: ', (microtime(true) - $timeStart),'.';

        return $section;
    }

    private function _createHistory($section)
    {
        $rec = new tblSectionPCHistory();
        $rec->section_id = $section->id;
        $rec->section_code = $section->section_code;
        $rec->geographical_area = $section->geographical_area;
        $rec->road_category = $section->road_category;
        $rec->km_from = $section->km_from;
        $rec->m_to = $section->m_to;
        $rec->km_to = $section->km_to;
        $rec->m_from = $section->m_from;
        $rec->section_length = $section->section_length;
        $rec->analysis_area = $section->analysis_area;
        $rec->structure = $section->structure;
        $rec->intersection = $section->intersection;
        $rec->overlapping = $section->overlapping;
        $rec->direction = $section->direction;
        $rec->surface_type = $section->surface_type;
        $rec->date_y = $section->date_y;
        $rec->date_m = $section->date_m;
        $rec->cracking_ratio_cracking = $section->cracking_ratio_cracking;
        $rec->cracking_ratio_patching = $section->cracking_ratio_patching;
        $rec->cracking_ratio_pothole = $section->cracking_ratio_pothole;
        $rec->cracking_ratio_total = $section->cracking_ratio_total;
        $rec->rutting_depth_max = $section->rutting_depth_max;
        $rec->rutting_depth_ave = $section->rutting_depth_ave;
        $rec->IRI = $section->IRI;
        $rec->MCI = $section->MCI;
        $rec->note = $section->note;
        $rec->SB_id = $section->SB_id;
        $rec->branch_id = $section->branch_id;
        $rec->lane_position_no = $section->lane_position_no;
        $rec->number_of_lane_U = $section->number_of_lane_U;
        $rec->number_of_lane_D = $section->number_of_lane_D;
        $rec->min_lat = $section->min_lat;
        $rec->max_lat = $section->max_lat;
        $rec->min_lng = $section->min_lng;
        $rec->max_lng = $section->max_lng;
        $rec->points = $section->points;
        $rec->save();
        $images = tblPCPoint::where('section_id', $section->id)->get();
        
        foreach ($images as $i) 
        {
            $rec_his = new tblPCPointHistory();
            $rec_his->name = $i->name;
            $rec_his->lat = $i->lat;
            $rec_his->lng = $i->lng;
            $rec_his->height = $i->height;
            $rec_his->order_id = $i->order_id;
            $rec_his->image_path = $i->image_path;
            $rec_his->section_history_id = $rec->id;
            $rec_his->save();
        }
    }
}
