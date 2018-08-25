<?php
namespace App\Http\Controllers\Ajax\Map;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblOrganization;
use App\Models\tblSectionPC;
use App\Models\tblBranch;
use App\Models\tblSegment;
use App\Models\tblSectionPCHistory;
use App, Auth, DB;
use App\Classes\Helper;


class IndexController extends Controller
{
	public function getOrganization(Request $request)
	{
		$oi = Auth::user()->organization_id;
		$parent_id = $request->parent_id;
		if ($parent_id == -1)
		{
			if (\Auth::user()->hasRole("userlv1") || \Auth::user()->hasRole("userlvl1p") || Auth::user()->hasRole("superadmin"))
			{
				$data = tblOrganization::whereNotNull('parent_id')->get();
			}
			elseif (\Auth::user()->hasRole("userlv2"))
			{
				$data = tblOrganization::where('parent_id', $parent_id)->get();
			}
		}
		else
		{
			$data = tblOrganization::where('parent_id', $parent_id)->get();
		}
		foreach ($data as $r)
		{	
			$name = (App::getLocale() =='en')?$r->name_en:$r->name_vn;
			$organization[] = ['id'=> $r->id,'name'=> $name];
		}
		return response($organization);
	}

	public function getRouteList(Request $request)
	{	
		// $oi = Auth::user()->organization_id;
		// $data_sb =  tblOrganization::select('id')->where('parent_id', $oi)->get();
		// $sb_id = $request->sb_id;
		// if ($sb_id == -1)
		// {
		// 	if (\Auth::user()->hasRole("userlv1"))
		// 	{
		// 		$segment = tblSegment::with('tblBranch')->get();
		// 	}
		// 	else if(\Auth::user()->hasRole("userlv2"))
		// 	{
		// 		$segment = tblSegment::with('tblBranch')->whereIn('SB_id', $data_sb)->get();
		// 	}
		// }
		// else
		// {
		// 	$segment = tblSegment::with('tblBranch')->where('SB_id', $sb_id)->get();
		// }
		// foreach($segment as $r)
		// {	
		// 	$name = (App::getLocale() =='en') ? $r->tblBranch->name_en : $r->tblBranch->name_vn;
		// 	$route[] = ['id'=> $r->tblBranch->id,'name'=> $name];
		// }
		// return response($route);
		$sb_id = $request->sb_id;
		$lang = (App::isLocale('en')) ? 'en' : 'vn';

		$records = tblBranch::whereHas('segments', function($query) use($sb_id) {
                $query->where('SB_id', $sb_id);
            })
            ->orderBy('branch_number')
            ->get();

        $route = [];
        foreach ($records as $rec) 
        {
        	$route[] = ['id' => $rec->id, 'name'=> $rec->{"name_$lang"}];
        }
        return $route;
	}

	public function getYear(Request $request)
	{
		$branch_id = $request->branch_id;
		$sb_id = $request->sb_id;
		$rmb_id = $request->rmb_id;
		$data = [];
		$data[] = ['value'=> 'latest', 'text' => trans('map.latest_year')];
		$data[] = ['value'=> 'second_latest', 'text'=> trans('map.second_latest_year')];
		$year = tblSectionPCHistory::select('date_y')
		        ->groupBy('date_y')
		        ->orderBy('date_y', 'desc');
		
		if ($rmb_id != -1 && $sb_id == -1)
		{	
			$sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
			if ($branch_id == -1)
			{
				$year = $year->whereIn('sb_id', $sb_id);
			}
			else
			{
				$year = $year->whereIn('sb_id', $sb_id)->where('branch_id', $branch_id);
			}
		}
		else if ($sb_id != -1 )
		{
			if ($branch_id == -1)
			{
				$year = $year->where('SB_id', $sb_id);
			}
			else
			{
				$year = $year->where('branch_id', $branch_id)->where('SB_id', $sb_id);
			}
		}
		else if ($branch_id != -1)
		{
			$year = $year->where('branch_id', $branch_id);
		}
		

		$year = $year->get();
		foreach ($year as $r)
		{
			$data[] = ['value'=> $r->date_y, 'text'=> $r->date_y];
		}
		return response($data);
	}

