<?php

namespace App\Http\Controllers\FrontEnd\PMSDataset;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth, DB;
use App\Http\Controllers\Controller;
use App\Classes\Helper;
use App\Models\tblPMSDataset;
use App\Models\tblPMSSectioning;
use App\Models\tblPMSDatasetInfo;
use App\Models\tblOrganization;

class IndexController extends Controller
{
	function index()
	{
		$year = tblPMSDataset::whereRaw('total_segment = completed_segment')->max('year');
        $pc_data_info = tblPMSDatasetInfo::where('year_of_dataset', $year)->where('pc_flg', 1);
        $pc_total = $pc_data_info->count();
        $pc_total_length = $pc_data_info->sum('section_length')/1000;
        $ri_data_info = tblPMSDatasetInfo::where('year_of_dataset', $year)->where('ri_flg', 1);
        $ri_total = $ri_data_info->count();
        $ri_total_length = $ri_data_info->sum('section_length')/1000;
        $mh_data_info = tblPMSDatasetInfo::where('year_of_dataset', $year)->where('mh_flg', 1);
        $mh_total = $mh_data_info->count();
        $mh_total_length = $mh_data_info->sum('section_length')/1000;

        $rec = tblPMSDataset::whereRaw('completed_segment <> total_segment')->count();	
		return view('front-end.pms_dataset.index', [
			'year' => @$year,
			'pc_total' => $pc_total,
			'pc_total_length' => $pc_total_length,
			'ri_total' => $ri_total,
			'ri_total_length' => $ri_total_length,
			'mh_total' => $mh_total,
			'mh_total_length' => $mh_total_length,
			'rec' => $rec
		]);
	}

	public function create(Request $request)
    {
    	$items = [];
		for ($i = 2000; $i <= 2100; $i++) 
		{ 
			$items[] = [
				'name' => $i,
				'value' => $i
			];
		}
        return view('front-end.pms_dataset.add', ['items' => $items]);
    }

    public function store(Request $request)
    {
    	\DB::beginTransaction();
    	try
    	{
    		$rec = tblPMSDataset::whereRaw('completed_segment <> total_segment')->count();
    		
    		if ($rec > 0)
    		{
	    		return redirect()->back()->withErrors(['year' => trans('pms_dataset.there_only_one_process_can_run_at_a_time')]);
	    	}
    		$year = $request->year;
    		
	        $rec = tblPMSDataset::where('year', $year)->first();
	        if (!$rec)
	        {
	        	$rec = new tblPMSDataset();
	        	$rec->year = $year;
	        	$rec->created_by = \Auth::user()->id;
	        }
	        else
	        {
	        	$rec->completed_segment = 0;
	        	$rec->updated_by = \Auth::user()->id;
	        }
	        // update segmentations
	        $rec->total_segment = ceil(1/500 * tblPMSSectioning::count());
	        $rec->save();

	        // H.ANH  20170817  optimize delete function
	        // tblPMSDatasetInfo::where('year_of_dataset', $year)->delete();

	        DB::statement("CREATE TABLE IF NOT EXISTS tblPMS_dataset_info_clone LIKE tblPMS_dataset_info");
	        DB::statement("INSERT INTO tblPMS_dataset_info_clone SELECT * FROM tblPMS_dataset_info WHERE year_of_dataset <> $year");

			DB::statement("RENAME TABLE tblPMS_dataset_info TO tblPMS_dataset_info_old, tblPMS_dataset_info_clone TO tblPMS_dataset_info");

			DB::statement("DROP TABLE tblPMS_dataset_info_old");

	        // end optimization
	        
			$job = (new \App\Jobs\merge_single_lane($rec->id))->onQueue('formulate_dataset');
	  		dispatch($job);
	  		
    		\DB::commit();
    	}
    	catch (\Exception $e)
    	{
    		\DB::rollBack();
    		dd($e->getMessage());
    	}

        return redirect('/user/pms_dataset');
    }

