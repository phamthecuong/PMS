<?php

namespace App\Models;

use App\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\tblRMDHistory;
use App\Models\tblSectionLayerHistory;
use App\Models\mstSurface;
use App\Models\tblOrganization;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Auth;

class tblSectiondataRMD extends Model
{
    protected $table = 'tblSectiondata_RMD';
    
    protected $fillable = [
        'segment_id', 'terrian_type_id', 'road_class_id',
        'from_lat', 'from_lng',
        'to_lat', 'to_lng',
        'km_from', 'm_from', 'km_to', 'm_to',
        'survey_time', 'direction', 'lane_pos_number',
        'lane_width', 'no_lane', 'construct_year',
        'service_start_year', 'temperature' , 'annual_precipitation',
        'actual_length', 'remark',
        'effect_at', 'nullity_at', 'pavement_type_id', 'pavement_thickness', 'ward_from_id', 'ward_to_id'
    ];

    function histories()
    {
        return $this->hasMany('App\Models\tblRMDHistory', 'sectiondata_id')->orderBy('survey_time', 'desc');
    }

    function routeClass()
    {
        return $this->belongsTo('App\Models\mstRoadClass','road_class_id');
    }

    function terrianType()
    {
        return $this->belongsTo('App\Models\tblTerrainType','terrian_type_id');
    }

    function layers()
    {
        return $this->hasMany('App\Models\tblSectionLayer', 'sectiondata_id')->where('type', 1);
    }

    function segment()
    {
        return $this->belongsTo('App\Models\tblSegment', 'segment_id');
    }

    function surface()
    {
        return $this->belongsTo('App\Models\mstSurface', 'pavement_type_id');
    }

    function wardFrom()
    {
        return $this->belongsTo('App\Models\tblWard', 'ward_from_id');
    }

    function wardTo()
    {
        return $this->belongsTo('App\Models\tblWard', 'ward_to_id');
    }

    public function getTerrianTypeIdAttribute($value)
    {
        return (string) $value;
    }

    public function getRoadClassIdAttribute($value)
    {
        return (string) $value;
    }

    public function getDirectionAttribute($value)
    {
        return (string) $value;
    }

    public function getKmFromAttribute($value)
    {
        return (int) $value;
    }

    public function getMFromAttribute($value)
    {
        return (int) $value;
    }

    public function getKmToAttribute($value)
    {
        return (int) $value;
    }

    public function getMToAttribute($value)
    {
        return (int) $value;
    }

    public function getLanePosNumberAttribute($value)
    {
        return (int) $value;
    }

    public function getNoLaneAttribute($value)
    {
        return (int) $value;
    }

    public function getLaneWidthAttribute($value)
    {
        return (float) $value;
    }

    public function getTemperatureAttribute($value)
    {
        return (float) $value;
    }

    public function getAnnualPrecipitationAttribute($value)
    {
        return (float) $value;
    }

    public function getActualLengthAttribute($value)
    {
        return (float) $value;
    }

    public function getWardFromIdAttribute($value)
    {
        return (int) $value;
    }

    public function getWardToIdAttribute($value)
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