	public function getCenter()
	{
		return response([
				'lat' => tblSectionPC::where('min_lat', '<>', 99.99999999)->avg('min_lat'),
				'lng' => tblSectionPC::where('min_lat', '<>', 99.99999999)->avg('min_lng')
			]);
	}

	public function mapLogin(Request $request)
	{
		$name = $request->user_name;
		$password = $request->password;
		if (Auth::attempt(['name'=> $name, 'password' => $password])) 
		{
			return ['code'=> 200];
		}
		else
		{
			return ['code' => 500];
		}
	}

	public function searchMap(Request $request)
	{	
		// \DB::enableQueryLog();
		//$data_history = new tblSectionPC();
		$data = $this->JoinTable();
		$data = $this->_filterSection($request, $data);
		// dd($data->toSql());
		// // dd($data->get()->toArray());
		// echo "<pre>";
		// print_r($data->first());
		// echo "</pre>";
		// die;
		// $data = $data->select(
		// 		\DB::raw('avg(h.min_lat) as min_lat'), 
		// 		\DB::raw('avg(h.min_lng) as min_lng'), 
		// 		\DB::raw('avg(h.max_lat) as max_lat'), 
		// 		\DB::raw('avg(h.max_lng) as max_lng'),
		// 		\DB::raw('min(h.min_lat) as min_lat_boundary'),
		// 		\DB::raw('max(h.max_lat) as max_lat_boundary'),
		// 		\DB::raw('min(h.min_lng) as min_lng_boundary'),
		// 		\DB::raw('max(h.max_lng) as max_lng_boundary')
		// 	)
		// 	->whereRaw('h.min_lat <= h.max_lat')
		// 	->whereRaw('h.min_lng <= h.max_lng')
		// 	->toSql();
		// dd($data);
		$data = $data->select(
				\DB::raw('avg(h.min_lat) as min_lat'), 
				\DB::raw('avg(h.min_lng) as min_lng'), 
				\DB::raw('avg(h.max_lat) as max_lat'), 
				\DB::raw('avg(h.max_lng) as max_lng'),
				\DB::raw('min(h.min_lat) as min_lat_boundary'),
				\DB::raw('max(h.max_lat) as max_lat_boundary'),
				\DB::raw('min(h.min_lng) as min_lng_boundary'),
				\DB::raw('max(h.max_lng) as max_lng_boundary')
			)
			->whereRaw('h.min_lat <= h.max_lat')
			->whereRaw('h.min_lng <= h.max_lng')
			// ->having('h.min_lat' ,'<=', 'h.max_lat')
			// ->having('h.min_lng', '<=' , 'h.max_lng')
			->first();
			// dd($data);
			// dd($data->toSql());
		return response([
			'lat' => 0.5 * ($data->min_lat + $data->max_lat),
			'lng' => 0.5 * ($data->min_lng + $data->max_lng),
			'zoom' => $this->_calculateProperZoom($data->max_lat_boundary - $data->min_lat_boundary, $data->max_lng_boundary - $data->min_lng_boundary)
		]);
	}

	function getDataMap(Request $request)
	{	
		// \DB::enableQueryLog();
		$data = $this->JoinTable();
		$data = $data
		->where('p.min_lat', '>=', $request->min_lat)
					->where('p.min_lng', '>=', $request->min_lng)
					->where('p.max_lat', '<=', $request->max_lat)
					->where('p.max_lng', '<=', $request->max_lng)
					->where('p.max_lng', '<=', $request->max_lng)
					->havingRaw('p_min_lat <= p_max_lat')
					->havingRaw('p_min_lng <= p_max_lng');
					//dd($data->get());
		$data = $this->_adjustByZoomLvl($data, $request->zoom_level, $request->date_y);
		if (isset($request->date_y))
		{
			$data = $this->_filterSection($request, $data);
		}
		return  $data->get();
	}
	
