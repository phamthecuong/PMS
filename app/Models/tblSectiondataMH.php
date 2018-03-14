<?php

namespace App\Models;

use App\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\tblMHHistory;
use App\Models\tblSectionLayerHistory;
use Illuminate\Support\Facades\DB;
use App\Models\tblOrganization;
use Auth;

class tblSectiondataMH extends Model
{
    protected $table = 'tblSectiondata_MH';
    protected $appends = ['r_struct_type_id'];
    protected $fillable = [
        'segment_id', 'km_from', 'm_from',
        'km_to', 'm_to',
        'survey_time', 'completion_date',
        'repair_duration', 'direction', 'actual_length', 'lane_pos_number',
        'total_width_repair_lane', 'r_classification_id', 'r_structType_id',
        'r_category_id', 'distance', 'direction_running',
        'remark', 'effect_at' , 'nullity_at',
        'from_lat', 'from_lng',
        'to_lat', 'to_lng',
        'ward_from_id', 'ward_to_id', 'pavement_type_id'
    ];

    function histories()
    {
        return $this->hasMany('App\Models\tblMHHistory', 'sectiondata_id')->orderBy('survey_time', 'desc');
    }

    function layers()
    {
        return $this->hasMany('App\Models\tblSectionLayer', 'sectiondata_id')->where('type', 2);
    }

    function segment()
    {
        return $this->belongsTo('App\Models\tblSegment', 'segment_id');
    }
    function wardFrom()
    {
        return $this->belongsTo('App\Models\tblWard', 'ward_from_id');
    }

    function wardTo()
    {
        return $this->belongsTo('App\Models\tblWard', 'ward_to_id');
    }

    function repairCategory()
    {
        return $this->belongsTo('App\Models\tblRCategory', 'r_category_id');
    }

    function repairStructType()
    {
        return $this->belongsTo('App\Models\tblRStructtype', 'r_structType_id');
    }

    function repairMethod()
    {
        return $this->belongsTo('App\Models\mstRepairMethod', 'repair_method_id');
    }

    function repairClassification()
    {
        return $this->belongsTo('App\Models\tblRClassification', 'r_classification_id');
    }

    public function getDirectionAttribute($value)
    {
        return (string) $value;
    }

    public function getDirectionRunningAttribute($value)
    {
        return (string) $value;
    }

    public function getRCategoryIdAttribute($value)
    {
        return (int) $value;
    }

    public function getRClassificationIdAttribute($value)
    {
        return (string) $value;
    }

