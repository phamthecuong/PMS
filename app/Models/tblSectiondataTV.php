<?php

namespace App\Models;

use App\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class tblSectiondataTV extends Model
{
    protected $table = 'tblSectiondata_TV' ;
    protected $appends = ["name"];
    protected $fillable = [
        'segment_id', 'name_en', 'name_vn',
        'km_station', 'm_station',
        'lat_station', 'lng_station',
        'survey_time', 'remark', 'effect_at', 'nullity_at',
        'total_traffic_volume_up', 'total_traffic_volume_down',
        'heavy_traffic_up', 'heavy_traffic_down', 'ward_id'
    ];

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        $lang == 'en' ? $key = "name_".$lang : $key = "name_vn";
        return $this->attributes[$key];
    }

    function histories()
    {
        return $this->hasMany('App\Models\tblTVHistory', 'sectiondata_id')->orderBy('survey_time', 'desc');
    }
    function layers()
    {
        return $this->hasMany('App\Models\tblSectionLayer', 'sectiondata_id')->where('type', 1);
    }
    
    public function vehicleInfos()
    {
        return $this->hasMany('App\Models\tblTVVehicleDetails', 'sectiondata_TV_id');
    }

    public function latestSegment()
    {
        return $this->hasOne('App\Models\tblSegmentHistory', 'segment_id', 'segment_id')->latest();
    }

    function segment()
    {
        return $this->belongsTo('App\Models\tblSegment', 'segment_id');
    }

    // function latest()
    // {
    //     return $this->hasOne('App\Models\tblTVHistory', 'sectiondata_id')->orderBy('survey_time', 'desc');
    // }

    public function getMStationAttribute($value)
    {
        return (float) $value;
    }

    public function getKmStationAttribute($value)
    {
        return (float) $value;
    }

    public function getWardIdAttribute($value)
    {
        return (int) $value;
    }

    /**
     * get segment that overlap to a chainage and in a branch
	 * @param segment_id: number, id in tblSegment
	 * @param side: number, -1: left, 1: right
     * @return array of segments
     */
    static function getYearHasData()
    {
        $dataset = [[
            'name' => trans('back_end.latest_data'),
            'value' => ''
        ]];

        $data = tblSectiondataTV::select(\DB::raw('YEAR(survey_time) as year_data'))->groupBy(\DB::raw('YEAR(survey_time)'))->get();
        foreach ($data as $d) 
        {
            $dataset[] = array(
                'name' => $d->year_data,
                'value' => $d->year_data,
            );
        }
        return $dataset;
    }

	static function getOutsideBoundarySection($segment_id, $side, $km_from = null, $m_from = null, $km_to = null, $m_to = null)
	{
		$segment = tblSegment::find($segment_id);
		$rec;
		if ($side == -1)
		{
		    if ($km_from == null || $m_from == null || $km_to == null || $m_to == null)
            {
    			$pos = 10000*$segment->km_from + $segment->m_from;
    			$rec = tblSectiondataTV::where('segment_id', $segment_id)
    				->whereRaw("10000*km_station+m_station < {$pos}")
    				->get();
            }
            else
            {
                $pos = 10000*$km_from + $m_from;
                $rec = tblSectiondataTV::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_station+m_station < {$pos}")
                    ->get();
            }	
		}
		else
		{
		    if ($km_from == null || $m_from == null || $km_to == null || $m_to == null)
            {
    			$pos = 10000*$segment->km_to + $segment->m_to;
    			$rec = tblSectiondataTV::where('segment_id', $segment_id)
    				->whereRaw("10000*km_station+m_station >= {$pos}")
    				->get();
            }
            else 
            {
                $pos = 10000*$km_to + $m_to;
                $rec = tblSectiondataTV::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_station+m_station >= {$pos}")
                    ->get();
            }
        }
		
		return $rec;
	}

    static public function boot()
    {
        tblSectiondataTV::saving(function($rec) {
            foreach ($rec->attributes as $key => $value) 
            {
                $rec->{$key} = ($value == NULL) ? NULL : $value;
            }
            if (\Auth::user())
            {
                if ($rec->id)
                {
                    $rec->updated_by = \Auth::user()->id;
                }
                else
                {
                    $rec->created_by = \Auth::user()->id;
                }
            }
        });

        tblSectiondataTV::deleting(function($model) {
            $tv = tblSectiondataTV::find($model->id);
            if (count($tv->vehicleInfos()->get()) > 0)
            {
                foreach ($tv->vehicleInfos()->get() as $layer) 
                {
                    $layer->delete();
                }
            }
        });
    }

    function createHistory()
    {
        $history = new tblTVHistory();
        $history->segment_id = $this->segment_id;
        $history->name_en = $this->name_en;
        $history->name_vn = $this->name_vn;
        $history->km_station = $this->km_station;
        $history->m_station = $this->m_station;
        $history->survey_time = $this->survey_time;
        $history->lat_station = $this->lat_station;
        $history->lng_station = $this->lng_station;
        $history->remark = $this->remark;
        $history->total_traffic_volume_up = $this->total_traffic_volume_up;
        $history->total_traffic_volume_down = $this->total_traffic_volume_down;
        $history->heavy_traffic_up = $this->heavy_traffic_up;
        $history->heavy_traffic_down = $this->heavy_traffic_down;
        $history->ward_id = $this->ward_id;
        $history->tv()->associate($this);
        $history->save();

        foreach ($this->vehicleInfos()->get() as $r) 
        {
            $l = new tblTVVehicleDetailHistory();
            $l->up = $r->up;
            $l->down = $r->down;
            $l->vehicle_type_id = $r->vehicle_type_id;
            $l->tvSection()->associate($history);
            $l->save();
        }
    }

    function updateHistory()
    {
        $history = tblTVHistory::where('sectiondata_id', $this->id)->orderBy('survey_time', 'desc')->first();
        $history->segment_id = $this->segment_id;
        $history->name_en = $this->name_en;
        $history->name_vn = $this->name_vn;
        $history->km_station = $this->km_station;
        $history->m_station = $this->m_station;
        $history->survey_time = $this->survey_time;
        $history->lat_station = $this->lat_station;
        $history->lng_station = $this->lng_station;
        $history->remark = $this->remark;
        $history->total_traffic_volume_up = $this->total_traffic_volume_up;
        $history->total_traffic_volume_down = $this->total_traffic_volume_down;
        $history->heavy_traffic_up = $this->heavy_traffic_up;
        $history->heavy_traffic_down = $this->heavy_traffic_down;
        $history->ward_id = $this->ward_id;
        $history->tv()->associate($this);
        $history->save();

        foreach ($this->vehicleInfos()->get() as $r) 
        {
            $l = tblTVVehicleDetailHistory::where('tv_history_id', $history->id)->where('vehicle_type_id', $r->vehicle_type_id)->first();
            $l->up = $r->up;
            $l->down = $r->down;
            $l->save();
        }
    }

    function scopeFilterByUser($query)
    {
        $user = Auth::user();

    }
    function scopeFilterByCondition($query, $request)
    {
        if ($request->branch_number != 0)
        {
            $query->whereHas('segment', function ($query) use($request)
            {
                $query->where('branch_id' , $request->branch_number);
            })->get();
        }
        if ($request->rmb != 0)
        {
            $query->whereHas('segment', function ($query) use($request)
            {
                $query->whereHas('tblOrganization', function ($query) use($request){
                    $query->where('parent_id', $request->rmb);
                });
            })->get();
        }
        if ($request->sb != 0)
        {
            $query->whereHas('segment', function ($query) use($request)
            {
                $query->where('SB_id', $request->sb);
            })->get();
        }
        if ($request->route_name != 0)
        {
            $query->whereHas('segment', function ($query) use($request) {
                $query->where('branch_id' , $request->route_name);
            })->get();
        }

        if (isset($request->name) && !empty($request->name))
        {
            if (App::getLocale() == 'en')
            {
                $query->Where('name_en', 'like', '%' . $request->name . '%')->get();
            }else
            {
                $query->Where('name_vn', 'like', '%' . $request->name . '%')->get();
            }
        }
    }

    function scopeFilterDropdown($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $query->where($key_in_db, $request->{$key_in_request})->get();
        }
    }

    function scopeFilterSuperInput($query, $key_in_request, $key_in_db,  $request)
    {
        if ($request->{$key_in_request} != '')
        {
            $data_request = $request->{$key_in_request};
            $parseSuperInput = Helper::parseSuperInput($data_request);
            if (is_numeric($parseSuperInput[1]))
            {
                $query->where($key_in_db ,$parseSuperInput[0], $parseSuperInput[1])->get();
            }
            else
            {
                $query->where($key_in_db , null)->get();
            }
        }
    }

    static public function config($except = [])
    {
        $object = [
            'section_id' => [
                'title' => trans('back_end.section_id'),
                'index' => 0,
                'type' => 'text',
                'width' => 100,
                'validate' => ''
            ],
            'route_branch' => [
                'title' => trans('back_end.route_branch'),
                'index' => 2,
                'type' => 'text',
                'width' => 100,
                'validate' => ''
            ],
            'rmb' => [
                'title' => trans('back_end.rmb'),
                'index' => 3,
                'type' => 'select',
                'modelCheck' => tblOrganization::getListRmb($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListRmb(),
                'width' => 100,
                'validate' => 'required'
            ],
            'sb' => [
                'relation' => [
                    'model'     => '\App\Models\tblOrganization',
                    'func'      => 'findSb',
                    'parent'    => 'rmb'
                ],
                'title' => trans('back_end.sb'),
                'index' => 4,
                'type' => 'select',
                'modelCheck' => tblOrganization::getListSB($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListSB(),
                'width' => 100,
                'validate' => 'required'
            ],
            'road' => [
                'relation' => [
                    'model'     => '\App\Models\tblBranch',
                    'func'      => 'findRouteBranch',
                    'parent'    => 'sb'
                ],
                'title' => trans('back_end.route_branch'),
                'index' => 1,
                'type' => 'select',
                'modelCheck' => tblBranch::allOptionToAjax($has_all = FALSE, $value_as_name = FALSE, $has_name = true),
                'item' => tblBranch::allOptionToAjax(),
                'width' => 100,
                'validate' => 'required'
            ],
            'name' => [
                'title' => trans('back_end.name'),
                'index' => 5,
                'type' => 'text',
                'width' => 100,
                'validate' =>  'required',
            ],
            'km_station' => [
                'title' => trans('back_end.km_station'),
                'index' => 6,
                'type' => 'text',
                'width' => 50,
                'validate' => ['required','integer','min:0'],
            ],
            'm_station' => [
                'title' => trans('back_end.m_station'),
                'index' => 7,
                'type' => 'text',
                'width' => 50,
                'validate' => ['required', 'numeric', 'min:0'],
            ],
            'lat_station' => [
                'title' => trans('back_end.lat_station'),
                'index' => 8,
                'type' => 'text',
                'width' => 100,
                'validate' => ['numeric','min:190004.0544','max:832157.7917'],
            ],
            'lng_station' => [
                'title' => trans('back_end.lng_station'),
                'index' => 9,
                'type' => 'text',
                'width' => 100,
                'validate' =>  ['numeric','min:663996.8088','max:2589882.7561',],
            ],
            'survey_time' => [
                'title' => trans('back_end.survey_time'),
                'index' => 10,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required','date_format:Y/m'),
            ],
            'total_traffic_volume_up' => [
                'title' => trans('back_end.total_traffic_volume_up'),
                'index' => 11,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'total_traffic_volume_down' => [
                'title' => trans('back_end.total_traffic_volume_down'),
                'index' => 12,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'traffic_volume_total' => [
                'title' => trans('back_end.total'),
                'index' => 13,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'heavy_traffic_up' => [
                'title' => trans('back_end.heavy_traffic_up'),
                'index' => 14,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'heavy_traffic_down' => [
                'title' => trans('back_end.heavy_traffic_down'),
                'index' => 15,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'heavy_traffic_total' => [
                'title' => trans('back_end.heavy_traffic_down'),
                'index' => 16,
                'type' => 'text',
                'width' => 100,
                'validate' =>'',
            ],
            'up1' => [
                'title' => trans('back_end.car_jeep_up'),
                'index' => 17,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down1' => [
                'title' => trans('back_end.car_jeep_down'),
                'index' => 18,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total1' => [
                'title' => trans('back_end.car_jeep_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up1',
                    'second' => 'down1',
                    'amount' => 'total1',
                ],
                'type_traffic' => 1,
                'index' => 19,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up2' => [
                'title' => trans('back_end.light_truck_up'),
                'index' => 20,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down2' => [
                'title' => trans('back_end.light_truck_down'),
                'index' => 21,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total2' => [
                'title' => trans('back_end.light_truck_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up2',
                    'second' => 'down2',
                    'amount' => 'total2',
                ],
                'type_traffic' => 0,
                'index' => 22,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up3' => [
                'title' => trans('back_end.medium_truck_up'),
                'index' => 23,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down3' => [
                'title' => trans('back_end.medium_truck_down'),
                'index' => 24,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total3' => [
                'title' => trans('back_end.medium_truck_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up3',
                    'second' => 'down3',
                    'amount' => 'total3',
                ],
                'type_traffic' => 0,
                'index' => 25,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up4' => [
                'title' => trans('back_end.heavy_truck_3_up'),
                'index' => 26,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down4' => [
                'title' => trans('back_end.heavy_truck_3_down'),
                'index' => 27,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total4' => [
                'title' => trans('back_end.heavy_truck_3_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up4',
                    'second' => 'down4',
                    'amount' => 'total4',
                ],
                'type_traffic' => 0,
                'index' => 28,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up5' => [
                'title' => trans('back_end.heavy_truck_'),
                'index' => 29,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down5' => [
                'title' => trans('back_end.heavy_truck_'),
                'index' => 30,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total5' => [
                'title' => trans('back_end.heavy_truck_'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up5',
                    'second' => 'down5',
                    'amount' => 'total5',
                ],
                'type_traffic' => 0,
                'index' => 31,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up6' => [
                'title' => trans('back_end.small_bus_up'),
                'index' => 32,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down6' => [
                'title' => trans('back_end.small_bus_down'),
                'index' => 33,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total6' => [
                'title' => trans('back_end.small_bus_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up6',
                    'second' => 'down6',
                    'amount' => 'total6',
                ],
                'type_traffic' => 1,
                'index' => 34,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up7' => [
                'title' => trans('back_end.large_bus_up'),
                'index' => 35,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down7' => [
                'title' => trans('back_end.large_bus_down'),
                'index' => 36,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total7' => [
                'title' => trans('back_end.large_bus_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up7',
                    'second' => 'down7',
                    'amount' => 'total7',
                ],
                'type_traffic' => 0,
                'index' => 37,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up8' => [
                'title' => trans('back_end.tractor_up'),
                'index' => 38,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down8' => [
                'title' => trans('back_end.tractor_down'),
                'index' => 39,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total8' => [
                'title' => trans('back_end.tractor_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up8',
                    'second' => 'down8',
                    'amount' => 'total8',
                ],
                'type_traffic' => 1,
                'index' => 40,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up9' => [
                'title' => trans('back_end.motorbike_including_3_wheeler_up'),
                'index' => 41,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down9' => [
                'title' => trans('back_end.motorbike_including_3_wheeler_down'),
                'index' => 42,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total9' => [
                'title' => trans('back_end.motorbike_including_3_wheeler_total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up9',
                    'second' => 'down9',
                    'amount' => 'total9',
                ],
                'type_traffic' => 1,
                'index' => 43,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'up10' => [
                'title' => trans('back_end.bicycle_pedicab_up'),
                'index' => 44,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'down10' => [
                'title' => trans('back_end.bicycle_pedicab_down'),
                'index' => 45,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total10' => [
                'title' => trans('back_end.total'),
                'sum' => [
                    'child' => 'surface',
                    'first' => 'up10',
                    'second' => 'down10',
                    'amount' => 'total10',
                ],
                'type_traffic' => 1,
                'index' => 46,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'grand_total' => [
                'title' => trans('back_end.grand_total'),
                'index' => 47,
                'type' => 'text',
                'width' => 100,
                'validate' =>'',
            ],
            'segment_id' => [
                'title' => trans('back_end.segment_id'),
                'index' => 48,
                'type' => 'text',
                'width' => 100,
                'validate' =>'',
            ],
            'ward_to' => [
                'title' => trans('back_end.segment_id'),
                'index' => 49,
                'type' => 'text',
                'width' => 100,
                'validate' =>'',
            ],
        ];
        // 1 = Car, Jeep ; 2 = Light Truck; 3 = Medium Truck  (2 Axles);
        // 4 = Heavy Truck  (3 Axle); 5 = Heavy Truck  (>3 Axle); 6 =Small Bus
        // 7 = Large Bus; 8 = Tractor; 9 = Motorbike including 3 Wheeler; 10 = Bicycle / Pedicab
        if (in_array('chainage', $except))
        {
            $object = array_except($object, ['km_to', 'm_to', 'to_lat', 'to_lng']);
        }
        return $object;
    }
}
