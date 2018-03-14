<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use DB, Config, Helper, Auth, Excel, App, Session;
use Illuminate\Routing\Redirector;
use Yajra\Datatables\Datatables;
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
use App\Models\tblDeteriorationOrganization;
use App\Models\tblConditionRank;
use Carbon\Carbon;

class HistoryController extends DeteriorationController
{
    public function index()
	{
		return view('front-end.deterioration.manage_history');
	}
	
	public function destroy($id)
	{
		$data = tblDeterioration::destroy($id);
		return redirect()->back();
	}

	public function show($id)
	{
		$data = tblDeterioration::find($id);
		// $status = $data->status ;
		return redirect()->route('data.summary', [$id]);
	}
	
	function data()
	{
		// $rec = DB::table('tblDeterioration')
		// 	->join('tblOrganization', 'tblOrganization.id', '=', 'tblDeterioration.organization_id')
		// 	->join('users', 'users.id', '=', 'tblDeterioration.created_by')
		// 	->select('tblDeterioration.id', 'tblDeterioration.year_of_dataset as year_of_dataset', 'tblDeterioration.created_at as created_at', 'tblDeterioration.status as status', 'users.name as user_create', 'tblOrganization.name_en', 'tblOrganization.name_vn')
   //          ->where('condition_rank', '!=', NULL)
			// ->where('deleted_at', '=', NULL)
            // ->get();

        $rec = App\Models\tblDeterioration::with('organizations')
        	->where('created_by', \Auth::user()->id)
        	->get();
     	
        return Datatables::of($rec)
        	->addColumn('progress', function($d) {
        		$total = 3 + 6 + 6 + 6;
        		// dd($d);
        		$completed = $d->benchmark_flg + $d->pav_type_flg + $d->route_flg + $d->section_flg;
                $percent = round(100 * $completed/$total);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })

	  //       ->editColumn('status', function ($rec) {
			// 	if ($rec->status == 1) 
			// 	{
			// 		return trans('deterioration.complete');
			// 	}
			// 	else
			// 	{
			// 		return trans('deterioration.running');
			// 	}
			// })		
			->addColumn('action', function ($rec) {
				$actions = [];
				$actions[] = \Form::lbButton(route('deterioration.history.view', [$rec->id]), 'GET', trans('deterioration.view'), ["class" => "btn btn-xs btn-warning"])->toHtml();

				if ($rec->dataset_flg == 0 && \Helper::getMonthDiff(Carbon::now(), $rec->created_at) >= 1)
				{
					$actions[] = view('custom.del_btn')->with([
							'route' => ['deterioration.history.delete', $rec->id], 
							'title' => trans('deterioration.delete'), 
							'confirm' => trans('deterioration.are_you_sure')
						])->render();
				}
				return implode(' ', $actions);
            })
		    ->make(true);
	}
	
	function getCoreDataset()
	{
		return view('front-end.deterioration.core_dataset');
	}
}