	function getPCImport()
	{
		// \DB::connection()->disableQueryLog();
		// \DB::beginTransaction();

		// try
		// {
		// 	$pc_source = 'pc_test/PC new form/PC_RMBIV_new_form/PCfile_RMBIV_new_form.csv';
		// 	$image_source = 'pc_test/PC new form/PC_RMBIV_new_form/ImageFile_RMBIV_new_form.csv';
			
		// 	$process_id = implode('', array_fill(0, 32, 2));

		// 	$csv = public_path($pc_source);
	 //        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE pavement_condition_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\r\\n' IGNORE 1 LINES (`section_id`, `geographical_area`, `rmb`, `sb`, `road_category`,`route_number`, `road_number_supplement`, `branch_number`, `route_name`, `kp_from`, `m_from`, `kp_to`, `m_to`, `section_length`, `analysis_area`, `structure`, `intersection`, `overlapping`, `number_of_lane_u`, `number_of_lane_d`, `direction`, `survey_lane`, `surface_type`, `survey_year`, `survey_month`, `cracking`, `patching`, `pothole`, `cracking_ratio`, `rutting_max`, `rutting_average`, `iri`, `mci`, `note`, @process_id ) SET process_id = '" . $process_id . "'", addslashes($csv));
	 //    	$total_rows = \DB::connection()->getpdo()->exec($query);

	 //    	$csv = public_path($image_source);
	 //        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE image_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 0 LINES (`section_id`, `image_id`, `road_category`, `route_number`, `road_number_supplement`, `branch_number`, `direction`, `survey_lane`, `latitude`, `longitude`, `height`, `image_path`, @process_id ) SET process_id = '" . $process_id . "'", addslashes($csv));

	 //    	$total_img_rows = \DB::connection()->getpdo()->exec($query);

		//     \DB::commit();
	 //        echo 'pc: ', $total_rows, 'images: ', $total_img_rows;
		// }
		// catch (\Exception $e)
		// {
		// 	\DB::rollBack();
		// 	dd($e);
		// }
	}

	private function _getReportIndex($ri_flg = null, $pc_flg = null, $mh_flg = null, $index, $data)
	{
		$data = $data->filter(function($d) use($ri_flg, $pc_flg, $mh_flg) {
			$ri_condition = true;
			if (isset($ri_flg))
			{
				$ri_condition = ($d->ri_flg == $ri_flg);
			}
			$pc_condition = true;
			if (isset($pc_flg))
			{
				$pc_condition = ($d->pc_flg == $pc_flg);
			}
			$mh_condition = true;
			if (isset($mh_flg))
			{
				$mh_condition = ($d->mh_flg == $mh_flg);
			}
			return ($ri_condition && $pc_condition && $mh_condition);
   		});
		
		$rsl = 0;
		foreach ($data as $d) 
		{
			$rsl+= $d->{$index};
		}
		return $rsl;
	}

