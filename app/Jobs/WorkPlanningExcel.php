<?php

namespace App\Jobs;

use App\Classes\Helper;
use App\Models\tblBranch;
use App\Models\mstRoadCategory;
use App\Models\mstRoadClass;
use App\Models\tblOrganization;
use App\Models\tblRCategory;
use App\Models\tblRClassification;
use App\Models\User;
use App\Models\tblWorkPlanning;
use App\Models\tblWorkPlanningOrganization;
use Carbon\Carbon;
use App\Models\mstSurface;
use App\Models\tblRepairMethodCost;
use App\Models\mstRepairMethod;
use App\Models\mstMethodUnit;
use App\Models\tblPlannedSection;
class WorkPlanningExcel 
{
    function _getCsvData($session_id, $type, $list, $language)
    {   
        $records = tblBranch::all();
        foreach ($records as $r)
        {
            $branches[intval($r->road_number)][intval($r->branch_number)][intval($r->road_number_supplement)][intval($r->road_category)] = $r->{"name_{$language}"};
        }

        $records = mstRoadClass::all();
        foreach ($records as $r)
        {
            $road_classes[$r->code_id] = $r->{"name_{$language}"};
        }

        $records = tblRCategory::get();
        foreach ($records as $r) 
        {
            $repair_categories[$r->id] = $r->code;
        }

        $records = tblRClassification::get();
        foreach ($records as $r) 
        {
            $repair_classifications[$r->id] = $r->code;
        }

        $records = tblOrganization::where('level', 3)->get();
        foreach ($records as $r)
        {
            $sb[$r->id] = $r->{"name_{$language}"};
            $rmb[$r->id][] = @$r->parent_id;
            $rmb[$r->id][] = @$r->rmb()->first()->{"name_{$language}"};
        }

        $records = tblPlannedSection::all();
        $planned_section = [];
        foreach ($records as $r)
        {
            $planned_section[$r->section_id]['planned_year'] = $r->planned_year;
            $planned_section[$r->section_id]['repair_method'] = $r->{"repair_method_{$language}"};
            $planned_section[$r->section_id]['repair_classification'] = $r->{"repair_classification_{$language}"};
            $planned_section[$r->section_id]['unit_cost'] = $r->repair_cost/$r->repair_quantity;
            $planned_section[$r->section_id]['quantity'] = $r->repair_quantity;
            $planned_section[$r->section_id]['amount'] = $r->repair_cost;
        }
        
        $input_file = 'input';
        if ($type == 2)
        {
            $input_file = 'input_forecast';
        }
        else if ($type == 11)
        {
            $input_file = 'input_method';
        }
        else if (in_array($type, [3, 4, 5, 6, 7, 8, 9, 10]))
        {
            if ($list == 0)
            {
                $input_file = 'input_final';
            }
            else if ($list == 1)
            {
                $input_file = 'input_proposal';
            }
            else 
            {
                $input_file = 'input_planned';
            }
            
        }

        $unit = [];
        $method_final = [];
        if (in_array($type, [3, 4, 5, 6, 7, 8, 9, 10]))
        {
            $records = mstMethodUnit::get();
            foreach ($records as $r) 
            {
                $unit[$r->id] = $r->code_name;
            }
            $method_final = $this->_getMethodList($session_id, $language);
        }
        $source = public_path("application/process/work_planning/" . $session_id . "/data/" . $input_file . ".csv");
        $dataset = array();
        $key = -1;

        Helper::getRowDataChunk($source, 5000, function($chunk, &$handle, $iteration) use(&$dataset, &$key, $branches, $road_classes, $repair_categories, $repair_classifications, $type, $unit, $method_final, $rmb, $sb, $planned_section) {
            $i = explode(',', $chunk);
            
            $key++;
            for ($j = 1; $j <= count($i) - 1; $j++)
            {
                $tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $i[$j]);
                $i[$j] = trim($tmp, '"');
            }

            if (count($i) < 5)
            {
                return false;
            }
            if (
                (in_array($type, [2, 3, 4, 5, 6, 7, 8, 9, 10]) && $i[0] != 0) ||
                ($type == 1 && $i[0] == 0)
            )
            {   
                return false;
            }
            
            $road_category = substr($i[1], 2, 1);
            $road_number = substr($i[1], 3, 3);
            $road_number_supplement = substr($i[1], 6, 3);
            $branch_number = substr($i[1], 9, 2);
            $direction_no = substr($i[1], 11, 1);
            $lane_pos_number = $i[21];
            $km_from = sprintf("%04d", $i[4]); 
            $m_from = sprintf("%05d", $i[5]);
            $section_id = $road_category . $road_number . $road_number_supplement . $branch_number . $direction_no . $lane_pos_number. '_' .$km_from . $m_from;
            $route_name = $branches[intval($road_number)][intval($branch_number)][intval($road_number_supplement)][intval($road_category)];
            $direction = "";

            if ($i[10] == 1)
            {
                $direction = trans('back_end.left');
            } 
            else if ($i[10] == 2)
            {
                $direction = trans('back_end.right');    
            }
            else if ($i[10] == 3)
            {
                $direction = trans('back_end.single');
            }
            if ($type == 11)
            {
                if(!isset($i[71]) || (isset($i[71]) && $i[71] != 6)) return;
                $tmp = [
                    'section_id' => $i[1],        
                    'route_name' => $route_name,
                    'branch_number' => $branch_number,
                    'rmb' => @$rmb[$i[11]][1],
                    'sb' => @$sb[$i[11]],
                    'km_from' => $i[4],
                    'm_from' => $i[5],
                    'km_to' => $i[6],
                    'm_to' => $i[7],
                    'section_length' => $i[8],
                    'direction' => $direction,
                    'survey_lane' => $i[21],
                    'planned_year' => $planned_section[$section_id]['planned_year'],
                    'repair_method' => $planned_section[$section_id]['repair_method'],
                    'repair_classification' => $planned_section[$section_id]['repair_classification'],
                    'unit_cost' => round($planned_section[$section_id]['unit_cost']),
                    'quantity' => $planned_section[$section_id]['quantity'],
                    'amount' => $planned_section[$section_id]['amount']
                ];
            }
            else
            {
                $tmp = [
                    'section_id' => $i[1],
                    'route_no' => $road_number,             
                    'route_name' => $route_name,
                    'branch_number' => $branch_number,
                    'rmb' => @$rmb[$i[11]][1],
                    'sb' => @$sb[$i[11]],
                    'road_class' => @$road_classes[$i[2]],
                    'construction_year' => $i[3],
                    'km_from' => $i[4],
                    'm_from' => $i[5],
                    'km_to' => $i[6],
                    'm_to' => $i[7],
                    'direction' => $direction,
                    'survey_lane' => $i[21],
                    'section_length' => $i[8],
                    'number_of_lanes' => $i[9],
                    'pavement_type' => $i[22],
                    'lane_width' => $i[12],
                    'pavement_thickness' => $i[13],
                    'latest_repair_time' => $i[14],
                    'repair_category' => @$repair_categories[$i[15]],   
                    'repair_classification' => @$repair_classifications[$i[16]],    
                    'traffic_survey_year' => $i[17],
                    'total_traffic_volume' => $i[18],
                    'heavy_traffic' => $i[19],
                    'pc_survey_time' => $i[20],
                    'pc_pavement_type' => $i[22],
                    'cracking' => $i[23],
                    'patching' => $i[24],
                    'pothole' => $i[25],
                    'cracking_ratio' => $i[26],
                    'rut_max' => $i[27],
                    'rut_avg' => $i[28],
                    'iri' => $i[29],
                    'mci' => $i[30],
                ];
                if ($type == 1)
                {
                    switch ($i[0])
                    {
                        case "1":
                            $error = trans("wp.road_class_invalid");
                            break;
                        case "2":
                            $error = trans("wp.cracking_invalid");
                            break;
                        case "3":
                            $error = trans("wp.rutting_invalid");
                            break;
                        case "4":
                            $error = trans("wp.iri_invalid");
                            break;
                        case "5":
                            $error = trans("wp.pavement_type_invalid");
                            break;
                        default:
                            $error = "";
                            break;
                    }
                    $tmp+= [
                        'error' => $error
                    ];
                }
                else if ($type == 2)
                {
                    for ($j = 31; $j <= 55; $j++)
                    {
                        $tmp+= [
                            $j => $i[$j]
                        ];
                    }
                }
                else if (in_array($type, [3, 9]))
                {
                    
                    if ($type == 9 && isset($i[71])) return;
                    $j = 31;
                    for ($index = 0; $index < 5; $index ++)
                    {
                        $max_j = $j + 4;
                        for ($j; $j <= $max_j; $j++)
                        {
                            $tmp+= [
                                $j => $i[$j]
                            ];
                        }
                        if (!empty($i[61 + $index]) && !empty($i[66 + $index]))
                        {
                            $unit_cost = trim($i[66 + $index])/(trim($i[61 + $index]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[56 + $index]][$rmb[$i[11]][0] - 1] / 1000;
                        }
                        
                        $tmp+= [
                            'selected_repair_method_'. $index => @$method_final[(string)$i[56 + $index]][5],
                            'selected_repair_classification_'. $index => @$method_final[(string)$i[56 + $index]][6],
                            'unit_cost_'.$index => round($unit_cost),
                            'selected_quantity_unit_'. $index => $i[61 + $index],
                            'selected_unit_quantity_'. $index => @$unit[$method_final[(string)$i[56 + $index]][7]],
                            'amount_'. $index => (!empty($i[66 + $index])) ? round((trim($i[66 + $index])*0.001)) : '0'
                        ];

                    }
                }
                else if (in_array($type, [4, 5, 6, 7, 8]))
                {
                    if (!isset($i[71])) return;
                    if ($type == 4 && isset($i[71]) && $i[71] != 1)
                    {
                        return;
                    }
                    if ($type == 5 && isset($i[71]) && $i[71] != 2)
                    {
                        return;
                    }
                    if ($type == 6 && isset($i[71]) && $i[71] != 3)
                    {
                        return;
                    }
                    if ($type == 7 && isset($i[71]) && $i[71] != 4)
                    {
                        return;
                    }
                    if ($type == 8 && isset($i[71]) && $i[71] != 5)
                    {
                        return;
                    }

                    // for ($j = 31; $j <= 71; $j++)
                    // {
                    //     $tmp+= [
                    //         $j => $i[$j]
                    //     ];
                    // }
                    // add mehtod info
                    switch ($type)
                    {
                        case 4:
                            for ($j = 31; $j <= 35; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[56]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[56]][6],
                                'unit_cost' => $i[66]/($i[61] * 1000),
                                'selected_quantity_unit' => $i[61],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[56]][7]],
                                'amount' => $i[66]*0.001
                            ];
                            break;
                        case 5:
                            for ($j = 36; $j <= 40; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[57]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[57]][6],
                                'unit_cost' => round($i[67]/($i[62] * 1000)),
                                'selected_quantity_unit' => $i[62],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[57]][7]],
                                'amount' => round($i[67]*0.001)
                            ];
                            break;
                        case 6:
                            for ($j = 41; $j <= 45; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[58]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[58]][6],
                                'unit_cost' => round($i[68]/($i[63] * 1000)),
                                'selected_quantity_unit' => $i[63],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[58]][7]],
                                'amount' => round($i[68]*0.001)
                            ];
                            break;
                        case 7:
                            for ($j = 46; $j <= 50; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[59]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[59]][6],
                                'unit_cost' => round($i[69]/($i[64] * 1000)),
                                'selected_quantity_unit' => $i[64],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[59]][7]],
                                'amount' => round($i[69]*0.001)
                            ];
                            break;
                        case 8:
                            for ($j = 51; $j <= 55; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[60]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[60]][6],
                                'unit_cost' => round($i[70]/($i[65] * 1000)),
                                'selected_quantity_unit' => $i[65],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[60]][7]],
                                'amount' => round($i[70]*0.001)
                            ];
                            break;
                        default:
                            break;
                    }
                    
                }
                // else if ($type == 9)
                // {
                //     if (isset($i[71])) return;
                //     for ($j = 31; $j < 71; $j++)
                //     {
                //         $tmp+= [
                //             $j => $i[$j]
                //         ];
                //     }
                // }
                else if ($type == 10)
                {
                    if (!isset($i[71])) return;
                    switch ($i[71])
                    {
                        case 1:
                            for ($j = 31; $j <= 35; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            if (!empty($i[61]) && !empty($i[66]))
                            {
                                $unit_cost = trim($i[66])/(trim($i[61]) * 1000);
                            }
                            else
                            {
                                $unit_cost = @$method_final[(string)$i[56]][$rmb[$i[11]][0] - 1] / 1000;
                            }
                           
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[56]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[56]][6],
                                'unit_cost' => round($unit_cost),
                                'selected_quantity_unit' => $i[61],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[56]][7]],
                                'amount' => (!empty($i[66])) ? round((trim($i[66])*0.001)) : '0'
                            ];
                            break;
                        case 2:
                            for ($j = 36; $j <= 40; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            if (!empty($i[62]) && !empty($i[67]))
                            {
                                $unit_cost = trim($i[67])/(trim($i[62]) * 1000);
                            }
                            else
                            {
                                $unit_cost = @$method_final[(string)$i[57]][$rmb[$i[11]][0] - 1] / 1000;
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[57]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[57]][6],
                                'unit_cost' => round($unit_cost),
                                'selected_quantity_unit' => $i[62],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[57]][7]],
                                'amount' => (!empty($i[67])) ? round((trim($i[67])*0.001)) : '0'
                            ];
                            break;
                        case 3:
                            for ($j = 41; $j <= 45; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            if (!empty($i[63]) && !empty($i[68]))
                            {
                                $unit_cost = trim($i[68])/(trim($i[63]) * 1000);
                            }
                            else
                            {
                                $unit_cost = @$method_final[(string)$i[58]][$rmb[$i[11]][0] - 1] / 1000;
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[58]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[58]][6],
                                'unit_cost' => round($unit_cost),
                                'selected_quantity_unit' => $i[63],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[58]][7]],
                                'amount' => (!empty($i[68])) ? round((trim($i[68])*0.001)) : '0'
                            ];
                            break;
                        case 4:
                            for ($j = 46; $j <= 50; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            if (!empty($i[64]) && !empty($i[69]))
                            {
                                $unit_cost = trim($i[69])/(trim($i[64]) * 1000);
                            }
                            else
                            {
                                $unit_cost = @$method_final[(string)$i[59]][$rmb[$i[11]][0] - 1] / 1000;
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[59]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[59]][6],
                                'unit_cost' => round($unit_cost),
                                'selected_quantity_unit' => $i[64],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[59]][7]],
                                'amount' => (!empty($i[69])) ? round((trim($i[69])*0.001)) : '0'
                            ];
                            break;
                        case 5:
                            for ($j = 51; $j <= 55; $j++)
                            {
                                $tmp+= [
                                    $j => $i[$j]
                                ];
                            }
                            if (!empty($i[65]) && !empty($i[70]))
                            {
                                $unit_cost = trim($i[70])/(trim($i[65]) * 1000);
                            }
                            else
                            {
                                $unit_cost = @$method_final[(string)$i[60]][$rmb[$i[11]][0] - 1] / 1000;
                            }
                            $tmp+= [
                                'selected_repair_method' => @$method_final[(string)$i[60]][5],
                                'selected_repair_classification' => @$method_final[(string)$i[60]][6],
                                'unit_cost' => round($unit_cost),
                                'selected_quantity_unit' => $i[65],
                                'selected_unit_quantity' => @$unit[$method_final[(string)$i[60]][7]],
                                'amount' => (!empty($i[70])) ? round((trim($i[70])*0.001)) : '0'
                            ];
                            break;
                        default:
                            break;
                    }
                }
            }
           
            $dataset[] = $tmp;
        }, 99999999);
        
        return collect($dataset);
    }
    function _writeSheet($session_id, $type, $xml, $list, $language ,$xlsx = NULL)
    {
        $data = $this->_getCsvData($session_id, $type, $list, $language);
        if ($type == 10)
        {
            /*** DELETE AUTO FILTER ROW ***/
            $row_7 = $xml->sheetData->row[6];
            $dom = dom_import_simplexml($row_7);
            $dom->parentNode->removeChild($dom);
            
            $row_6 = $xml->sheetData->row[5];
            $dom = dom_import_simplexml($row_6);
            $dom->parentNode->removeChild($dom);
            /*** END HERE ***/
            
            /** ADD AUTOFILTER **/
            $table = $xlsx->arrXMLs['/xl/tables/table5.xml'];
            $table['ref'] = "A5:AT". (count($data) + 5);
            $table->autoFilter['ref'] = "A5:AT". (count($data) + 5);
            /** END **/
        }
        foreach ($data as $row)
        {
            $new_row = $xml->sheetData->addChild('row');

            foreach ($row as $k => $v)
            {
                $new_cell = $new_row->addChild('c'); 
                if (is_numeric($v))
                {   
                    $new_cell->addAttribute('t', "n");
                    $new_v = $new_cell->addChild('v', $v);
                }
                else
                {
                    $new_cell->addAttribute('t', "inlineStr");  
                    $new_is = $new_cell->addChild('is');
                    if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v); 
                    $new_T = $new_is->addChild('t', htmlspecialchars($v)); 
                }
            }
        }
        return $xml;
        // $xlsx->arrXMLs['/xl/worksheets/sheet6.xml'] = $xml->asXML();

        //$xlsx->Output("Output_WP.xlsx", "F");
    }

    private function _getMethodList($session_id, $language)
    {
        $method_final = [];
        $file = public_path("application/process/work_planning/".$session_id."/input/repair/list.csv");

        $fp = fopen($file, 'r');
        
        while ($line = fgetcsv($fp)) 
        {
            $method_tmp[] = $line;
        }
        fclose($fp);

        foreach ($method_tmp as $row)
        {   
            $key = $row[0];
            $method_final[$key] = [$row[1], $row[2], $row[3], $row[4], $row[5]];
            if ($language == 'en')
            {
                $method_final[$key][] = $row[7];
                $method_final[$key][] = $row[9];
            }
            else
            {
                $method_final[$key][] = $row[6];
                $method_final[$key][] = $row[8];
            }
            $method_final[$key][] = $row[10];
        }           
        return $method_final;
    }

    function _writeSheetProcessInfo($session_id, $xlsx, $user_id, $language)
    {
         /*** Processing Information ***/
        $process_info = $this->_getProcessInfo($session_id, $user_id);
        $sheet_name = ($language == 'en') ? '1_Processing Information' : '1_Thongtinchung';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);
        $sheet->data('H5', $process_info['user_account']);
        $sheet->data('H6', $process_info['name']);
        $sheet->data('H7', $process_info['organization']);
        $sheet->data('H8', $process_info['email']);
        $sheet->data('H11', $process_info['date']);
        $sheet->data('H12', $process_info['start_time']);
        $sheet->data('H13', $process_info['finish_time']);
        $sheet->data('I16', $process_info['year'], 'n');
        $sheet->data('I17', $process_info['target_org']);
        $sheet->data('I24', $process_info['start_year'], 'n');

        $sheet->data('E36', $process_info['bg_year_1'], 'n');
        $sheet->data('E37', $process_info['bg_year_2'], 'n');
        $sheet->data('E38', $process_info['bg_year_3'], 'n');
        $sheet->data('E39', $process_info['bg_year_4'], 'n');
        $sheet->data('E40', $process_info['bg_year_5'], 'n');
        $sheet->data('J36', $process_info['price_esca_factor'], 'n');
        $sheet->data('J37', $process_info['price_esca_factor'], 'n');
        $sheet->data('J38', $process_info['price_esca_factor'], 'n');
        $sheet->data('J39', $process_info['price_esca_factor'], 'n');
        $sheet->data('J40', $process_info['price_esca_factor'], 'n');

    }

    function _getProcessInfo($session_id, $user_id)
    {
        $process_info = [];
        $user = User::findOrFail($user_id);
        $user_name = $user->name;
        $email = $user->email;
        $organization_id = $user->organization_id;
        $org_name = tblOrganization::findOrFail($organization_id)->organization_name;
        // $rmb = tblWorkPlanningOrganization::where('work_planning_id', $session_id)->get();
        // foreach ($rmb as $item)
        // {
        //     $rmb_id[] = $item->organization_id;
        // }

        // $target_org = tblOrganization::whereIn('id', $rmb_id)->get();
        // $rmb_name = "";
        // foreach ($target_org as $item)
        // {
        //     $rmb_name .= $item->organization_name . ", ";
        // }
        // $rmb_name = substr($rmb_name, 0, -2);
       
        $work_planning = tblWorkPlanning::findOrFail($session_id);
        $text_region = $work_planning->getInfoOrganization($session_id);
        $process_info = [
            'user_account' => $user_name,
            'name' => $user_name,
            'organization' => $org_name,
            'email' => $email,
            'date' => Carbon::parse($work_planning->created_at)->format('m/d/Y'),
            'start_time' => $work_planning->created_at->format('m/d/Y h:i:s A'),
            'finish_time' => $work_planning->updated_at->format('m/d/Y h:i:s A'),
            'year' => $work_planning->year,
            'target_org' => $text_region,
            'start_year' => $work_planning->base_planning_year,
            'bg_year_1' => $work_planning->year_1,
            'bg_year_2' => $work_planning->year_2,
            'bg_year_3' => $work_planning->year_3,
            'bg_year_4' => $work_planning->year_4,
            'bg_year_5' => ($work_planning->total_budget - $work_planning->year_1 - $work_planning->year_2 - $work_planning->year_3 - $work_planning->year_4),
            'price_esca_factor' => $work_planning->price_esca_factor
        ];
        return $process_info;
    }

    function _writeRepairStandardSetting($session_id, $xlsx, $language)
    {
        $sheet_name = ($language == 'en') ? '2_Repair Standard Setting' : '2_TC Sua chua';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);
        $structure = [
                0 => [
                    1, 2, 3, 4
                ],
                1 => [
                    1, 2, 3, 4, 5, 6
                ]
            ];
        foreach ($structure as $road_type => $road_classes) 
        {
            foreach ($road_classes as $road_class) 
            {
                $file = public_path("application/process/work_planning/".$session_id."/input/repair/". $road_type. "/"."/".$road_class ."/repair_matrix1.csv");
                $fp = fopen($file, 'r');
                while ($line = fgetcsv($fp)) 
                {
                    $table_ac[] = $line;
                }
                fclose($fp);
                
                $file = public_path("application/process/work_planning/".$session_id."/input/repair/". $road_type. "/"."/".$road_class ."/repair_matrix2.csv");
                $fp = fopen($file, 'r');
                while ($line = fgetcsv($fp)) 
                {
                    $table_bst[] = $line;
                }
                fclose($fp);

                $file = public_path("application/process/work_planning/".$session_id."/input/repair/". $road_type. "/"."/".$road_class."/repair_matrix3.csv");
                $fp = fopen($file, 'r');
                while ($line = fgetcsv($fp)) 
                {
                    $table_cc[] = $line;
                }
                fclose($fp);
            }
        }
        foreach ($table_ac as $key => $value)
        {
            $cell = "E";
            foreach ($value as $sub_k => $sub_v)
            {
                if ($sub_v == 0)
                {
                    $sub_v = 1000;
                }
                $sheet->data($cell . (6 + $key), $sub_v, "n");
                $cell++;
            }
        }
        
        foreach ($table_bst as $key => $value)
        {
            $cell = "E";
            foreach ($value as $sub_k => $sub_v)
            {
                if ($sub_v == 0)
                {
                    $sub_v = 2000;
                }
                $sheet->data($cell . (80 + $key), $sub_v, "n");
                $cell++;
            }
        }
        foreach ($table_cc as $key => $value)
        {
            $cell = "E";
            foreach ($value as $sub_k => $sub_v)
            {
                if ($sub_v == 0)
                {
                    $sub_v = 3000;
                }
                $sheet->data($cell . (154 + $key), $sub_v, "n");
                $cell++;
            }
        }

        $method_final = [];
        $work_planning = tblWorkPlanning::findOrFail($session_id);
        $method_tmp = mstRepairMethod::all();

        foreach ($method_tmp as $row)
        {   
            if ($row->id == 1001)
            {
                $method_final[1000][] = 1000;
                $method_final[1000][] = trans("wp.no_repair_ac");
                $method_final[1000][] = "AC";
            }
            else if ($row->id == 2001)
            {
                $method_final[2000][] = 2000;
                $method_final[2000][] = trans("wp.no_repair_bst");
                $method_final[2000][] = "BST";
            }
            else if ($row->id == 3001)
            {
                $method_final[3000][] = 3000;
                $method_final[3000][] = trans("wp.no_repair_cc");
                $method_final[3000][] = "CC";
            }

            $pavement_type = mstSurface::findOrFail($row->pavement_type)->code_name;
            $unit = mstMethodUnit::findOrFail($row->unit_id)->code_name;
            $repair_method_cost = tblRepairMethodCost::where('repair_method_id', $row->id)->orderBy('organization_id', 'asc')->get();
            $repair_category = tblRCategory::findOrFail($row->zone_id)->{'name_' . $language};
            $repair_classification = tblRClassification::findOrFail($row->classification_id)->{'name_' . $language};
            $key = $row->id;
            $method_final[$key][] = $key;
            if ($language == 'en')
            {
                $method_final[$key][] = $row->name_en;
            }
            else
            {
                $method_final[$key][] = $row->name_vn;
            }
             $method_final[$key][] = $pavement_type;
            $method_final[$key][] = $unit;
            $method_final[$key][] = $work_planning->year;
            foreach ($repair_method_cost as $item)
            {
                $method_final[$key][] = $item->cost;
            }
            $method_final[$key][] = $repair_category;
            $method_final[$key][] = $repair_classification;
            $method_final[$key][] = $row->updated_at->toDateString();
        }
        $index = 0;
        foreach ($method_final as $key => $value)
        {
            $cell = "Q";
            foreach ($value as $sub_k => $sub_v)
            {
                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (7 + $index), $sub_v, "n");
                }
                else
                {
                    $sheet->data($cell . (7 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }
    }

    function _writeRMD($session_id, $xlsx, $language)
    {
        $tmp = $this->_getDataRMD($session_id, $language);
       
        $xml = $xlsx->arrXMLs['/xl/worksheets/sheet16.xml'];
        /** Delete rows which dont need **/
        $row_15 = $xml->sheetData->row[14];
        $dom = dom_import_simplexml($row_15);
        $dom->parentNode->removeChild($dom);

        $row_14 = $xml->sheetData->row[13];
        $dom = dom_import_simplexml($row_14);
        $dom->parentNode->removeChild($dom);
        
        $row_13 = $xml->sheetData->row[12];
        $dom = dom_import_simplexml($row_13);
        $dom->parentNode->removeChild($dom);

         /** ADD AUTOFILTER **/
        $table = $xlsx->arrXMLs['/xl/tables/table6.xml'];
        $table['ref'] = "B12:AQ". (count($tmp) + 12);
        $table->autoFilter['ref'] = "B12:AQ". (count($tmp) + 12);
        /** END **/

        foreach ($tmp as $row)
        {
            $new_row = $xml->sheetData->addChild('row');
            $blank_cell = $new_row->addChild('c');
            $blank_cell->addAttribute('t', "inlineStr");
            $new_is = $blank_cell->addChild('is');
            $new_T = $new_is->addChild('t', '');  
            foreach ($row as $k => $v)
            {
                $new_cell = $new_row->addChild('c'); 
                if (is_numeric($v))
                {   
                    $new_cell->addAttribute('t', "n");
                    $new_v = $new_cell->addChild('v', $v);
                }
                else
                {
                    $new_cell->addAttribute('t', "inlineStr");  
                    $new_is = $new_cell->addChild('is');
                    if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v); 
                    $new_T = $new_is->addChild('t', htmlspecialchars($v)); 
                }
                // $new_cell = $new_row->addChild('c'); 

                // $new_cell->addAttribute('t', "inlineStr");
                // $new_is = $new_cell->addChild('is');
                // // text has to be saved as utf-8 (otherwise the spreadsheet file become corrupted)
                // if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v); 
                // $new_T = $new_is->addChild('t', htmlspecialchars($v));    
                
            }
        }

        return $xml;
    }

    function _getDataRMD($session_id, $language)
    {
        $tmp = [];
        $rmb = tblWorkPlanningOrganization::where('work_planning_id', $session_id)->get();
        foreach ($rmb as $item)
        {
            $rmb_id[] = $item->organization_id;
        }
        $rmd = \App\Models\tblSectiondataRMD::whereHas('segment', function ($query) use ($rmb_id) {
            $query->whereHas('tblOrganization', function($sub_query) use ($rmb_id) {
                $sub_query->whereIn('parent_id', $rmb_id);
            });
        })->get();

        foreach($rmd as $key => $value)
        {
            $segment = $value->segment()->first();
            $branch = $segment->tblBranch()->first();
            $sb = $segment->tblOrganization()->first();
            $rmb = tblOrganization::findOrFail($sb->parent_id)->{"name_{$language}"};
            $ward_from = @$value->wardFrom()->first();
            if (!empty($ward_from))
            {
                $district_from = @$ward_from->district()->first();
                if (!empty($district_from)) $province_from = $district_from->province()->first();
            }
            
            $ward_to = @$value->wardTo()->first();
            if (!empty($ward_to))
            {
                $district_to = @$ward_to->district()->first();  
                if (!empty($district_to)) $province_to = $district_to->province()->first();
            }
            
            $design_speed_model = \App\Models\tblDesignSpeed::where('terrain_id', $value->terrian_type_id)->where('road_class_id', $value->road_class_id)->first();

            if(!empty($design_speed_model))
            {
                $speed = $design_speed_model->speed;
            }
            $direction = "";
            if ($value->direction == 1)
            {
                $direction = trans('back_end.left');
            } 
            else if ($value->direction == 2)
            {
                $direction = trans('back_end.right');    
            }
            else if ($value->direction == 3)
            {
                $direction = trans('back_end.single');
            }

            $tmp[] = [
                'road_id' => '',
                'road_name' => $branch->{"name_{$language}"},
                'route_name' => $segment->{"name_{$language}"},
                'branch_no' => $branch->branch_number,
                'road_class' => @$value->routeClass()->first()->{"name_{$language}"},
                'road_category' => @$branch->mstRoadCategory()->first()->code_name,
                'rmb' => $rmb,
                'sb' => $sb->{"name_{$language}"},
                'km_from' => $value->km_from,
                'm_from' => $value->m_from,
                'km_to' => $value->km_to,
                'm_to' => $value->m_to,
                'latitude_from' => $value->from_lat,
                'longitude_from' => $value->from_lng,
                'latitude_to' => $value->to_lat,
                'longitude_to' => $value->to_lng,
                'province_from' => @$province_from->{"name_{$language}"},
                'district_from' => @$district_from->{"name_{$language}"},
                'ward_from' => @$ward_from->{"name_{$language}"},
                'province_to' => @$province_to->{"name_{$language}"},
                'district_to' => @$district_to->{"name_{$language}"},
                'ward_to' => @$ward_to->{"name_{$language}"},
                'kp_date' => '',
                'date_update' => $value->survey_time,
                'manage_length' => $value->actual_length,
                'actual_length' => $value->actual_length,
                'construct_year' => substr($value->construct_year, 0, 4),
                'construct_month' => substr($value->construct_year, 4, 2),
                'service_year' => substr($value->service_start_year, 0, 4),
                'service_month' => substr($value->service_start_year, 4, 2),
                'terrain_type' => @$value->terrianType()->first()->{"name_{$language}"},
                'temperature' => $value->temperature,
                'annual_preciptation' => $value->annual_precipitation,
                'design_speed' => @$speed,
                'direction_type' => $direction,
                'pavement_width' => '',
                'total_thickness' => $value->pavement_thickness,
                'no_of_lane' => $value->no_lane,
                'lane_no' => $value->lane_pos_number,
                'lane_width' => $value->lane_width,
                'pavement_type' => @$value->surface()->first()->code_name
            ];
        }
        return $tmp;
    }
}
