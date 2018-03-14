<?php

namespace App\Http\Controllers\FrontEnd\M13\Import;

use App\Http\Controllers\FrontEnd\M13\BaseImportController;
use App\Models\mstPavementType;
use App\Models\tblSectiondataRMD;
use App\Models\tblRMDHistory;
use App\Models\tblSectionLayer;
use App\Models\tblSectionLayerHistory;
use App\Models\tblSegment;
use Illuminate\Http\Request;
use App\Classes\Helper;
use Excel;
class RoadInventoryController extends BaseImportController
{
    protected
        $custom_column_datatable = ['section_id','road','segment_id','rmb', 'sb', 'km_from', 'm_from', 'km_to', 'm_to', 'direction', 'lane_pos_number'];

    function __construct()
    {
        parent::__construct(
            '\App\Models\tblSectiondataRMD',
            'road_inventories',
            $this->custom_column_datatable,
            'front-end.m13.road_inventory.extra_view_import',
            'tblSectiondata_RMD',
            [],
            'side_menu_road_inventory',
            'road_inventory',
            12
        );
    }

    public function downloadEN()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_RMD_EN.xlsx');
        Excel::load($tpl_file,  function ($excel){
        })
        ->download('xlsx');
    }

    public function downloadVI()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_RMD_VI.xlsx');
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
                'r_category_id' => $d['r_category_id'],
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
                'kilometpost' => $d['kilometpost'],
                'survey_time' => $d['survey_time'],
                'length_as_per_chainage' => $d['length_as_per_chainage'],
                'actual_length' => $d['actual_length'],
                'construct_year_y' => $d['construct_year_y'],
                'construct_year_m' => $d['construct_year_m'],
                'service_start_year_y' => $d['service_start_year_y'],
                'service_start_year_m' => $d['service_start_year_m'],
                'terrian_type_id' => $d['terrian_type_id'],
                'temperature' => $d['temperature'],
                'annual_precipitation' => $d['annual_precipitation'],
                'design_speed' => $d['design_speed'],
                'direction' => $direction,
                'pavement_width' => $d['pavement_width'],
                'pavement_thickness' => $d['pavement_thickness'],
                'no_lane' => $d['no_lane'],
                'lane_pos_number' => $d['lane_pos_number'],
                'lane_width' => $d['lane_width'],
                'pavement_type_id' => $pavement,
                'remark' => $d['remark'],
                'error' => $error,
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

    public function store(Request $request, $file_name)
    {
        $data_success = $this->dataSuccess($file_name);
        $data = Helper::checkSurveyTime($data_success,$this->check, $this->prefix_url);
        $err = $this->allData($file_name);
        $data_err = Helper::checkSurveyTime($err, $this->check, $this->prefix_url);
        $count_new = count($data['new']);
        $update_h = count($data['update']);
        $count_err = count($data_err['err']);
        \DB::beginTransaction();
        try {
            if (!empty($data['new']))
            {
                foreach ($data['new'] as $new) 
                {
                    $add_new = new tblSectiondataRMD();
                    $add_new->id = $new['section_id'];
                    $add_new->segment_id = $new['segment_id'];
                    $add_new->terrian_type_id = $new['terrian_type_id'];
                    $add_new->road_class_id = $new['road_class_id'];
                    $add_new->from_lat = $new['from_lat'];
                    $add_new->from_lng = $new['from_lng'];
                    $add_new->to_lat = $new['to_lat'];
                    $add_new->to_lng = $new['to_lng'];
                    $add_new->km_from = $new['km_from'];
                    $add_new->m_from = $new['m_from'];
                    $add_new->km_to = $new['km_to'];
                    $add_new->m_to = $new['m_to'];
                    $add_new->survey_time = $new['survey_time'];
                    $add_new->direction = $new['direction'];
                    $add_new->lane_pos_number = $new['lane_pos_number'];
                    $add_new->lane_width = $new['lane_width'];
                    $add_new->no_lane = $new['no_lane'];
                    $add_new->construct_year = $new['construct_year_y'].$new['construct_year_m'];
                    $add_new->service_start_year = $new['service_start_year_y'].$new['service_start_year_m'];
                    $add_new->temperature = $new['temperature'];
                    $add_new->annual_precipitation = $new['annual_precipitation'];
                    $add_new->actual_length = $new['actual_length'];
                    $add_new->remark = $new['remark'];
                    if ($new['pavement_type_id'] != null) 
                    {
                        $add_new->pavement_type_id = $this->convertSurface($new['pavement_type_id']);
                    }
                    $add_new->pavement_thickness = $new['pavement_thickness'];
                    $add_new->ward_from_id = $new['ward_from'];
                    $add_new->ward_to_id = $new['ward_to'];
                    $add_new->save();
                    if ($new['pavement_type_id'] != null) 
                    {
                        $layer = new tblSectionLayer();
                        $layer->thickness = '';
                        $layer->description = '';
                        $layer->type = 1;
                        $layer->material_type_id = $new['pavement_type_id'];
                        $layer->layer_id = '6';
                        $layer->rmdSection()->associate($add_new);
                        $layer->save();
                    }
                }
            }
           
            if (!empty($data['update']))
            {
                foreach ($data['update'] as $update)
                {
                    $updateHistory =  tblSectiondataRMD::findOrFail($update['section_id']);
                    $updateHistory->segment_id = $update['segment_id'];
                    $updateHistory->terrian_type_id = $update['terrian_type_id'];
                    $updateHistory->road_class_id = $update['road_class_id'];
                    $updateHistory->from_lat = $update['from_lat'];
                    $updateHistory->from_lng = $update['from_lng'];
                    $updateHistory->to_lat = $update['to_lat'];
                    $updateHistory->to_lng = $update['to_lng'];
                    $updateHistory->km_from = $update['km_from'];
                    $updateHistory->m_from = $update['m_from'];
                    $updateHistory->km_to = $update['km_to'];
                    $updateHistory->m_to = $update['m_to'];
                    $updateHistory->survey_time = $update['survey_time'];
                    $updateHistory->direction = $update['direction'];
                    $updateHistory->lane_pos_number = $update['lane_pos_number'];
                    $updateHistory->lane_width = $update['lane_width'];
                    $updateHistory->no_lane = $update['no_lane'];
                    $updateHistory->construct_year = $update['construct_year_y'].$update['construct_year_m'];
                    $updateHistory->service_start_year = $update['service_start_year_y'].$update['service_start_year_m'];
                    $updateHistory->temperature = $update['temperature'];
                    $updateHistory->annual_precipitation = $update['annual_precipitation'];
                    $updateHistory->actual_length = $update['actual_length'];
                    $updateHistory->remark = $update['remark'];
                    if ($update['pavement_type_id'] != null) 
                    {
                        $updateHistory->pavement_type_id = $this->convertSurface($new['pavement_type_id']);
                    }
                    $updateHistory->pavement_thickness = $update['pavement_thickness'];
                    $updateHistory->ward_from_id = $update['ward_from'];
                    $updateHistory->ward_to_id = $update['ward_to'];
                    $updateHistory->save();
                    if ($update['pavement_type_id'] != null) 
                    {
                        $layer = tblSectionLayer::where('sectiondata_id', $update['section_id'])->first();
                        if (!empty($l))
                        {
                            $layer->thickness = '';
                            $layer->description = '';
                            $layer->type = 1;
                            $layer->material_type_id = $update['pavement_type_id'];
                            $layer->layer_id = 6;
                            $layer->save();
                        }
                    }
                }
            }
            \DB::commit();
            return redirect(url('/admin/' . $this->prefix_url))->with([
                'count_new' => $count_new,
                'update_h' => $update_h,
                'count_err' => $count_err,
                'file_name' => $file_name
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            dd($e);
        }
    }
}