        $data = tblSectiondataRMD::select(\DB::raw('YEAR(survey_time) as year_data'))->groupBy(\DB::raw('YEAR(survey_time)'))->get();
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
                $rec = tblSectiondataRMD::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_from+m_from < {$pos}")
                    ->get();
            }
            else
            {
                $pos = 10000*$km_from + $m_from;
                $rec = tblSectiondataRMD::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_from+m_from < {$pos}")
                    ->get();
            }
        }
        else
        {
            if ($km_from == null || $m_from == null || $km_to == null || $m_to == null)
            {
                $pos = 10000*$segment->km_to + $segment->m_to;
                $rec = tblSectiondataRMD::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_to+m_to > {$pos}")
                    ->get();
            }
            else
            {
                $pos = 10000*$km_to + $m_to;
                $rec = tblSectiondataRMD::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_to+m_to > {$pos}")
                    ->get();
            }
        }
        
        return $rec;
    }

    static function allToOption()
    {
        // $data = DB::table('tblSectiondata_RMD')->groupBy('direction')->get();
        // $dataset = array();
        // foreach ($data as $s)
        // {
        //     $direction = '';
        //     if ($s->direction == 1)
        //     {
        //         $direction .= 'left';
        //     }
        //     elseif ($s->direction == 2)
        //     {
        //         $direction .= 'right';
        //     }
        //     else
        //     {
        //         $direction .= 'single';
        //     }
        //     $dataset[$s->id] = array(
        //         'name' => $direction,
        //         'value' => $s->direction,
        //         'selected' => FALSE,
        //     );
        // }
        // return $dataset;
        
    }

    static function getListSegment()
    {
        // $data = tblSectiondataRMD::with('segment')->groupBy('segment_id')->get();

        $user = \Auth::user();
        $user_organization = \Auth::user()->organization_id;
        $organization = tblOrganization::where('parent_id', $user_organization)->get();
        $organization_id = [];
        foreach ($organization as $item)
        {
            $organization_id[] = $item->id;
        }
        if(\Auth::user()->hasRole('userlv1'))
        {
            $data = tblSectiondataRMD::with('segment')->groupBy('segment_id')->get();
        }
        elseif (\Auth::user()->hasRole('userlv2'))
        {
            // return $query->where('manage_l1_id', $user_organization);

            $data = tblSectiondataRMD::with('segment')->whereHas('segment', function ($query) use ($organization_id) {
                $query->whereIn('SB_id', $organization_id);
            })->get();
        }
        else
        {
            // return $query->where('manage_l2_id', $user_organization);
            $data = tblSectiondataRMD::with('segment')->whereHas('segment', function ($query) use ($user_organization) {
                $query->where('SB_id', $user_organization);
            })->get();
        }


        $dataset = array();
        foreach ($data as $segment)
        {
            $segment_id = @$segment->segment->id;
            $dataset[$segment->id] = array(
                'name' => @$segment->segment->name,
                'value' =>@$segment_id,
                'selected' => FALSE,
            );
        }
        return $dataset;
    }

    static public function boot()
    {
        tblSectiondataRMD::saving(function($rec) 
        {
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

        tblSectiondataRMD::deleting(function($model) 
        {
            $rmd = tblSectiondataRMD::find($model->id);
            if (count($rmd->layers()->get()) > 0)
            {
                foreach ($rmd->layers()->get() as $layer) 
                {
                    $layer->delete();
                }
            }
        });
    }

    function createHistory()
    {
        $history = new tblRMDHistory();
        $history->segment_id = $this->segment_id;
        $history->terrian_type_id = $this->terrian_type_id;
        $history->road_class_id = $this->road_class_id;
        $history->from_lat = $this->from_lat;
        $history->from_lng = $this->from_lng;
        $history->to_lat = $this->to_lat;
        $history->to_lng = $this->to_lng;
        $history->km_from = $this->km_from;
        $history->m_from = $this->m_from;
        $history->km_to = $this->km_to;
        $history->m_to = $this->m_to;
        $history->survey_time = $this->survey_time;
        $history->direction = $this->direction;
        $history->lane_pos_number = $this->lane_pos_number;
        $history->lane_width = $this->lane_width;
        $history->no_lane = $this->no_lane;
        $history->construct_year = $this->construct_year;
        $history->service_start_year = $this->service_start_year;
        $history->temperature = $this->temperature;
        $history->annual_precipitation = $this->annual_precipitation;
        $history->actual_length = $this->actual_length;
        $history->remark = $this->remark;
        $history->pavement_type_id = $this->pavement_type_id;
        $history->pavement_thickness = $this->pavement_thickness;
        $history->ward_from_id = $this->ward_from_id ;
        $history->ward_to_id = $this->ward_to_id  ;
        $history->rmd()->associate($this);
        $history->save();

        foreach ($this->layers()->get() as $layer) 
        {
            $l = new tblSectionLayerHistory();
            $l->thickness = $layer->thickness;
            $l->description = $layer->description;
            $l->type = 1;
            $l->material_type_id = $layer->material_type_id;
            $l->layer_id = $layer->layer_id;
            $l->rmdSection()->associate($history);
            $l->save();
        }
    }

    function updateHistory()
    {
        $history = tblRMDHistory::where('sectiondata_id', $this->id)->orderBy('survey_time', 'desc')->first();
        $history->segment_id = $this->segment_id;
        $history->terrian_type_id = $this->terrian_type_id;
        $history->road_class_id = $this->road_class_id;
        $history->from_lat = $this->from_lat;
        $history->from_lng = $this->from_lng;
        $history->to_lat = $this->to_lat;
        $history->to_lng = $this->to_lng;
        $history->km_from = $this->km_from;
        $history->m_from = $this->m_from;
        $history->km_to = $this->km_to;
        $history->m_to = $this->m_to;
        $history->survey_time = $this->survey_time;
        $history->direction = $this->direction;
        $history->lane_pos_number = $this->lane_pos_number;
        $history->lane_width = $this->lane_width;
        $history->no_lane = $this->no_lane;
        $history->construct_year = $this->construct_year;
        $history->service_start_year = $this->service_start_year;
        $history->temperature = $this->temperature;
        $history->annual_precipitation = $this->annual_precipitation;
        $history->actual_length = $this->actual_length;
        $history->remark = $this->remark;
        $history->pavement_type_id = $this->pavement_type_id;
        $history->pavement_thickness = $this->pavement_thickness;
        $history->ward_from_id = $this->ward_from_id ;
        $history->ward_to_id = $this->ward_to_id  ;
        $history->save();

        foreach ($this->layers()->get() as $layer) 
        {
            $l = tblSectionLayerHistory::where('sectiondata_history_id', $history->id)->where('layer_id', $layer->layer_id)->whereType(1)->first();
            if (!empty($l))
            {
                $l->thickness = $layer->thickness;
                $l->description = $layer->description;
                $l->material_type_id = $layer->material_type_id;
                $l->save();
            }
            else
            {
                $sl = new tblSectionLayerHistory();
                $sl->thickness = $layer->thickness;
                $sl->description = $layer->description;
                $sl->type = 1;
                $sl->material_type_id = $layer->material_type_id;
                $sl->layer_id = $layer->layer_id;
                $sl->rmdSection()->associate($history);
                $sl->save();
            }
        }
    }

    function scopeFilterByUser($query)
    {
        $user = \Auth::user();
        $user_organization = Auth::user()->organization_id;
        $organization = tblOrganization::where('parent_id', $user_organization)->get();
        $organization_id = [];
        foreach ($organization as $item)
        {
            $organization_id[] = $item->id;
        }
        if(Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlvl1p'))
        {
            return $query;
        }
        elseif (Auth::user()->hasRole('userlv2'))
        {
            // return $query->where('manage_l1_id', $user_organization);

            $query->whereHas('segment', function ($query) use ($organization_id) {
                $query->whereIn('SB_id', $organization_id);
            });
        }
        else
        {
            // return $query->where('manage_l2_id', $user_organization);
            $query->whereHas('segment', function ($query) use ($user_organization) {
                $query->where('SB_id', $user_organization);
            });
        }
        
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
    }

    function scopeFilterDropdown($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $query->where($key_in_db, $request->{$key_in_request})->get();
        }
    }

    function scopeFilterDropdownByProvince($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $k_r = $request->{$key_in_request};
            $query->whereHas('segment', function ($query) use ($k_r, $key_in_db) {
                $query->where($key_in_db, $k_r);
            });
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
    //import
    static public function config($except = [])
    {
        $object = [
            'section_id' => [
                'title' => trans('back_end.section_id'),
                'type' => 'text',
                'width' => 100,
                'index' => 0,
                'validate' => ''
            ],
            'route_branch' => [
                'title' => trans('back_end.route_branch'),
                'index' => 3,
                'type' => 'text',
                'width' => 100,
                'validate' => ''
            ],
            'road_class_id' => [
                'title' => trans('back_end.road_class'),
                'type' => 'select',
                'modelCheck' => \App\Models\mstRoadClass::allOptionToAjax($has_all = FALSE, $road_type = 1, $value_as_name = false, $has_name = true, $has_code = true, $has_text_id = true),
                'item' => \App\Models\mstRoadClass::allOptionToAjax(),
                'width' => 100,
                'index' => 4,
                'validate' => 'required'
            ],
            'r_category_id' => [
                'title' => trans('back_end.r_category_id'),
                'type' => 'text',
                'width' => 100,
                'index' => 5,
                'validate' => '',
            ],
            'rmb' => [
                'title' => trans('back_end.rmb'),
                'type' => 'select',
                'item_model' => '\App\Models\tblOrganization',
                'modelCheck' => tblOrganization::getListRmb($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListRmb(),
                'width' => 100,
                'index' => 6,
                'validate' => 'required'
            ],
            'sb' => [
                'relation' => [
                    'model'     => '\App\Models\tblOrganization',
                    'func'      => 'findSb',
                    'parent'    => 'rmb'
                ],
                'title' => trans('back_end.sb'),
                'type' => 'select',
                'item_model' => '\App\Models\tblOrganization',
                'modelCheck' => tblOrganization::getListSB($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListSB(),
                'width' => 100,
                'index' => 7,
                'validate' => 'required'
            ],
            'road' => [
                'relation' => [
                    'model'     => '\App\Models\tblBranch',
                    'func'      => 'findRouteBranch',
                    'parent'    => 'sb'
                ],
                'checkitem' => [
                    'model'     => '\App\Models\tblBranch',
                    'func'      => 'getCheck',
                    'data'      => 'sb'
                ],
                'title' => trans('back_end.route_branch'),
                'type' => 'select',
                'item_model' => '\App\Models\tblBranch',
                'modelCheck' => tblBranch::allOptionToAjax($has_all = FALSE, $value_as_name = FALSE, $has_name = true),
                'item' => tblBranch::allOptionToAjax(),
                'width' => 100,
                'index' => 1,
                'validate' => 'required'
            ],
            'segment_id' => [
                'checkitem' => [
                    'model'     => '\App\Models\tblSegment',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'road'
                ],
                'title' => trans('back_end.route'),
                'index' => 2,
                'type' => 'select',
                'special_name' => true,
                // 'item_model' => '\App\Models\tblSegment',
                'modelCheck' => \App\Models\tblSegment::allOptionToAjax($check = Null, $has_all = FALSE, $demo = FALSE, $has_name = true, $has_code = true),
                'item' => \App\Models\tblSegment::allOptionToAjax(),
                'width' => 100,
                'validate' => 'required'
            ],
            'km_from' => [
                'title' => trans('back_end.km_from'),
                'type' => 'text',
                'width' => 50,
                'index' => 8,
                'validate' => ['required','integer','min:0'],
            ],
            'm_from' => [
                'title' => trans('back_end.m_from'),
                'type' => 'text',
                'width' => 50,
                'index' => 9,
                'validate' => ['required','min:0'],

            ],
            'km_to' => [
                'title' => trans('back_end.km_to'),
                'type' => 'text',
                'width' => 50,
                'index' => 10,
                'validate' => ['required','integer','min:0'],
            ],
            'm_to' => [
                'title' => trans('back_end.m_to'),
                'type' => 'text',
                'width' => 50,
                'index' => 11,
                'validate' => ['required','min:0'],
            ],
            'from_lat' => [
                'title' => trans('back_end.from_lat'),
                'type' => 'text',
                'width' => 100,
                'index' => 12,
                'validate' => ['numeric','min:190004.0544','max:832157.7917'],
            ],
            'from_lng' => [
                'title' => trans('back_end.from_lng'),
                'type' => 'text',
                'width' => 100,
                'index' => 13,
                'validate' =>  ['numeric','min:663996.8088','max:2589882.7561'],
            ],
            'to_lat' => [
                'title' => trans('back_end.to_lat'),
                'type' => 'text',
                'width' => 100,
                'index' => 14,
                'validate' => ['numeric','min:190004.0544','max:832157.7917'],
            ],
            'to_lng' => [
                'title' => trans('back_end.to_lng'),
                'type' => 'text',
                'width' => 100,
                'index' => 15,
                'validate' => ['numeric','min:663996.8088','max:2589882.7561'],
            ],
            'province_from' => [
                'title' => trans('back_end.province_from'),
                'type' => 'select',
                'modelCheck' => tblCity::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblCity::allToOption(),
                'width' => 100,
                'index' => 16,
                'validate' => ''
            ],
            'district_from' => [
                'relation' => [
                    'model'     => '\App\Models\tblDistrict',
                    'func'      => 'findDistrict',
                    'parent'    => 'province_from'
                ],
                'checkitem' => [
                    'model'     => '\App\Models\tblDistrict',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'province_from'
                ],
                'title' => trans('back_end.district_from'),
                'type' => 'select',
                'width' => 100,
                'modelCheck' => tblDistrict::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblDistrict::allToOption(),
                'index' => 17,
                'validate' => ''
            ],
            'ward_from' => [
                'relation' => [
                    'model'     => '\App\Models\tblWard',
                    'func'      => 'findWard',
                    'parent'    => 'district_from'
                ],
                'checkitem' => [
                    'model'     => '\App\Models\tblWard',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'district_from'
                ],
                'title' => trans('back_end.ward_from'),
                'type' => 'select',
                'width' => 100,
                'modelCheck' => tblWard::allToOption($has_name = true),
                'item' => tblWard::allToOption(),
                'index' => 18,
                'validate' => ''
            ],
            'province_to' => [
                'title' => trans('back_end.province_to'),
                'type' => 'select',
                'width' => 100,
                'modelCheck' => tblCity::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblCity::allToOption(),
                'index' => 19,
                'validate' => ''
            ],
            'district_to' => [
                'relation' => [
                    'model'     => '\App\Models\tblDistrict',
                    'func'      => 'findDistrict',
                    'parent'    => 'province_to'
                ],

                'checkitem' => [
                    'model'     => '\App\Models\tblDistrict',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'province_to'
                ],
                'title' => trans('back_end.district_to'),
                'type' => 'select',
                'width' => 100,
                'modelCheck' => tblDistrict::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblDistrict::allToOption(),
                'index' => 20,
                'validate' => ''
            ],
            'ward_to' => [
                'relation' => [
                    'model'     => '\App\Models\tblWard',
                    'func'      => 'findWard',
                    'parent'    => 'district_to'
                ],
                'checkitem' => [
                    'model'     => '\App\Models\tblWard',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'district_to'
                ],
                'title' => trans('back_end.ward_to'),
                'type' => 'select',
                'width' => 100,
                'modelCheck' => tblWard::allToOption($has_name = true),
                'item' => tblWard::allToOption(),
                'index' => 21,
                'validate' => ''
            ],
            'kilometpost' => [
                'title' => trans('back_end.kilometpost'),
                'type' => 'text',
                'width' => 100,
                'index' => 22,
                'validate' =>'',
            ],
            'survey_time' => [
                'title' => trans('back_end.survey_time'),
                'type' => 'text',
                'width' => 100,
                'index' => 23,
                'validate' => array('required', 'date_format:Y-m-d', 'before:tomorrow',),
            ],
            'length_as_per_chainage' => [
                'title' => trans('back_end.length_as_per_chainage'),
                'type' => 'text',
                'width' => 100,
                'index' => 24,
                'validate' => '',
            ],
            'actual_length' => [
                'title' => trans('back_end.actual_length'),
                'type' => 'text',
                'width' => 100,
                'index' => 25,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'construct_year_y' => [
                'title' => trans('back_end.construct_year'),
                'type' => 'text',
                'width' => 100,
                'index' => 26,
                'validate' => ''
            ],
            'construct_year_m' => [
                'title' => trans('back_end.construct_year'),
                'type' => 'text',
                'width' => 100,
                'index' => 27,
                'validate' => '',
            ],
            'service_start_year_y' => [
                'title' => trans('back_end.service_start_year'),
                'type' => 'text',
                'width' => 100,
                'index' => 28,
                'validate' => ''
            ],
            'service_start_year_m' => [
                'title' => trans('back_end.service_start_year'),
                'type' => 'text',
                'width' => 100,
                'index' => 29,
                'validate' => ''
            ],
            'terrian_type_id' => [
                'title' => trans('back_end.terrian_type'),
                'type' => 'select',
                'modelCheck' => \App\Models\tblTerrainType::allToOption($has_all = FALSE, $has_name = true, $has_code = true, $has_code_name = true),
                'item' => \App\Models\tblTerrainType::allToOption(),
                'width' => 100,
                'index' => 30,
                'validate' => 'required'
            ],
            'temperature' => [
                'title' => trans('back_end.temperature'),
                'type' => 'text',
                'width' => 100,
                'index' => 31,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'annual_precipitation' => [
                'title' => trans('back_end.annual_precipitation'),
                'type' => 'text',
                'width' => 100,
                'index' =>  32,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'design_speed' => [
                'title' => trans('back_end.design_speed'),
                'type' => 'text',
                'width' => 100,
                'index' => 33,
                'validate' => ''
            ],
            'direction' => [
                'title' => trans('back_end.direction'),
                'type' => 'check_select',
                'items' => [
                    ['name' => trans('back_end.left'), 'value' => 1],
                    ['name' => trans('back_end.right'), 'value' => 2],
                    ['name' => trans('back_end.single'), 'value' => 3]
                ],
                'width' => 100,
                'index' => 34,
                'validate' => 'required'
            ],
            'pavement_width' => [
                'title' => trans('back_end.pavement_width'),
                'type' => 'text',
                'width' => 100,
                'index' => 35,
                'validate' => '',
            ],
            'pavement_thickness' => [
                'title' => trans('back_end.pavement_thickness'),
                'type' => 'text',
                'width' => 100,
                'index' => 36,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'no_lane' => [
                'title' => trans('back_end.no_lane'),
                'type' => 'text',
                'width' => 100,
                'index' => 37,
                'validate' => ['integer','min:0']
            ],
            'lane_pos_number' => [
                'title' => trans('back_end.lane_pos_number'),
                'type' => 'text',
                'width' => 100,
                'index' => 38,
                'validate' => ['integer','min:0', 'required']
            ],
            'lane_width' => [
                'title' => trans('back_end.lane_width'),
                'type' => 'text',
                'width' => 100,
                'index' => 39,
                'validate' => ['min:0', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/']
            ],
            'pavement_type_id' => [
                'title' => trans('back_end.pavement_type_id'),
                'index' => 40,
                'type' => 'checkdata',
                'item' => \App\Models\mstPavementType::toOption(),
                'width' => 100,
                'validate' => 'required',
            ],
            'remark' => [
                'title' => trans('back_end.remark'),
                'type' => 'text',
                'width' => 100,
                'index' => 41,
                'validate' => ""
            ],
        ];
        
        return $object;
    }

}
