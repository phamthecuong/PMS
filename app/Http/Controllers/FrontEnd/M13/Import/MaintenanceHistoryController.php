<?php

namespace App\Http\Controllers\FrontEnd\M13\Import;

use App\Classes\Helper;
use App\Http\Controllers\FrontEnd\M13\BaseImportController;
use App\Models\mstPavementType;
use App\Models\tblMHHistory;
use App\Models\tblSectiondataMH;
use App\Models\tblSectionLayer;
use App\Models\tblSectionLayerHistory;
use App\Models\tblSegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;

class MaintenanceHistoryController extends BaseImportController
{
    protected
        $custom_column_datatable = ['section_id','road','segment_id','rmb', 'sb', 'km_from','m_from','km_to','m_to', 'direction', 'lane_pos_number'];

    function __construct()
    {
        parent::__construct(
            '\App\Models\tblSectiondataMH',
            'maintenance_history',
            $this->custom_column_datatable,
            'front-end.m13.maintenance_history.extra_view_import',
            'tblSectiondata_MH',
            [],
            'side_menu_maintenance_history',
            'maintenance_history',
            12
        );
    }

    public function downloadEN()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_MH_EN.xlsx');
        Excel::load($tpl_file,  function ($excel){
        })
            ->download('xlsx');
    }

    public function downloadVI()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_MH_VI.xlsx');
        Excel::load($tpl_file,  function ($excel){
        })
            ->download('xlsx');
    }

    public function exportInvalid($file_name)
    {
        $data = [];
        $check = $this->allData($file_name);
        $backup = Helper::checkSurveyTime($check,$this->check, $this->prefix_url);
        foreach ($backup['err'] as $d)
        {
            $error = $d['error'];
            if ($error == 1)
            {
                $error = trans('back_end.status_err');
            }
            if ($error == 2)
            {
                $error = trans('back_end.overlap');
            }
            $direction = $d['direction'];
            if ($direction == 1)
            {
                $direction = trans('back_end.left_direction');
            }
            if($direction == 2)
            {
                $direction =trans('back_end.right_direction');

            }
            if($direction == 3)
            {
                $direction = trans('back_end.single_direction');
            }
            if($direction == '')
            {
                $direction =  '';
            }
            $segment_id = (int)$d['segment_id'];
            $segment_id = $segment_id != 0 ? tblSegment::where('id', $segment_id)->first()->name : $d['segment_id'];

            $pavement = (int)$d['pavement_type_id'];
            $pavement = $pavement != 0 ? mstPavementType::where('id', $pavement)->first()->name : $d['pavement_type_id'];
            $result =[
                'section_id' => $d['section_id'],
                'road' => $d['road'],
                'segment_id' => $segment_id,
                'route_branch' => $d['route_branch'],
                'road_class_id' => $d['road_class_id'],
                'rmb' => $d['rmb'],
                'sb' => $d['sb'],
                'km_from' => $d['km_from'],
                'm_from' => $d['m_from'],
                'km_to' => $d['km_to'],
                'm_to' => $d['m_to'],
                'from_lat' => $d['from_lat'],
                'from_lng' => $d['from_lng'],
                'to_lat' => $d['to_lat'],
                'to_lng' => $d['to_lng'],
                'province_from' => $d['province_from'],
                'district_from' => $d['district_from'],
                'ward_from' => $d['ward_from'],
                'province_to' => $d['province_to'],
                'district_to' => $d['district_to'],
                'ward_to' => $d['ward_to'],
                'kilopost_adjustment_date' => $d['kilopost_adjustment_date'],
                'survey_time' => $d['survey_time'],
                'direction' => $direction,
                'length' => $d['length'],
                'actual_length' => $d['actual_length'],
                'lane_pos_number' => $d['lane_pos_number'],
                'completion_date' => $d['completion_date'],
                'repair_duration' => $d['repair_duration'],
                'r_category_id' => $d['r_category_id'],
                'repair_method' => $d['repair_method'],
                'r_classification_id' => $d['r_classification_id'],
                'pavement_type_id' => $pavement,
                'binder_course' => $d['binder_course'],
                'wearing_course' => $d['wearing_course'],
                'total' => $d['total'],
                'total_pavement_thickness' => $d['total_pavement_thickness'],
                'total_width_repair_lane' => $d['total_width_repair_lane'],
                'direction_running' => $d['direction_running'],
                'distance' => $d['distance'],
                'error' => $error
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
    public function store(Request $request, $file_name)
    {
        $data = $this->dataSuccess($file_name);
        $data_check = Helper::checkSurveyTime($data, $this->check, $this->prefix_url);
        
        $err = $this->allData($file_name);
        $data_err = Helper::checkSurveyTime($err, $this->check, $this->prefix_url);
        $count_new = count($data_check['new']);
        $update_h = count($data_check['update']);
        $count_err = count($data_err['err']);

        DB::beginTransaction();
        try {
            if (!empty($data_check['new']))
            {
                foreach ($data_check['new'] as $add_new)
                {
                    $maintenance_history = new tblSectiondataMH();
                    $maintenance_history->id = $add_new['section_id'];
                    $maintenance_history->segment_id = $add_new['segment_id'];
                    $maintenance_history->km_from = $add_new['km_from'];
                    $maintenance_history->m_from = $add_new['m_from'];
                    $maintenance_history->km_to = $add_new['km_to'];
                    $maintenance_history->m_to = $add_new['m_to'];
                    $maintenance_history->survey_time = $add_new['survey_time'];
                    $maintenance_history->completion_date = $add_new['completion_date'];
                    $maintenance_history->repair_duration = $add_new['repair_duration'];
                    $maintenance_history->direction = $add_new['direction'];
                    $maintenance_history->actual_length = $add_new['actual_length'];
                    $maintenance_history->lane_pos_number = $add_new['lane_pos_number'];
                    $maintenance_history->total_width_repair_lane = $add_new['total_width_repair_lane'];
                    $maintenance_history->r_classification_id = $add_new['r_classification_id'];
                    $maintenance_history->repair_method_id = $add_new['repair_method'];
                    $maintenance_history->r_category_id = $add_new['r_category_id'];
                    $maintenance_history->distance = $add_new['distance'];
                    $maintenance_history->direction_running = $add_new['direction_running'];
                    $maintenance_history->from_lat = $add_new['from_lat'];
                    $maintenance_history->from_lng = $add_new['from_lng'];
                    $maintenance_history->to_lat = $add_new['to_lat'];
                    $maintenance_history->to_lng = $add_new['to_lng'];
                    if ($add_new['pavement_type_id'] != null) 
                    {
                        $maintenance_history->pavement_type_id = $this->convertSurface($add_new['pavement_type_id']);
                    }
                    $maintenance_history->ward_from_id = $add_new['ward_from'];
                    $maintenance_history->ward_to_id = $add_new['ward_to'];
                    $maintenance_history->save();
                    //save history
                    if ($add_new['pavement_type_id'] != null) 
                    {
                        $layer = new tblSectionLayer();
                        $layer->thickness = '';
                        $layer->description = '';
                        $layer->type = 2;
                        $layer->material_type_id = $add_new['pavement_type_id'];
                        $layer->layer_id = '6';
                        $layer->rmdSection()->associate($maintenance_history);
                        $layer->save();
                    }
                    // if ($add_new['total_pavement_thickness'] != null)
                    // {
                    //     $l = new tblSectionLayer();
                    //     $l->thickness = $add_new['total_pavement_thickness'];
                    //     $l->description = '';
                    //     $l->type = 2;
                    //     $l->material_type_id = $add_new['pavement_type_id'];
                    //     $l->layer_id = '6';
                    //     $l->rmdSection()->associate($maintenance_history);
                    //     $l->save();
                    // }
                }
            }
            
            if (!empty($data_check['update']))
            {
                foreach ($data_check['update'] as $update_history)
                {
                    $mh_history = tblSectiondataMH::findOrFail($update_history['section_id']);
                    $mh_history->segment_id = $update_history['segment_id'];
                    $mh_history->km_from = $update_history['km_from'];
                    $mh_history->m_from = $update_history['m_from'];
                    $mh_history->km_to = $update_history['km_to'];
                    $mh_history->m_to = $update_history['m_to'];
                    $mh_history->survey_time = $update_history['survey_time'];
                    $mh_history->completion_date = $update_history['completion_date'];
                    $mh_history->repair_duration = $update_history['repair_duration'];
                    $mh_history->direction = $update_history['direction'];
                    $mh_history->actual_length = $update_history['actual_length'];
                    $mh_history->lane_pos_number = $update_history['lane_pos_number'];
                    $mh_history->total_width_repair_lane = $update_history['total_width_repair_lane'];
                    $mh_history->r_classification_id = $update_history['r_classification_id'];
                    $mh_history->repair_method_id = $update_history['repair_method'];
                    $mh_history->r_category_id = $update_history['r_category_id'];
                    $mh_history->distance = $update_history['distance'];
                    $mh_history->direction_running = $update_history['direction_running'];
                    $mh_history->from_lat = $update_history['from_lat'];
                    $mh_history->from_lng = $update_history['from_lng'];
                    $mh_history->to_lat = $update_history['to_lat'];
                    $mh_history->to_lng = $update_history['to_lng'];
                    if ($update_history['pavement_type_id'] != null) 
                    {
                        $mh_history->pavement_type_id = $this->convertSurface($update_history['pavement_type_id']);
                    }
                    $mh_history->ward_from_id = $update_history['ward_from'];
                    $mh_history->ward_to_id = $update_history['ward_to'];
                    $mh_history->save();
                    if ($update_history['pavement_type_id'] != null) 
                    {
                        $layer = tblSectionLayer::where('sectiondata_id', $update_history['section_id'])->first();
                        if (!empty($layer))
                        {
                            $layer->thickness = '';
                            $layer->description = '';
                            $layer->type = 2;
                            $layer->material_type_id = $update_history['pavement_type_id'];
                            $layer->layer_id = 6;
                            $layer->save();
                        }
                    }
                    // if ($update_history['total_pavement_thickness'] != null)
                    // {
                    //     $l = tblSectionLayer::where('sectiondata_id', $update_history['section_id'])->first();
                    //     $l->thickness = $update_history['total_pavement_thickness'];
                    //     $l->description = '';
                    //     $l->type = 2;
                    //     $l->material_type_id = $update_history['pavement_type_id'];
                    //     $l->layer_id = 6;
                    //     $l->save();
                    // }
                    // $l = tblSectionLayerHistory::where('sectiondata_history_id', $update_history['section_id'])->where('layer_id', 6)->whereType(2)->first();
                    // if (isset($l))
                    // {
                    //     $l->thickness = $update_history['total_pavement_thickness'];
                    //     $l->description = '';
                    //     $l->material_type_id = $update_history['pavement_type_id'];
                    //     $l->save();
                    // }
                }
            }
            DB::commit();
            return redirect(url('/admin/' . $this->prefix_url))->with([
                'count_new' => $count_new,
                'update_h' => $update_h,
                'count_err' => $count_err,
                'file_name' => $file_name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }


}