    public function getRStructTypeIdAttribute($value)
    {
        return (string) $this->attributes['r_structType_id'];
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

    public function getRepairDurationAttribute($value)
    {
        return (float) $value;
    }

    public function getLanePosNumberAttribute($value)
    {
        return (int) $value;
    }

    public function getActualLengthAttribute($value)
    {
        return (float) $value;
    }

    public function getTotalWidthRepairLaneAttribute($value)
    {
        return (float) $value;
    }

    public function getDistanceAttribute($value)
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

    public function getRepairMethodIdAttribute($value)
    {
        return (string) $value;
    }

    /**
     * get segment that overlap to a chainage and in a branch
	 * @param segment_id: number, id in tblSegment
	 * @param side: number, -1: left, 1: right
     * @return array of segments
     */
	static function getOutsideBoundarySection($segment_id, $side, $km_from = null, $m_from = null, $km_to = null, $m_to = null)
	{
		$segment = tblSegment::find($segment_id);
		$rec;
		if ($side == -1)
		{
		    if ($km_from == null || $m_from == null || $km_to == null || $m_to == null)
            {
    			$pos = 10000*$segment->km_from + $segment->m_from;
    			$rec = tblSectiondataMH::where('segment_id', $segment_id)
    				->whereRaw("10000*km_from+m_from < {$pos}")
    				->get();
            }
            else 
            {
                $pos = 10000*$km_from + $m_from;
                $rec = tblSectiondataMH::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_from+m_from < {$pos}")
                    ->get();
            }	
		}
		else
		{
		    if ($km_from == null || $m_from == null || $km_to == null || $m_to == null)
            {
    			$pos = 10000*$segment->km_to + $segment->m_to;
    			$rec = tblSectiondataMH::where('segment_id', $segment_id)
    				->whereRaw("10000*km_to+m_to > {$pos}")
    				->get();
            }
            else
            {
                $pos = 10000*$km_to + $m_to;
                $rec = tblSectiondataMH::where('segment_id', $segment_id)
                    ->whereRaw("10000*km_to+m_to > {$pos}")
                    ->get();
            }
		}
		return $rec;
	}

    static public function boot()
    {
        tblSectiondataMH::saving(function($rec) {
            foreach ($rec->attributes as $key => $value) 
            {
                if ($key == 'from_lat' || $key == 'from_lng' || $key == 'to_lat' || $key == 'to_lng') {
                    $rec->{$key} = ($value == NULL) ? NULL : $value;
                }
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

        tblSectiondataMH::deleting(function($model) {
            $mh = tblSectiondataMH::find($model->id);
            if (count($mh->layers()->get()) > 0)
            {
                foreach ($mh->layers()->get() as $layer) 
                {
                    $layer->delete();
                }
            }
        });
    }
    static public function getDirection()
    {
        // $data = DB::table('tblSectiondata_MH')->groupBy('direction')->get();
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

    function createHistory()
    {
        $history = new tblMHHistory();
        $history->segment_id = $this->segment_id;
        $history->from_lat = $this->from_lat;
        $history->from_lng = $this->from_lng;
        $history->to_lat = $this->to_lat;
        $history->to_lng = $this->to_lng;
        $history->km_from = $this->km_from;
        $history->m_from = $this->m_from;
        $history->km_to = $this->km_to;
        $history->m_to = $this->m_to;
        $history->survey_time = $this->survey_time;
        $history->completion_date = $this->completion_date;
        $history->repair_duration = $this->repair_duration;
        $history->direction = $this->direction;
        $history->actual_length = $this->actual_length;
        $history->lane_pos_number = $this->lane_pos_number;
        $history->total_width_repair_lane = $this->total_width_repair_lane;
        $history->r_classification_id = $this->r_classification_id;
        $history->r_structType_id = $this->r_structType_id;
        $history->r_category_id = $this->r_category_id;
        $history->distance = $this->distance;
        $history->direction_running = $this->direction_running;
        $history->remark = $this->remark;
        $history->pavement_type_id = $this->pavement_type_id;
        $history->ward_from_id = $this->ward_from_id ;
        $history->ward_to_id = $this->ward_to_id  ;
        $history->repair_method_id = $this->repair_method_id  ;
        $history->mh()->associate($this);
        $history->save();

        foreach ($this->layers()->get() as $layer) 
        {
            $l = new tblSectionLayerHistory();
            $l->thickness = $layer->thickness;
            $l->description = $layer->description;
            $l->type = 2;
            $l->material_type_id = $layer->material_type_id;
            $l->layer_id = $layer->layer_id;
            $l->rmdSection()->associate($history);
            $l->save();
        }
    }
    function newHistory()
    {
        $history = new tblMHHistory();
        $history->segment_id = $this->segment_id;
        $history->from_lat = $this->from_lat;
        $history->from_lng = $this->from_lng;
        $history->to_lat = $this->to_lat;
        $history->to_lng = $this->to_lng;
        $history->km_from = $this->km_from;
        $history->m_from = $this->m_from;
        $history->km_to = $this->km_to;
        $history->m_to = $this->m_to;
        $history->survey_time = $this->survey_time;
        $history->completion_date = $this->completion_date;
        $history->repair_duration = $this->repair_duration;
        $history->direction = $this->direction;
        $history->actual_length = $this->actual_length;
        $history->lane_pos_number = $this->lane_pos_number;
        $history->total_width_repair_lane = $this->total_width_repair_lane;
        $history->r_classification_id = $this->r_classification_id;
        $history->r_category_id = $this->r_category_id;
        $history->distance = $this->distance;
        $history->direction_running = $this->direction_running;
        $history->pavement_type_id = $this->pavement_type_id;
        $history->ward_from_id = $this->ward_from_id ;
        $history->ward_to_id = $this->ward_to_id  ;
        $history->repair_method_id = $this->repair_method_id  ;
        $history->mh()->associate($this);
        $history->save();

        foreach ($this->layers()->get() as $layer) 
        {
            $l = new tblSectionLayerHistory();
            $l->thickness = $layer->thickness;
            $l->description = $layer->description;
            $l->type = 2;
            $l->material_type_id = $layer->material_type_id;
            $l->layer_id = $layer->layer_id;
            $l->rmdSection()->associate($history);
            $l->save();
        }
    }

    function updateHistory()
    {
        $history = tblMHHistory::where('sectiondata_id', $this->id)->orderBy('survey_time', 'desc')->first();
        $history->segment_id = $this->segment_id;
        $history->from_lat = $this->from_lat;
        $history->from_lng = $this->from_lng;
        $history->to_lat = $this->to_lat;
        $history->to_lng = $this->to_lng;
        $history->km_from = $this->km_from;
        $history->m_from = $this->m_from;
        $history->km_to = $this->km_to;
        $history->m_to = $this->m_to;
        $history->survey_time = $this->survey_time;
        $history->completion_date = $this->completion_date;
        $history->repair_duration = $this->repair_duration;
        $history->direction = $this->direction;
        $history->actual_length = $this->actual_length;
        $history->lane_pos_number = $this->lane_pos_number;
        $history->total_width_repair_lane = $this->total_width_repair_lane;
        $history->r_classification_id = $this->r_classification_id;
        $history->r_structType_id = $this->r_structType_id;
        $history->r_category_id = $this->r_category_id;
        $history->distance = $this->distance;
        $history->direction_running = $this->direction_running;
        $history->remark = $this->remark;
        $history->pavement_type_id = $this->pavement_type_id;
        $history->ward_from_id = $this->ward_from_id;
        $history->ward_to_id = $this->ward_to_id;
        $history->repair_method_id = $this->repair_method_id;
        $history->save();

        foreach ($this->layers()->get() as $layer) 
        {
            $l = tblSectionLayerHistory::where('sectiondata_history_id', $history->id)->where('layer_id', $layer->layer_id)->whereType(2)->first();
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
                $sl->type = 2;
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
        if (isset($request->survey_time) && !empty($request->survey_time))
        {
            $query->Where('survey_time', 'like', '%' . $request->survey_time . '%')->get();
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
   
    //import
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
                'index' => 3,
                'type' => 'text',
                'width' => 100,
                'validate' => ''
            ],
            'road_class_id' => [
                'title' => trans('back_end.road_class'),
                'index' => 4,
                'type' => 'select',
                'modelCheck' => \App\Models\mstRoadClass::allOptionToAjax($has_all = FALSE, $road_type = 1, $value_as_name = false, $has_name = true, $has_code = true, $has_text_id = true),
                'item' => \App\Models\mstRoadClass::allOptionToAjax($has_all = FALSE, $road_type = 1, $value_as_name = false, $has_name = true, $has_code = true, $has_text_id = true),
                'width' => 100,
                'validate' => ''
            ],
            'rmb' => [
                'title' => trans('back_end.rmb'),
                'index' => 5,
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
                'index' => 6,
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
            'segment_id' => [
                'checkitem' => [
                    'model'     => '\App\Models\tblSegment',
                    'func'      => 'allOptionToAjax',
                    'data'      => 'road'
                ],
                'title' => trans('back_end.route'),
                'index' => 2,
                'type' => 'select',
                'modelCheck' => \App\Models\tblSegment::allOptionToAjax($check = null, $has_all = FALSE, $demo = FALSE, $has_name = true, $has_code = true),
                'item' => \App\Models\tblSegment::allOptionToAjax($check = null, $has_all = FALSE, $demo = FALSE, $has_name = true, $has_code = true),
                'width' => 100,
                'validate' => 'required'
            ],
            'km_from' => [
                'title' => trans('back_end.km_from'),
                'index' => 7,
                'type' => 'text',
                'width' => 50,
                'validate' => array('required','integer','min:0'),
            ],
            'm_from' => [
                'title' => trans('back_end.m_from'),
                'index' => 8,
                'type' => 'text',
                'width' => 50,
                'validate' => ['required', 'min:0'],
            ],
            'km_to' => [
                'title' => trans('back_end.km_to'),
                'index' => 9,
                'type' => 'text',
                'width' => 50,
                'validate' => ['required','integer','min:0'],
            ],
            'm_to' => [
                'title' => trans('back_end.m_to'),
                'index' => 10,
                'type' => 'text',
                'width' => 50,
                'validate' => ['required', 'min:0'],
            ],
            'from_lat' => [
                'title' => trans('back_end.from_lat'),
                'index' => 11,
                'type' => 'text',
                'width' => 100,
                'validate' => ['numeric','min:190004.0544','max:832157.7917'],
            ],
            'from_lng' => [
                'title' => trans('back_end.from_lng'),
                'index' => 12,
                'type' => 'text',
                'width' => 100,
                'validate' =>  ['numeric','min:663996.8088','max:2589882.7561'],
            ],
            'to_lat' => [
                'title' => trans('back_end.to_lat'),
                'index' => 13,
                'type' => 'text',
                'width' => 100,
                'validate' => ['numeric','min:190004.0544','max:832157.7917'],
            ],
            'to_lng' => [
                'title' => trans('back_end.to_lng'),
                'index' => 14,
                'type' => 'text',
                'width' => 100,
                'validate' => ['numeric','min:663996.8088','max:2589882.7561'],
            ],
            'province_from' => [
                'title' => trans('back_end.province_from'),
                'index' => 15,
                'type' => 'select',
                'modelCheck' => tblCity::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblCity::allToOption(),
                'width' => 100,
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
                'index' => 16,
                'type' => 'select',
                'modelCheck' => tblDistrict::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblDistrict::allToOption(),
                'width' => 100,
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
                'index' => 17,
                'type' => 'select',
                'modelCheck' => tblWard::allToOption($has_name = true),
                'item' => tblWard::allToOption(),
                'width' => 100,
                'validate' => ''
            ],
            'province_to' => [
                'title' => trans('back_end.province_to'),
                'index' => 18,
                'type' => 'select',
                'modelCheck' => tblCity::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblCity::allToOption(),
                'width' => 100,
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
                'index' => 19,
                'type' => 'select',
                'modelCheck' => tblDistrict::allToOption($has_all = FALSE, $has_name = true),
                'item' => tblDistrict::allToOption(),
                'width' => 100,
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
                'index' => 20,
                'type' => 'select',
                'modelCheck' => tblWard::allToOption($has_name = true),
                'item' => tblWard::allToOption(),
                'width' => 100,
                'validate' => ''
            ],
            'kilopost_adjustment_date' => [
                'title' => trans('back_end.kilopost_adjustment_date'),
                'index' => 21,
                'type' => 'text',
                'width' => 100,
                'validate' =>'',
            ],
            'survey_time' => [
                'title' => trans('back_end.survey_time'),
                'index' => 22,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required', 'date_format:Y-m-d', 'before:tomorrow',),
            ],
            'direction' => [
                'title' => trans('back_end.direction'),
                'index' => 23,
                'type' => 'check_select',
                'items' => [
                    ['name' => trans('back_end.left'), 'value' => 1],
                    ['name' => trans('back_end.right'), 'value' => 2],
                    ['name' => trans('back_end.single'), 'value' => 3]
                ],
                'width' => 100,
                'validate' => 'required'
            ],
            'length' => [
                'sum' => [
                    'child' => 'chainage',
                    'amount' => 'length',
                ],
                'title' => trans('back_end.Length'),
                'index' => 24,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'actual_length' => [
                'title' => trans('back_end.actual_length'),
                'index' => 25,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required','regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'lane_pos_number' => [
                'title' => trans('back_end.lane_no'),
                'index' => 26,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required','integer','min:0'),
            ],
            'completion_date' => [
                'title' => trans('back_end.completion_date'),
                'index' => 27,
                'type' => 'text',
                'width' => 100,
                'validate' => 'required',
            ],
            'repair_duration' => [
                'title' => trans('back_end.repair_duration'),
                'index' => 28,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required','regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'r_category_id' => [
                'title' => trans('back_end.repair_classification'),
                'index' => 29,
                'type' => 'select',
                'modelCheck' => \App\Models\tblRCategory::allToOption($has_name = true, $has_code = true),
                'item' => \App\Models\tblRCategory::allToOption(),
                'width' => 100,
                'validate' => '',
            ],
            'repair_method' => [
                'title' => trans('back_end.repair_method'),
                'index' => 30,
                'type' => 'select',
                'modelCheck' => \App\Models\mstRepairMethod::allToOptionTwo($has_name = true, $has_code = true),
                'item' => \App\Models\mstRepairMethod::allToOptionTwo(),
                'width' => 100,
                'validate' => '',
            ],
            'r_classification_id' => [
                'title' => trans('back_end.repair_category'),
                'index' => 31,
                'type' => 'select',
                'modelCheck' => \App\Models\tblRClassification::allToOption($has_name = true, $has_code = true),
                'item' => \App\Models\tblRClassification::allToOption(),
                'width' => 100,
                'validate' => '',
            ],
            'pavement_type_id' => [
                'title' => trans('back_end.pavement_type_id'),
                'index' => 32,
                'type' => 'checkdata',
                'item' => \App\Models\mstPavementType::toOption(),
                'width' => 100,
                'validate' => '',
            ],
            'binder_course' => [
                'title' => trans('back_end.binder_course'),
                'index' => 33,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'wearing_course' => [
                'title' => trans('back_end.wearing_course'),
                'index' => 34,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'total' => [
                'title' => trans('back_end.total'),
                'index' => 35,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],
            'total_pavement_thickness' => [
                'title' => trans('back_end.total_pavement_thickness'),
                'index' => 36,
                'type' => 'text',
                'width' => 100,
                'validate' => array('regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'total_width_repair_lane' => [
                'title' => trans('back_end.repair_width'),
                'index' => 37,
                'type' => 'text',
                'width' => 100,
                'validate' => array('required','regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'direction_running' => [
                'title' => trans('back_end.direction_running'),
                'index' => 38,
                'type' => 'radio_check',
                'width' => 100,
                'validate' => '',
            ],
            'distance' => [
                'title' => trans('back_end.distance_to_center'),
                'index' => 39,
                'type' => 'text',
                'width' => 100,
                'validate' => '',
            ],


        ];
        if (in_array('chainage', $except))
        {
            $object = array_except($object, ['km_to', 'm_to', 'to_lat', 'to_lng']);
        }
        return $object;
    }
}