	function getReport($year)
	{
		for ($index = 1; $index <= 4; $index++)
		{
			$sb = \App\Models\tblOrganization::where('parent_id', $index)->get()->pluck('id')->toArray();

			$dataset = \DB::table('tblPMS_dataset_info')
				->select(\DB::raw('count(*) as total, (0.001 * sum(section_length)) as total_length, pc_flg, ri_flg, mh_flg'))
				->where('year_of_dataset', $year)
				->whereIn('sb_id', $sb)
				->groupBy('pc_flg', 'ri_flg', 'mh_flg')
				->get();

			$pc_total[$index] = $this->_getReportIndex(null, 1, null, 'total', $dataset);
        	$pc_total_length[$index] = $this->_getReportIndex(null, 1, null, 'total_length', $dataset);
        	
	        $ri_total[$index] = $this->_getReportIndex(1, null, null, 'total', $dataset);
	        $ri_total_length[$index] = $this->_getReportIndex(1, null, null, 'total_length', $dataset);

	        $mh_total[$index] = $this->_getReportIndex(null, null, 1, 'total', $dataset);
	        $mh_total_length[$index] = $this->_getReportIndex(null, null, 1, 'total_length', $dataset);
	        $pc_match_ri_total_length[$index] = $this->_getReportIndex(1, 1, null, 'total_length', $dataset);
	        
	        $data[$index] = [];
	        $total[$index] = 0;
	        $total_wp[$index] = 0;
	        $total_budget[$index] = 0;

	        for ($i=1; $i >= 0; $i--)
	        {
	            // $i => ri;
	            for ($j=1; $j>=0; $j--)
	            {
	            	// $j => pc
	                for($k=1; $k>=0; $k--)
	                {       
	                	// $k => mh
	                    if ($i == 1 || $j == 1 || $k == 1)
	                    {
	                        $key = $i. "-" . $j. "-" .$k;

	                        $data[$index][$key] = [
	                            // 'total' => tblPMSDatasetInfo::whereIn('sb_id', $sb)->getSectionDataWithYear($i, $j, $k, $year)->sum('section_length')/1000,
	                        	'total' => $this->_getReportIndex($i, $j, $k, 'total_length', $dataset),
	                            'wp' => ($i == 1 && $j == 1) ? $this->_getReportIndex($i, $j, $k, 'total_length', $dataset) : 0,
	                            'budget' => !($i == 0 && $j == 1) ? $this->_getReportIndex($i, $j, $k, 'total_length', $dataset) : 0,
	                        ];
	                        $total[$index] += $data[$index][$key]['total'];
							$total_wp[$index] += $data[$index][$key]['wp'];
							$total_budget[$index] += $data[$index][$key]['budget'];
	                    }
	                }
	            }
	        }
		}

		return view('front-end.pms_dataset.report', [
			'year' => $year,
			'pc_total' => $pc_total,
			'pc_total_length' => $pc_total_length,
			'ri_total' => $ri_total,
			'ri_total_length' => $ri_total_length,
			'mh_total' => $mh_total,
			'mh_total_length' => $mh_total_length,
			'pc_match_ri_total_length' => $pc_match_ri_total_length,
			'data' => $data,
			'total' => $total,
			'total_wp' => $total_wp,
			'total_budget' => $total_budget,
		]);	
	}

	function exportDataReport($index, $year, $type, Request $request)
	{
		if ($type == 0)
		{
			$ri_flg = 0;
			$pc_flg = 1;
			$file_name = "PMS Dataset ". $year ." - PC not match RI.xlsx";
		}
		else if ($type == 1)
		{
			$ri_flg = 1;
			$pc_flg = 0;
			$file_name = "PMS Dataset ". $year ." - RI not match PC.xlsx";
		}

		$direction = [
			1 => trans('back_end.left'),
			2 => trans('back_end.right'),
			3 => trans('back_end.single')
		];
		$organizations = tblOrganization::where('level', 3)->get();
		$rmb = [];
		$sb = [];
		foreach ($organizations as $item)
		{
			$sb[$item->id] = $item->organization_name;
			$rmb[$item->id] = @$item->rmb()->first()->organization_name;
		}

		$dataset = [];
		tblPMSDatasetInfo::has('sectioning.branch')->with('sectioning.branch')
			->getSectionDataByRMB($index)
			->where('year_of_dataset', $year)
			->where('ri_flg', $ri_flg)
			->where('pc_flg', $pc_flg)
			->chunk(5000, function ($data) use (&$dataset, $direction, $rmb, $sb) {
				foreach ($data as $item)
				{
					$rec = $item->sectioning;
					$dataset[] = [
						'km_from' => $rec->km_from,
						'm_from' => $rec->m_from,
						'km_to' => $rec->km_to,
						'm_to' => $rec->m_to,
						'direction' => $direction[$rec->direction],
						'lane_pos_no' => $rec->lane_pos_no,
						'branch_name' => $rec->branch->name,
						'rmb' => $rmb[$item->sb_id],
						'sb' => $sb[$item->sb_id]
					];
				}

			});
		$template = public_path('excel_templates/pms_dataset_template.xlsx');
		include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
		$xlsx = new \eiseXLSX($template);
		$xml = $xlsx->arrXMLs['/xl/worksheets/sheet1.xml'];
		foreach ($dataset as $row)
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
		$xlsx->arrXMLs['/xl/worksheets/sheet1.xml'] = $xml;
      	setcookie(
	        'fileDownloadToken',
	        $request->downloadTokenValue,
	        time() + 60*60,            // expires January 1, 2038
	        "/",                   // your path
	        $_SERVER["HTTP_HOST"], // your domain
	        false,               // Use true over HTTPS
	        false              // Set true for $AUTH_COOKIE_NAME
	    );
	    
        $xlsx->Output($file_name, "D");

	}
}