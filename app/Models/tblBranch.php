<?php

namespace App\Models;

use App;
use Config;
use Illuminate\Database\Eloquent\Model;

class tblBranch extends Model
{
    protected $table = 'tblBranch';

    protected $appends = ["route_name", "name"];

    public function getNameAttribute()
    {
        $key = "name_".\App::getLocale();
        if ($key == "name_vi")
        {
            $key = "name_vn";
        }
        return $this->attributes[$key];
    }
    // public function tblRoute()
    // {
    //     return $this->belongsTo('App\Models\tblRoad', 'route_id');
    // }
    public function tblSectionPCHistory()
    {
        return $this->hasMany('App\Models\tblSectionPCHistory', 'branch_id');
    }
    public function segments()
    {
        return $this->hasMany('App\Models\tblSegment', 'branch_id');
    }

    public function mstRoadCategory()
    {
        return $this->belongsTo('App\Models\mstRoadCategory', 'road_category', 'code_id');
    }

    public function mstRoadNumberSupplement()
    {
        return $this->belongsTo('App\Models\mstRoadNumberSupplement', 'road_number_supplement', 'id');
    }

    public function userCreate()
    {
        return $this->belongsTo('App\Models\user', 'created_by', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('App\Models\user', 'updated_by', 'id');
    }

    public function getRouteNameAttribute()
    {
        $lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return $this->{"name_{$lang}"};
    }

    static function findRouteNameByRouteId($route_id)
    {
        $lang_cd = (App::getLocale() == 'en') ? 'en' : 'vn';
        $rec = tblBranch::where('road_number', substr($route_id, 3, 3))
            ->where('road_number_supplement', substr($route_id, 6, 3))
            ->where('road_category', substr($route_id, 2, 1))
            ->orderBy('branch_number')
            ->first();
        if ($rec) {
            return $rec->{"name_$lang_cd"};
        } else {
            return '';
        }
    }

    static function allToOption($has_all = FALSE)
    {
        $data = tblBranch::all();

        $dataset = array();
        if ($has_all !== FALSE) {
            $dataset += array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r) {
            $dataset += array(
                $r->id => (Config::get('app.locale') == 'en') ? $r->name_en : $r->name_vn,
            );
        }

        return $dataset;
    }

    static function allOptionToAjax($has_all = FALSE, $value_as_name = FALSE, $has_name = FALSE)
    {
        $data;
        if (\Auth::check()) 
        {
            $user = \Auth::user();
            $user_organization = \Auth::user()->organization_id;
            $organization = tblOrganization::where('parent_id', $user_organization)->get();
            $organization_id = [];
            foreach ($organization as $item)
            {
                $organization_id[] = $item->id;
            }
            if(\Auth::user()->hasRole('userlv1') || \Auth::user()->hasRole('superadmin') || \Auth::user()->hasRole('userlvl1p'))
            {
                $data = tblBranch::all();
            }
            elseif (\Auth::user()->hasRole('userlv2'))
            {
                // return $query->where('manage_l1_id', $user_organization);

                $data = tblBranch::whereHas('segments', function ($query) use ($organization_id) {
                    $query->whereIn('SB_id', $organization_id);
                })->get();
            }
            else
            {
                // return $query->where('manage_l2_id', $user_organization);
                $data = tblBranch::whereHas('segments', function ($query) use ($user_organization) {
                    $query->where('SB_id', $user_organization);
                })->get();
            }
        }
        else
        {
            $data = tblBranch::all();
        }
        // $data = tblBranch::all();

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
                $dataset[] = array(
                    'name' => $b->name_vn,
                    'value' => $b->id
                );
                $dataset[] = array(
                    'name' => $b->name_en,
                    'value' => $b->id
                );
            }
            else
            {
                $dataset[$b->id] = array(
                    'name' => (\App::isLocale('en')) ? $b->name_en : $b->name_vn,
                    'value' => $value_as_name ? ((\App::isLocale('en')) ? $b->name_en : $b->name_vn) : $b->id,
                    // 'selected' => FALSE,
                );
            }
        }

        return $dataset;
    }
    static function getCheck($check = null ,$has_all = FALSE, $value_as_name = FALSE, $has_name = FALSE)
    {
        $data;
        if (\Auth::check()) 
        {
            $user = \Auth::user();
            $user_organization = \Auth::user()->organization_id;
            $organization = tblOrganization::where('parent_id', $user_organization)->get();
            $organization_id = [];
            foreach ($organization as $item)
            {
                $organization_id[] = $item->id;
            }
            if(\Auth::user()->hasRole('userlv1') || \Auth::user()->hasRole('superadmin') || \Auth::user()->hasRole('userlvl1p'))
            {
                if ($check != null) {
                    $data = tblBranch::all();
                }
                else
                {
                    $data = tblBranch::all();
                }
            }
            elseif (\Auth::user()->hasRole('userlv2'))
            {
                // return $query->where('manage_l1_id', $user_organization);

                $data = tblBranch::whereHas('segments', function ($query) use ($organization_id) {
                    $query->whereIn('SB_id', $organization_id);
                })->get();
            }
            else
            {
                // return $query->where('manage_l2_id', $user_organization);
                $data = tblBranch::whereHas('segments', function ($query) use ($user_organization) {
                    $query->where('SB_id', $user_organization);
                })->get();
            }
        }
        else
        {
            $data = tblBranch::all();
        }
        // $data = tblBranch::all();

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
                $dataset[] = array(
                    'name' => $b->name_vn,
                    'value' => $b->id
                );
                $dataset[] = array(
                    'name' => $b->name_en,
                    'value' => $b->id
                );
            }
            else
            {
                $dataset[$b->id] = array(
                    'name' => (\App::isLocale('en')) ? $b->name_en : $b->name_vn,
                    'value' => $value_as_name ? ((\App::isLocale('en')) ? $b->name_en : $b->name_vn) : $b->id,
                    // 'selected' => FALSE,
                );
            }
        }

        return $dataset;
    }
    static function branchNumberOptionToAjax($has_all = FALSE)
    {
        $data = tblBranch::groupBy('branch_number')->get();

        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset += array(
                -1 => $has_all,
            );
        }

        foreach ($data as $b)
        {
            $dataset[$b->id] = array(
                'name' => $b->branch_number,
                'value' => $b->branch_number,
                'selected' => FALSE,
            );
        }

        return $dataset;
    }

    static function getRouteByRmb($rmb)
    {
        $data = tblSegment::join('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id');
                            
        if (is_array($rmb))
        {
            $data = $data->join('tblOrganization', function ($join) use ($rmb) {
                                $join->on('tblOrganization.id', '=', 'tblSegment.SB_id')
                                     ->whereIn('tblOrganization.parent_id', $rmb);
                            });
        }
        else
        {
            $data = $data->join('tblOrganization', function ($join) use ($rmb) {
                                $join->on('tblOrganization.id', '=', 'tblSegment.SB_id')
                                     ->where('tblOrganization.parent_id', '=', $rmb);
                            });
        }
                            
        $data = $data->select('tblBranch.*')->groupBy('tblBranch.id')->get();
        $roads = array();
        foreach ($data as $p)
        {
            $roads[] = array(
                'id' => $p->id,
                'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn, 
            );
        }
        return $roads;
    }

    static function findRouteBranch($sb_id)
    {
        return tblBranch::orderBy('branch_number')
            ->whereHas('segments', function($query) use($sb_id)
            {
                $query->where('SB_id', $sb_id);
            })->pluck('id')->toArray();
    }
}
