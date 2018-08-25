<?php

namespace App\Http\Controllers\FrontEnd\m24;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, DB, App;
use App\Models\tblSectionPC;
use App\Models\tblSectionPCHistory;
use App\Models\tblOrganization;
use Auth, Session;

class DataMapController extends Controller
{
	
	public function webMap()
	{	
		
		// $rec = tblSectionPC::with(secondLatest('2016'));
		// dd($rec);
		return view('front-end.m24.layout.content');
	}
	
	function getLaneData(Request $request)
	{
		//\DB::enableQueryLog();
		$section_code = $request->section_code;
		$route_number = substr($section_code, 1, 3);
		$branch_number = substr($section_code, 7, 2);
		$km = intval(substr($section_code, 12, 4));
		$mode = $request->mode;
		$date_y = $request->year;
		$data = $this->_JoinTable();
		$data = $data->whereRaw("SUBSTR(p.section_code, 2, 3) = '{$route_number}'")
			->whereRaw("SUBSTR(p.section_code, 8, 2) = '{$branch_number}'");
		switch ($mode) 
		{
			case 0:
				$data = $data->whereBetween("p.km_from", [$km - 1, $km + 1])
					->orderBy('p.km_from')
					->orderBy('p.m_from');
				break;
			case 1:
				$data = $data->where("p.km_from", ">=", $km)
					->orderBy('p.km_from')
					->orderBy('p.m_from')
					->limit(50);
				break;
			case 2:
				$data = $data->where("p.km_from", "<=", $km)
					->orderBy('p.km_from', 'DESC')
					->orderBy('p.m_from', 'DESC')
					->limit(50);
				break;
			default:
				# code...
				break;
		}
		if ($date_y == 'latest')
		{	
			$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id ORDER BY date_y DESC LIMIT 0, 1)");
		}
		else if ($date_y == 'second_latest')
		{	
			$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id ORDER BY date_y DESC LIMIT 1, 1)");
		}
		else
		{
			$data = $data->where('h.date_y', $date_y);
		}
		$data = $data->get();
		return $data;
	}

	private function _JoinTable()
	{	
		$data = DB::table('tblSection_PC AS p')
			->select(DB::raw('h.*'))
			->join('tblSection_PC_history AS h', 'p.id', '=', 'h.section_id');
		return $data;
	}

	public function logout()
	{
		Auth::logout();
		return redirect(url('web_map'));
	}


	
}