	private function _adjustByZoomLvl($data, $zoom_level)
	{
		if ($zoom_level <= 16)
		{
			$data = $data
				->where('p.lane_position_no', 1)
				->whereIn('p.direction', ['L', 'U']);	
		}
		if ($zoom_level <= 5)
		{
			$data = $data
				->whereRaw('0 = 1');
		}
		
		else if ($zoom_level == 6)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 20000 = 0");
		}
		else if ($zoom_level == 7)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 10000 = 0");
		}	
		else if ($zoom_level <= 8)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 8000 = 0");
		}
		else if ($zoom_level <= 9)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 1500 = 0");
		}
		else if ($zoom_level <= 10)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 800 = 0");
		}
		else if ($zoom_level <= 11)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 600 = 0");
		}
		else if ($zoom_level <= 12)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 400 = 0");
		}
		else if ($zoom_level <= 13)
		{
			$data = $data
				->whereRaw("(p.km_from * 1000 + p.m_from) % 200 = 0");
		}
		
		return $data;
	}

	private function _filterSection($request, $data)
	{	
		$where = false;
		 //\DB::enableQueryLog();
		$rmb_id = $request->rmb_id;
		$sb_id = $request->sb_id;
		$branch_id = $request->branch_id;
		$kilopost_from = $request->kilopost_from;
		$kilopost_to = $request->kilopost_to;
		$date_y = $request->date_y;

		if ($sb_id != -1)
		{
			$data = $data->where('p.SB_id', $sb_id);
		}
		else
		{
			if ($rmb_id != -1)
			{
				$array_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
				$data = $data->whereIn('p.SB_id', $array_id);
			}
		}
		if (!empty($branch_id) && $branch_id != -1)
		{
			$data = $data->where('p.branch_id', $branch_id);
		}
		if (!empty($kilopost_from))
		{
 			$data = $data->where('p.km_from', '>=', $kilopost_from);
		}
		
		if (!empty($kilopost_to))
		{
 			$data = $data->where('p.km_to', '<=', $kilopost_to);
		}

		if ($date_y == 'latest')
		{	
			$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id ORDER BY date_y DESC LIMIT 0, 1)");
			//dd($data->toSql());
		}
		else if ($date_y == 'second_latest')
		{	
			$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id ORDER BY date_y DESC LIMIT 1, 1)");
			//dd($data->first());
		}
		else
		{
			$data = $data->where('h.date_y', $date_y);
		}
		return $data;
	}

	private function _calculateProperZoom($delta_lat, $delta_lng)
	{
		$zoom;
		if ($delta_lng > $delta_lat)
		{
			// longitude mode
			$data = [
				'0.0034179688' => 19,
				'0.0068359375' => 18,
				'0.013671875' => 17,
				'0.02734375' => 16,
				'0.0546875' => 15,
				'0.109375' => 14,
				'0.21875' => 13,
				'0.4375' => 12,
				'0.875' => 11,
				'1.75' => 10,
				'3.5' => 9,
				'7' => 8,
				'13' => 7,
				'26' => 6
			];
			$zoom = Helper::vlookup($delta_lng, $data);
		}
		else
		{
			// latitude mode
			$data = [
				'0.0014648438' => 19,
				'0.0029296875' => 18,
				'0.005859375' => 17,
				'0.01171875' => 16,
				'0.0234375' => 15,
				'0.046875' => 14,
				'0.09375' => 13,
				'0.1875' => 12,
				'0.375' => 11,
				'0.75' => 10,
				'1.5' => 9,
				'3' => 8,
				'6' => 7,
				'12' => 6, 
			];

			$zoom = Helper::vlookup($delta_lat, $data);
		}
		return (isset($zoom) ? $zoom : 19);
	}

	function JoinTable()
	{	
		$data = DB::table('tblSection_PC AS p')
			->select(DB::raw('p.min_lat as p_min_lat, p.max_lat as p_max_lat, p.min_lng as p_min_lng, p.max_lng as p_max_lng, h.min_lat, h.min_lng, h.max_lat, h.max_lng, h.points, h.section_code, h.cracking_ratio_total, h.rutting_depth_ave, h.IRI, h.MCI, h.surface_type, h.km_from, h.km_to, h.m_to, h.m_from, h.branch_id, h.section_length, h.analysis_area, h.direction, h.lane_position_no, h.date_m, h.date_y'))
			->join('tblSection_PC_history AS h', 'p.id', '=', 'h.section_id');
		return $data;
	}
	
}
