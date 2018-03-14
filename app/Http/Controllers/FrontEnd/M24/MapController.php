<?php

namespace App\Http\Controllers\FrontEnd\M24;

use Validator, DB, App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tblSectionPC;

class MapController extends Controller
{
	function getDataMap(Request $request)
	{
		$data = tblSectionPC::where('min_lat', '>=', $request->min_lat)
				->where('min_lng', '>=', $request->min_lng)
				->where('max_lat', '<=', $request->max_lat)
				->where('max_lng', '<=', $request->max_lng)
				->select('min_lat', 'min_lng', 'max_lat', 'max_lng', 'points', 'section_code', 'cracking_ratio_total', 'rutting_depth_ave', 'IRI', 'MCI', 'surface_type');

		if ($request->zoom_level <= 13)
		{
			$data = $data
				->where('lane_position_no', 1)
				->whereIn('direction', ['L', 'U'])
				->groupBy(DB::raw('SUBSTR(section_code, 1, 3)'))
				->groupBy(DB::raw('SUBSTR(section_code, 4, 2)'));	
		}

		if ($request->zoom_level <= 4)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 64'));
		}
		else if ($request->zoom_level <= 5)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 32'));
		}
		else if ($request->zoom_level <= 6)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 16'));
		}
		else if ($request->zoom_level <= 7)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 8'));
		}
		else if ($request->zoom_level <= 8)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 4'));
		}
		else if ($request->zoom_level <= 9)
		{
			$data = $data
				->groupBy(DB::raw('km_from DIV 2'));
		}
		else if ($request->zoom_level <= 10)
		{
			$data = $data
				->groupBy('km_from');
		}
		else if ($request->zoom_level <= 11)
		{
			$data = $data
				->groupBy('km_from')
				->groupBy(DB::raw('m_from DIV 500'));
		}
		else if ($request->zoom_level <= 12)
		{
			$data = $data
				->groupBy('km_from')
				->groupBy(DB::raw('m_from DIV 200'));
		}
		else if ($request->zoom_level <= 13)
		{
			$data = $data
				->groupBy('km_from')
				->groupBy(DB::raw('m_from DIV 200'));
		}
		else if ($request->zoom_level <= 16)
		{
			$data = $data
				->where('lane_position_no', 1)
				->whereIn('direction', ['L', 'U']);
		}
		// $loaded_sections = json_decode($request->loaded_sections);
		// if (count($loaded_sections) > 0)
		// {
		// 	$data = $data->whereNotIn('section_code', $loaded_sections);
		// }
		$data = $data->get();
		
		return $data;
	}

	function getLaneData(Request $request)
	{
		$section_code = $request->section_code;
		$route_number = substr($section_code, 1, 3);
		$branch_number = substr($section_code, 7, 2);
		$km = intval(substr($section_code, 12, 4));
		$mode = $request->mode;

		$data = tblSectionPC::whereRaw("SUBSTR(section_code, 1, 3) = '{$route_number}'")
			->whereRaw("SUBSTR(section_code, 7, 2) = '{$branch_number}'");
		switch ($mode) 
		{
			case 0:
				$data = $data->whereBetween("km_from", [$km - 1, $km + 1])
					->orderBy('km_from')
					->orderBy('m_from');
				break;
			case 1:
				$data = $data->where("km_from", ">=", $km)
					->orderBy('km_from')
					->orderBy('m_from')
					->limit(50);
				break;
			case 2:
				$data = $data->where("km_from", "<=", $km)
					->orderBy('km_from', 'DESC')
					->orderBy('m_from', 'DESC')
					->limit(50);
				break;
			default:
				# code...
				break;
		}
		$data = $data->get();
		return $data;
	}
}