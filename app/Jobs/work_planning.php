<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\tblBudgetSimulation;
use App\Models\tblWorkPlanning;
use App\Models\tblWorkPlanningOrganization;
use App\Models\tblDeterioration;
use App\Models\tblRepairMatrixCell;
use App\Models\tblRepairMatrixCellValue;
use App\Models\tblPlannedSection;

class work_planning implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $session_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session_id)
    {
        $this->session_id = $session_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	try
        {
            $this->_mergeMatrix();
            // flag
            $rec = tblWorkPlanning::findOrFail($this->session_id);
            $this->_increaseProgress($rec);
        }
        catch (\Exception $e)
        {
            \Log::info('matrix');
            echo $e->getMessage();
        }
    }

    private function _getRepairMethods($rec)
    {
        // $repair_methods = \App\Models\mstRepairMethod::with([
        //         'costs' => function($q) use($rec){
        //             $q->whereIn('organization_id', $rec->organizations()->get()->pluck('id')->toArray());
        //         },
        //         'unit'
        //     ])
        //     ->get();
        $repair_methods = \App\Models\mstRepairMethod::with([
                'costs' => function($q){
                    $q->orderBy('organization_id', 'ASC');
                },
                'unit'
            ])
            ->get();
        $data_repair_method = [];
        foreach ($repair_methods as $key => $method)
        {
            if ($method->costs->count() == 0)
            {
                continue;
            }
            // $data_repair_method[$method->id] = array(
            //     'cost' => 1000 * round(array_sum($method->costs->pluck('cost')->toArray())/$method->costs->count()),
            //     'unit' => $method->unit->code_id,
            // );
            $data_repair_method[$method->id] = array(
                'cost_1' => @$method->costs[0]->cost * 1000,
                'cost_2' => @$method->costs[1]->cost * 1000,
                'cost_3' => @$method->costs[2]->cost * 1000,
                'cost_4' => @$method->costs[3]->cost * 1000,
                'unit' => $method->unit->code_id,
            );
        }
        return $data_repair_method;
    }

    private function _getMatrixMethod($work_planning)
    {
        $repair_methods = $this->_getRepairMethods($work_planning);

        $data;
        $latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
            ->where('user_id', $work_planning->created_by)
            ->where('target_type', 2)
            ->count();
        if ($latest_matrix_chk > 0)
        {
            $data = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
                ->where('user_id', $work_planning->created_by)
                ->where('target_type', 2);
        }
        else
        {
            $data = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
                ->whereNull('user_id');
        }
                                                
        $info_matrix = $data->with([
                'crackValue',
                'rutValue',
                'repairMethodValue',
                'roadTypeValue',
                'roadClassValue',
                'surfaceValue'
            ])
            ->get();

        $repair_matrix_structure = [];
        foreach ($info_matrix as $i) 
        {
            $key = implode('-', [
                $i->roadTypeValue->value,
                $i->roadClassValue->value,
                $i->surfaceValue->value,
                $i->row, 
                $i->column
            ]);
            // $repair_matrix_structure[$key] = [
            //     'id' => $i->repairMethodValue->value,
            //     'cost' => @$repair_methods[$i->repairMethodValue->value]['cost'],
            //     'unit' => @$repair_methods[$i->repairMethodValue->value]['unit']
            // ];
            $repair_matrix_structure[$key] = [
                'id' => $i->repairMethodValue->value,
                'cost_1' => @$repair_methods[$i->repairMethodValue->value]['cost_1'],
                'cost_2' => @$repair_methods[$i->repairMethodValue->value]['cost_2'],
                'cost_3' => @$repair_methods[$i->repairMethodValue->value]['cost_3'],
                'cost_4' => @$repair_methods[$i->repairMethodValue->value]['cost_4'],
                'unit' => @$repair_methods[$i->repairMethodValue->value]['unit']
            ];
        }
        return $repair_matrix_structure;
    }

    private function _readInputSource($work_planning)
    {
        $source = public_path("application/process/work_planning/" . $work_planning->id . "/data/input_forecast.csv");
        $input = array();

        Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use(&$input) {
            $array_cake = explode(',', $chunk);
            for ($i = 1; $i <= count($array_cake) - 1; $i++)
            {
                $tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $array_cake[$i]);
                $array_cake[$i] = trim($tmp, '"');
            }
            if (count($array_cake) > 5)
            {
                $input[] = $array_cake;
            }
        }, 99999999);
        return $input;
    }

    private function _increaseProgress($rec)
    {
        \DB::transaction(function() use($rec) {
            $rec->matrix_flg += 1;
            $rec->save();
        });
    }

    private function _mergeMatrix()
    {
        $work_planning = tblWorkPlanning::find($this->session_id);
        $planned_section = tblPlannedSection::where('planned_year', '>=', $work_planning->base_planning_year)->where('planned_year', '<=', $work_planning->base_planning_year + 4)->get();
        $planned_section_id = [];
        foreach ($planned_section as $item)
        {
            $planned_section_id[] = $item->section_id; 
        }
        // convert matrix method into array
        $matrix_method = $this->_getMatrixMethod($work_planning);

        $this->_increaseProgress($work_planning);
        $input = $this->_readInputSource($work_planning);
        $this->_increaseProgress($work_planning);
        $crack_rank = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get()->pluck('rank', 'from')->toArray();
        $rut_rank = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get()->pluck('rank', 'from')->toArray();
        $organizations = \App\Models\tblOrganization::where('level', 3)->get();
        $sb_parent = [];
        foreach ($organizations as $item)
        {
            $sb_parent[$item->id] = @$item->rmb()->first()->id;
        }
        $open = fopen(public_path("application/process/work_planning/" . $work_planning->id . "/data/input_method.csv"), 'w+');
        foreach ($input as $index => $tmp) 
        {
            $method = [];
            $weight = [];
            $price = [];
            $error = $tmp[0];
            if ($error != 0)
            {
                // $new_data[] = $input[$index];
                fputcsv($open, $input[$index]);
                continue;
            }

            $section_id2 = $tmp[1];
            $road_class = $tmp[2];
            $surface = $tmp[22];
            
            $surface_key = $this->_convertSurface($surface);
            $road_category = substr($section_id2, 2, 1);
            // echo '---\\n';
            // echo 'section id: ', $section_id2, '\\n';

            for ($i = 32; $i <= 52; $i+= 5)
            {
                $row = \Helper::vlookup($tmp[$i], $crack_rank) - 1;
                if ($surface_key == 3)
                {
                    $col = 0;
                }
                else
                {
                    $col = \Helper::vlookup($tmp[$i + 1], $rut_rank) - 1;
                }
                
                // echo $i, '\\n';
                // echo 'rut: ', $tmp[$i + 1], ', crack: ', $tmp[$i], '\\n';
                // echo 'method-rut: ', $col, ', method-crack: ', $row, '\\n';
                $key = implode('-', [
                    $road_category,
                    $road_class,
                    $surface_key,
                    $row, 
                    $col
                ]);
                // echo 'key: ' , $key, '\\n';
                if (isset($matrix_method[$key]))
                {
                    $method[] = $matrix_method[$key]['id'];
                    if ($matrix_method[$key]['unit'] == 0)
                    {
                        // m
                        $weight[] = $tmp[8];
                        $price[] = $tmp[8] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
                    }
                    else if ($matrix_method[$key]['unit'] == 1)
                    {
                        // m2
                        if ($matrix_method[$key]['id'] == 1001 || $matrix_method[$key]['id'] == 1002 || $matrix_method[$key]['id'] == 1003)
                        {
                            $weight[] = $tmp[8] * 1.6;
                            $price[] = $tmp[8] * 1.6 * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
                        }
                        else
                        {
                            $weight[] = $tmp[8] * $tmp[12];
                            $price[] = $tmp[8] * $tmp[12] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
                        }
                    }
                    else
                    {
                        $weight[] = $tmp[8] * $tmp[12] * $tmp[13];
                        $price[] = $tmp[8] * $tmp[12] * $tmp[13] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
                    }
                }
                else
                {
                    $method[] = '0';
                    $weight[] = '0';
                    $price[] = '0';
                }
            }
            $road_category = substr($tmp[1], 2, 1);
            $road_number = substr($tmp[1], 3, 3);
            $road_number_supplement = substr($tmp[1], 6, 3);
            $branch_number = substr($tmp[1], 9, 2);
            $direction = substr($tmp[1], 11, 1);
            $lane_pos_number = $tmp[21];
            //$number_of_lanes = substr($item['section_id'], 11, 1);
            $km_from = sprintf("%04d", $tmp[4]); 
            $m_from = sprintf("%05d", $tmp[5]);
            $section_id = $road_category . $road_number . $road_number_supplement . $branch_number . $direction . $lane_pos_number. '_' .$km_from . $m_from;            

            $planned = [];
            if (in_array($section_id, $planned_section_id))
            {
                $planned[] = '6';
                fputcsv($open, array_merge($input[$index], $method, $weight, $price, $planned));
            }
            else
            {
                fputcsv($open, array_merge($input[$index], $method, $weight, $price));
            }
            
        }
        $this->_increaseProgress($work_planning);
        
        fclose($open);
    }

    private function _convertSurface($pt)
    {
        $convert_surface = ['AC' => 1, 'BST' => 2, 'CC' => 3];
        return isset($convert_surface[$pt]) ? $convert_surface[$pt] : 0;
    }
}
