<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use Illuminate\Support\Facades\App;

class tblSegment extends Model
{
    protected $table = 'tblSegment';
    protected $fillable = ["segname_en", "segname_vn"];
    protected $appends = ["name","segment_info"];

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        $lang == 'en' ? $key = "segname_".$lang : $key = "segname_vn";
        return $this->attributes[$key];
    }
    public function tblBranch()
    {
        return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }
    
    public function tblOrganization()
    {
        return $this->belongsTo('App\Models\tblOrganization', 'SB_id');
    }
    
    public function tblCity_from()
    {
        return $this->belongsTo('App\Models\tblCity', 'prfrom_id');
    }
    
    public function tblCity_to()
    {
        return $this->belongsTo('App\Models\tblCity', 'prto_id');
    }
    
    public function tblDistrict_from()
    {
        return $this->belongsTo('App\Models\tblDistrict', 'distfrom_id');
    }
    
    public function tblDistrict_to()
    {
        return $this->belongsTo('App\Models\tblDistrict', 'distto_id');
    }
    public function tblward_to()
    {
        return $this->belongsTo('App\Models\tblWard', 'commune_to');
    }
    public function tblward_from()
    {
        return $this->belongsTo('App\Models\tblWard', 'commune_from');
    }
	
	public function sectionDataRMD()
	{
		return $this->hasMany('App\Models\tblSectiondataRMD', 'segment_id');
	}
	
	public function sectionDataMH()
	{
		return $this->hasMany('App\Models\tblSectiondataMH', 'segment_id');
	}

	public function sectionDataTV()
	{
		return $this->hasMany('App\Models\tblSectiondataTV', 'segment_id');
	}

	function getSegmentInfoAttribute()
	{
		$lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return 'Km' . $this->km_from . '+' . $this->m_from . ' - ' . 'Km' . $this->km_to . '+' . $this->m_to . ': ' . $this->{"segname_{$lang}"};
	}
	
	static function check_exit_segment($data, $branch_id, $sb_id)
	{
		$result = TRUE;
		for ($i = 0; $i < count($data) - 1; $i ++)
		{
			// if ($i == 3) {
				// DB::enableQueryLog();
			// }
			$check = tblSegment::where(function ($sql) use ($sb_id, $branch_id, $data, $i) {
			 						$sql->where('branch_id', $branch_id)
										->where('SB_id', $sb_id)
										->where('id', '<>', $data[$i]['segment_id'])
										->where('id', '<>', $data[$i+1]['segment_id'])
										->where(function($query) use ($data, $i) {
											$query->where(function ($query_from) use ($data, $i) {
												$query_from->where('km_from', '>', $data[$i]['km_from'])
														->where('km_from', '<', $data[$i]['km_to']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '>', $data[$i + 1]['km_from'])
														->where('km_to', '<', $data[$i + 1]['km_to']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '>', $data[$i]['km_to'])
														->where('km_from', '<', $data[$i + 1]['km_from']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '>', $data[$i]['km_to'])
														->where('km_to', '<', $data[$i + 1]['km_from']);
											})
											// cuoi trung dau 1
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i]['km_from'])
														->where('m_to', '>', $data[$i]['m_from']);
											})
											// cuoi trung dau 2
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i + 1]['km_from'])
														->where('m_to', '>=', $data[$i + 1]['m_from'])
														->where('km_from', '<', $data[$i + 1]['km_from']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i + 1]['km_from'])
														->where('m_to', '>=', $data[$i + 1]['m_from'])
														->where('km_from', '=', $data[$i + 1]['km_from'])
														->where('m_from', '<', $data[$i + 1]['m_from']);
											})
											//  cuoi trung cuoi 1
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i]['km_to'])
														->where('m_to', '>=', $data[$i]['m_to'])
														->where('km_from', '<', $data[$i]['km_to']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i]['km_to'])
														->where('m_to', '>=', $data[$i]['m_to'])
														->where('km_from', '=', $data[$i]['km_to'])
														->where('m_from', '<', $data[$i]['m_to']);
											})
											// cuoi trung cuoi 2
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i + 1]['km_to'])
														->where('km_from', '<', $data[$i + 1]['km_to'])
														->where('m_to', '>=', $data[$i + 1]['m_to']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i + 1]['km_to'])
														->where('km_from', '=', $data[$i + 1]['km_to'])
														->where('m_to', '>=', $data[$i + 1]['m_to'])
														->where('m_from', '<', $data[$i + 1]['m_to']);
											})
											// dau trung cuoi 1
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i]['km_to'])
														->where('m_from', '<=', $data[$i]['m_to'])
														->where('km_to', '>', $data[$i]['km_to']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i]['km_to'])
														->where('m_from', '<=', $data[$i]['m_to'])
														->where('km_to', '=', $data[$i]['km_to'])
														->where('m_to', '>', $data[$i]['m_to']);
											})
											//dau trung cuoi 2
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i + 1]['km_to'])
														->where('m_from', '<', $data[$i + 1]['m_to']);
											})
											// dau trung dau 1
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i]['km_from'])
														->where('m_from', '<', $data[$i]['m_from'])
														->where('km_to', '>', $data[$i]['km_from']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i]['km_from'])
														->where('m_from', '<', $data[$i]['m_from'])
														->where('km_to', '=', $data[$i]['km_from'])
														->where('m_to', '>', $data[$i]['m_from']);
											})
											//dau trung dau 2
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i + 1]['km_from'])
														->where('m_from', '<', $data[$i + 1]['m_from'])
														->where('km_to', '>', $data[$i + 1]['km_from']);
											})
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_from', '=', $data[$i + 1]['km_from'])
														->where('m_from', '<', $data[$i + 1]['m_from'])
														->where('km_to', '=', $data[$i + 1]['km_from'])
														->where('m_to', '>', $data[$i + 1]['m_from']);
											})
											// cuoi 1 == dau 2 va dau nam giua
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i]['km_from'])
														->where('km_from', '=', $data[$i + 1]['km_from'])
														->where('m_from', '>=', $data[$i]['m_to'])
														->where('m_from', '<=', $data[$i + 1]['m_from']);
											})
											// cuoi 1 == dau 2 va dau nam giua
											->orwhere(function ($query) use ($data, $i) {
												$query->where('km_to', '=', $data[$i]['km_from'])
														->where('km_from', '=', $data[$i + 1]['km_from'])
														->where('m_to', '>=', $data[$i]['m_to'])
														->where('m_to', '<=', $data[$i + 1]['m_from']);
											});
										});
			 					})
								->count();
			 					// ->get();
			// DD(DB::getQueryLog());
			// if ($check->count() > 0)
			if ($check > 0)
			{
				// if ($i == 3) {
					// DD(DB::getQueryLog());	
				// }
// 				
				// var_dump($i);
				// var_dump($data[$i]['segment_id']);
				// var_dump($check->toArray());die;
				$result = FALSE;
				break;
			}
		}
		
		return $result;
	}
	
	/**
     * get data contains point
     * @param model for type data
	 * @param km static point
	 * @param m static point
	 * @param branch id
	 * @param sb ID
     * @return object for model
     */
	static function check_point_middel($model, $km_static, $m_static, $segment_id)
	{
		switch ($model) {
			case 'tblSectiondataRMD':
				$data = tblSectiondataRMD::where('segment_id', $segment_id)
										->where(function ($query) use ($km_static, $m_static) {
											$query->where(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', '<', $km_static)
													->where('km_to', '>', $km_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', $km_static)
													->where('m_from', '<=', $m_static)
													->where('km_to', '>', $km_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', '<', $km_static)
													->where('km_to', $km_static)
													->where('m_to', '>=', $m_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', $km_static)
													->where('m_from', '<=', $m_static)
													->where('km_to', $km_static)
													->where('m_to', '>=', $m_static);
											});
										})
										->get();
				break;
			
			case 'tblSectiondataMH':
				$data = tblSectiondataMH::where('segment_id', $segment_id)
										->where(function ($query) use ($km_static, $m_static) {
											$query->where(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', '<', $km_static)
													->where('km_to', '>', $km_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', $km_static)
													->where('m_from', '<=', $m_static)
													->where('km_to', '>', $km_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', '<', $km_static)
													->where('km_to', $km_static)
													->where('m_to', '>=', $m_static);
											})
											->orwhere(function ($sql) use ($km_static, $m_static) {
												$sql->where('km_from', $km_static)
													->where('m_from', '<=', $m_static)
													->where('km_to', $km_static)
													->where('m_to', '>=', $m_static);
											});
										})
										->get();
				break;
				
			case 'tblSectiondataTV':
				$data = tblSectiondataTV::where('km_station', $km_static)
										->where('m_station', $m_static)
										->where('segment_id', $segment_id)
										->first();
				break;
			default:
				break;
		}
		return $data;
	}
	
	/**
     * check data overlap
     * @param model for type data
	 * @param km static point
	 * @param m static point
     * @return boolean
     */
	static function check_data_overlap($km_first, $m_first, $km_mid, $m_mid, $km_last, $m_last)
	{
		$exp = max(strlen($m_first), strlen($m_mid), strlen($m_last));
		$check_first = $km_first * pow(10, $exp) + $m_first;
		$check_mid = $km_mid * pow(10, $exp) + $m_mid;
		$check_last = $km_last * pow(10, $exp) + $m_last;
		
		if ($check_mid <= $check_first || $check_mid >= $check_last )
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
     * get segment previous or next of point
	 * @param type : previous or next
	 * @param km static point
	 * @param m static point
	 * @param branch id
	 * @param sb ID
     * @return boolean
     */
	// H.ANH  2016.12.04  remove unused function
	// static function getSegmentAdjacent($type, $km_point, $m_point, $branch_id, $sb_id)
	// {
	// 	$data = tblSegment::where('branch_id', $branch_id)
	// 						->where('SB_id', $sb_id);
	// 	if ($type == 'previous')
	// 	{
	// 		$data = $data->where(function ($query) use ($km_point, $m_point) {
	// 			$query->where(function ($query) use ($km_point, $m_point) {
	// 					$query->where('km_from', '<', $km_point);
	// 				})
	// 				->orwhere(function ($query) use ($km_point, $m_point) {
	// 					$query->where('km_from', '=', $km_point)
	// 						->where('m_from', '<=', $m_point);
	// 				});
	// 		})
	// 		->orderBy('km_to', 'desc')
	// 		->orderBy('m_to', 'desc');
	// 	}
	// 	else if ($type == 'next')
	// 	{
	// 		$data = $data->where(function ($query) use ($km_point, $m_point) {
	// 			$query->where(function ($query) use ($km_point, $m_point) {
	// 					$query->where('km_from', '>', $km_point);
	// 				})
	// 				->orwhere(function ($query) use ($km_point, $m_point) {
	// 					$query->where('km_from', '=', $km_point)
	// 						->where('m_from', '>=', $m_point);
	// 				});
	// 		})
	// 		->orderBy('km_from', 'desc')
	// 		->orderBy('m_from', 'desc');
	// 	}
	// 	$data = $data->first();
	// 	return $data;
	// }

	/**
     * get segment that overlap to a chainage and in a branch
     * @param km_f: number, kilopost from
	 * @param m_f: number, meter from
	 * @param km_t: number, kilometer to
	 * @param m_t: number, meter to
	 * @param branch_id: number, id in tblBranch
	 * @param segment_id: number, excluded segment id
     * @return array of segments
     */
    static function allOptionToAjax($check = null, $has_all = FALSE, $demo = FALSE, $has_name = FALSE, $has_code = FALSE)
    {
        $data;
        if ($demo !== FALSE)
        {
            $data = tblSegment::where('id', '<=', 1)->get();
        }
        elseif($check !== null)
        {
        	$data = tblSegment::where('branch_id', $check)->get();
        	
        }
        else
        {
            $data = tblSegment::all();
        }
        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset += array(
                -1 => $has_all,
            );
        }

        foreach ($data as $b)
        {
            if ($has_name)
            {
                $dataset[]  = array(
                    'name' => $b->segname_vn,
                    'value' => $b->id
                );
                $dataset[]  = array(
                    'name' => $b->segname_en,
                    'value' => $b->id
                );
            }
            else
            {
            	$dataset[$b->id] = array(
	                'name' => (Config::get('app.locale') == 'en') ? $b->segname_en : $b->segname_vn,
	                'value' => $b->id,
	                // 'selected' => FALSE,
	            );
            }
        }

        return $dataset;
    }
	static function getRelatedSegments($km_f, $m_f, $km_t, $m_t, $branch_id, $sb, $segment_id = -1)
	{
		$point_f = $km_f * 1000 + $m_f;
		$point_t = $km_t * 1000 + $m_t;
		// DB::enableQueryLog();
		$segment = tblSegment::where('branch_id', $branch_id)
			->where('id', '<>', $segment_id)
			->where('SB_id', $sb)
			->whereRaw("((1000*km_from+m_from >= {$point_f} AND 1000*km_from+m_from < {$point_t}) OR ( 1000*km_from+m_from < {$point_f} AND {$point_f} < 1000*km_to+m_to))")
			->get();
		// dd(DB::getQueryLog());
		return $segment;
	}
}
