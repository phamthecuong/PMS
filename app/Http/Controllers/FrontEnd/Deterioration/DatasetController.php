<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use DB, Config, Helper, Auth, Excel, App, Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\tblOrganization;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataTV;
use App\Models\tblRoad;
use App\Models\tblSegment; 
use App\Models\tblBudgetSimulation;
use App\Models\tblBudgetSimulationRoad;
use App\Models\tblBudgetSimulationOrganization;
use App\Models\tblDeterioration;
use App\Models\tblRepairMatrix;
use App\Models\mstRepairMethod;
use App\Models\tblConditionRank;
use App\Models\tblNotification;

class DatasetController extends DeteriorationController
{
    public function index()
	{
		$org_lvl2 = $this->_getOrgLvl2();
		$year = $this->_getAvailableYear();
		return view('front-end.deterioration.dataset_import')->with(array(
			'region' => $org_lvl2,
			'year' => $year
		));
	}
	
	/**
	 * @para $request 
 	 * 1: year in view('front-end.deterioration.dataset_import')
 	 * 2: list_region[] in view('front-end.deterioration.dataset_import') list organization
	 */
	public function postInit(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$lang = (App::getLocale() == 'en') ? 'en' : 'vn';
			$org = tblOrganization::findOrFail($request->list_region);
			
			$det_name_en = $org->name_en . '-' . $request->year;
			$det_name_vn = $org->name_vn . '-' . $request->year;

			$rec = new tblDeterioration();
			$rec->year_of_dataset = $request->year;
			$rec->created_by = Auth::user()->id;
			$rec->organization_id = $org->id;
			$rec->name_en = $det_name_en;
			$rec->name_vn = $det_name_vn;
			$rec->save();
			
			$this->_createApplication($rec->id);
			\DB::commit();
			return response()->json(array(
				'code' => 200,
				'deterioration_id' => $rec->id
			));
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}
	}

	private function _createApplication($session_id)
	{
		\Helper::recurseCopy('../public/application/core/deterioration/', '../public/application/process/deterioration/' . $session_id . '/');
		\Helper::chmodr('../public/application/process/deterioration/' . $session_id . '/', 0777, 0777);
	}
	
	public function dataSummary($session_id)
	{
		$show_history = FALSE;
		try
		{
			$lang = (App::getLocale() == 'en') ? 'en' : 'vn';
			$det = tblDeterioration::findOrFail($session_id);

			if (isset($det->condition_rank))
			{
				$show_history = TRUE;
				\Session::put("history-{$session_id}", 1);
			}

			$year = $det->year_of_dataset;
			$org = tblOrganization::findOrFail($det->organization_id);

			$condition_data = $this->_prepareDataForCondition($det);
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e);
		}
		
		return view('front-end.deterioration.data_summary')->with([
			'crack_data' => $condition_data['crack_data'],
			'crack_data_json' => json_encode($condition_data['crack_data']),
			'rut_data' => $condition_data['rut_data'], 
			'rut_data_json' => json_encode($condition_data['rut_data']),
			'iri_data' => $condition_data['iri_data'],
			'iri_data_json' => json_encode($condition_data['iri_data']),
			'total_ac_after' => $condition_data['total_ac_after'],
			'total_bt_after' => $condition_data['total_bt_after'],
			'total_cc_after' => $condition_data['total_cc_after'],
			"rut_total_ac_after" => $condition_data['rut_total_ac_after'],
			"rut_total_bt_after" => $condition_data['rut_total_bt_after'],
			"rut_total_cc_after" => $condition_data['rut_total_cc_after'],
			"iri_total_ac_after" => $condition_data['iri_total_ac_after'],
			"iri_total_bt_after" => $condition_data['iri_total_bt_after'],
			"iri_total_cc_after" => $condition_data['iri_total_cc_after'],
			'length_crack' => $condition_data['length_crack'],
			'length_rut' => $condition_data['length_rut'],
			'length_iri' => $condition_data['length_iri'],
			'year' => $year,		
	        'organization' => $org->{"name_{$lang}"},
	        'session_id' => $session_id,
	        'history' => $show_history,
	        'crack_selected_rank' => $condition_data['crack_selected_rank'],
	        'rut_selected_rank' => $condition_data['rut_selected_rank'],
	        'iri_selected_rank' => $condition_data['iri_selected_rank']
		]);

	}

	// get value $session_id = tblDeterioration.id in  view('front-end.deterioration.data_summary') when click back
	public function postBack($session_id)
	{
		DB::table('tblDeterioration')->where('id', $session_id)->delete();
	}

	// get value $session_id = tblDeterioration.id in  view('front-end.deterioration.data_summary') when click estimation
	public function postDataSummary(Request $request , $session_id)
	{
		\DB::beginTransaction();
		try
		{
			// prevent double run
			$rec = tblDeterioration::findOrFail($session_id);
			if (isset($rec->condition_rank))
			{
				return redirect()->route('deterioration.benchmarking', ['session_id' => $session_id]);
			}

			// run benchmark --------------------job group 1-----------run 001--------index = 1-----------------
			$crack_11 = (new \App\Jobs\crack_11($session_id))->onQueue('deterioration_crack');
			dispatch($crack_11);
			
			$rut_11 = (new \App\Jobs\rut_11($session_id))->onQueue('deterioration_rut');
			dispatch($rut_11);
			
			$iri_11 = (new \App\Jobs\iri_11($session_id))->onQueue('deterioration_iri');
			dispatch($iri_11);

			$condition_rank_data = tblConditionRank::all();
				
			$condition_rank = json_encode($condition_rank_data);
			// get value 3 table in vieu 
			// get data crack in database curent ---------------------------------------
			$crack_rank = tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
			$rut_rank = tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
			$iri_rank = tblConditionRank::where('target_type', 3)->orderBy('rank')->get();


			// data will save in $tblDeterioration->crack_summary_table_data ------------------
			$crack_data_json = $request->crack_data_json;
			$rut_data_json = $request->rut_data_json;
			$iri_data_json = $request->iri_data_json;
			
			// get  number checkbox of crack in view
			$crack_selected_rank = 1;
			while ($request->{'checkboxCrack'.$crack_selected_rank} != NULL)
			{
				$crack_selected_rank += 1; 
			}

			// get number checkbox of rut in view
			$rut_selected_rank = 1;
			while ($request->{'checkboxRut'.$rut_selected_rank} != NULL)
			{
				$rut_selected_rank += 1; 
			}

			// get number checkbox of iri in view
			$iri_selected_rank = 1;
			while ($request->{'checkboxIri'.$iri_selected_rank} != NULL)
			{
				$iri_selected_rank += 1; 
			}
			
			// update value in tblDeterioration where id = session_id
			$tblDeterioration = tblDeterioration::find($session_id);
	        $notification = new tblNotification;
	        $notification->type = 1;
	        $notification->reference_id = $session_id;
	        $notification->user_id = Auth::user()->id;
	        $notification->status_notification = 0;
	        $notification->status_process = $tblDeterioration->status;
	        $notification->percent = (int)(($tblDeterioration->benchmark_flg + $tblDeterioration->pav_type_flg + $tblDeterioration->route_flg + $tblDeterioration->section_flg)*100/21);
	        $notification->save();
			// 
			$tblDeterioration->crack_summary_table_data = $crack_data_json;
			$tblDeterioration->crack_selected_rank = $crack_selected_rank-1;
			// 
			$tblDeterioration->rut_summary_table_data = $rut_data_json;
			$tblDeterioration->rut_selected_rank = $rut_selected_rank-1;
			// 
			$tblDeterioration->iri_summary_table_data = $iri_data_json;
			$tblDeterioration->iri_selected_rank  = $iri_selected_rank-1;
			// 
			$tblDeterioration->condition_rank = $condition_rank;
			$tblDeterioration->save();
			
			// write to file
			Excel::load('../public/application/process/deterioration/' . $session_id . '/crack/condition/rank.csv', function($reader) use ($crack_rank, $crack_selected_rank) {
	            $reader->sheet(0, function($sheet) use ($crack_rank, $crack_selected_rank) {
	                $sheet->cell('A1', function($cell) use ($crack_rank) {
	                    $cell->setValue(count($crack_rank));
	                });
	                $sheet->cell('A2', function($cell) use ($crack_selected_rank) {
	                    $cell->setValue($crack_selected_rank-1);
	                });
	            });
	        })->store('csv', '../public/application/process/deterioration/' . $session_id . '/crack/condition/');
	        Excel::load('../public/application/process/deterioration/' . $session_id . '/rut/condition/rank.csv', function($reader) use ($rut_rank, $rut_selected_rank) {
	            $reader->sheet(0, function($sheet) use ($rut_rank, $rut_selected_rank) {
	                $sheet->cell('A1', function($cell) use ($rut_rank) {
	                    $cell->setValue(count($rut_rank));
	                });
	                $sheet->cell('A2', function($cell) use ($rut_selected_rank) {
	                    $cell->setValue($rut_selected_rank-1);
	                });
	            });
	        })->store('csv', '../public/application/process/deterioration/' . $session_id . '/rut/condition/');
	        Excel::load('../public/application/process/deterioration/' . $session_id . '/IRI/condition/rank.csv', function($reader) use ($iri_rank, $iri_selected_rank) {
	            $reader->sheet(0, function($sheet) use ($iri_rank, $iri_selected_rank) {
	                $sheet->cell('A1', function($cell) use ($iri_rank) {
	                    $cell->setValue(count($iri_rank));
	                });
	                $sheet->cell('A2', function($cell) use ($iri_selected_rank) {
	                    $cell->setValue($iri_selected_rank-1);
	                });
	            });
	        })->store('csv', '../public/application/process/deterioration/' . $session_id . '/IRI/condition/');

	        // generate input.csv
			$this->_writeCSV($tblDeterioration, 'application/process/deterioration/' . $session_id . '/crack/data/input.csv', 'C', $crack_rank);
			$this->_writeCSV($tblDeterioration, 'application/process/deterioration/' . $session_id . '/rut/data/input.csv', 'R', $rut_rank);
			$this->_writeCSV($tblDeterioration, 'application/process/deterioration/' . $session_id . '/IRI/data/input.csv', 'I', $iri_rank);

	        \DB::commit();
	        return redirect()->route('deterioration.benchmarking', ['session_id' => $session_id]);	
	    }
	    catch (\Exception $e)
	    {
	    	\DB::rollBack();
	    	dd($e);
	    }
	}

	private function _convertToLookUp($data)
	{
		$dataset = [];
		foreach ($data as $d) 
		{
			$dataset+= [
				(string)$d->from => $d->rank
			];
		}
		return $dataset;
	}

	private function _writeCSV($det, $path, $key = 'C', $rank)
	{
		$surfaces = [
            'AC' => 1,
            'BST' => 2,
            'CC' => 3
        ];
		$date = "{$det->year_of_dataset}-12-31";
		$rank = $this->_convertToLookUp($rank);
		$sbs = tblOrganization::where('parent_id', $det->organization_id)->get()->pluck('id')->toArray(); 
		$dataset = [
			["before_{$key}", "after_{$key}", "Interval", "C", "Latest_Pavement_type", "Route_ID", "Section_ID2"]
		];
		$latest_key;
		$latest2_key;
		if ($key == 'C')
		{
			$latest_key = 'latest_cracking_ratio';
			$latest2_key = 'latest2_cracking_ratio';
		}
		else if ($key == 'R')
		{
			$latest_key = 'latest_rutting_max';
			$latest2_key = 'latest2_rutting_max';
		}
		else
		{
			$latest_key = 'latest_IRI';
			$latest2_key = 'latest2_IRI';
		}
 		\App\Models\tblPMSDatasetInfo::whereIn('case', [1, 2, 3])
			->has('sectioning')
			->where('year_of_dataset', $det->year_of_dataset)
			->whereNotNull('latest_condition_year')
			->whereNotNull('latest_condition_month')
			->whereNotNull('latest2_condition_year')
			->whereNotNull('latest2_condition_month')
			->whereNotNull('latest_pavement_type')
			->whereNotNull('latest_cracking_ratio')
			->whereNotNull('latest_rutting_max')
			->whereNotNull('latest_IRI')
			->whereNotNull('latest2_cracking_ratio')
			->whereNotNull('latest2_rutting_max')
			->whereNotNull('latest2_IRI')
			->whereRaw('(latest_condition_year+(latest_condition_month-1)/12) - (latest2_condition_year+(latest2_condition_month-1)/12) >= 0.1')
			// ->whereHas('latestSegment', function($query) use($date, $sbs){
			// 	$query->whereRaw('(updated_at is null or updated_at <= "' . $date . '")')
			// 		->whereIn('SB_id', $sbs);
			// })
			->whereRaw('latest_cracking_ratio >= latest2_cracking_ratio')
			->whereRaw('latest_rutting_max >= latest2_rutting_max')
			->whereRaw('latest_IRI >= latest2_IRI')
			->whereBetween('latest_cracking_ratio', [0, 100])
			->whereBetween('latest2_cracking_ratio', [0, 100])
			->whereIn('sb_id', $sbs)
			->chunk(10000, function($rec) use(&$dataset, $rank, $surfaces, $latest_key, $latest2_key) {
				foreach ($rec as $r) 
				{
					$dataset[] = [
				    	\Helper::vlookup(floatval($r->{$latest2_key}), $rank),
				    	\Helper::vlookup(floatval($r->{$latest_key}), $rank),
				    	($r->latest_condition_year+($r->latest_condition_month-1)/12) - ($r->latest2_condition_year+($r->latest2_condition_month-1)/12),
				    	1,
				    	$surfaces[$r->latest_pavement_type],
				    	$r->route_id,
				    	$r->section_id2
				    ];
				}
			});
		
		\Helper::writeCSV($dataset, $path);
	}


	private function _getOrgLvl2()
	{
		$RMB = tblOrganization::where('level', 2)->get(); 
		$data = array();
		$lang = (App::getLocale() == 'en') ? 'en' : 'vn';
		foreach ($RMB as $key => $value) {
			$data[] = [
				'name' => $value->{"name_$lang"},
				'value' => $value->id
			];
		}
		return $data;
	}

	/**
	 * get available year in PMS Dataset
	 */
	private function _getAvailableYear()
	{
		$data = array();
		$rec = \App\Models\tblPMSDataset::whereRaw('completed_segment = total_segment')
			->orderBy('year', 'desc')
			->get()
			->pluck('year');
		foreach ($rec as $r) 
		{
			$data[] = [
				'name' => $r,
				'value' => $r
			];
		}
		return $data;
	}

	private function _initialRankArray($rank_data, $key)
	{
		$dataset = [];
		foreach ($rank_data as $dnd)
		{
			$dataset[] = [
				'rank' => $dnd->rank,
				'condition' => Helper::convertConditionInforToText($dnd->from , $dnd->to , $key),
				// cc
				'CC_before'  => 0,
				'CC_after'   => 0,
				// AC
				'AC_before'  => 0,
				'AC_after'   => 0,
				// BST
				'BT_before'  => 0,
				'BT_after'   => 0,
			];
		}
		return $dataset;
	}

	private function _prepareDataForCondition($det)
	{
		$sbs = tblOrganization::where('parent_id', $det->organization_id)->get()->pluck('id')->toArray(); 

		$total_ac_after = 0;
		$total_bt_after = 0;
		$total_cc_after = 0;
		$rut_total_ac_after = 0;
		$rut_total_bt_after = 0;
		$rut_total_cc_after = 0;
		$iri_total_ac_after = 0;
		$iri_total_bt_after = 0;
		$iri_total_cc_after = 0;
		$crack_selected_rank = 7;
		$iri_selected_rank = 7;
		$rut_selected_rank = 7;

		if (isset($det->condition_rank))
		{
			$crack = json_decode($det->crack_summary_table_data);
			foreach ($crack as $key => $value) 
			{
				$total_ac_after  += $value->AC_after;
				$total_bt_after  += $value->BT_after;
				$total_cc_after  += $value->CC_after;
			}
			//
			$crack_data = [];
			foreach ($crack as $key => $value) {
				$crack_data[] = (array) $value;
				// var_dump($value);
			}
			$iri = json_decode($det->iri_summary_table_data);
			foreach ($iri as $key => $value) 
			{	
				$iri_total_ac_after  += $value->AC_after;
				$iri_total_bt_after  += $value->BT_after;
				$iri_total_cc_after  += $value->CC_after;
			}
			$iri_data = [];
			foreach ($iri as $key => $value) {
				$iri_data[] = (array) $value;
			}
			
			$rut = json_decode($det->rut_summary_table_data);
			foreach ($rut as $key => $value) 
			{	
				$rut_total_ac_after  += $value->AC_after;
				$rut_total_bt_after  += $value->BT_after;
				$rut_total_cc_after  += $value->CC_after;
			}
			$rut_data = [];
			foreach ($rut as $key => $value) 
			{
				$rut_data[] = (array) $value;
			}
			
			$length_crack = (count($crack_data));
			$length_rut = (count($rut_data));
			$length_iri = (count($iri_data));
			
			for ($i=0; $i < $length_rut; $i++) 
			{ 
				$rut_total_ac_after  += 0;
				$rut_total_bt_after  += 0;
				$rut_total_cc_after  += 0;
			}
			
			$crack_selected_rank = $det->crack_selected_rank;
			$iri_selected_rank = $det->iri_selected_rank;
			$rut_selected_rank = $det->rut_selected_rank;
		}
		else
		{
			$crack = tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
			$crack_data = $this->_initialRankArray($crack, 'C');
			$crack = $this->_convertToLookUp($crack);
			// data ac bt cc 
			$total_ac_after = 0;
			$total_bt_after = 0;
			$total_cc_after = 0;
			
			// get data rut
			$rut = tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
			$rut_data = $this->_initialRankArray($rut, 'R');
			$rut = $this->_convertToLookUp($rut);
			// data ac bt cc 
			$rut_total_ac_after = 0;
			$rut_total_bt_after = 0;
			$rut_total_cc_after = 0;
			
			// get iri data
			$iri = tblConditionRank::where('target_type', 3)->orderBy('rank')->get();
			$iri_data = $this->_initialRankArray($iri, 'IRI');
			$iri = $this->_convertToLookUp($iri);
			// data ac bt cc
			$iri_total_ac_after = 0;
			$iri_total_bt_after = 0;
			$iri_total_cc_after = 0;

			$date = "{$det->year_of_dataset}-12-31";
			\App\Models\tblPMSDatasetInfo::whereIn('case', [1, 2, 3])
				->has('sectioning')
				->where('year_of_dataset', $det->year_of_dataset)
				->whereNotNull('latest_condition_year')
				->whereNotNull('latest_condition_month')
				->whereNotNull('latest2_condition_year')
				->whereNotNull('latest2_condition_month')
				->whereNotNull('latest_pavement_type')
				->whereNotNull('latest_cracking_ratio')
				->whereNotNull('latest_rutting_max')
				->whereNotNull('latest_IRI')
				->whereNotNull('latest2_cracking_ratio')
				->whereNotNull('latest2_rutting_max')
				->whereNotNull('latest2_IRI')
				->whereRaw('(latest_condition_year+(latest_condition_month-1)/12)-(latest2_condition_year+(latest2_condition_month-1)/12) >= 0.1')
				->whereBetween('latest_cracking_ratio', [0, 100])
				->whereBetween('latest2_cracking_ratio', [0, 100])
				->whereRaw('latest_cracking_ratio >= latest2_cracking_ratio')
				->whereRaw('latest_rutting_max >= latest2_rutting_max')
				->whereRaw('latest_IRI >= latest2_IRI')
				->whereHas('latestSegment', function($query) use($date, $sbs) {
					$query->whereRaw('(updated_at is null or updated_at <= "' . $date . '")')
						->whereIn('SB_id', $sbs);
				})
				->chunk(10000, function($records) use(
						$crack, $rut, $iri,
						&$iri_data, &$crack_data, &$rut_data, 
						&$total_ac_after, &$total_bt_after, &$total_cc_after,
						&$rut_total_ac_after, &$rut_total_bt_after, &$rut_total_cc_after,
						&$iri_total_ac_after, &$iri_total_bt_after, &$iri_total_cc_after
					) {
					foreach ($records as $rec) 
					{
						if (floatval($rec->latest_cracking_ratio) < 0) continue;
						switch ($rec->latest_pavement_type) 
						{
						 	case 'AC':
						 		$index = \Helper::vlookup(floatval($rec->latest_cracking_ratio), $crack);
						 		$crack_data[$index - 1]['AC_after']+= 1;
								$total_ac_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_cracking_ratio), $crack);
								$crack_data[$index - 1]['AC_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_rutting_max), $rut);
								$rut_data[$index - 1]['AC_after']+= 1;
								$rut_total_ac_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_rutting_max), $rut);
								$rut_data[$index - 1]['AC_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_IRI), $iri);
								$iri_data[$index - 1]['AC_after']+= 1;
								$iri_total_ac_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_IRI), $iri);
								$iri_data[$index - 1]['AC_before']+= 1;
						 		break;
						 	case 'BST':
						 		$index = \Helper::vlookup(floatval($rec->latest_cracking_ratio), $crack);
								$crack_data[$index - 1]['BT_after']+= 1;
								$total_bt_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_cracking_ratio), $crack);
								$crack_data[$index - 1]['BT_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_rutting_max), $rut);
								$rut_data[$index - 1]['BT_after']+= 1;
								$rut_total_bt_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_rutting_max), $rut);
								$rut_data[$index - 1]['BT_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_IRI), $iri);
								$iri_data[$index - 1]['BT_after']+= 1;
								$iri_total_bt_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_IRI), $iri);
								$iri_data[$index - 1]['BT_before']+= 1;
						 		break;
						 	case 'CC':
						 		$index = \Helper::vlookup(floatval($rec->latest_cracking_ratio), $crack);
								$crack_data[$index - 1]['CC_after']+= 1;
								$total_cc_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_cracking_ratio), $crack);
								$crack_data[$index - 1]['CC_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_rutting_max), $rut);
								$rut_data[$index - 1]['CC_after']+= 1;
								$rut_total_cc_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_rutting_max), $rut);
								$rut_data[$index - 1]['CC_before']+= 1;

								$index = \Helper::vlookup(floatval($rec->latest_IRI), $iri);
								$iri_data[$index - 1]['CC_after']+= 1;
								$iri_total_cc_after+= 1;
								$index = \Helper::vlookup(floatval($rec->latest2_IRI), $iri);
								$iri_data[$index - 1]['CC_before']+= 1;
						 		break;
						 	default:
						 		break;
						}
						
					}
				});
 			
			// foreach ($iri as $dnd)
			// {
			// 	$iri_ac_after = $this->_calculateCount($det, $dnd, 'AC', 'latest_IRI', $sbs);
			// 	$iri_bt_after = $this->_calculateCount($det, $dnd, 'BST', 'latest_IRI', $sbs);
			// 	$iri_cc_after = $this->_calculateCount($det, $dnd, 'CC', 'latest_IRI', $sbs);
			// 	$iri_ac_before = $this->_calculateCount($det, $dnd, 'AC', 'latest2_IRI', $sbs);
			// 	$iri_bt_before = $this->_calculateCount($det, $dnd, 'BST', 'latest2_IRI', $sbs);
			// 	$iri_cc_before = $this->_calculateCount($det, $dnd, 'CC', 'latest2_IRI', $sbs);

			// 	$length_iri += 1;
			// 	$iri_total_ac_after += $iri_ac_after;
			// 	$iri_total_bt_after += $iri_bt_after;
			// 	$iri_total_cc_after += $iri_cc_after;
			// 	$iri_data[] = [
			// 		'rank' => $dnd->rank,
			// 		'condition' => Helper::convertConditionInforToText($dnd->from , $dnd->to , 'IRI'),
			// 		// cc
			// 		'CC_before'  => $iri_cc_before,
			// 		'CC_after'   => $iri_cc_after,
			// 		// AC
			// 		'AC_before'  => $iri_ac_before,
			// 		'AC_after'   => $iri_ac_after,
			// 		// BT
			// 		'BT_before'  => $iri_bt_before,
			// 		'BT_after'   => $iri_bt_after,
			// 	];
			// }
			// foreach ($rut as $dnd)
			// {
			// 	$rut_ac_after = $this->_calculateCount($det, $dnd, 'AC', 'latest_rutting_max', $sbs);
			// 	$rut_bt_after = $this->_calculateCount($det, $dnd, 'BST', 'latest_rutting_max', $sbs);
			// 	$rut_cc_after = $this->_calculateCount($det, $dnd, 'CC', 'latest_rutting_max', $sbs);
			// 	$rut_ac_before = $this->_calculateCount($det, $dnd, 'AC', 'latest2_rutting_max', $sbs);
			// 	$rut_bt_before = $this->_calculateCount($det, $dnd, 'BST', 'latest2_rutting_max', $sbs);
			// 	$rut_cc_before = $this->_calculateCount($det, $dnd, 'CC', 'latest2_rutting_max', $sbs);

			// 	$length_rut += 1;
			// 	$rut_total_ac_after  += $rut_ac_after;
			// 	$rut_total_bt_after  += $rut_bt_after;
			// 	$rut_total_cc_after  += $rut_cc_after;
			// 	$rut_data[] = [
			// 		// get data condition rank 
			// 		'rank'       => $dnd->rank,
			// 		'condition'  => Helper::convertConditionInforToText($dnd->from , $dnd->to , 'R'),
			// 		// cc
			// 		'CC_before'  => $rut_cc_before,
			// 		'CC_after'   => $rut_cc_after,
			// 		// AC
			// 		'AC_before'  => $rut_ac_before,
			// 		'AC_after'   => $rut_ac_after,
			// 		// BT
			// 		'BT_before'  => $rut_bt_before,
			// 		'BT_after'   => $rut_bt_after,
			// 	];
			// }
			// foreach ($crack as $dnd)
			// {
			// 	$ac_after = $this->_calculateCount($det, $dnd, 'AC', 'latest_cracking_ratio', $sbs);
			// 	$bt_after = $this->_calculateCount($det, $dnd, 'BST', 'latest_cracking_ratio', $sbs);
			// 	$cc_after = $this->_calculateCount($det, $dnd, 'CC', 'latest_cracking_ratio', $sbs);
			// 	$ac_before = $this->_calculateCount($det, $dnd, 'AC', 'latest2_cracking_ratio', $sbs);
			// 	$bt_before = $this->_calculateCount($det, $dnd, 'BST', 'latest2_cracking_ratio', $sbs);
			// 	$cc_before = $this->_calculateCount($det, $dnd, 'CC', 'latest2_cracking_ratio', $sbs);
			// 	$length_crack += 1;
			// 	$total_ac_after  += $ac_after;
			// 	$total_bt_after  += $bt_after;
			// 	$total_cc_after  += $cc_after;
			// 	$crack_data[] = [
					
			// 		'rank' => $dnd->rank,
			// 		'condition' => Helper::convertConditionInforToText($dnd->from , $dnd->to , 'C'),
			// 		// cc
			// 		'CC_before'  => $cc_before,
			// 		'CC_after'   => $cc_after,
			// 		// AC
			// 		'AC_before'  => $ac_before,
			// 		'AC_after'   => $ac_after,
			// 		// BT
			// 		'BT_before'  => $bt_before,
			// 		'BT_after'   => $bt_after,
			// 	];
			// }
		}
		return [
			'crack_selected_rank' => $crack_selected_rank,
			'iri_selected_rank' => $iri_selected_rank,
			'rut_selected_rank' => $rut_selected_rank,
			'crack_data' => $crack_data,
			'rut_data' => $rut_data,
			'iri_data' => $iri_data,
			'total_ac_after' => $total_ac_after,
			'total_bt_after' => $total_bt_after,
			'total_cc_after' => $total_cc_after,
			"rut_total_ac_after" => $rut_total_ac_after,
			"rut_total_bt_after" => $rut_total_bt_after,
			"rut_total_cc_after" => $rut_total_cc_after,
			"iri_total_ac_after" => $iri_total_ac_after,
			"iri_total_bt_after" => $iri_total_bt_after,
			"iri_total_cc_after" => $iri_total_cc_after,
			'length_crack' => count($crack),
			'length_rut' => count($rut),
			'length_iri' => count($iri),
		];
	}

	// private function _calculateCount($det, $dnd, $pavement_type, $key, $sbs)
	// {
		

		// $from = floatval($dnd->from);
  //       $to = floatval($dnd->to);
        
  //       if ($from == $to )
  //       {
  //           $rec = $rec->where($key, '=', $to);
  //       }
  //       else if ($to == NULL)
  //       {
  //       	$rec = $rec->where($key, '=', $from);
  //       }
  //       else
  //       {
  //       	$rec = $rec->where($key, '>=', $from)
  //       		->where($key, '<', $to);
  //       }
	// 	return $rec->count();
	// }

}
