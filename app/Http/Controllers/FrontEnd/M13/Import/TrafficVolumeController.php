<?php

namespace App\Http\Controllers\FrontEnd\M13\Import;

use App\Classes\Helper;
use App\Http\Controllers\FrontEnd\M13\BaseImportController;
use App\Models\tblSectiondataTV;
use App\Models\tblTVVehicleDetailHistory;
use App\Models\tblTVVehicleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Excel;

class TrafficVolumeController extends BaseImportController
{
    protected
        $custom_column_datatable = ['section_id','road','segment_id','rmb', 'sb', 'name', 'km_station', 'm_station', 'survey_time'];

    function __construct()
    {
        parent::__construct(
            '\App\Models\tblSectiondataTV',
            'traffic_volume',
            $this->custom_column_datatable,
            'front-end.m13.traffic_volume.extra_view_import',
            'tblSectiondata_TV',
            [],
            'side_menu_traffic_volume',
            'traffic_volume',
            11
        );
    }

    public function downloadEN()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_TV_EN.xlsx');
        Excel::load($tpl_file,  function ($excel){
        })
            ->download('xlsx');
    }

    public function downloadVI()
    {
        $tpl_file = public_path('excel_templates/M13/Tpl_TV_VI.xlsx');
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
            $result =[
                'section_id' => $d['section_id'],
                'road' => $d['road'],
                'route_branch' => $d['route_branch'],
                'rmb' => $d['rmb'],
                'sb' => $d['sb'],
                'name' => $d['name'],
                'km_station' => $d['km_station'],
                'm_station' => $d['m_station'],
                'lat_station' => $d['lat_station'],
                'lng_station' => $d['lng_station'],
                'survey_time' => substr($d['survey_time'], 0,-3),
                'total_traffic_volume_up' => $d['total_traffic_volume_up'],
                'total_traffic_volume_down' => $d['total_traffic_volume_down'],
                'traffic_volume_total' => $d['traffic_volume_total'],
                'heavy_traffic_up' => $d['heavy_traffic_up'],
                'heavy_traffic_down' => $d['heavy_traffic_down'],
                'heavy_traffic_total' => $d['heavy_traffic_total'],
                'up1' => $d['up1'],
                'down1' => $d['down1'],
                'total1' => $d['total1'],
                'up2' => $d['up2'],
                'down2' => $d['down2'],
                'total2' => $d['total2'],
                'up3' => $d['up3'],
                'down3' => $d['down3'],
                'total3' => $d['total3'],
                'up4' => $d['up4'],
                'down4' => $d['down4'],
                'total4' => $d['total4'],
                'up5' => $d['up5'],
                'down5' => $d['down5'],
                'total5' => $d['total5'],
                'up6' => $d['up6'],
                'down6' => $d['down6'],
                'total6' => $d['total6'],
                'up7' => $d['up7'],
                'down7' => $d['down7'],
                'total7' => $d['total7'],
                'up8' => $d['up8'],
                'down8' => $d['down8'],
                'total8' => $d['total8'],
                'up9' => $d['up9'],
                'down9' => $d['down9'],
                'total9' => $d['total9'],
                'up10' => $d['up10'],
                'down10' => $d['down10'],
                'total10' => $d['total10'],
                'grand_total' => $d['grand_total'],
                'error' => $error,
            ];
            $data[] = $result;
        }
        $lang = \App::getLocale();
        $tpl_file = public_path('excel_templates/M13/Tpl_TV_'.strtoupper($lang) . '.xlsx');
        Excel::load($tpl_file,  function ($reader) use($data) {
            $reader->sheet(0, function($sheet) use ($data) {
                $sheet->fromArray($data, NULL, 'B12', null, false);
            });
        })
            ->setFilename('Template_TV_'.strtoupper($lang).'_Invalid')
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
                    $traffic_volume = new tblSectiondataTV();
                    $traffic_volume->id = $add_new['section_id'];
                    $traffic_volume->name_en = $add_new['name'];
                    $traffic_volume->name_vn = $add_new['name'];
                    $traffic_volume->segment_id = $add_new['segment_id'];
                    $traffic_volume->km_station = $add_new['km_station'];
                    $traffic_volume->m_station  = $add_new['m_station'];
                    $traffic_volume->survey_time = $add_new['survey_time'].'/01';
                    $traffic_volume->total_traffic_volume_up = $add_new['total_traffic_volume_up'];
                    $traffic_volume->total_traffic_volume_down = $add_new['total_traffic_volume_down'];
                    $traffic_volume->heavy_traffic_up = $add_new['heavy_traffic_up'];
                    $traffic_volume->heavy_traffic_down = $add_new['heavy_traffic_down'];
                    $traffic_volume->lat_station = $add_new['lat_station'];
                    $traffic_volume->lng_station = $add_new['lng_station'];
                    $traffic_volume->save();
                    for ($i = 1; $i <= 10; $i++)
                    {
                        $vehicle_detail = new tblTVVehicleDetails();
                        $vehicle_detail->vehicle_type_id = $i;
                        $vehicle_detail->sectiondata_TV_id = $traffic_volume->id;
                        $vehicle_detail->up = $add_new['up'.$i];
                        $vehicle_detail->down = $add_new['down'.$i];
                        $vehicle_detail->save();
                    }
                }
            }
            
            if (!empty($data_check['update']))
            {
                foreach ($data_check['update'] as $update_history)
                {
                    $traffic_volume_history = tblSectiondataTV::firstOrFail($update_history['section_id']);
                    $traffic_volume_history->name_en = $update_history['name'];
                    $traffic_volume_history->name_vn = $update_history['name'];
                    $traffic_volume_history->segment_id = $update_history['segment_id'];
                    $traffic_volume_history->km_station = $update_history['km_station'];
                    $traffic_volume_history->m_station  = $update_history['m_station'];
                    $traffic_volume_history->survey_time = $update_history['survey_time'].'/01';
                    $traffic_volume_history->total_traffic_volume_up = $update_history['total_traffic_volume_up'];
                    $traffic_volume_history->total_traffic_volume_down = $update_history['total_traffic_volume_down'];
                    $traffic_volume_history->heavy_traffic_up = $update_history['heavy_traffic_up'];
                    $traffic_volume_history->heavy_traffic_down = $update_history['heavy_traffic_down'];
                    $traffic_volume_history->lat_station = $update_history['lat_station'];
                    $traffic_volume_history->lng_station = $update_history['lng_station'];
                    $traffic_volume_history->save();
                    for ($i = 1; $i <= 10; $i++)
                    {
                        $vehicle_detail_history = tblTVVehicleDetailHistory::where('tv_history_id', $traffic_volume_history->id)->firstOrFail();
                        $vehicle_detail_history->vehicle_type_id = $i;
                        $vehicle_detail_history->sectiondata_TV_id = $traffic_volume_history->id;
                        $vehicle_detail_history->up = $update_history['up'.$i];
                        $vehicle_detail_history->down = $update_history['down'.$i];
                        $vehicle_detail_history->save();
                    }
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
